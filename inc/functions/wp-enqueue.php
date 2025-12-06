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
// 1. بارگذاری فایل Global Cart
// =========================================================
function akamode_enqueue_global_logic() {
    // لود کردن فایل JS اصلی در تمام صفحات
    wp_enqueue_script(
        'akamode-global-cart', 
        get_template_directory_uri() . '/assets/js/global-cart.js', 
        array('jquery'), 
        '1.0', 
        true
    );

    // پاس دادن متغیرها به JS
    wp_localize_script('akamode-global-cart', 'wbs_data', array(
        'ajax_url'     => admin_url('admin-ajax.php'),
        'cart_url'     => home_url('/cart'),      // نامک صفحه سبد خرید را چک کنید
        'checkout_url' => home_url('/checkout')   // نامک صفحه پرداخت را چک کنید
    ));
}
add_action('wp_enqueue_scripts', 'akamode_enqueue_global_logic');


