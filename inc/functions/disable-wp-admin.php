<?php
/**
 * Disable WordPress Admin Bar and Restrict Admin Access for non-admin users
 */

// 1. مخفی کردن نوار سیاه (Admin Bar) در ظاهر سایت
function wbs_disable_admin_bar_for_non_admins() {
    // اگر کاربر دسترسی مدیریت ندارد (ادمین نیست)
    if (!current_user_can('manage_options')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'wbs_disable_admin_bar_for_non_admins');

// 2. جلوگیری از دسترسی به پیشخوان وردپرس (wp-admin)
function wbs_restrict_admin_access() {
    // اگر درخواست مربوط به محیط ادمین است و ایجکس نیست و کاربر ادمین نیست
    if (is_admin() && !wp_doing_ajax() && !current_user_can('manage_options')) {
        // ریدایرکت به داشبورد اختصاصی کاربر (طبق منطق کد قبلی خودتان)
        $redirect = home_url('/userdashboard/');
        wp_redirect($redirect);
        exit;
    }
}
// هوک init برای ریدایرکت امن‌تر است
add_action('init', 'wbs_restrict_admin_access');