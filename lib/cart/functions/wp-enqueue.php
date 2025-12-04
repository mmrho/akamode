<?php
function wbs_cart_enqueue_scripts() {
    
    // فقط در صفحه سبد خرید بارگذاری شود
    if (is_single() || is_page_template('cart.php')) {
        
        // استایل‌ها
        wp_enqueue_style('cartStyle', THEME_LIB . 'cart/assets/scss/style.css');
        
        // اسکریپت‌ها
        wp_enqueue_script('cartScript', THEME_LIB . 'cart/assets/js/script.js', array('jquery'), THEME_VERSION, true);

        // *** FIX: ارسال متغیر wbs_ajax به فایل جاوااسکریپت ***
        wp_localize_script('cartScript', 'wbs_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('cart_nonce') // جهت امنیت (اختیاری)
        ));
    }
}
add_action('wp_enqueue_scripts', 'wbs_cart_enqueue_scripts');