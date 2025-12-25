<?php
/**
 * Sanitization helpers for RSS Smart Importer
 */

if (!defined('ABSPATH')) exit;

/**
 * Sanitize HTML content with optional iframe support
 *
 * @param string $html Raw HTML content
 * @param bool $allow_iframes Whether to allow iframe tags
 * @return string Sanitized HTML
 */
function rsi_sanitize_html($html, $allow_iframes = false) {
  $allowed = wp_kses_allowed_html('post');
  
  if ($allow_iframes) {
    $allowed['iframe'] = array(
      'src' => true,
      'width' => true,
      'height' => true,
      'allow' => true,
      'allowfullscreen' => true,
      'frameborder' => true,
      'title' => true,
      'loading' => true
    );
  }
  
  return wp_kses($html, $allowed);
}

/**
 * Generate normalized content fingerprint for duplicate detection
 *
 * @param string $content Post content
 * @return string MD5 hash fingerprint
 */
function rsi_content_fingerprint($content) {
  // Strip all HTML tags and normalize whitespace
  $normalized = preg_replace('/\s+/', ' ', strip_tags($content));
  $normalized = trim(strtolower($normalized));
  return md5($normalized);
}

/**
 * Convert kebab-case to Title Case
 *
 * @param string $kebab Kebab-case string (e.g., "world-news")
 * @return string Title Case string (e.g., "World News")
 */
function rsi_kebab_to_title($kebab) {
  $words = explode('-', $kebab);
  $title = array_map('ucfirst', $words);
  return implode(' ', $title);
}

