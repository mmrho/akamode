<?php
function wbs_cart_enqueue_scripts() {
    
    if (is_single() || is_page_template('cart.php')) {
        wp_enqueue_style('cartStyle', THEME_LIB . 'cart/assets/scss/style.css');
        wp_enqueue_script('cartScript', THEME_LIB . 'cart/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_cart_enqueue_scripts');





