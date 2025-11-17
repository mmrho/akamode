<?php
function wbs_singleProduct_enqueue_scripts() {
    
    if (is_single() || is_page_template('single-product.php')) {
        wp_enqueue_style('singleProductStyle', THEME_LIB . 'singleProduct/assets/scss/style.css');
        wp_enqueue_script('singleProductScript', THEME_LIB . 'singleProduct/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_singleProduct_enqueue_scripts');

/*
بعدا در صورت کخ به پروداکت تغییر دادیم اینکیو باید به این صورت باشه 
function wbs_singleProduct_enqueue_scripts() {
    if (is_singular('product')) {
        wp_enqueue_style('singleProductStyle', THEME_LIB . 'singleProduct/assets/css/style.css'); // scss رو به css تغییر دادم، چون enqueue مستقیم scss نمی‌کنه 😈
        wp_enqueue_script('singleProductScript', THEME_LIB . 'singleProduct/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_singleProduct_enqueue_scripts');



*/





// Enable Aparat embed support in WordPress
function aparat_oembed_support() {
    wp_oembed_add_provider(
        '#https?://(www\.)?aparat\.com/v/.*#i',
        'https://www.aparat.com/oembed',
        true
    );
}
add_action('init', 'aparat_oembed_support');
