<?php
// wp-enqueue.php

function wbs_search_enqueue_scripts() {
    
    // Always load search styles/scripts globally or condition properly
    // Note: Live search is in the header, so it might be needed on all pages, not just is_search()
    
    wp_enqueue_style( 'searchStyle', THEME_LIB . 'search/assets/scss/style.css' );
    wp_enqueue_script( 'searchScript', THEME_LIB . 'search/assets/js/script.js', array( 'jquery' ), THEME_VERSION, true );

    // LOCALIZE SCRIPT: Pass PHP data to JS
    wp_localize_script( 'searchScript', 'wbs_data', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'wbs_search_nonce' ),
        'base_api_url' => 'https://akamode.com' // Needed for image paths
    ));
        
}
add_action( 'wp_enqueue_scripts', 'wbs_search_enqueue_scripts' );