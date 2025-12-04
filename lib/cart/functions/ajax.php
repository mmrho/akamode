<?php

// =========================================================================
// HANDLER FOR DISCOUNT CODE (Cart Module)
// =========================================================================

// فقط اگر اکشن قبلاً تعریف نشده باشد آن را اضافه کن (جهت جلوگیری از تداخل)
if (!has_action('wp_ajax_apply_discount_code')) {
    add_action('wp_ajax_apply_discount_code', 'handle_discount_code_ajax');
    add_action('wp_ajax_nopriv_apply_discount_code', 'handle_discount_code_ajax');
}

function handle_discount_code_ajax() {
    // 1. اعتبارسنجی ورودی‌ها
    $code = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';
    $items = isset($_POST['items']) ? $_POST['items'] : [];

    if (empty($code) || empty($items)) {
        wp_send_json_error(['message' => 'اطلاعات کد تخفیف ناقص است.']);
    }

    // 2. فراخوانی API
    if (class_exists('Laravel_API_Client')) {
        $api = Laravel_API_Client::get_instance();
        $result = $api->check_discount($code, $items);

        // 3. بررسی نتیجه
        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        } else {
            // بررسی فیلد valid در پاسخ JSON سرور
            if (isset($result['valid']) && $result['valid'] === true) {
                wp_send_json_success($result);
            } else {
                $msg = isset($result['message']) ? $result['message'] : 'کد تخفیف معتبر نیست.';
                wp_send_json_error(['message' => $msg]);
            }
        }
    } else {
        wp_send_json_error(['message' => 'کلاس ارتباط با API پیدا نشد.']);
    }
}