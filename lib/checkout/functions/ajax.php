<?php
if (!function_exists('handle_akamode_checkout_ajax')) {
    add_action('wp_ajax_akamode_process_checkout', 'handle_akamode_checkout_ajax');

    function handle_akamode_checkout_ajax() {
        // خواندن داده ورودی
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // بررسی لاگین بودن
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'لطفا ابتدا وارد حساب کاربری شوید.']);
        }

        // دریافت توکن از دیتابیس
        $user_id = get_current_user_id();
        $token = get_user_meta($user_id, '_laravel_api_token', true);

        // لاگ برای دیباگ
        if (!$token) {
            error_log("REAL AJAX CHECKOUT: Token MISSING for user $user_id");
            wp_send_json_error([
                'message' => 'خطای احراز هویت. لطفا مجدد وارد شوید.',
                'force_logout' => true,
                'redirect_url' => home_url('/login')
            ]);
        } else {
            error_log("REAL AJAX CHECKOUT: Token found for user $user_id");
        }

        // فراخوانی API
        if (class_exists('Laravel_API_Client')) {
            $api = Laravel_API_Client::get_instance();
            
            // 1. ست کردن توکن در کلاس
            $api->set_token($token);
            
            // 2. ارسال توکن به عنوان پارامتر دوم (تزریق مستقیم)
            $result = $api->checkout($data, $token);

            if (is_wp_error($result)) {
                $msg = $result->get_error_message();
                if (strpos($msg, 'Unauthenticated') !== false) {
                    wp_send_json_error([
                        'message' => 'نشست کاربری نامعتبر است. لطفا مجدد وارد شوید.',
                        'force_logout' => true,
                        'redirect_url' => home_url('/login')
                    ]);
                } else {
                    wp_send_json_error(['message' => $msg]);
                }
            } else {
                if (isset($result['success']) && $result['success'] == true) {
                    wp_send_json_success($result);
                } else {
                    $msg = isset($result['message']) ? $result['message'] : 'خطا در ثبت سفارش.';
                    wp_send_json_error(['message' => $msg]);
                }
            }
        } else {
            wp_send_json_error(['message' => 'خطای سیستمی API']);
        }
    }
}
?>