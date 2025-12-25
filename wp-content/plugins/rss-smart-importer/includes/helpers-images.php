<?php
/**
 * Image handling helpers for RSS Smart Importer
 */

if (!defined('ABSPATH')) exit;

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

/**
 * Check if running in development environment
 *
 * @return bool True if development environment
 */
function rsi_is_development() {
  // Check WP_ENVIRONMENT_TYPE (WordPress 5.5+)
  if (function_exists('wp_get_environment_type')) {
    $env_type = wp_get_environment_type();
    if (in_array($env_type, array('local', 'development'))) {
      return true;
    }
  }
  
  // Check WP_DEBUG constant
  if (defined('WP_DEBUG') && WP_DEBUG === true) {
    return true;
  }
  
  // Check for localhost/local domain
  $site_url = get_site_url();
  if (preg_match('/localhost|127\.0\.0\.1|\.local/i', $site_url)) {
    return true;
  }
  
  return false;
}

/**
 * Download images from content and rewrite URLs to local
 *
 * @param string $content Post content with external image URLs
 * @param int $post_id Post ID to attach images to
 * @param object|null $item SimplePie feed item (for enclosure support)
 * @return array ['content' => rewritten HTML, 'featured_id' => first image ID or 0]
 */
function rsi_process_images($content, $post_id, $item = null) {
  $featured_id = 0;
  
  // First, check for enclosure images (used as featured if available)
  if ($item) {
    $enclosures = $item->get_enclosures();
    error_log('[RSS Smart Importer] Checking enclosures for post ' . $post_id . ': ' . (empty($enclosures) ? 'none found' : count($enclosures) . ' found'));
    
    if (!empty($enclosures)) {
      foreach ($enclosures as $enclosure) {
        $type = $enclosure->get_type();
        $enclosure_url = $enclosure->get_link();
        
        error_log('[RSS Smart Importer] Enclosure found - Type: ' . ($type ?? 'null') . ', URL: ' . ($enclosure_url ?? 'null'));
        
        // Check if it's an image type
        if ($type && strpos($type, 'image/') === 0) {
          if ($enclosure_url) {
            error_log('[RSS Smart Importer] Attempting to download enclosure image: ' . $enclosure_url);
            $attachment_id = rsi_sideload_image($enclosure_url, $post_id);
            
            if ($attachment_id) {
              error_log('[RSS Smart Importer] Successfully downloaded enclosure image, attachment ID: ' . $attachment_id);
              if ($featured_id === 0) {
                $featured_id = $attachment_id;
                break; // Use first image enclosure as featured
              }
            } else {
              error_log('[RSS Smart Importer] Failed to download enclosure image: ' . $enclosure_url);
            }
          }
        } else {
          error_log('[RSS Smart Importer] Skipping enclosure - not an image type');
        }
      }
    }
  }
  
  // Find all img tags in content
  preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);
  
  if (empty($matches[1])) {
    return array('content' => $content, 'featured_id' => $featured_id);
  }
  
  $url_map = array(); // Map external URL => local URL
  
  foreach ($matches[1] as $image_url) {
    // Skip if already processed
    if (isset($url_map[$image_url])) {
      continue;
    }
    
    $attachment_id = false;
    
    // Handle base64 images (with or without data: prefix)
    if (strpos($image_url, 'data:image/') === 0 || strpos($image_url, 'image/') === 0) {
      $attachment_id = rsi_sideload_base64_image($image_url, $post_id);
    }
    // Handle regular URLs
    elseif (filter_var($image_url, FILTER_VALIDATE_URL)) {
      $attachment_id = rsi_sideload_image($image_url, $post_id);
    }
    
    if ($attachment_id) {
      $local_url = wp_get_attachment_url($attachment_id);
      if ($local_url) {
        $url_map[$image_url] = $local_url;
        // Set first content image as featured (if no enclosure was found)
        if ($featured_id === 0) {
          $featured_id = $attachment_id;
        }
      }
    }
  }
  
  // Rewrite all URLs in content
  foreach ($url_map as $old_url => $new_url) {
    $content = str_replace($old_url, $new_url, $content);
  }
  
  return array('content' => $content, 'featured_id' => $featured_id);
}

/**
 * Sideload base64 image to media library
 *
 * @param string $base64_data Base64 image data (e.g., data:image/jpeg;base64,/9j/4AAQ...)
 * @param int $post_id Post ID to attach to
 * @return int|false Attachment ID or false on failure
 */
function rsi_sideload_base64_image($base64_data, $post_id) {
  try {
    // Parse base64 data - handle both formats:
    // Format 1: data:image/jpeg;base64,/9j/4AAQ...
    // Format 2: image/jpeg;base64,/9j/4AAQ...
    if (preg_match('/^data:image\/([a-zA-Z]+);base64,(.+)$/', $base64_data, $matches)) {
      // Standard data: URI format
      $image_type = $matches[1];
      $image_data = base64_decode($matches[2]);
    } elseif (preg_match('/^image\/([a-zA-Z]+);base64,(.+)$/', $base64_data, $matches)) {
      // Missing data: prefix format
      $image_type = $matches[1];
      $image_data = base64_decode($matches[2]);
    } else {
      error_log('[RSS Smart Importer] Invalid base64 image format: ' . substr($base64_data, 0, 50) . '...');
      return false;
    }
    
    if ($image_data === false) {
      error_log('[RSS Smart Importer] Failed to decode base64 image data');
      return false;
    }
    
    // Validate image type
    $allowed_types = array('jpeg', 'jpg', 'png', 'gif', 'webp');
    if (!in_array(strtolower($image_type), $allowed_types)) {
      error_log('[RSS Smart Importer] Unsupported base64 image type: ' . $image_type);
      return false;
    }
    
    // Create temporary file
    $temp_file = wp_tempnam('base64_image');
    if (!$temp_file) {
      error_log('[RSS Smart Importer] Failed to create temporary file for base64 image');
      return false;
    }
    
    // Write image data to temp file
    $bytes_written = file_put_contents($temp_file, $image_data);
    if ($bytes_written === false) {
      error_log('[RSS Smart Importer] Failed to write base64 image data to temp file');
      @unlink($temp_file);
      return false;
    }
    
    // Generate filename
    $file_name = 'base64-image-' . time() . '.' . $image_type;
    
    // Prepare file array
    $file_array = array(
      'name' => $file_name,
      'tmp_name' => $temp_file
    );
    
    // Sideload to media library
    $attachment_id = media_handle_sideload($file_array, $post_id);
    
    if (is_wp_error($attachment_id)) {
      @unlink($temp_file);
      error_log('[RSS Smart Importer] Failed to sideload base64 image: ' . $attachment_id->get_error_message());
      return false;
    }
    
    return $attachment_id;
    
  } catch (Exception $e) {
    error_log('[RSS Smart Importer] Exception processing base64 image: ' . $e->getMessage());
    return false;
  }
}

/**
 * Sideload single image to media library
 *
 * @param string $url External image URL
 * @param int $post_id Post ID to attach to
 * @return int|false Attachment ID or false on failure
 */
function rsi_sideload_image($url, $post_id) {
  try {
    // Check if in development environment
    $is_dev = rsi_is_development();
    
    // In development, disable SSL verification for image downloads
    if ($is_dev) {
      add_filter('https_ssl_verify', '__return_false');
      add_filter('https_local_ssl_verify', '__return_false');
      add_filter('http_request_args', function($args, $request_url) {
        $args['sslverify'] = false;
        $args['timeout'] = 30;
        return $args;
      }, 10, 2);
    }
    
    // Download file to temp location
    $timeout_seconds = 30;
    $temp_file = download_url($url, $timeout_seconds);
    
    // Remove filters after download attempt
    if ($is_dev) {
      remove_filter('https_ssl_verify', '__return_false');
      remove_filter('https_local_ssl_verify', '__return_false');
      remove_all_filters('http_request_args');
    }
    
    if (is_wp_error($temp_file)) {
      error_log('[RSS Smart Importer] Failed to download image: ' . $url . ' - ' . $temp_file->get_error_message());
      return false;
    }
    
    // Get file name from URL
    $file_name = basename(parse_url($url, PHP_URL_PATH));
    if (empty($file_name)) {
      $file_name = 'image-' . time() . '.jpg';
    }
    
    // Prepare file array
    $file_array = array(
      'name' => $file_name,
      'tmp_name' => $temp_file
    );
    
    // Sideload to media library
    $attachment_id = media_handle_sideload($file_array, $post_id);
    
    if (is_wp_error($attachment_id)) {
      @unlink($temp_file);
      error_log('[RSS Smart Importer] Failed to sideload image: ' . $url . ' - ' . $attachment_id->get_error_message());
      return false;
    }
    
    return $attachment_id;
    
  } catch (Exception $e) {
    error_log('[RSS Smart Importer] Exception downloading image: ' . $url . ' - ' . $e->getMessage());
    return false;
  }
}

