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


