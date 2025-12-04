<?php
function wbs_404_enqueue_scripts() {
    
    // Check if the current page is a 404 error page
    if ( is_404() ) {
        // Enqueue styles
        wp_enqueue_style( '404Style', THEME_LIB . '404/assets/scss/style.css' );
        
        // Enqueue scripts
        wp_enqueue_script( '404Script', THEME_LIB . '404/assets/js/script.js', array('jquery'), THEME_VERSION, true );
    }
}
add_action( 'wp_enqueue_scripts', 'wbs_404_enqueue_scripts' );

