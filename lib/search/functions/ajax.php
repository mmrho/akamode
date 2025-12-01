<?php
// functions/ajax.php

// هندل کردن جستجوی زنده (هدر)
add_action('wp_ajax_wbs_api_search', 'wbs_handle_ajax_search');
add_action('wp_ajax_nopriv_wbs_api_search', 'wbs_handle_ajax_search');

function wbs_handle_ajax_search() {
    $query = isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '';

    if (empty($query)) {
        wp_send_json_error(['message' => 'Query is empty']);
    }

    try {
        // چک کردن اینکه کلاس وجود دارد
        if (!class_exists('Laravel_API_Client')) {
            wp_send_json_error(['message' => 'API Client Class not found']);
        }

        $api = Laravel_API_Client::get_instance();
        $api_response = $api->search($query); 

        if (is_wp_error($api_response)) {
            wp_send_json_error(['message' => $api_response->get_error_message()]);
        }

        wp_send_json_success($api_response);

    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}

// هندل کردن فیلتر پیشرفته (صفحه جستجو)
add_action('wp_ajax_wbs_filter_search', 'wbs_handle_filter_search');
add_action('wp_ajax_nopriv_wbs_filter_search', 'wbs_handle_filter_search');

function wbs_handle_filter_search() {
    $params = [
        'q'         => isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '',
        'page'      => isset($_POST['page']) ? intval($_POST['page']) : 1,
        'sort'      => isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'newest',
        'min_price' => isset($_POST['min_price']) ? intval($_POST['min_price']) : null,
        'max_price' => isset($_POST['max_price']) ? intval($_POST['max_price']) : null,
        'colors'    => isset($_POST['colors']) && is_array($_POST['colors']) ? $_POST['colors'] : [],
        'sizes'     => isset($_POST['sizes']) && is_array($_POST['sizes']) ? $_POST['sizes'] : [],
        'categories'=> isset($_POST['categories']) && is_array($_POST['categories']) ? $_POST['categories'] : [],
    ];

    try {
        if (!class_exists('Laravel_API_Client')) {
            wp_send_json_error(['message' => 'API Client Class not found']);
        }

        $api = Laravel_API_Client::get_instance();
        $api_response = $api->search($params);

        if (is_wp_error($api_response)) {
            wp_send_json_error(['message' => $api_response->get_error_message()]);
        }

        wp_send_json_success($api_response);

    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}