<?php
/**
 * RSS Smart Importer Settings Page
 */

if (!defined('ABSPATH')) exit;

class RSI_Settings {
  
  public function __construct() {
    add_action('admin_menu', array($this, 'add_menu'));
    add_action('admin_init', array($this, 'register_settings'));
    add_action('admin_post_rsi_run_now', array($this, 'handle_run_now'));
    add_action('admin_post_rsi_clear_lock', array($this, 'handle_clear_lock'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    add_action('wp_ajax_rsi_import_post', array($this, 'handle_import_post'));
  }
  
  /**
   * Add settings menu
   */
  public function add_menu() {
    add_menu_page(
      'RSS Smart Importer',
      'RSS Importer',
      'manage_options',
      'rss-smart-importer',
      array($this, 'render_page'),
      'dashicons-rss',
      30
    );
  }
  
  /**
   * Register settings
   */
  public function register_settings() {
    register_setting('rsi_settings_group', 'rsi_settings', array($this, 'sanitize_settings'));
  }
  
  /**
   * Sanitize settings on save
   */
  public function sanitize_settings($input) {
    $sanitized = array();
    // Don't use sanitize_textarea_field as it corrupts URLs with query params
    // Instead, sanitize each URL individually
    $feeds_raw = $input['feeds'] ?? '';
    $feeds_lines = array_filter(array_map('trim', explode("\n", $feeds_raw)));
    $sanitized_feeds = array();
    foreach ($feeds_lines as $feed_url) {
      // Use esc_url_raw to properly sanitize URLs while preserving query parameters
      $sanitized_url = esc_url_raw($feed_url);
      if (!empty($sanitized_url)) {
        $sanitized_feeds[] = $sanitized_url;
      }
    }
    $sanitized['feeds'] = implode("\n", $sanitized_feeds);
    $sanitized['items_per_feed'] = max(1, intval($input['items_per_feed'] ?? 10));
    
    // Validate post status against all available WordPress post statuses
    $valid_statuses = array_keys(get_post_statuses());
    $sanitized['post_status'] = in_array($input['post_status'] ?? '', $valid_statuses) ? $input['post_status'] : 'draft';
    
    // Schedule settings
    $hours = max(0, intval($input['schedule_hours'] ?? 0));
    $minutes = max(0, intval($input['schedule_minutes'] ?? 0));
    
    // Validate min 5 min, max 24 hours
    $total_seconds = ($hours * 3600) + ($minutes * 60);
    if ($total_seconds > 0 && $total_seconds < 300) {
      $minutes = 5;
      $hours = 0;
      add_settings_error('rsi_settings', 'interval_too_short', 'Minimum interval is 5 minutes. Adjusted to 5 minutes.', 'warning');
    }
    if ($total_seconds > 86400) {
      $hours = 24;
      $minutes = 0;
      add_settings_error('rsi_settings', 'interval_too_long', 'Maximum interval is 24 hours. Adjusted to 24 hours.', 'warning');
    }
    
    $sanitized['schedule_hours'] = $hours;
    $sanitized['schedule_minutes'] = $minutes;
    
    // Bearer token
    $sanitized['bearer_token'] = sanitize_text_field($input['bearer_token'] ?? '');
    
    // Update cron schedule
    RSI_Importer::update_schedule($hours, $minutes);
    
    return $sanitized;
  }
  
  /**
   * Handle Run Now button
   */
  public function handle_run_now() {
    check_admin_referer('rsi_run_now');
    
    if (!current_user_can('manage_options')) {
      wp_die('Unauthorized');
    }
    
    $importer = new RSI_Importer();
    $result = $importer->run_import();
    
    wp_redirect(add_query_arg(array('page' => 'rss-smart-importer', 'ran' => '1'), admin_url('admin.php')));
    exit;
  }
  
  /**
   * Handle Clear Lock button
   */
  public function handle_clear_lock() {
    check_admin_referer('rsi_clear_lock');
    
    if (!current_user_can('manage_options')) {
      wp_die('Unauthorized');
    }
    
    RSI_Importer::clear_lock();
    
    wp_redirect(add_query_arg(array('page' => 'rss-smart-importer', 'lock_cleared' => '1'), admin_url('admin.php')));
    exit;
  }
  
  /**
   * Enqueue admin scripts
   */
  public function enqueue_admin_scripts($hook) {
    // Only load on our settings page
    if ($hook !== 'toplevel_page_rss-smart-importer') {
      return;
    }
    
    wp_enqueue_script(
      'rsi-admin-js',
      RSI_PLUGIN_URL . 'includes/admin.js',
      array('jquery'),
      RSI_VERSION,
      true
    );
    
    wp_localize_script('rsi-admin-js', 'rsiAdmin', array(
      'apiBaseUrl' => RSI_API_BASE_URL,
      'ajaxUrl' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('rsi_post_import')
    ));
  }
  
  /**
   * Handle AJAX import post request
   */
  public function handle_import_post() {
    check_ajax_referer('rsi_post_import', 'nonce');
    
    if (!current_user_can('manage_options')) {
      wp_send_json_error(array('message' => 'Unauthorized'), 403);
    }
    
    $article_url = sanitize_text_field($_POST['url'] ?? '');
    $force_refresh = !empty($_POST['forceRefresh']);
    $post_status = sanitize_key($_POST['status'] ?? 'draft');
    
    if (empty($article_url)) {
      wp_send_json_error(array('message' => 'URL is required'), 400);
    }
    
    // Validate post status - check if it's a valid WordPress post status
    $valid_statuses = array_keys(get_post_statuses());
    if (!in_array($post_status, $valid_statuses)) {
      $post_status = 'draft';
    }
    
    // Build API URL
    $api_url = RSI_API_BASE_URL . '/rss/article?url=' . urlencode($article_url) . '&forceRefresh=' . ($force_refresh ? 'true' : 'false');
    
    // Import the feed using existing logic
    $importer = new RSI_Importer();
    $result = $importer->import_single_feed($api_url, 1, $post_status);
    
    if ($result['failed'] > 0) {
      // Get more detailed error information
      $error_details = '';
      $bearer_token = rsi_get_bearer_token();
      $has_token = !empty($bearer_token);
      $token_source = 'settings';
      if (defined('RSI_API_BEARER_TOKEN') && !empty(RSI_API_BEARER_TOKEN)) {
        $token_source = 'constant (RSI_API_BEARER_TOKEN)';
      } elseif (defined('RSI_BEARER_TOKEN') && !empty(RSI_BEARER_TOKEN)) {
        $token_source = 'constant (RSI_BEARER_TOKEN)';
      } elseif (getenv('RSI_API_BEARER_TOKEN') ?: getenv('RSI_BEARER_TOKEN')) {
        $token_source = 'environment variable';
      }
      
      // Check WordPress debug log for more details
      $error_details = 'Failed to fetch from API. ';
      if (!$has_token) {
        $error_details .= 'Note: No Bearer token configured in environment variable or settings. ';
      } else {
        $error_details .= 'Bearer token is configured (' . $token_source . '). ';
      }
      $error_details .= 'Please check that the API is running at ' . RSI_API_BASE_URL . ' and verify the Bearer token is correct.';
      
      wp_send_json_error(array(
        'message' => $error_details,
        'result' => $result,
        'api_url' => $api_url,
        'has_bearer_token' => $has_token,
        'token_source' => $token_source
      ), 500);
    }
    
    if ($result['imported'] > 0) {
      wp_send_json_success(array(
        'message' => 'Article imported successfully as ' . $post_status . '!',
        'result' => $result
      ));
    } elseif ($result['updated'] > 0) {
      wp_send_json_success(array(
        'message' => 'Article was already imported and has been updated!',
        'result' => $result
      ));
    } elseif ($result['duplicates'] > 0) {
      wp_send_json_success(array(
        'message' => 'Article already exists with no changes.',
        'result' => $result
      ));
    } else {
      wp_send_json_error(array(
        'message' => 'No article found or imported.',
        'result' => $result
      ), 404);
    }
  }
  
  /**
   * Render settings page
   */
  public function render_page() {
    $settings = get_option('rsi_settings', array());
    $history = get_option('rsi_last_runs', array());
    $last_run = !empty($history) ? $history[0] : null;
    $show_results = isset($_GET['ran']) && $_GET['ran'] === '1';
    $lock_cleared = isset($_GET['lock_cleared']) && $_GET['lock_cleared'] === '1';
    
    ?>
    <div class="wrap">
      <h1>RSS Smart Importer</h1>
      
      <?php settings_errors('rsi_settings'); ?>
      
      <?php if ($lock_cleared): ?>
        <div class="notice notice-success is-dismissible">
          <p><strong>Import lock cleared!</strong> You can now run imports again.</p>
        </div>
      <?php endif; ?>
      
      <?php if ($show_results && $last_run): ?>
        <div class="notice notice-success is-dismissible">
          <p><strong>Import completed!</strong> Duration: <?php echo esc_html($last_run['duration']); ?>s</p>
        </div>
        
        <h2>Last Run Results</h2>
        <table class="wp-list-table widefat fixed striped">
          <thead>
            <tr>
              <th>Feed URL</th>
              <th>Found</th>
              <th>Imported</th>
              <th>Updated</th>
              <th>Duplicates</th>
              <th>Failed</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($last_run['results'] as $result): ?>
              <tr>
                <td><?php echo esc_html($result['feed']); ?></td>
                <td><?php echo intval($result['found']); ?></td>
                <td><?php echo intval($result['imported']); ?></td>
                <td><?php echo intval($result['updated']); ?></td>
                <td><?php echo intval($result['duplicates']); ?></td>
                <td><?php echo intval($result['failed']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th><strong>Totals</strong></th>
              <th><?php echo intval($last_run['totals']['found']); ?></th>
              <th><?php echo intval($last_run['totals']['imported']); ?></th>
              <th><?php echo intval($last_run['totals']['updated']); ?></th>
              <th><?php echo intval($last_run['totals']['duplicates']); ?></th>
              <th><?php echo intval($last_run['totals']['failed']); ?></th>
            </tr>
          </tfoot>
        </table>
      <?php endif; ?>
      
      <h2>Schedule Import</h2>
      <?php
      // Check for API host from constant or environment variable
      $api_host_source = null;
      $api_host_value = null;
      if (defined('RSI_API_HOST')) {
        $api_host_source = 'constant (RSI_API_HOST)';
        $api_host_value = RSI_API_HOST;
      } elseif (defined('RSI_API_BASE_URL')) {
        $api_host_source = 'constant (RSI_API_BASE_URL)';
        $api_host_value = RSI_API_BASE_URL;
      } else {
        $env_api_host = getenv('RSI_API_HOST') ?: getenv('RSI_API_BASE_URL');
        if ($env_api_host) {
          $api_host_source = 'environment variable';
          $api_host_value = $env_api_host;
        }
      }
      if ($api_host_value):
      ?>
        <div class="notice notice-info" style="margin-bottom: 15px;">
          <p><strong>API Configuration:</strong> API host is set via <?php echo esc_html($api_host_source); ?>: <code><?php echo esc_html($api_host_value); ?></code></p>
        </div>
      <?php endif; ?>
      <?php
      // Check for Bearer token from constant or environment variable
      $token_source = null;
      $token_value = null;
      if (defined('RSI_API_BEARER_TOKEN') && !empty(RSI_API_BEARER_TOKEN)) {
        $token_source = 'constant (RSI_API_BEARER_TOKEN)';
        $token_value = RSI_API_BEARER_TOKEN;
      } elseif (defined('RSI_BEARER_TOKEN') && !empty(RSI_BEARER_TOKEN)) {
        $token_source = 'constant (RSI_BEARER_TOKEN)';
        $token_value = RSI_BEARER_TOKEN;
      } else {
        $env_token = getenv('RSI_API_BEARER_TOKEN') ?: getenv('RSI_BEARER_TOKEN');
        if (!empty($env_token)) {
          $token_source = 'environment variable';
          $token_value = $env_token;
        }
      }
      if ($token_value):
        $token_last_4 = substr($token_value, -4);
      ?>
        <div class="notice notice-info" style="margin-bottom: 15px;">
          <p><strong>API Configuration:</strong> API Bearer Token is set via <?php echo esc_html($token_source); ?> (last 4: <code><?php echo esc_html($token_last_4); ?></code>)</p>
        </div>
      <?php endif; ?>
      <form method="post" action="options.php">
        <?php settings_fields('rsi_settings_group'); ?>
        
        <table class="form-table">
          <tr>
            <th scope="row"><label for="feeds">Feed URLs</label></th>
            <td>
              <textarea name="rsi_settings[feeds]" id="feeds" rows="8" class="large-text"><?php echo esc_textarea($settings['feeds'] ?? ''); ?></textarea>
              <p class="description">Enter one feed URL per line</p>
            </td>
          </tr>
          
          <tr>
            <th scope="row"><label for="items_per_feed">Items per Feed</label></th>
            <td>
              <input type="number" name="rsi_settings[items_per_feed]" id="items_per_feed" value="<?php echo intval($settings['items_per_feed'] ?? 10); ?>" min="1" max="100" class="small-text">
              <p class="description">Maximum items to import per feed per run (default: 10)</p>
            </td>
          </tr>
          
          <tr>
            <th scope="row"><label for="post_status">Post Status</label></th>
            <td>
              <select name="rsi_settings[post_status]" id="post_status">
                <?php
                $post_statuses = get_post_statuses();
                $current_status = $settings['post_status'] ?? 'draft';
                foreach ($post_statuses as $status_value => $status_label) {
                  echo '<option value="' . esc_attr($status_value) . '" ' . selected($current_status, $status_value, false) . '>' . esc_html($status_label) . '</option>';
                }
                ?>
              </select>
              <p class="description">Default status for imported posts</p>
            </td>
          </tr>
          
          <tr>
            <th scope="row"><label>Schedule Interval</label></th>
            <td>
              <input type="number" name="rsi_settings[schedule_hours]" value="<?php echo intval($settings['schedule_hours'] ?? 0); ?>" min="0" max="24" class="small-text"> Hours
              <input type="number" name="rsi_settings[schedule_minutes]" value="<?php echo intval($settings['schedule_minutes'] ?? 0); ?>" min="0" max="59" class="small-text"> Minutes
              <p class="description">Set to 0 hours and 0 minutes for manual-only mode. Min: 5 minutes, Max: 24 hours</p>
            </td>
          </tr>
          
          <?php if (!$token_value): ?>
          <tr>
            <th scope="row"><label for="bearer_token">API Bearer Token</label></th>
            <td>
              <input type="password" name="rsi_settings[bearer_token]" id="bearer_token" value="<?php echo esc_attr($settings['bearer_token'] ?? ''); ?>" class="regular-text">
              <p class="description">Bearer token for API authentication. You can also set it via constant (<code>RSI_API_BEARER_TOKEN</code> or <code>RSI_BEARER_TOKEN</code> in wp-config.php) or environment variable (takes precedence).</p>
            </td>
          </tr>
          <?php endif; ?>
        </table>
        
        <p class="submit">
          <?php submit_button('Save Changes', 'primary', 'submit', false); ?>
          
          <span style="margin-left: 10px;">
            <button type="button" onclick="document.getElementById('rsi_run_now_form').submit();" class="button button-secondary">Run Import Now</button>
          </span>
          
          <span style="margin-left: 10px;">
            <button type="button" onclick="document.getElementById('rsi_clear_lock_form').submit();" class="button button-secondary">Clear Cache</button>
          </span>
        </p>
      </form>
      
      <form id="rsi_run_now_form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display: none;">
        <input type="hidden" name="action" value="rsi_run_now">
        <?php wp_nonce_field('rsi_run_now'); ?>
      </form>
      
      <form id="rsi_clear_lock_form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display: none;">
        <input type="hidden" name="action" value="rsi_clear_lock">
        <?php wp_nonce_field('rsi_clear_lock'); ?>
      </form>
      
      <hr>
      
      <h2>Post Import</h2>
      <div id="rsi-post-import-section">
        <table class="form-table">
          <tr>
            <th scope="row"><label for="rsi_post_url">Article URL</label></th>
            <td>
              <input type="text" id="rsi_post_url" class="regular-text" placeholder="Enter article URL">
              <p class="description">Enter the URL of the article to import</p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="rsi_post_status">Post Status</label></th>
            <td>
              <select id="rsi_post_status" class="regular-text">
                <?php
                $post_statuses = get_post_statuses();
                foreach ($post_statuses as $status_value => $status_label) {
                  $selected = ($status_value === 'draft') ? ' selected' : '';
                  echo '<option value="' . esc_attr($status_value) . '"' . $selected . '>' . esc_html($status_label) . '</option>';
                }
                ?>
              </select>
              <p class="description">Select the status for the imported post</p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="rsi_force_refresh">Force Refresh</label></th>
            <td>
              <label>
                <input type="checkbox" id="rsi_force_refresh" value="1">
                Force refresh the article from source
              </label>
            </td>
          </tr>
        </table>
        
        <button type="button" id="rsi_import_post_btn" class="button button-primary">Import</button>
        
        <div id="rsi_import_result" style="margin-top: 15px;"></div>
      </div>
      
      <?php if (!empty($history)): ?>
        <hr>
        <h2>Run History (Last 10)</h2>
        <table class="wp-list-table widefat fixed striped">
          <thead>
            <tr>
              <th>Time</th>
              <th>Duration</th>
              <th>Found</th>
              <th>Imported</th>
              <th>Updated</th>
              <th>Duplicates</th>
              <th>Failed</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($history as $run): ?>
              <tr>
                <td><?php echo esc_html($run['time']); ?></td>
                <td><?php echo esc_html($run['duration']); ?>s</td>
                <td><?php echo intval($run['totals']['found']); ?></td>
                <td><?php echo intval($run['totals']['imported']); ?></td>
                <td><?php echo intval($run['totals']['updated']); ?></td>
                <td><?php echo intval($run['totals']['duplicates']); ?></td>
                <td><?php echo intval($run['totals']['failed']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
    <?php
  }
}

