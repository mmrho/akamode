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


// =========================================================
// 2. هندل کردن درخواست Checkout (AJAX)
// =========================================================
add_action('wp_ajax_akamode_process_checkout', 'akamode_process_checkout_handler');
add_action('wp_ajax_nopriv_akamode_process_checkout', 'akamode_process_checkout_handler');

function akamode_process_checkout_handler() {
    // دریافت دیتای JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (empty($data)) {
        wp_send_json_error(['message' => 'داده‌ای دریافت نشد.']);
    }

    // آماده‌سازی دیتا برای API لاراول طبق فرمت Postman شما
    $api_payload = [
        'address' => [
            'full_name' => sanitize_text_field($data['full_name']),
            'address'   => sanitize_text_field($data['address']),
            'city'      => sanitize_text_field($data['city']),
            'state'     => sanitize_text_field($data['state']),
            'zip_code'  => sanitize_text_field($data['zip_code']),
            'phone'     => sanitize_text_field($data['phone']),
            'country'   => 'Iran', // یا داینامیک
        ],
        'shipping_method'  => sanitize_text_field($data['shipping_method']),
        'payment_method'   => 'card', // فعلا ثابت، بعدا میتواند داینامیک باشد
        'items'            => $data['items'], // آرایه variant_id و quantity
        'discount_code'    => null,
        'packaging_id'     => 0
    ];

    // اتصال به کلاس API (فرض بر این است فایل Laravel_API_Client را دارید)
    if (class_exists('Laravel_API_Client')) {
        $api = Laravel_API_Client::get_instance();
        
        // اگر کاربر لاگین است، توکن را ست کنید (اختیاری/وابسته به منطق لاگین شما)
        // $api->set_token('USER_TOKEN_FROM_COOKIE_OR_SESSION');

        $result = $api->checkout($api_payload);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        } else {
            // موفقیت
            wp_send_json_success([
                'redirect_url' => isset($result['redirect_url']) ? $result['redirect_url'] : home_url('/checkout/success')
            ]);
        }
    } else {
        wp_send_json_error(['message' => 'خطای داخلی: کلاس API یافت نشد.']);
    }
}
?>


