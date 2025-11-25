<?php
function wbs_500_enqueue_scripts() {
    
    if (is_single() || is_page_template('500.php')) {
        wp_enqueue_style('500Style', THEME_LIB . '500/assets/scss/style.css');
        wp_enqueue_script('500Script', THEME_LIB . '500/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_500_enqueue_scripts');





