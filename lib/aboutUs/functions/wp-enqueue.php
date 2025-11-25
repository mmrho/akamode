<?php
function wbs_about_us_enqueue_scripts() {
    
    if (is_single() || is_page_template('about-us.php')) {
        wp_enqueue_style('aboutUsStyle', THEME_LIB . 'aboutUs/assets/scss/style.css');
        wp_enqueue_script('aboutUsScript', THEME_LIB . 'aboutUs/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_about_us_enqueue_scripts');





