<?php
function wbs_category_enqueue_scripts() {


    //is_search()
    
    if ( is_category() || is_archive() ) {
        
        wp_enqueue_style( 'categoryStyle', THEME_LIB . 'category/assets/scss/style.css' );
        wp_enqueue_script( 'categoryScript', THEME_LIB . 'category/assets/js/script.js', array( 'jquery' ), THEME_VERSION, true );
        
    }
}
add_action( 'wp_enqueue_scripts', 'wbs_category_enqueue_scripts' );
