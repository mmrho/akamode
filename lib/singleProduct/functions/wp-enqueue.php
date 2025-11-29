<?php

function wbs_singleProduct_enqueue_scripts() {
    
    // Check if the 'product_slug' query variable exists.
    // This confirms we are on the virtual product page created by rewrite rules.
    if ( get_query_var('product_slug') ) {
        
        wp_enqueue_style('singleProductStyle', THEME_LIB . 'singleProduct/assets/scss/style.css');
        
        // Assuming THEME_VERSION is defined in your theme
        wp_enqueue_script('singleProductScript', THEME_LIB . 'singleProduct/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_singleProduct_enqueue_scripts');





/**
 * 1. Create a Rewrite Rule
 * Converts the /product/slug URL into a query variable.
 */
function akamode_add_product_rewrite_rule()
{
    add_rewrite_rule(
        '^product/([^/]*)/?',      // Address pattern (Regex)
        'index.php?product_slug=$matches[1]', // Convert to WordPress variable
        'top'
    );
}
add_action('init', 'akamode_add_product_rewrite_rule');

/**
 * 2. Register Query Var
 * We tell WordPress that product_slug is a valid variable.
 */
function akamode_register_query_var($vars)
{
    $vars[] = 'product_slug';
    return $vars;
}
add_filter('query_vars', 'akamode_register_query_var');

/**
 * 3. Load the template (Template Loader)
 * If the product_slug variable exists, execute the api-product.php file.
 */
function akamode_load_product_template($template)
{
    // Check if this request is related to the API product?
    if (get_query_var('product_slug')) {

        $new_template = locate_template(array('single-product.php'));

        if ('' != $new_template) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'akamode_load_product_template');



























/**
 * تغییر عنوان صفحه (Meta Title) برای محصولات API
 */
add_filter('document_title_parts', function($title) {
    if (get_query_var('product_slug')) {
        // دریافت اسلاگ
        $slug = get_query_var('product_slug');
        
        // فراخوانی سریع API (چون کش دارد، فشار نمی‌آورد)
        $api = Laravel_API_Client::get_instance();
        $response = $api->get_product_single($slug);
        
        if (!is_wp_error($response) && !empty($response['data'])) {
            // تغییر تایتل به نام محصول
            $title['title'] = $response['data']['name'];
            
            // می‌توانید نام دسته را هم اضافه کنید
            // $title['site'] = 'فروشگاه آکامد'; 
        }
    }
    return $title;
});

/**
 * اضافه کردن توضیحات متا (Meta Description) و Open Graph
 * برای اینکه Yoast یا RankMath بفهمند این صفحه دیتا دارد
 */
add_action('wp_head', function() {
    if (get_query_var('product_slug')) {
        $slug = get_query_var('product_slug');
        $api = Laravel_API_Client::get_instance();
        $response = $api->get_product_single($slug);

        if (!is_wp_error($response) && !empty($response['data'])) {
            $product = $response['data'];
            
            // توضیحات متا (خلاصه توضیحات محصول)
            // معمولا ۱۵۰ کاراکتر اول توضیحات را برمی‌داریم
            $desc = mb_substr(strip_tags($product['description']), 0, 160) . '...';
            
            echo '<meta name="description" content="' . esc_attr($desc) . '" />' . "\n";
            
            // تگ‌های شبکه اجتماعی (Open Graph)
            echo '<meta property="og:title" content="' . esc_attr($product['name']) . '" />' . "\n";
            echo '<meta property="og:description" content="' . esc_attr($desc) . '" />' . "\n";
            
            // عکس محصول برای اشتراک گذاری در تلگرام/واتساپ
            if (!empty($product['images'][0]['url'])) {
                $base_url = defined('LARAVEL_API_URL') ? LARAVEL_API_URL : 'https://akamode.com';
                $img_url = untrailingslashit($base_url) . $product['images'][0]['url'];
                echo '<meta property="og:image" content="' . esc_url($img_url) . '" />' . "\n";
            }
            
            // حذف تگ‌های کنونیکال اشتباهی که ممکن است افزونه‌ها اضافه کنند
            remove_action('wp_head', 'rel_canonical');
            echo '<link rel="canonical" href="' . home_url('/product/' . $slug) . '" />' . "\n";
        }
    }
}, 1);