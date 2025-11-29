<?php

/**
 * Class Laravel_API_Client
 * A professional, singleton service class to handle Laravel API integration in WordPress.
 * Features: Authentication, Caching, Error Logging, and Type-Safe requests.
 */
class Laravel_API_Client {

    /**
     * API Base URL (e.g., http://localhost:8000 or https://akamode.com)
     * @var string
     */
    private $base_url;

    /**
     * Request Timeout in seconds
     * @var int
     */
    private $timeout;

    /**
     * Singleton Instance
     * @var Laravel_API_Client|null
     */
    private static $instance = null;

    /**
     * Bearer Token for authenticated requests
     * @var string|null
     */
    private $access_token = null;

    /**
     * Constructor
     */
    private function __construct() {
        // Use a constant if defined in wp-config.php, otherwise fallback to localhost
        // Defined example: define('LARAVEL_API_URL', 'https://api.mysite.com');
        $this->base_url = defined('LARAVEL_API_URL') ? untrailingslashit(LARAVEL_API_URL) : 'https://akamode.com';
        $this->timeout  = 20;
    }

    /**
     * Get Singleton Instance
     * @return Laravel_API_Client
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set Authentication Token
     * @param string $token
     * @return $this
     */
    public function set_token($token) {
        $this->access_token = trim($token);
        return $this;
    }

    /**
     * Clear all API caches manually (Useful for admin actions)
     */
    public function flush_api_cache() {
        global $wpdb;
        $wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_api_cache_%')");
        $wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_timeout_api_cache_%')");
    }

    // =========================================================================
    // 1. AUTHENTICATION (No Cache)
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
    // 2. PUBLIC DATA (Cached)
    // =========================================================================

    public function get_menus() {
        return $this->request_with_cache('/api/v1/menus', [], 12 * HOUR_IN_SECONDS);
    }

    public function get_categories() {
        return $this->request_with_cache('/api/v1/categories', [], 6 * HOUR_IN_SECONDS);
    }

    public function get_category_single($slug) {
        return $this->request_with_cache("/api/v1/categories/{$slug}", [], 6 * HOUR_IN_SECONDS);
    }

    public function get_products($page = 1) {
        return $this->request_with_cache('/api/v1/products', ['page' => $page], HOUR_IN_SECONDS);
    }

    public function get_product_single($slug) {
        return $this->request_with_cache("/api/v1/products/{$slug}", [], HOUR_IN_SECONDS);
    }

    public function get_blog_posts() {
        return $this->request_with_cache('/api/v1/blog/posts', [], 2 * HOUR_IN_SECONDS);
    }

    public function get_blog_single($slug) {
        return $this->request_with_cache("/api/v1/blog/posts/{$slug}", [], 2 * HOUR_IN_SECONDS);
    }

    /**
     * Search should NOT be cached usually, or cached for very short time.
     */
    public function search($query) {
        return $this->make_request('/api/v1/search', 'GET', ['q' => $query]);
    }

    // =========================================================================
    // 3. USER PRIVATE DATA (Protected, No Cache)
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

    public function get_orders() {
        return $this->make_request('/api/v1/user/orders', 'GET');
    }

    public function get_order_single($order_id) {
        return $this->make_request("/api/v1/user/orders/{$order_id}", 'GET');
    }

    public function get_addresses() {
        return $this->make_request('/api/v1/user/addresses', 'GET');
    }

    public function add_address($data) {
        return $this->make_request('/api/v1/user/addresses', 'POST', $data);
    }

    public function update_address($address_id, $data) {
        // Laravel convention for PUT via FormData
        $data['_method'] = 'PUT'; 
        return $this->make_request("/api/v1/user/addresses/{$address_id}", 'POST', $data);
    }

    public function delete_address($address_id) {
        return $this->make_request("/api/v1/user/addresses/{$address_id}", 'DELETE');
    }

    public function submit_review($product_id, $rating, $comment) {
        return $this->make_request("/api/v1/products/{$product_id}/reviews", 'POST', [
            'rating'  => $rating,
            'comment' => $comment
        ]);
    }

    /**
     * Checkout sends Raw JSON body.
     */
    public function checkout($checkout_data) {
        return $this->make_request('/api/v1/checkout', 'POST', $checkout_data, 'json');
    }

    // =========================================================================
    // CORE FUNCTIONS (Logic & Caching)
    // =========================================================================

    /**
     * Wrapper to handle caching for GET requests
     */
    private function request_with_cache($endpoint, $params = [], $seconds = 3600) {
        // Create unique cache key
        $cache_key = 'api_cache_' . md5($endpoint . json_encode($params));

        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }

        $response = $this->make_request($endpoint, 'GET', $params);

        // Only cache if successful and not empty
        if (!is_wp_error($response) && !empty($response)) {
            set_transient($cache_key, $response, $seconds);
        }

        return $response;
    }

    /**
     * Main HTTP Request Handler
     * * @param string $endpoint
     * @param string $method (GET, POST, DELETE, PUT)
     * @param array $params Data to send
     * @param string $body_type 'form' for FormData, 'json' for Raw JSON
     * @return array|WP_Error
     */
    private function make_request($endpoint, $method = 'GET', $params = [], $body_type = 'form') {
        $url = $this->base_url . $endpoint;
        
        $args = [
            'timeout'   => $this->timeout,
            'method'    => $method,
            'sslverify' => false, // Set to true for Production
            'headers'   => [
                'Accept' => 'application/json',
            ]
        ];

        // Add Auth Token if exists
        if ($this->access_token) {
            $args['headers']['Authorization'] = 'Bearer ' . $this->access_token;
        }

        // Prepare Data
        if ($method === 'GET') {
            if (!empty($params)) {
                $url = add_query_arg($params, $url);
            }
        } else {
            // POST/PUT/DELETE
            if (!empty($params)) {
                if ($body_type === 'json') {
                    $args['headers']['Content-Type'] = 'application/json';
                    $args['body'] = json_encode($params);
                } else {
                    $args['body'] = $params; // WP handles Multipart/Form-data
                }
            }
        }

        // Execute Request
        $response = wp_remote_request($url, $args);

        // Network/WP Errors
        if (is_wp_error($response)) {
            error_log('Laravel API Network Error: ' . $response->get_error_message());
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // API Logic Errors (4xx, 5xx)
        if ($code >= 400) {
            $error_msg = isset($data['message']) ? $data['message'] : "API Error $code";
            error_log("Laravel API Error [$code] at $endpoint: $error_msg");
            
            // Return WP_Error so frontend can handle it gracefully
            return new WP_Error('api_error', $error_msg, [
                'status' => $code, 
                'data' => $data
            ]);
        }

        // JSON Decode Error
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Laravel API JSON Error at $endpoint: " . json_last_error_msg());
            return new WP_Error('json_error', 'Invalid JSON response from server');
        }

        return $data;
    }
}
?>