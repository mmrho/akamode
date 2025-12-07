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


/**
 * 1. The Helper Function (Use this in your theme files)
 */
/**
 * Helper Function: Date AND Time
 */
function get_persian_date($date_string) {
    if (empty($date_string)) return '';

    $timestamp = strtotime($date_string);
    
    // 1. Calculate the Date
    $g_y = date('Y', $timestamp);
    $g_m = date('n', $timestamp);
    $g_d = date('j', $timestamp);
    list($j_y, $j_m, $j_d) = gregorian_to_jalali($g_y, $g_m, $g_d);

    // Add leading zeros (e.g., 5 -> 05)
    if ($j_m < 10) $j_m = '0' . $j_m;
    if ($j_d < 10) $j_d = '0' . $j_d;

    // 2. Extract the Time (Hours:Minutes)
    $time = date('H:i', $timestamp);

    // 3. Combine them (Format: YYYY-MM-DD HH:MM)
    return $j_y . '-' . $j_m . '-' . $j_d . ' ' . $time;
}

/**
 * 2. The Math Core (Don't touch this)
 * Converts Gregorian to Jalali cleanly.
 */
function gregorian_to_jalali($gy, $gm, $gd) {
    $g_d_m = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = 355666 + (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400)) + $gd + $g_d_m[$gm - 1];
    $jy = -1595 + (33 * ((int)($days / 12053)));
    $days %= 12053;
    $jy += 4 * ((int)($days / 1461));
    $days %= 1461;
    if ($days > 365) {
        $jy += (int)(($days - 1) / 365);
        $days = ($days - 1) % 365;
    }
    if ($days < 186) {
        $jm = 1 + (int)($days / 31);
        $jd = 1 + ($days % 31);
    } else {
        $jm = 7 + (int)(($days - 186) / 30);
        $jd = 1 + (($days - 186) % 30);
    }
    return array($jy, $jm, $jd);
}