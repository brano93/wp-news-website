<?php
/**
 * Uninstall script for RSS Smart Importer
 * Cleans up options and scheduled events
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
  exit;
}

// Unschedule cron events
$timestamp = wp_next_scheduled('rsi_import_cron');
if ($timestamp) {
  wp_unschedule_event($timestamp, 'rsi_import_cron');
}

// Delete plugin options
delete_option('rsi_settings');
delete_option('rsi_last_runs');

// Delete transient lock if exists
delete_transient('rsi_lock');

// Optional: Delete all posts meta created by this plugin
// Uncomment if you want to clean up post meta on uninstall
/*
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('_rss_guid', '_rss_fp_v1')");
*/

