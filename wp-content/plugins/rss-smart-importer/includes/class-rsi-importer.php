<?php
/**
 * RSS Importer - Core import logic and scheduling
 */

if (!defined('ABSPATH')) exit;

class RSI_Importer {
  
  const LOCK_KEY = 'rsi_lock';
  const LOCK_TTL = 600; // 10 minutes
  const CRON_HOOK = 'rsi_import_cron';
  
  public function __construct() {
    add_action(self::CRON_HOOK, array($this, 'run_import'));
    add_filter('cron_schedules', array($this, 'add_cron_interval'));
  }
  
  /**
   * Clear stuck import lock
   */
  public static function clear_lock() {
    delete_transient(self::LOCK_KEY);
  }
  
  /**
   * Check if running in development environment
   *
   * @return bool True if development environment
   */
  private function is_development() {
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
   * Register custom cron interval
   */
  public function add_cron_interval($schedules) {
    $settings = get_option('rsi_settings', array());
    $hours = intval($settings['schedule_hours'] ?? 0);
    $minutes = intval($settings['schedule_minutes'] ?? 0);
    $interval_seconds = ($hours * 3600) + ($minutes * 60);
    
    if ($interval_seconds > 0) {
      // Validate min 5 min, max 24 hours
      if ($interval_seconds < 300) $interval_seconds = 300;
      if ($interval_seconds > 86400) $interval_seconds = 86400;
      
      $schedules['rsi_custom'] = array(
        'interval' => $interval_seconds,
        'display' => sprintf('Every %d hours %d minutes', $hours, $minutes)
      );
    }
    
    return $schedules;
  }
  
  /**
   * Schedule or reschedule cron based on settings
   */
  public static function update_schedule($hours, $minutes) {
    // Clear any existing scheduled events
    $timestamp = wp_next_scheduled(self::CRON_HOOK);
    if ($timestamp) {
      wp_unschedule_event($timestamp, self::CRON_HOOK);
    }
    
    // Only schedule if not manual-only (hours or minutes > 0)
    if ($hours > 0 || $minutes > 0) {
      wp_schedule_event(time(), 'rsi_custom', self::CRON_HOOK);
    }
  }
  
  /**
   * Run import with lock to prevent overlaps
   *
   * @return array Results stats
   */
  public function run_import() {
    // Check lock
    if (get_transient(self::LOCK_KEY)) {
      error_log('[RSS Smart Importer] Import already running, skipping...');
      return array('error' => 'Import already in progress');
    }
    
    // Set lock
    set_transient(self::LOCK_KEY, true, self::LOCK_TTL);
    
    try {
      $start_time = microtime(true);
      $settings = get_option('rsi_settings', array());
      $feeds = array_filter(array_map('trim', explode("\n", $settings['feeds'] ?? '')));
      $items_per_feed = intval($settings['items_per_feed'] ?? 10);
      $post_status = sanitize_key($settings['post_status'] ?? 'draft');
      
      
      $results = array();
      $totals = array('found' => 0, 'duplicates' => 0, 'imported' => 0, 'updated' => 0, 'failed' => 0);
      
      foreach ($feeds as $feed_url) {
        $feed_result = $this->import_feed($feed_url, $items_per_feed, $post_status, true);
        $results[] = $feed_result;
        
        // Aggregate totals
        foreach ($totals as $key => $value) {
          $totals[$key] += $feed_result[$key];
        }
      }
      
      $duration = round(microtime(true) - $start_time, 2);
      
      // Save to history (last 10 runs)
      $history = get_option('rsi_last_runs', array());
      array_unshift($history, array(
        'time' => current_time('mysql'),
        'results' => $results,
        'totals' => $totals,
        'duration' => $duration
      ));
      $history = array_slice($history, 0, 10);
      update_option('rsi_last_runs', $history);
      
      return array('results' => $results, 'totals' => $totals, 'duration' => $duration);
      
    } catch (Exception $e) {
      error_log('[RSS Smart Importer] Exception during import: ' . $e->getMessage());
      return array('error' => 'Import failed: ' . $e->getMessage());
      
    } catch (Throwable $e) {
      error_log('[RSS Smart Importer] Fatal error during import: ' . $e->getMessage());
      return array('error' => 'Import failed: ' . $e->getMessage());
      
    } finally {
      // ALWAYS release lock, even on fatal errors
      delete_transient(self::LOCK_KEY);
    }
  }
  
  /**
   * Public method to import a single feed (for manual imports)
   *
   * @param string $feed_url The feed URL to import
   * @param int $limit Number of items to import
   * @param string $post_status Post status for imported posts
   * @return array Import statistics
   */
  public function import_single_feed($feed_url, $limit = 1, $post_status = 'draft') {
    return $this->import_feed($feed_url, $limit, $post_status, true);
  }
  
  /**
   * Import single feed
   */
  private function import_feed($feed_url, $limit, $post_status, $allow_iframes) {
    $stats = array('feed' => $feed_url, 'found' => 0, 'duplicates' => 0, 'imported' => 0, 'updated' => 0, 'failed' => 0);
    
    try {
      // Allow localhost URLs only in development environment
      $is_dev = $this->is_development();
      
      // Clear feed cache if force_refresh is requested or in development
      // Always clear cache for API URLs to ensure fresh requests with Bearer token
      if ($is_dev || strpos($feed_url, 'force_refresh=true') !== false || strpos($feed_url, RSI_API_BASE_URL) === 0) {
        // Clear SimplePie cache for this specific feed
        $cache_key = 'feed_' . md5($feed_url);
        delete_transient($cache_key);
        delete_transient('feed_mod_' . md5($feed_url));
        
        // Also clear WordPress feed cache
        $feed_cache_key = '_transient_feed_' . md5($feed_url);
        $feed_mod_key = '_transient_feed_mod_' . md5($feed_url);
        delete_transient($feed_cache_key);
        delete_transient($feed_mod_key);
      }
      
      // Get Bearer token from environment variable or settings
      // Environment variable takes precedence
      $bearer_token = rsi_get_bearer_token();
      
      // Create Bearer token filter callback (used for both dev and production)
      // This ensures Bearer token is applied to all HTTP requests including Post Import
      $bearer_token_filter = function($args, $url) use ($bearer_token) {
        // Initialize headers array if not set
        if (!isset($args['headers'])) {
          $args['headers'] = array();
        }
        
        // Add Bearer token to all HTTP requests (RSS feeds and API calls)
        if (!empty($bearer_token)) {
          $args['headers']['Authorization'] = 'Bearer ' . $bearer_token;
        }
        
        return $args;
      };
      
      if ($is_dev) {
        // Allow localhost URLs by temporarily modifying WordPress HTTP settings
        add_filter('http_request_host_is_external', '__return_true');
        add_filter('http_request_reject_unsafe_urls', '__return_false');
        add_filter('http_request_args', function($args, $url) use ($bearer_token) {
          // Force allow localhost URLs and increase timeout
          $args['reject_unsafe_urls'] = false;
          $args['timeout'] = 60; // Increase timeout to 60 seconds
          $args['redirection'] = 5;
          
          // Initialize headers array if not set
          if (!isset($args['headers'])) {
            $args['headers'] = array();
          }
          
          // Add Bearer token if available (for Post Import and RSS feeds)
          if (!empty($bearer_token)) {
            $args['headers']['Authorization'] = 'Bearer ' . $bearer_token;
          }
          
          return $args;
        }, 10, 2);
      }
      
      // Add Bearer token filter for all environments (applies to all HTTP requests)
      // This ensures Post Import and scheduled imports both use the Bearer token
      if (!empty($bearer_token)) {
        add_filter('http_request_args', $bearer_token_filter, 10, 2);
      }
      
      // Temporarily increase feed cache timeout to ensure fresh fetch
      add_filter('wp_feed_cache_transient_lifetime', function($lifetime) {
        return strpos($GLOBALS['current_feed_url'] ?? '', 'force_refresh=true') !== false ? 1 : $lifetime;
      });
      
      $GLOBALS['current_feed_url'] = $feed_url;
      $feed = fetch_feed($feed_url);
      unset($GLOBALS['current_feed_url']);
      
      // Remove the filters after fetch attempt
      if ($is_dev) {
        remove_filter('http_request_host_is_external', '__return_true');
        remove_filter('http_request_reject_unsafe_urls', '__return_false');
        // In dev mode, remove_all_filters removes everything including Bearer token filter
        remove_all_filters('http_request_args');
      } else {
        // Remove Bearer token filter in production
        if (!empty($bearer_token) && isset($bearer_token_filter)) {
          remove_filter('http_request_args', $bearer_token_filter, 10);
        }
      }
      remove_all_filters('wp_feed_cache_transient_lifetime');
      
      if (is_wp_error($feed)) {
        $error_message = $feed->get_error_message();
        $error_code = $feed->get_error_code();
        error_log('[RSS Smart Importer] Failed to fetch feed: ' . $feed_url);
        error_log('[RSS Smart Importer] Error code: ' . $error_code);
        error_log('[RSS Smart Importer] Error message: ' . $error_message);
        
        // Log Bearer token status (without exposing the actual token)
        $has_token = !empty($bearer_token);
        $token_source = 'Settings';
        if (defined('RSI_API_BEARER_TOKEN') && !empty(RSI_API_BEARER_TOKEN)) {
          $token_source = 'Constant (RSI_API_BEARER_TOKEN)';
        } elseif (defined('RSI_BEARER_TOKEN') && !empty(RSI_BEARER_TOKEN)) {
          $token_source = 'Constant (RSI_BEARER_TOKEN)';
        } elseif (getenv('RSI_API_BEARER_TOKEN') ?: getenv('RSI_BEARER_TOKEN')) {
          $token_source = 'Environment variable';
        }
        error_log('[RSS Smart Importer] Bearer token configured: ' . ($has_token ? 'Yes (' . $token_source . ')' : 'No'));
        
        $stats['failed'] = 1;
        return $stats;
      }
      
      
      $items = $feed->get_items(0, $limit);
      $stats['found'] = count($items);
      
      foreach ($items as $item) {
        $result = $this->import_item($item, $post_status, true);
        $stats[$result]++;
      }
      
      return $stats;
      
    } catch (Exception $e) {
      error_log('[RSS Smart Importer] Exception importing feed ' . $feed_url . ': ' . $e->getMessage());
      $stats['failed'] = 1;
      return $stats;
      
    } catch (Throwable $e) {
      error_log('[RSS Smart Importer] Fatal error importing feed ' . $feed_url . ': ' . $e->getMessage());
      $stats['failed'] = 1;
      return $stats;
    }
  }
  
  /**
   * Import single feed item
   */
  private function import_item($item, $post_status, $allow_iframes) {
    try {
      // Get unique identifier
      $guid = $item->get_id() ?: $item->get_permalink();
      if (!$guid) return 'failed';
      
      // Get content
      $content = $item->get_content() ?: $item->get_description();
      $content = rsi_sanitize_html($content, $allow_iframes);
      
      // Generate fingerprint
      $fingerprint = rsi_content_fingerprint($content);
      
      // Check for existing post by GUID
      $existing = $this->find_post_by_guid($guid);
      
      if ($existing) {
        $old_fp = get_post_meta($existing->ID, '_rss_fp_v1', true);
        
        // If fingerprint unchanged, it's a duplicate
        if ($old_fp === $fingerprint) {
          return 'duplicates';
        }
        
        // Update existing post
        return $this->update_post($existing->ID, $item, $content, $fingerprint, true);
      }
      
      // Create new post
      return $this->create_post($item, $content, $guid, $fingerprint, $post_status, true);
      
    } catch (Exception $e) {
      error_log('[RSS Smart Importer] Exception importing item: ' . $e->getMessage());
      return 'failed';
    }
  }
  
  /**
   * Find post by GUID meta
   */
  private function find_post_by_guid($guid) {
    $query = new WP_Query(array(
      'post_type' => 'post',
      'post_status' => 'any',
      'posts_per_page' => 1,
      'meta_key' => '_rss_guid',
      'meta_value' => $guid,
      'fields' => 'ids'
    ));
    
    return $query->have_posts() ? get_post($query->posts[0]) : null;
  }
  
  /**
   * Create new post from feed item
   */
  private function create_post($item, $content, $guid, $fingerprint, $post_status, $allow_iframes) {
    // Get publication date
    $pub_date = $item->get_date('Y-m-d H:i:s');
    if (!$pub_date) $pub_date = current_time('mysql');
    
    // Create post
    $post_id = wp_insert_post(array(
      'post_title' => sanitize_text_field($item->get_title()),
      'post_content' => $content,
      'post_status' => $post_status,
      'post_date' => $pub_date,
      'post_author' => 1
    ));
    
    if (is_wp_error($post_id)) {
      error_log('[RSS Smart Importer] Failed to create post: ' . $post_id->get_error_message());
      return 'failed';
    }
    
    // Save meta
    update_post_meta($post_id, '_rss_guid', $guid);
    update_post_meta($post_id, '_rss_fp_v1', $fingerprint);
    
    // Process images (pass $item for enclosure support)
    $image_result = rsi_process_images($content, $post_id, $item);
    if ($image_result['content'] !== $content) {
      wp_update_post(array('ID' => $post_id, 'post_content' => $image_result['content']));
    }
    if ($image_result['featured_id']) {
      set_post_thumbnail($post_id, $image_result['featured_id']);
    }
    
    // Handle category
    $this->assign_category($post_id, $item);
    
    return 'imported';
  }
  
  /**
   * Update existing post
   */
  private function update_post($post_id, $item, $content, $fingerprint, $allow_iframes) {
    wp_update_post(array(
      'ID' => $post_id,
      'post_title' => sanitize_text_field($item->get_title()),
      'post_content' => $content
    ));
    
    update_post_meta($post_id, '_rss_fp_v1', $fingerprint);
    
    // Process images (pass $item for enclosure support)
    $image_result = rsi_process_images($content, $post_id, $item);
    if ($image_result['content'] !== $content) {
      wp_update_post(array('ID' => $post_id, 'post_content' => $image_result['content']));
    }
    if ($image_result['featured_id']) {
      set_post_thumbnail($post_id, $image_result['featured_id']);
    }
    
    return 'updated';
  }
  
  /**
   * Assign category from feed item
   */
  private function assign_category($post_id, $item) {
    $categories = $item->get_categories();
    if (empty($categories)) {
      return;
    }
    
    $category_name = $categories[0]->get_label();
    $title_case = rsi_kebab_to_title($category_name);
    
    // Check if category exists by name
    $term = get_term_by('name', $title_case, 'category');
    
    if ($term) {
      wp_set_post_categories($post_id, array($term->term_id));
    }
    // Otherwise leave as Uncategorized (default)
  }
}

