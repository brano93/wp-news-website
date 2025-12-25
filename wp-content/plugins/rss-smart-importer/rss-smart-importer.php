<?php
/**
 * Plugin Name: RSS Smart Importer
 * Description: Import RSS feeds with smart duplicate detection, image handling, and flexible scheduling.
 * Version: 1.0.0
 * Author: Branimir Ilic
 * Text Domain: rss-smart-importer
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

if (!defined('ABSPATH')) exit;

define('RSI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RSI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RSI_VERSION', '1.0.0');
// Read API base URL from constant, environment variable, or fallback to default
// Priority: 1. Constant (RSI_API_HOST or RSI_API_BASE_URL), 2. Environment variable, 3. Default
$api_host = defined('RSI_API_HOST') ? RSI_API_HOST : (defined('RSI_API_BASE_URL') ? RSI_API_BASE_URL : null);
if (!$api_host) {
  $api_host = getenv('RSI_API_HOST') ?: getenv('RSI_API_BASE_URL');
}
define('RSI_API_BASE_URL', $api_host ?: 'http://localhost:3030');

// Load required files
require_once RSI_PLUGIN_DIR . 'includes/helpers-sanitize.php';
require_once RSI_PLUGIN_DIR . 'includes/helpers-images.php';
require_once RSI_PLUGIN_DIR . 'includes/class-rsi-importer.php';
require_once RSI_PLUGIN_DIR . 'includes/class-rsi-settings.php';

/**
 * Activation hook: set default options
 */
function rsi_activate() {
  if (!get_option('rsi_settings')) {
    add_option('rsi_settings', array(
      'feeds' => '',
      'items_per_feed' => 10,
      'post_status' => 'draft',
      'schedule_hours' => 0,
      'schedule_minutes' => 0,
      'bearer_token' => ''
    ));
  }
  if (!get_option('rsi_last_runs')) {
    add_option('rsi_last_runs', array());
  }
}
register_activation_hook(__FILE__, 'rsi_activate');

/**
 * Get Bearer token from constant, environment variable, or settings
 * Priority: 1. Constant, 2. Environment variable, 3. Settings
 *
 * @return string Bearer token or empty string
 */
function rsi_get_bearer_token() {
  // Check constant first (defined via define() in wp-config.php)
  if (defined('RSI_API_BEARER_TOKEN') && !empty(RSI_API_BEARER_TOKEN)) {
    return trim(RSI_API_BEARER_TOKEN);
  }
  if (defined('RSI_BEARER_TOKEN') && !empty(RSI_BEARER_TOKEN)) {
    return trim(RSI_BEARER_TOKEN);
  }
  
  // Check environment variable
  $env_token = getenv('RSI_API_BEARER_TOKEN') ?: getenv('RSI_BEARER_TOKEN');
  if (!empty($env_token)) {
    return trim($env_token);
  }
  
  // Fallback to settings
  $settings = get_option('rsi_settings', array());
  return !empty($settings['bearer_token']) ? trim($settings['bearer_token']) : '';
}

/**
 * Initialize plugin
 */
function rsi_init() {
  if (is_admin()) {
    new RSI_Settings();
  }
  new RSI_Importer();
}
add_action('plugins_loaded', 'rsi_init');

