<?php
if (!is_user_logged_in()) {
    $target_url = home_url('/login') . '?redirect_to=' . urlencode(home_url('/checkout'));
    if (!headers_sent()) wp_redirect($target_url);
    else echo '<script>window.location.href = "' . $target_url . '";</script>';
    exit;
}

// ==========================================
// 1. تنظیمات روش‌های ارسال (Shipping Config)
// ==========================================
// برای تغییر قیمت یا اضافه کردن روش جدید، این آرایه را ویرایش کنید
$shipping_methods_config = [
    'pishaz' => [
        'title' => 'پست پیشتاز',
        'cost'  => 35000,
        'desc'  => 'ارسال سریع به سراسر کشور'
    ],
    'tipax' => [
        'title' => 'تیپاکس',
        'cost'  => 60000, // هزینه صفر یعنی پس کرایه یا رایگان
        'desc'  => 'ارسال سریع به سراسر کشور'
    ],
    // مثال برای اضافه کردن پیک موتوری:
    // 'bike' => [
    //     'title' => 'پیک موتوری (فقط تهران)',
    //     'cost'  => 50000,
    //     'desc'  => 'تحویل فوری'
    // ],
];

// ==========================================
// 2. تنظیمات روش‌های پرداخت (Payment Config)
// ==========================================
$payment_methods_config = [
    'online' => [
        'title' => 'پرداخت آنلاین',
        'icon'  => 'fa-credit-card'
    ],
    'cod' => [
        'title' => 'پرداخت در محل',
        'icon'  => 'fa-home'
    ],
    'card' => [
        'title' => 'کارت به کارت',
        'icon'  => 'fa-exchange'
    ]
];

// دریافت اطلاعات کاربر از API
$user_info = [];
$user_addresses = []; 

if (class_exists('Laravel_API_Client')) {
    $api = Laravel_API_Client::get_instance();
    $user_id = get_current_user_id();
    $token = get_user_meta($user_id, '_laravel_api_token', true);

    if ($token) {
        $api->set_token($token);
        
        $info_r = $api->get_user_info();
        if (!is_wp_error($info_r) && isset($info_r['data'])) $user_info = $info_r['data'];
        elseif (!is_wp_error($info_r)) $user_info = $info_r;

        $addr_r = $api->get_addresses();
        if (!is_wp_error($addr_r) && isset($addr_r['data'])) $user_addresses = $addr_r['data']; 
        elseif (!is_wp_error($addr_r)) $user_addresses = $addr_r; 
    }
}

$pre_name = isset($user_info['name']) ? $user_info['name'] : '';
$pre_email = isset($user_info['email']) ? $user_info['email'] : '';
$pre_phone = isset($user_info['mobile']) ? $user_info['mobile'] : '';
?>

<style>
    .akamode-popup-container { font-family: inherit !important; }
    .akamode-popup-title { font-size: 18px !important; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    .akamode-popup-confirm { background-color: #000 !important; color: #fff !important; border-radius: 4px !important; }
    .swal2-radio { display: grid !important; gap: 8px !important; justify-content: flex-start !important; text-align: right !important; }
    .swal2-radio label { display: flex; align-items: center; gap: 10px; width: 100%; cursor: pointer; }
    
    /* استایل‌های اختصاصی برای بخش‌های جدید */
    .method-row {
        border: 1px solid #eee;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .method-row:hover { border-color: #ccc; }
    .method-row label { cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: space-between; margin: 0; }
    .method-row input[type="radio"] { margin-left: 10px; }
    .ship-method .title, .pay-method .title { font-weight: bold; margin-bottom: 15px; margin-top: 20px; font-size: 16px; }
    .checkout-container .right { gap: 0; }
</style>

<div class="container">
    <div class="main">
        <div class="top">
            <h1>تسویه حساب</h1>
            <p>تکمیل اطلاعات و پرداخت نهایی</p>
        </div>

        <div class="checkout-container">
            <div class="right">
                <div class="ship-info">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                        <div>
                            <p class="title" style="margin:0;">اطلاعات ارسال</p>
                            <p class="text" style="margin:5px 0 0 0; font-size:12px; color:#777;">لطفا آدرس و مشخصات گیرنده را وارد کنید.</p>
                        </div>
                        <?php if(is_array($user_addresses) && count($user_addresses) > 0): ?>
                            <button type="button" id="btn-change-address" class="hew-button secondary" style="font-size:12px; padding:5px 15px;">
                                <i class="fa fa-map-marker"></i> انتخاب از آدرس‌ها
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form" id="checkout-form">
                        <div>
                            <div class="select input"><input type="text" id="billing_name" placeholder="نام و نام خانوادگی *" value="<?php echo esc_attr($pre_name); ?>"></div>
                            <div class="select input"><input type="email" id="billing_email" placeholder="ایمیل (اختیاری)" value="<?php echo esc_attr($pre_email); ?>"></div>
                        </div>
                        <div>
                            <div class="select input"><input type="text" id="billing_phone" placeholder="‌شماره تماس *" value="<?php echo esc_attr($pre_phone); ?>"></div>
                            <div class="select input"><input type="text" id="billing_postcode" placeholder="کد پستی *"></div>
                        </div>
                        <div>
                            <div class="select">
                                <select id="billing_state">
                                    <option value="">استان *</option>
                                </select>
                            </div>
                            <div class="select">
                                <select id="billing_city">
                                    <option value="">شهر *</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="select input"><input type="text" id="billing_address" placeholder="آدرس کامل پستی *"></div>
                        </div>
                    </div>
                </div>

                <div class="ship-method">
                    <p class="title">روش ارسال</p>
                    <div class="methods">
                        <?php 
                        $first_ship = true;
                        foreach($shipping_methods_config as $key => $method): 
                            $cost_display = ($method['cost'] > 0) ? number_format($method['cost']) . ' تومان' : 'پس‌کرایه';
                        ?>
                            <div class="method-row">
                                <label>
                                    <div style="display:flex; align-items:center;">
                                        <input type="radio" name="ship-method" value="<?php echo esc_attr($key); ?>" data-cost="<?php echo esc_attr($method['cost']); ?>" <?php echo $first_ship ? 'checked' : ''; ?>>
                                        <span style="display:flex; flex-direction:column; margin-right:8px;">
                                            <span style="font-weight:500;"><?php echo esc_html($method['title']); ?></span>
                                            <?php if(!empty($method['desc'])): ?>
                                                <small style="color:#888; font-size:11px;"><?php echo esc_html($method['desc']); ?></small>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="price"><?php echo $cost_display; ?></div>
                                </label>
                            </div>
                        <?php 
                            $first_ship = false;
                        endforeach; 
                        ?>
                    </div>
                </div>

                <div class="pay-method">
                    <p class="title">روش پرداخت</p>
                    <div class="methods">
                        <?php 
                        $first_pay = true;
                        foreach($payment_methods_config as $key => $method): 
                        ?>
                            <div class="method-row">
                                <label>
                                    <div style="display:flex; align-items:center;">
                                        <input type="radio" name="payment-method" value="<?php echo esc_attr($key); ?>" <?php echo $first_pay ? 'checked' : ''; ?>>
                                        <span style="margin-right:8px; font-weight:500;">
                                            <?php if(isset($method['icon'])): ?><i class="fa <?php echo $method['icon']; ?>" style="margin-left:5px; color:#888;"></i><?php endif; ?>
                                            <?php echo esc_html($method['title']); ?>
                                        </span>
                                    </div>
                                </label>
                            </div>
                        <?php 
                            $first_pay = false;
                        endforeach; 
                        ?>
                    </div>
                </div>

            </div>

            <div class="left">
                <p>خلاصه صورت‌حساب</p>
                <div class="financial-container">
                    <div id="checkout-items-wrapper"></div>
                    <div class="body">
                        <table>
                            <tr><td>جمع کل</td><td class="total-cart-price">0 تومان</td></tr>
                            <tr><td>هزینه ارسال</td><td id="shipping-cost-display">--</td></tr>
                            <tr id="discount-row" style="display:none; color:green;"><td>تخفیف</td><td>- <span id="discount-amount-display">0</span> تومان</td></tr>
                        </table>
                    </div>
                    <div class="bottom">
                        <table><tr><td>مبلغ قابل پرداخت</td><td id="final-payable-price">0 تومان</td></tr></table>
                        <button id="btn-place-order" class="submit-purchase" style="width:100%;">ثبت سفارش و پرداخت</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var wbs_checkout_data = {
        addresses: <?php echo json_encode(is_array($user_addresses) ? $user_addresses : []); ?>,
        ajax_url: "<?php echo admin_url('admin-ajax.php'); ?>"
    };
</script>