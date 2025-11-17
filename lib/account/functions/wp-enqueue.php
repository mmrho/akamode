<?php
function wbs_account_enqueue_scripts() {
    
    if (is_single() || is_page_template('account.php')) {
        wp_enqueue_style('accountStyle', THEME_LIB . 'account/assets/scss/style.css');
        wp_enqueue_script('accountScript', THEME_LIB . 'account/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_account_enqueue_scripts');





