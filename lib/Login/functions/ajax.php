<?php
// =============================================================================
//  توابع کمکی و هندلرهای لاگین با API
// =============================================================================

/**
 * ارسال پاسخ خطا استاندارد
 */
function akamode_send_error($message, $redirect = false) {
    wp_send_json_error([
        'message' => $message,
        'redirect' => $redirect
    ]);
}

/**
 * ارسال پاسخ موفقیت استاندارد
 */
function akamode_send_success($data) {
    wp_send_json_success($data);
}

/**
 * ایجاد یا بروزرسانی کاربر در وردپرس پس از لاگین موفق
 */
function akamode_create_or_update_user($api_user_data, $token) {
    $mobile = isset($api_user_data['mobile']) ? $api_user_data['mobile'] : '';
    $name   = isset($api_user_data['name']) ? $api_user_data['name'] : '';
    $api_id = isset($api_user_data['id']) ? $api_user_data['id'] : 0;

    if (empty($mobile)) return new WP_Error('no_mobile', 'شماره موبایل در پاسخ API وجود ندارد.');

    $user = get_user_by('login', $mobile);
    
    if (!$user) {
        // ثبت نام کاربر جدید
        $userdata = array(
            'user_login'      => $mobile,
            'user_pass'       => wp_generate_password(16, true, true),
            'role'            => 'subscriber',
            'display_name'    => $name,
            'first_name'      => $name,
            'user_registered' => current_time('mysql')
        );
        
        $userID = wp_insert_user($userdata);
        
        if (is_wp_error($userID)) {
            return $userID;
        }
    } else {
        // کاربر قدیمی
        $userID = $user->ID;
        // بروزرسانی نام اگر تغییر کرده باشد
        if (!empty($name) && $user->display_name !== $name) {
            wp_update_user(['ID' => $userID, 'display_name' => $name, 'first_name' => $name]);
        }
    }
    
    // ذخیره متادیتاهای مهم
    update_user_meta($userID, '_laravel_user_id', $api_id);
    update_user_meta($userID, '_laravel_api_token', $token);
    
    return $userID;
}

// =============================================================================
//  AJAX HANDLERS
// =============================================================================

// 1. هندلر ارسال کد تایید (Send OTP)
add_action('wp_ajax_nopriv_akamode_send_otp', 'akamode_handle_send_otp');
add_action('wp_ajax_akamode_send_otp', 'akamode_handle_send_otp');

function akamode_handle_send_otp() {
    // بررسی Nonce برای امنیت
    if(isset($_POST['security'])) {
        check_ajax_referer('akamode_auth_nonce', 'security');
    }

    $fields = isset($_POST['fields']) ? $_POST['fields'] : [];
    
    // استفاده از کلاس WbsUtility برای تمیزکاری و تبدیل اعداد
    if (isset($fields['mobile'])) {
        $raw_mobile = WbsUtility::convertFaNum2EN($fields['mobile']); 
        $phone = WbsUtility::inputClean($raw_mobile); 
    } else {
        $phone = '';
    }

    // ولیدیشن فرمت موبایل با استفاده از کلاس WbsUtility
    if (!class_exists('WbsUtility') || !WbsUtility::wbsCheckPhone($phone)) {
        akamode_send_error('شماره موبایل وارد شده صحیح نمی‌باشد!');
        return;
    }

    // --- سیستم محدودیت ارسال (Rate Limiting) ---
    $hashed_phone = md5($phone); 
    $block_key = 'otp_blocked_' . $hashed_phone;
    $count_key = 'otp_count_' . $hashed_phone;

    // ۱. بررسی اینکه آیا کاربر بلاک شده است؟
    if (get_transient($block_key)) {
        akamode_send_error('تعداد درخواست‌های شما بیش از حد مجاز است. لطفا ۱۰ دقیقه دیگر تلاش کنید.');
        return;
    }

    // ۲. بررسی تعداد تلاش‌ها
    $attempts = (int) get_transient($count_key);
    
    if ($attempts >= 5) {
        // اگر به ۵ تلاش رسید، بلاک کن برای ۱۰ دقیقه
        set_transient($block_key, true, 10 * 60); 
        delete_transient($count_key); 
        akamode_send_error('شما ۵ بار درخواست کد داده‌اید. دسترسی شما به مدت ۱۰ دقیقه محدود شد.');
        return;
    } else {
        // افزایش شمارنده (اعتبار شمارنده ۱۵ دقیقه)
        set_transient($count_key, $attempts + 1, 15 * 60);
    }
    // ---------------------------------------------

    if (!class_exists('Laravel_API_Client')) {
        akamode_send_error('خطای سیستمی: کلاس API یافت نشد.');
        return;
    }

    $api = Laravel_API_Client::get_instance();
    $response = $api->send_otp($phone);

    if (is_wp_error($response)) {
        akamode_send_error('خطا در ارتباط با سامانه پیامکی.');
        return;
    }

    if (isset($response['success']) && $response['success'] == true) {
        akamode_send_success([
            'message' => 'کد تایید با موفقیت ارسال شد.',
            'mobile'  => $phone
        ]);
    } else {
        $msg = isset($response['message']) ? $response['message'] : 'خطا در ارسال پیامک!';
        akamode_send_error($msg);
    }
}

// 2. هندلر بررسی کد و ورود (Verify OTP)
add_action('wp_ajax_nopriv_akamode_verify_otp', 'akamode_handle_verify_otp');
add_action('wp_ajax_akamode_verify_otp', 'akamode_handle_verify_otp');

function akamode_handle_verify_otp() {
    if(isset($_POST['security'])) {
        check_ajax_referer('akamode_auth_nonce', 'security');
    }

    $fields = isset($_POST['fields']) ? $_POST['fields'] : [];
    
    // استفاده از WbsUtility برای ورودی‌ها
    $phone = isset($fields['mobile']) ? WbsUtility::inputClean(WbsUtility::convertFaNum2EN($fields['mobile'])) : '';
    $code  = isset($fields['otp']) ? WbsUtility::inputClean(WbsUtility::convertFaNum2EN($fields['otp'])) : '';
    
    $redirect_input = isset($fields['redirect_to']) ? esc_url_raw($fields['redirect_to']) : '';

    if (empty($phone) || empty($code)) {
        akamode_send_error('لطفا کد تایید را وارد کنید.');
        return;
    }

    if (!class_exists('Laravel_API_Client')) {
        akamode_send_error('خطای سیستمی: کلاس API یافت نشد.');
        return;
    }

    $api = Laravel_API_Client::get_instance();
    $response = $api->verify_otp($phone, $code);

    if (is_wp_error($response)) {
        $error_msg = $response->get_error_message();
        if (empty($error_msg)) {
            $error_msg = 'کد تایید اشتباه است یا منقضی شده است.';
        }
        akamode_send_error($error_msg);
        return;
    }

    // بررسی موفقیت لاگین در API
    if (isset($response['success']) && $response['success'] == true) {
        
        $token = isset($response['token']) ? $response['token'] : null;
        $user_data = isset($response['user']) ? $response['user'] : [];

        if (!$token) {
            akamode_send_error('خطا: توکن ورود دریافت نشد.');
            return;
        }

        // ثبت یا آپدیت کاربر در وردپرس
        $userID = akamode_create_or_update_user($user_data, $token);

        if (is_wp_error($userID)) {
            akamode_send_error('خطا در ثبت کاربر در سایت.');
            return;
        }

        // لاگین کردن کاربر در وردپرس
        wp_set_current_user($userID);
        wp_set_auth_cookie($userID);

        // حذف شمارنده تلاش‌های ناموفق پس از لاگین موفق (اختیاری)
        // delete_transient('otp_count_' . md5($phone));

        // ریدایرکت
        if (!empty($redirect_input)) {
            $final_redirect = $redirect_input;
        } else {
            $final_redirect = home_url('/dashboard');
        }

        akamode_send_success([
            'message'      => 'ورود موفقیت‌آمیز بود.',
            'redirect_url' => $final_redirect
        ]);

    } else {
        $msg = isset($response['message']) ? $response['message'] : 'کد تایید اشتباه است.';
        akamode_send_error($msg);
    }
}
?>