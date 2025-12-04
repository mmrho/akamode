<?php
// فایل: AKAMODE/lib/checkout/functions/ajax.php

if (!function_exists('handle_akamode_checkout_ajax')) {

    add_action('wp_ajax_akamode_process_checkout', 'handle_akamode_checkout_ajax');
    add_action('wp_ajax_nopriv_akamode_process_checkout', 'handle_akamode_checkout_ajax');

    function handle_akamode_checkout_ajax() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            wp_send_json_error(['message' => 'داده‌ای ارسال نشده است.']);
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'لطفا ابتدا وارد حساب کاربری شوید.']);
        }

        $user_id = get_current_user_id();
        $token = get_user_meta($user_id, '_laravel_api_token', true);

        if (!$token) {
            wp_send_json_error(['message' => 'خطای احراز هویت: توکن یافت نشد.']);
        }

        if (class_exists('Laravel_API_Client')) {
            $api = Laravel_API_Client::get_instance();
            $api->set_token($token);
            
            $result = $api->checkout($data);

            if (is_wp_error($result)) {
                wp_send_json_error(['message' => $result->get_error_message()]);
            } else {
                if (isset($result['success']) && $result['success'] == true) {
                    wp_send_json_success($result);
                } else {
                    $msg = isset($result['message']) ? $result['message'] : 'خطا در ثبت سفارش.';
                    wp_send_json_error(['message' => $msg]);
                }
            }
        } else {
            wp_send_json_error(['message' => 'API Client class not found.']);
        }
    }
}