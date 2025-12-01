<?php
/**
 * Header & Menu Configuration
 * Fetches menu data dynamically from Laravel API and maps it to the theme structure.
 */

// 1. Shared site information
$site_data = [
    'name'               => get_bloginfo('name') ?: 'آکامد',
    'url'                => esc_url(home_url('/')),
    'logo'               => get_template_directory_uri() . '/images/svg/akamode-logo.svg',
    'phone'              => '۹۸۴۱۳۵۵۲۱۰۹۸',
    'search_placeholder' => 'دنبال چی میگردی؟'
];

// 2. Fetch and Map Main Menu from API
$main_menu = [];

try {
    $api = Laravel_API_Client::get_instance();
    // Fetch raw data (Cached)
    $api_response = $api->get_menus();

    // Check if we have the 'main_header' key in the response
    if ( ! is_wp_error($api_response) && ! empty($api_response['main_header']) ) {
        
        $raw_menu_items = $api_response['main_header'];

        // Map API structure to Theme structure
        foreach ($raw_menu_items as $item) {
            
            // Process Submenu (Children)
            $submenu_items = [];
            if (!empty($item['children']) && is_array($item['children'])) {
                foreach ($item['children'] as $child) {
                    $submenu_items[] = [
                        'title' => $child['name'],
                        // Ensure relative paths from API become absolute URLs in WP
                        'url'   => home_url($child['link_url']), 
                    ];
                }
            }

            // Build the main item
            $main_menu[] = [
                'title'       => $item['name'],
                'url'         => home_url($item['link_url']),
                'has_submenu' => !empty($submenu_items),
                'submenu'     => $submenu_items
            ];
        }

    } else {
        // Log warning if API is reachable but 'main_header' is missing
        if (!is_wp_error($api_response)) {
            error_log('Header Menu Warning: API response did not contain "main_header" key.');
        }
    }

} catch (Exception $e) {
    error_log('Header Menu Exception: ' . $e->getMessage());
}

// Fallback: Use static menu if API failed or returned empty result
if (empty($main_menu)) {
    $main_menu = [
        [
            'title' => 'خانه (عدم ارتباط با سرور)',
            'url' => home_url(),
            'has_submenu' => false,
            'submenu' => []
        ]
    ];
}

// 3. Function buttons
$action_buttons = [
    'service' => [
        'text'  => 'سرویس‌های آکامد',
        'class' => 'service-button'
    ],
    'login' => [
        'text'  => 'ورود',
        'class' => 'login-button'
    ]
];

// 4. Support information
$support_info = [
    'phone_label'         => '+',
    'phone_number'        => $site_data['phone'],
    'support_text'        => 'پشتیبانی',
    'support_icon'        => 'icon-support_icon',
    'search_icon'         => 'icon-search-aka',
    'close_icon'          => 'icon-Multiplication',
    'account_icon'        => 'icon-account',
    'shoping_bag_icon'    => 'icon-shoping_bag',
    'hamburger-menu_icon' => 'icon-hamburger-menu-aka',
];



// 1. Determine the URL based on login status
if (is_user_logged_in()) {
    // If user is logged in, send them to Dashboard
    $account_href = home_url('/userdashboard/');
} else {
    // If user is NOT logged in, send them to Login page
    $account_href = home_url('/login/');
}