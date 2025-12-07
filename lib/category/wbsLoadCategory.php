<?php
defined('ABSPATH') || exit;

require_once THEME_LIB_DIR . 'category/functions/wp-enqueue.php';
require_once THEME_LIB_DIR . 'category/functions/ajax.php';

function wbsLoadCategory()
{
    $api = Laravel_API_Client::get_instance();
    
    // دریافت شماره صفحه فعلی
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // متغیرهای پیش‌فرض
    $is_single_category = false;
    $page_data = [];
    $products = [];
    $meta = []; // برای صفحه‌بندی
    $current_category = null;

    // 1. بررسی اینکه آیا در صفحه آرشیو یک دسته‌بندی خاص هستیم؟
    if (is_category()) {
        $cat_slug = get_query_var('category_name');
        
        if (!$cat_slug) {
            $obj = get_queried_object();
            if ($obj) $cat_slug = $obj->slug;
        }

        // درخواست به API برای گرفتن محصولات این دسته خاص
        if ($cat_slug) {
            // نکته: طبق مستندات، ممکن است API شما محصولات را داخل همین اندپوینت برگرداند
            // یا نیاز باشد از اندپوینت search یا products با فیلتر استفاده کنید.
            // فرض بر اساس فایل جیسون شما: /api/v1/categories/{slug}
            $response = $api->get_category_single($cat_slug); // باید متدی باشد که محصولات را هم برگرداند یا جداگانه کال کنید
            
            if (!is_wp_error($response) && isset($response['category'])) {
                $is_single_category = true;
                $current_category = $response['category'];
                
                // اگر API محصولات را داخل کلید products برمی‌گرداند
                $products = isset($response['products']['data']) ? $response['products']['data'] : (isset($response['products']) ? $response['products'] : []);
                $meta = isset($response['products']['meta']) ? $response['products']['meta'] : [];
            }
        }
    } 
    
    // 2. اگر صفحه اصلی دسته‌بندی‌ها بود (یا دیتای سینگل یافت نشد)
    if (!$is_single_category) {
        // گرفتن همه محصولات با صفحه‌بندی
        $product_response = $api->get_products($paged);
        
        if (!is_wp_error($product_response)) {
            $products = isset($product_response['data']) ? $product_response['data'] : [];
            $meta = isset($product_response['meta']) ? $product_response['meta'] : [];
        }
        
        // گرفتن لیست همه دسته‌بندی‌ها (فقط در صفحه اصلی نمایش داده می‌شود)
        $cats_response = $api->get_categories();
        $page_data['categories'] = isset($cats_response['data']) ? $cats_response['data'] : [];
    }

    // ارسال متغیرها به فایل ویو (Template)
    set_query_var('wbs_is_single_cat', $is_single_category);
    set_query_var('wbs_current_cat', $current_category);
    set_query_var('wbs_products', $products);
    set_query_var('wbs_meta', $meta); // متادیتای صفحه‌بندی
    set_query_var('wbs_all_cats', isset($page_data['categories']) ? $page_data['categories'] : []);

    require_once "template/categoryPage.php";
}