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








// =========================================================================
// COMMENT AJAX HANDLERS
// =========================================================================


add_action('wp_ajax_wbs_get_comments', 'wbs_ajax_get_comments');
add_action('wp_ajax_nopriv_wbs_get_comments', 'wbs_ajax_get_comments');

function wbs_ajax_get_comments() {
    $item_id = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;
    if ($item_id <= 0) wp_send_json_error(['message' => 'Invalid ID']);

    $api = Laravel_API_Client::get_instance();
    $response = $api->get_reviews($item_id);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => $response->get_error_message()]);
    } else {
       
        $data = isset($response['data']) ? $response['data'] : $response;
        wp_send_json_success($data);
    }
}


add_action('wp_ajax_wbs_submit_comment', 'wbs_ajax_submit_comment');

function wbs_ajax_submit_comment() {
    
    check_ajax_referer('wbs_comment_nonce', 'security');

   
    @session_start();
    $token = isset($_SESSION['user_token']) ? $_SESSION['user_token'] : null;

    if (!$token) {
        wp_send_json_error(['message' => 'لطفاً ابتدا وارد حساب کاربری خود شوید.']);
    }

    $item_id = intval($_POST['item_id']);
    $rating = intval($_POST['rating']);
    $comment = sanitize_textarea_field($_POST['comment']);

    $api = Laravel_API_Client::get_instance();
    $api->set_token($token); 
    
    $response = $api->submit_review($item_id, $rating, $comment);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => $response->get_error_message()]);
    } else {
        wp_send_json_success(['message' => 'نظر شما با موفقیت ثبت شد.']);
    }
}


