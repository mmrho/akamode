<?php

/**
 * Class Laravel_API_Client
 * A professional, singleton service class to handle Laravel API integration.
 * Fixed: Added missing get_orders methods + Auth fixes.
 */
class Laravel_API_Client {

    /**
     * API Base URL
     */
    private $base_url;

    /**
     * Request Timeout
     */
    private $timeout;

    /**
     * Singleton Instance
     */
    private static $instance = null;

    /**
     * Bearer Token
     */
    private $access_token = null;

    /**
     * API Key (For specific endpoints like discount)
     */
    private $api_key = '-6H8XkhpJVyv5tq3qbrhr';

    /**
     * Constructor
     */
    private function __construct() {
        // آدرس به صورت ثابت برای جلوگیری از مشکلات کانفیگ
        $this->base_url = 'https://api.akamode.com';
        $this->timeout  = 45; 
    }

    /**
     * Get Singleton Instance
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set Authentication Token
     */
    public function set_token($token) {
        if (!empty($token) && is_string($token)) {
            $this->access_token = trim($token);
        }
        return $this;
    }

    public function get_token() {
        return $this->access_token;
    }

    /**
     * Clear Caches
     */
    public function flush_api_cache() {
        global $wpdb;
        $wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_api_cache_%')");
        $wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_timeout_api_cache_%')");
    }

    // =========================================================================
    // 1. AUTHENTICATION
    // =========================================================================

    public function send_otp($mobile) {
        return $this->make_request('/api/v1/login/send-otp', 'POST', ['mobile' => $mobile]);
    }

    public function verify_otp($mobile, $code) {
        return $this->make_request('/api/v1/login/verify-otp', 'POST', [
            'mobile' => $mobile,
            'code'   => $code
        ]);
    }

    public function logout() {
        return $this->make_request('/api/v1/logout', 'POST');
    }

    // =========================================================================
    // 2. USER DASHBOARD (Profile, Address, Orders)
    // =========================================================================

    public function get_user_info() {
        return $this->make_request('/api/v1/user', 'GET');
    }

    public function update_profile($name, $email) {
        return $this->make_request('/api/v1/user/profile', 'POST', [
            'name'  => $name,
            'email' => $email
        ]);
    }

    public function get_addresses() {
        return $this->make_request('/api/v1/user/addresses', 'GET');
    }

    public function add_address($data) {
        return $this->make_request('/api/v1/user/addresses', 'POST', $data);
    }

    public function update_address($address_id, $data) {
        $data['_method'] = 'PUT'; 
        return $this->make_request("/api/v1/user/addresses/{$address_id}", 'POST', $data);
    }

    public function delete_address($address_id) {
        return $this->make_request("/api/v1/user/addresses/{$address_id}", 'DELETE');
    }

    /**
     * دریافت لیست سفارشات (اضافه شد)
     */
    public function get_orders($page = 1) {
        return $this->make_request('/api/v1/user/orders', 'GET', ['page' => $page]);
    }

    /**
     * دریافت جزئیات یک سفارش (اضافه شد)
     */
    public function get_order_single($order_id) {
        return $this->make_request("/api/v1/user/orders/{$order_id}", 'GET');
    }

    // =========================================================================
    // 3. SHOP & CHECKOUT
    // =========================================================================

    public function check_discount($code, $items) {
        $endpoint = '/api/v1/cart/check-discount';
        $endpoint = add_query_arg('api_key', $this->api_key, $endpoint);
        
        $body = [
            'code'  => $code,
            'items' => $items
        ];
        return $this->make_request($endpoint, 'POST', $body, 'json');
    }

    /**

     * Checkout

     * پارامتر دوم ($direct_token) برای اطمینان از دریافت توکن از ajax.php است.

     */

     public function checkout($checkout_data, $direct_token = null)

     {
         if (!empty($direct_token)) {
 
             $this->set_token($direct_token);
         }

         if (empty($this->access_token)) {
 
             error_log("CRITICAL: Attempting checkout without token!");
         }
 
         return $this->make_request('/api/v1/checkout', 'POST', $checkout_data, 'json');
     }
 
 

    public function submit_review($product_id, $rating, $comment) {
        return $this->make_request("/api/v1/products/{$product_id}/reviews", 'POST', [
            'rating'  => $rating,
            'comment' => $comment
        ]);
    }

    // =========================================================================
    // 4. PUBLIC DATA (Products, Blog, Categories)
    // =========================================================================

    public function get_products($page = 1) {
        return $this->request_with_cache('/api/v1/products', ['page' => $page], HOUR_IN_SECONDS);
    }

    public function get_product_single($slug) {
        return $this->request_with_cache("/api/v1/products/{$slug}", [], 30 * MINUTE_IN_SECONDS);
    }

    public function search($params) {
        if (is_string($params)) $params = ['q' => $params];
        return $this->request_with_cache('/api/v1/search', $params, 30 * MINUTE_IN_SECONDS);
    }

    public function get_categories() {
        return $this->request_with_cache('/api/v1/categories', [], 6 * HOUR_IN_SECONDS);
    }

    public function get_category_single($slug) {
        return $this->request_with_cache("/api/v1/categories/{$slug}", [], 6 * HOUR_IN_SECONDS);
    }

    public function get_menus() {
        return $this->request_with_cache('/api/v1/menus', [], 12 * HOUR_IN_SECONDS);
    }

    public function get_blog_posts() {
        return $this->request_with_cache('/api/v1/blog/posts', [], 2 * HOUR_IN_SECONDS);
    }

    public function get_blog_single($slug) {
        return $this->request_with_cache("/api/v1/blog/posts/{$slug}", [], 2 * HOUR_IN_SECONDS);
    }


    // =========================================================================
    // CORE FUNCTIONS
    // =========================================================================

    private function request_with_cache($endpoint, $params = [], $seconds = 3600) {
        $cache_key = 'api_cache_' . md5($endpoint . json_encode($params));
        $cached = get_transient($cache_key);
        if ($cached !== false) return $cached;

        $response = $this->make_request($endpoint, 'GET', $params);
        
        if (!is_wp_error($response) && !empty($response)) {
            set_transient($cache_key, $response, $seconds);
        }
        return $response;
    }

    private function make_request($endpoint, $method = 'GET', $params = [], $body_type = 'form') {
        $url = $this->base_url . $endpoint;
        
        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];

        // 1. تنظیم هدر احراز هویت
        if ($this->access_token) {
            $headers['Authorization'] = 'Bearer ' . $this->access_token;
        }

        $body = null;
        
        if ($method === 'GET') {
            if (!empty($params)) {
                $url = add_query_arg($params, $url);
            }
            unset($headers['Content-Type']); 
        } else {
            if (!empty($params)) {
                if ($body_type === 'json') {
                    // انکودینگ UTF8 برای پشتیبانی از فارسی
                    $body = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    $headers['Content-Type'] = 'application/x-www-form-urlencoded';
                    $body = $params;
                }
            }
        }

        $args = [
            'method'    => $method,
            'timeout'   => $this->timeout,
            'sslverify' => false,
            'headers'   => $headers,
            'body'      => $body
        ];

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            error_log('Laravel API Network Error: ' . $response->get_error_message());
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $body_response = wp_remote_retrieve_body($response);
        $data = json_decode($body_response, true);

        if ($code >= 400) {
            $msg = isset($data['message']) ? $data['message'] : "API Error $code";
            
            if ($code == 401) {
                error_log("Laravel API Auth Failed (401) for URL: $url");
            }

            return new WP_Error('api_error', $msg, ['status' => $code, 'data' => $data]);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Laravel API JSON Error: " . json_last_error_msg());
            return new WP_Error('json_error', 'Invalid JSON response');
        }

        return $data;
    }
}
?>