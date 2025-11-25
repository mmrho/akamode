<?php
function wbs_faq_enqueue_scripts() {
    
    if (is_single() || is_page_template('faq.php')) {
        wp_enqueue_style('faqStyle', THEME_LIB . 'faq/assets/scss/style.css');
        wp_enqueue_script('faqScript', THEME_LIB . 'faq/assets/js/script.js', array('jquery'), THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'wbs_faq_enqueue_scripts');





