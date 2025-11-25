<?php
function wbs_checkout_enqueue_scripts() {
    
    if (is_single() || is_page_template('checkout.php')) {
        wp_enqueue_style('checkoutStyle', THEME_LIB . 'checkout/assets/scss/style.css');
        wp_enqueue_script('checkoutScript', THEME_LIB . 'checkout/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_checkout_enqueue_scripts');





