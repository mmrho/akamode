<?php
function wbs_blog_enqueue_scripts() {
    
    if (is_single() || is_page_template('home.php')) {
        wp_enqueue_style('blogStyle', THEME_LIB . 'blog/assets/scss/style.css');
        wp_enqueue_script('blogScript', THEME_LIB . 'blog/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_blog_enqueue_scripts');




