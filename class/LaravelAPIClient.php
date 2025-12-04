<?php

/**
 * Class Laravel_API_Client
 * A professional, singleton service class to handle Laravel API integration.
 * Revised version to fix Unauthenticated issues.
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
    private $api_key = 'sibaneh-6H8XkhpJVyv5tq3qbrhr';

    /**
     * Constructor
     */
    private function __construct() {
        // تنظیم آدرس پایه API
        $this->base_url = defined('LARAVEL_API_URL') ? untrailingslashit(LARAVEL_API_URL) : 'https://api.akamode.com';
        $this->timeout  = 45; // افزایش تایم‌اوت برای اطمینان
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
     * پاکسازی توکن از فضاهای خالی احتمالی
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
    // ENDPOINTS
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
        // لاراول برای آپدیت گاهی اوقات نیاز به method spoofing دارد
        $data['_method'] = 'PUT'; 
        return $this->make_request("/api/v1/user/addresses/{$address_id}", 'POST', $data);
    }

    public function delete_address($address_id) {
        return $this->make_request("/api/v1/user/addresses/{$address_id}", 'DELETE');
    }

    /**
     * Check Discount
     * این متد نیاز به API Key در کوئری دارد
     */
    public function check_discount($code, $items) {
        $endpoint = '/api/v1/cart/check-discount';
        // افزودن کلید امنیتی به آدرس
        $endpoint = add_query_arg('api_key', $this->api_key, $endpoint);
        
        $body = [
            'code'  => $code,
            'items' => $items
        ];
        // ارسال به صورت JSON
        return $this->make_request($endpoint, 'POST', $body, 'json');
    }

    /**
     * Checkout
     * ارسال داده‌های کامل سفارش به صورت JSON
     */
    public function checkout($checkout_data) {
        // لاگ برای دیباگ (حتما چک کنید این داده‌ها درست باشند)
        error_log('Checkout Payload: ' . print_r($checkout_data, true));
        
        return $this->make_request('/api/v1/checkout', 'POST', $checkout_data, 'json');
    }

    // ... (سایر متدها مثل محصولات و دسته‌بندی که نیاز به لاگین ندارند اینجا هستند و تغییری نکردند) ...
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
    public function get_menus() {
        return $this->request_with_cache('/api/v1/menus', [], 12 * HOUR_IN_SECONDS);
    }


    // =========================================================================
    // CORE REQUEST HANDLER (اصلاح شده و دقیق)
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

    /**
     * تابع اصلی ارسال درخواست به لاراول
     */
    private function make_request($endpoint, $method = 'GET', $params = [], $body_type = 'form') {
        $url = $this->base_url . $endpoint;
        
        // تنظیمات هدر پیش‌فرض
        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json', // پیش‌فرض JSON
        ];

        // 1. تنظیم توکن احراز هویت (مهمترین بخش برای خطای Unauthenticated)
        if ($this->access_token) {
            $headers['Authorization'] = 'Bearer ' . $this->access_token;
        }

        // 2. آماده‌سازی بدنه درخواست
        $body = null;
        
        if ($method === 'GET') {
            // برای GET پارامترها به URL اضافه می‌شوند
            if (!empty($params)) {
                $url = add_query_arg($params, $url);
            }
            unset($headers['Content-Type']); // GET بادی ندارد
        } else {
            // برای POST, PUT, DELETE
            if (!empty($params)) {
                if ($body_type === 'json') {
                    // انکود کردن دقیق JSON با پشتیبانی از فارسی
                    $body = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    // اگر فرم دیتا معمولی باشد (مثل آپلود فایل)
                    $headers['Content-Type'] = 'application/x-www-form-urlencoded'; // یا حذف شود تا WP هندل کند
                    $body = $params;
                }
            }
        }

        // 3. تنظیمات آرگومان‌های تابع وردپرس
        $args = [
            'method'    => $method,
            'timeout'   => $this->timeout,
            'sslverify' => false, // در محیط پروداکشن واقعی بهتر است true باشد
            'headers'   => $headers,
            'body'      => $body
        ];

        // *** DEBUG LOG: ثبت درخواست ارسالی در debug.log ***
        // error_log("API Request URL: " . $url);
        // if(isset($headers['Authorization'])) error_log("API Token Present: Yes");
        // else error_log("API Token Present: NO (Warning)");
        // if($body) error_log("API Body: " . (is_string($body) ? $body : json_encode($body)));

        // 4. ارسال درخواست
        $response = wp_remote_request($url, $args);

        // 5. بررسی خطای شبکه وردپرس
        if (is_wp_error($response)) {
            error_log('WP API Network Error: ' . $response->get_error_message());
            return $response;
        }

        // 6. دریافت کد وضعیت و بدنه پاسخ
        $code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        // *** DEBUG LOG: ثبت پاسخ دریافتی ***
         error_log("API Response Code: " . $code);
         error_log("API Response Body: " . substr($response_body, 0, 500) . '...'); // خلاصه پاسخ

        // 7. مدیریت خطاها (4xx و 5xx)
        if ($code >= 400) {
            $error_message = isset($data['message']) ? $data['message'] : "API Error {$code}";
            
            // اگر خطای 401 باشد، یعنی توکن واقعا کار نمی‌کند
            if ($code == 401) {
                error_log("API Authentication Failed (401). Check Token validity.");
            }

            return new WP_Error('api_error', $error_message, [
                'status' => $code,
                'data'   => $data,
                'full_response' => $data
            ]);
        }

        // 8. بررسی صحت JSON دریافتی
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("API JSON Decode Error: " . json_last_error_msg());
            return new WP_Error('json_error', 'Invalid JSON from server');
        }

        return $data;
    }
}
?>