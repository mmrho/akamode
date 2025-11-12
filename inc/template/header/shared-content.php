<?php
// Shared site information
$site_data = [
    'name' => get_bloginfo('name') ?: 'آکامد',
    'url' => esc_url(home_url('/')),
    'logo' => get_template_directory_uri() . '/images/svg/akamode-logo.svg',
    'phone' => '۹۸۴۱۳۵۵۲۱۰۹۸',
    'search_placeholder' => 'دنبال چی میگردی؟'
];

// Main menus
$main_menu = [
   /* [
        'title' => 'خانه',
        'url' => '#',
        'has_submenu' => false
    ],*/
    [
        'title' => 'پوشاک زنان',
        'url' => '#',
        'has_submenu' => true,
        'submenu' => [
            ['title' => 'آیتم زیرمنو 1', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 2', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 3', 'url' => '#']
        ]
    ],
    [
        'title' => 'پوشاک مردان',
        'url' => '#',
        'has_submenu' => true,
        'submenu' => [
            ['title' => 'آیتم زیرمنو 1', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 2', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 3', 'url' => '#']
        ]
    ],
    [
        'title' => 'پوشاک کودکان',
        'url' => '#',
        'has_submenu' => true,
        'submenu' => [
            ['title' => 'آیتم زیرمنو 1', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 2', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 3', 'url' => '#']
        ]
    ],
    [
        'title' => 'پوشاک کودکان',
        'url' => '#',
        'has_submenu' => true,
        'submenu' => [
            ['title' => 'آیتم زیرمنو 1', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 2', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 3', 'url' => '#']
        ]
    ],
    [
        'title' => 'اکسسوری',
        'url' => '#',
        'has_submenu' => true,
        'submenu' => [
            ['title' => 'آیتم زیرمنو 1', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 2', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 3', 'url' => '#']
        ]
    ],
    [
        'title' => 'کفش',
        'url' => '#',
        'has_submenu' => true,
        'submenu' => [
            ['title' => 'آیتم زیرمنو 1', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 2', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 3', 'url' => '#']
        ]
    ],
    [
        'title' => 'کیف',
        'url' => '#',
        'has_submenu' => true,
        'submenu' => [
            ['title' => 'آیتم زیرمنو 1', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 2', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 3', 'url' => '#']
        ]
    ],
    [
        'title' => 'جدیدترین‌های فصل',
        'url' => '#',
        'has_submenu' => true,
        'submenu' => [
            ['title' => 'آیتم زیرمنو 1', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 2', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 3', 'url' => '#']
        ]
    ],
    [
        'title' => 'مناسب هدیه',
        'url' => '#',
        'has_submenu' => true,
        'submenu' => [
            ['title' => 'آیتم زیرمنو 1', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 2', 'url' => '#'],
            ['title' => 'آیتم زیرمنو 3', 'url' => '#']
        ]
    ]
];

// Function buttons
$action_buttons = [
    'service' => [
        'text' => 'سرویس‌های آکامد',
        'class' => 'service-button'
    ],
    'login' => [
        'text' => 'ورود',
        'class' => 'login-button'
    ]
];

// Support information
$support_info = [
    'phone_label' => '+',
    'phone_number' => $site_data['phone'],
    'support_text' => 'پشتیبانی',
    'support_icon' => 'icon-support_icon',
    'search_icon' => 'icon-search-aka',
    'account_icon' => 'icon-account',
    'shoping_bag_icon' => 'icon-shoping_bag',
    'hamburger-menu_icon' => 'icon-hamburger-menu-aka',
];
?>