<?php
function wbs_404_enqueue_scripts() {
    
    if (is_single() || is_page_template('404.php')) {
        wp_enqueue_style('404Style', THEME_LIB . '404/assets/scss/style.css');
        wp_enqueue_script('404Script', THEME_LIB . '404/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_404_enqueue_scripts');





