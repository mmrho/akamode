<?php
defined('ABSPATH') || exit;
add_action('wp_enqueue_scripts', 'wbs_enqueue_scripts');
function wbs_enqueue_scripts()
{
    require_once "globalEnqueue.php";
    
    if (is_front_page() || is_home()) {
        wp_enqueue_script('slider', THEME_ASSETS . 'js/Modules/slider.js', array('jquery'), THEME_VERSION, true);
        wp_enqueue_script('swing', THEME_ASSETS . 'js/Modules/swing.js', array('jquery'), THEME_VERSION, true);
        wp_enqueue_script('ribbon', THEME_ASSETS . 'js/Modules/ribbon.js', array('jquery'), THEME_VERSION, true);
    }


    $items = [
        'AjaxUrl' => admin_url('admin-ajax.php'),
        'SecurityNonce' => wp_create_nonce("akamode_auth_nonce"),
        'themeUrl' => get_template_directory_uri()
    ];

    wp_localize_script('script', 'wbs_script', $items); 
}



// =========================================================
// 1.Global Cart
// =========================================================
function akamode_enqueue_global_logic() {
  
    wp_enqueue_script(
        'akamode-global-cart', 
        get_template_directory_uri() . '/assets/js/global-cart.js', 
        array('jquery'), 
        '1.0', 
        true
    );

   
    wp_localize_script('akamode-global-cart', 'wbs_data', array(
        'ajax_url'     => admin_url('admin-ajax.php'),
        'cart_url'     => home_url('/cart'),    
        'checkout_url' => home_url('/checkout')  
    ));
}
add_action('wp_enqueue_scripts', 'akamode_enqueue_global_logic');


// =========================================================================
//Prevent indexing of User Dashboard pages (Clean Method)
// Uses 'wp_robots' filter to modify the existing meta tag instead of creating a duplicate.
// =========================================================================

function wbs_noindex_dashboard_pages($robots) {
    
    if ( is_page_template('page-dashboard.php') || is_page_template('login.php') ) {
       
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
        
        // (Optional) If you want to remove max-image-preview as well, uncomment the following line:
        // unset($robots['max-image-preview']);
    }
    return $robots;
}
add_filter('wp_robots', 'wbs_noindex_dashboard_pages');


