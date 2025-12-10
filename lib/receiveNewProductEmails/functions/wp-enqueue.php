<?php
function wbs_receiveNewProductEmails_enqueue_scripts() {

    
    if (is_page_template('receiveNewProductEmails.php')) {
        
        wp_enqueue_style( 'receiveNewProductEmailsStyle', THEME_LIB . 'receiveNewProductEmails/assets/scss/style.css' );
        wp_enqueue_script( 'receiveNewProductEmailsScript', THEME_LIB . 'receiveNewProductEmails/assets/js/script.js', array( 'jquery' ), THEME_VERSION, true );
        
    }
}
add_action( 'wp_enqueue_scripts', 'wbs_receiveNewProductEmails_enqueue_scripts' );
