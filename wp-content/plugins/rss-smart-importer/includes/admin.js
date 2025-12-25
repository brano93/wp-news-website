jQuery(document).ready(function($) {
  /**
   * Escape HTML to prevent XSS attacks
   * @param {string} text Text to escape
   * @return {string} Escaped text
   */
  function escapeHtml(text) {
    if (typeof text !== 'string') {
      return '';
    }
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }
  
  $('#rsi_import_post_btn').on('click', function() {
    var url = $('#rsi_post_url').val().trim();
    var forceRefresh = $('#rsi_force_refresh').is(':checked');
    var postStatus = $('#rsi_post_status').val();
    var $button = $(this);
    var $result = $('#rsi_import_result');
    
    // Validate URL
    if (!url) {
      $result.html('<div class="notice notice-error"><p>Please enter a URL.</p></div>');
      return;
    }
    
    // Disable button and show loading
    $button.prop('disabled', true).text('Importing...');
    $result.html('<div class="notice notice-info"><p>Importing article, please wait...</p></div>');
    
    // Build request data
    var requestData = {
      action: 'rsi_import_post',
      nonce: rsiAdmin.nonce,
      url: url,
      status: postStatus
    };
    
    // Only include forceRefresh if checked
    if (forceRefresh) {
      requestData.forceRefresh = true;
    }
    
    // Make AJAX request to WordPress
    $.ajax({
      url: rsiAdmin.ajaxUrl,
      method: 'POST',
      data: requestData,
      timeout: 60000, // 60 second timeout
      success: function(response) {
        if (response.success) {
          var message = escapeHtml(response.data.message || 'Article imported successfully.');
          $result.html('<div class="notice notice-success"><p><strong>Success!</strong> ' + message + '</p></div>');
          // Clear form
          $('#rsi_post_url').val('');
          $('#rsi_force_refresh').prop('checked', false);
          $('#rsi_post_status').val('draft');
        } else {
          var errorMessage = escapeHtml(response.data.message || 'Failed to import article.');
          $result.html('<div class="notice notice-error"><p><strong>Error:</strong> ' + errorMessage + '</p></div>');
        }
      },
      error: function(xhr, status, error) {
        var errorMsg = 'Failed to import article.';
        if (status === 'timeout') {
          errorMsg = 'Request timed out. The article may still be importing.';
        } else if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
          errorMsg = xhr.responseJSON.data.message;
        } else if (xhr.responseText) {
          // Only use responseText if it's a simple error message, escape it
          errorMsg = xhr.responseText;
        } else if (error) {
          errorMsg = error;
        }
        var escapedErrorMsg = escapeHtml(errorMsg);
        $result.html('<div class="notice notice-error"><p><strong>Error:</strong> ' + escapedErrorMsg + '</p></div>');
      },
      complete: function() {
        $button.prop('disabled', false).text('Import');
      }
    });
  });
});

