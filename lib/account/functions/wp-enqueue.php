<?php

function wbs_account_enqueue_scripts()
{
    if (is_page('userdashboard') || is_page_template('page-dashboard.php')) {
        wp_enqueue_style('accountStyle', THEME_LIB . 'account/assets/scss/style.css', array(), THEME_VERSION);

        wp_enqueue_script('accountScript', THEME_LIB . 'account/assets/js/script.js', array('jquery'), THEME_VERSION, true);

        wp_localize_script('accountScript', 'wbsData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('wbs_dashboard_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'wbs_account_enqueue_scripts');

function my_login_redirect($redirect_to, $request, $user)
{
    // Check if the user has a specific role (optional), or redirect all users
    if (isset($user->roles) && is_array($user->roles)) {
        // Check for administrators
        if (in_array('administrator', $user->roles)) {
            // Admins go to WP Admin Panel
            return admin_url();
        } else {
            // Normal users go to the custom Dashboard page
            return home_url('/userdashboard/');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'my_login_redirect', 10, 3);
