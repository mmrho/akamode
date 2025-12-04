<?php
// 1. Add button to admin bar
add_action('admin_bar_menu', function($wp_admin_bar) {
    if (!current_user_can('manage_options')) return;

    $wp_admin_bar->add_node([
        'id'    => 'flush_laravel_api',
        'title' => 'پاکسازی کش API',
        'href'  => add_query_arg('flush_laravel_api_cache', '1'), // لینک فعلی + پارامتر پاکسازی
        'meta'  => ['title' => 'پاک کردن تمام کش‌های دریافتی از لاراول']
    ]);
}, 100);

// 2. Perform the cleanup operation when the button is clicked
add_action('init', function() {
    if (isset($_GET['flush_laravel_api_cache']) && $_GET['flush_laravel_api_cache'] == '1' && current_user_can('manage_options')) {
        
       // Call the cleanup method from the class we wrote
        Laravel_API_Client::get_instance()->flush_api_cache();
        
        // If you are using LiteSpeed, also clear the cache of the current page so that new HTML can be generated
        if (defined('LSCWP_V')) {
            do_action('litespeed_purge_current_page');
        }
        
        // Success message and redirect (to remove parameter from URL)
        wp_redirect(remove_query_arg('flush_laravel_api_cache'));
        exit;
    }
});