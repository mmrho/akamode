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
 * تبدیل اعداد فارسی/عربی به انگلیسی و تمیزکاری
 */
function akamode_sanitize_mobile($number) {
    $number = sanitize_text_field($number);
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    $number = str_replace($persian, $english, $number);
    $number = str_replace($arabic, $english, $number);
    
    // فقط اعداد باقی بمانند
    return preg_replace('/[^0-9]/', '', $number);
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
    check_ajax_referer('akamode_auth_nonce', 'security');

    // خواندن از آرایه fields (طبق ساختار wbsAjax)
    $fields = isset($_POST['fields']) ? $_POST['fields'] : [];
    $phone  = isset($fields['mobile']) ? akamode_sanitize_mobile($fields['mobile']) : '';

    if (empty($phone) || strlen($phone) < 10) {
        akamode_send_error('شماره موبایل وارد شده صحیح نمی‌باشد!');
        return;
    }

    $api = Laravel_API_Client::get_instance();
    $response = $api->send_otp($phone);

    // خطای کلی ارتباط
    if (is_wp_error($response)) {
        akamode_send_error('خطا در ارتباط با سامانه پیامکی.');
        return;
    }

    // بررسی موفقیت منطقی
    if (isset($response['success']) && $response['success'] == true) {
        // نکته مهم: پیام موفقیت را دستی می‌نویسیم تا کد تایید (اگر در پاسخ API بود) لو نرود.
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
    check_ajax_referer('akamode_auth_nonce', 'security');

    $fields = isset($_POST['fields']) ? $_POST['fields'] : [];
    $phone  = isset($fields['mobile']) ? akamode_sanitize_mobile($fields['mobile']) : '';
    $code   = isset($fields['otp']) ? akamode_sanitize_mobile($fields['otp']) : '';

    if (empty($phone) || empty($code)) {
        akamode_send_error('لطفا کد تایید را وارد کنید.');
        return;
    }

    $api = Laravel_API_Client::get_instance();
    $response = $api->verify_otp($phone, $code);

    // مدیریت خطای API (مثل کد اشتباه)
    if (is_wp_error($response)) {
        // تلاش برای استخراج پیام خطای دقیق از API
        $error_msg = $response->get_error_message();
        
        // اگر API مسیج خاصی فرستاده بود، همان را نمایش بده
        if (empty($error_msg)) {
            $error_msg = 'کد تایید اشتباه است یا منقضی شده است.';
        }
        
        akamode_send_error($error_msg);
        return;
    }

    // بررسی موفقیت
    if (isset($response['success']) && $response['success'] == true) {
        
        $token = isset($response['token']) ? $response['token'] : null;
        $user_data = isset($response['user']) ? $response['user'] : [];

        if (!$token) {
            akamode_send_error('خطا: توکن ورود دریافت نشد.');
            return;
        }

        // عملیات ثبت/آپدیت در دیتابیس وردپرس
        $userID = akamode_create_or_update_user($user_data, $token);

        if (is_wp_error($userID)) {
            akamode_send_error('خطا در ثبت کاربر در سایت.');
            return;
        }

        // لاگین اجباری
        wp_set_current_user($userID);
        wp_set_auth_cookie($userID);

        akamode_send_success([
            'message'      => 'ورود موفقیت‌آمیز بود.',
            'redirect_url' => home_url('/dashboard') // لینک ریدایرکت را اینجا تنظیم کنید
        ]);

    } else {
        // حالتی که سرور 200 برگردانده اما success فالس است
        $msg = isset($response['message']) ? $response['message'] : 'کد تایید اشتباه است.';
        akamode_send_error($msg);
    }
}
?>