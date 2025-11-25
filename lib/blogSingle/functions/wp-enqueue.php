<?php
function wbs_blog_single_enqueue_scripts() {
    
    if (is_single() || is_page_template('blog-single.php')) {
        wp_enqueue_style('blogSingleStyle', THEME_LIB . 'blogSingle/assets/scss/style.css');
        wp_enqueue_script('blogSingleScript', THEME_LIB . 'blogSingle/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_blog_single_enqueue_scripts');