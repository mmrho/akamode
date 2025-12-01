<?php
/**
 * Template Name: User Dashboard
 * File: accountPage.php
 * Description: Main controller for user dashboard, handles API logic and view routing.
 */

// 1. بررسی امنیت و لاگین بودن کاربر
if (!is_user_logged_in()) {
    // اگر کاربر لاگین نیست، به صفحه ورود هدایت شود
    wp_redirect(home_url('/user-login')); 
    exit;
}

// 2. دریافت توکن و تنظیمات API
$current_user_id = get_current_user_id();
// توکن را از متای کاربر که در مرحله لاگین ذخیره شده می‌خوانیم
$token = get_user_meta($current_user_id, '_laravel_api_token', true);

$api = Laravel_API_Client::get_instance();
if ($token) {
    $api->set_token($token);
}

// متغیرهای نمایش پیام به کاربر
$success_msg = '';
$error_msg   = '';

// 3. پردازش فرم‌ها (وقتی دکمه‌ای زده می‌شود)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- الف) آپدیت پروفایل (نام و ایمیل) ---
    if (isset($_POST['wbs_action']) && $_POST['wbs_action'] === 'update_profile') {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wbs_profile_update')) {
            $error_msg = 'نشست امنیتی منقضی شده است. لطفا صفحه را رفرش کنید.';
        } else {
            $name  = sanitize_text_field($_POST['account_name']);
            $email = sanitize_email($_POST['account_email']);
            
            // ارسال به API
            $result = $api->update_profile($name, $email);
            
            if (is_wp_error($result)) {
                $error_msg = $result->get_error_message();
            } else {
                // موفقیت: همزمان در وردپرس هم آپدیت می‌کنیم
                wp_update_user([
                    'ID' => $current_user_id,
                    'display_name' => $name,
                    'first_name' => $name,
                    'user_email' => $email
                ]);
                $success_msg = 'اطلاعات حساب با موفقیت بروزرسانی شد.';
            }
        }
    }

    // --- ب) افزودن آدرس جدید ---
    if (isset($_POST['wbs_action']) && $_POST['wbs_action'] === 'add_address') {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wbs_address_action')) {
            $error_msg = 'خطای امنیتی.';
        } else {
            $address_data = [
                'full_name' => sanitize_text_field($_POST['full_name']),
                'phone'     => sanitize_text_field($_POST['phone']),
                'state'     => sanitize_text_field($_POST['state']), // کلید صحیح طبق API لاراول
                'city'      => sanitize_text_field($_POST['city']),
                'address'   => sanitize_textarea_field($_POST['address']),
                'zip_code'  => sanitize_text_field($_POST['zip_code']),
            ];

            $result = $api->add_address($address_data);

            if (is_wp_error($result)) {
                $error_msg = $result->get_error_message();
            } else {
                $success_msg = 'آدرس جدید با موفقیت ثبت شد.';
            }
        }
    }

    // --- ج) ویرایش آدرس موجود (جدید) ---
    if (isset($_POST['wbs_action']) && $_POST['wbs_action'] === 'update_address') {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wbs_address_action')) {
            $error_msg = 'خطای امنیتی.';
        } else {
            $addr_id = intval($_POST['address_id']);
            $address_data = [
                'full_name' => sanitize_text_field($_POST['full_name']),
                'phone'     => sanitize_text_field($_POST['phone']),
                'state'     => sanitize_text_field($_POST['state']),
                'city'      => sanitize_text_field($_POST['city']),
                'address'   => sanitize_textarea_field($_POST['address']),
                'zip_code'  => sanitize_text_field($_POST['zip_code']),
            ];

            // کلاینت API خودش متد PUT را هندل می‌کند
            $result = $api->update_address($addr_id, $address_data);

            if (is_wp_error($result)) {
                $error_msg = $result->get_error_message();
            } else {
                $success_msg = 'آدرس با موفقیت ویرایش شد.';
            }
        }
    }

    // --- د) حذف آدرس ---
    if (isset($_POST['wbs_action']) && $_POST['wbs_action'] === 'delete_address') {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wbs_delete_address')) {
            $error_msg = 'خطای امنیتی.';
        } else {
            $addr_id = intval($_POST['address_id']);
            $result = $api->delete_address($addr_id);
            
            if (is_wp_error($result)) {
                $error_msg = $result->get_error_message();
            } else {
                $success_msg = 'آدرس با موفقیت حذف شد.';
            }
        }
    }
}

// 4. دریافت اطلاعات کاربر (برای نمایش در هدر)
$user_info = $api->get_user_info();
if (is_wp_error($user_info)) {
    // اگر API قطع بود، از دیتای وردپرس استفاده کن تا صفحه سفید نشود
    $current_wp_user = wp_get_current_user();
    $user = [
        'name' => $current_wp_user->display_name,
        'email' => $current_wp_user->user_email,
        'mobile' => $current_wp_user->user_login
    ];
} else {
    $user = $user_info; 
}

// 5. مدیریت تب‌ها و URL
$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';
$base_url   = get_permalink(); 
$logout_url = wp_logout_url(home_url()); // لینک خروج استاندارد وردپرس

?>

<div class="modal-container">
    <div class="modal">
        <div class="top">
            <div>خروج از حساب کاربری</div>
            <span class="times">&times;</span>
        </div>
        <div class="message">
            آیا برای خروج از حساب کاربری خود اطمینان دارید؟
        </div>
        <div class="buttons">
            <button class="close">انصراف</button>
            <a href="<?php echo esc_url($logout_url); ?>" class="logout" style="text-decoration:none;">خروج</a>
        </div>
    </div>
 </div>

<div class="container">
    <div class="main">

        <div class="top">
            <h1>حساب کاربری</h1>
            <p><?php echo isset($user['name']) ? 'سلام ' . esc_html($user['name']) : 'خوش آمدید'; ?></p>
            
            <?php if($success_msg): ?>
                <div style="background:#d4edda;color:#155724;padding:10px;border-radius:5px;width:100%;text-align:center; margin-bottom: 20px;">
                    <?php echo esc_html($success_msg); ?>
                </div>
            <?php endif; ?>

            <?php if($error_msg): ?>
                <div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;width:100%;text-align:center; margin-bottom: 20px;">
                    <?php echo esc_html($error_msg); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <?php 
        // سناریوی 1: نمایش جزئیات یک سفارش خاص (بدون تب‌ها)
        if ($active_tab == 'view-order') : 
            set_query_var('api_client', $api); 
            get_template_part('template-parts/account/content-view-order'); 

        // سناریوی 2: نمایش تب‌های استاندارد
        else : 
        ?>

            <div class="tabs-container">
                <div class="tabs">
                    <a href="<?php echo esc_url($base_url); ?>" class="tab <?php echo ($active_tab == 'dashboard') ? 'active' : ''; ?>">داشبورد</a>
                    <a href="<?php echo esc_url(add_query_arg('tab', 'orders', $base_url)); ?>" class="tab <?php echo ($active_tab == 'orders') ? 'active' : ''; ?>">سفارش ها</a>
                    <a href="<?php echo esc_url(add_query_arg('tab', 'address', $base_url)); ?>" class="tab <?php echo ($active_tab == 'address') ? 'active' : ''; ?>">آدرس ها</a>
                    <a href="<?php echo esc_url(add_query_arg('tab', 'details', $base_url)); ?>" class="tab <?php echo ($active_tab == 'details') ? 'active' : ''; ?>">جزئیات اکانت</a>
                    <a href="#" class="tab logout">خروج</a>
                </div>

                <div class="body">
                <?php
                    // ارسال متغیرها به فایل‌های تمپلت (بسیار مهم)
                    set_query_var('user_data', $user);
                    set_query_var('api_client', $api);

                    if ($active_tab == 'orders') {
                        get_template_part('template-parts/account/content-orders');
                    } elseif ($active_tab == 'address') {
                        // فایل آدرس‌ها که اصلاح کردیم
                        get_template_part('template-parts/account/content-address');
                    } elseif ($active_tab == 'details') {
                        get_template_part('template-parts/account/content-details');
                    } else {
                        get_template_part('template-parts/account/content-dashboard');
                    }
                ?>
                </div>
            </div>

        <?php endif; ?>
        
    </div>
</div>