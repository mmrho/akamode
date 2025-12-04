<?php
// فایل: AKAMODE/lib/checkout/template/checkoutPage.php

if (!is_user_logged_in()) {
    $target_url = home_url('/login') . '?redirect_to=' . urlencode(home_url('/checkout'));
    if (!headers_sent()) {
        wp_redirect($target_url);
        exit;
    } else {
        echo '<script>window.location.href = "' . $target_url . '";</script>';
        exit;
    }
}

// دریافت اطلاعات کاربر و آدرس‌ها
$user_info = [];
$user_addresses = []; 

if (class_exists('Laravel_API_Client')) {
    $api = Laravel_API_Client::get_instance();
    $user_id = get_current_user_id();
    $token = get_user_meta($user_id, '_laravel_api_token', true);

    if ($token) {
        $api->set_token($token);
        
        $info_response = $api->get_user_info();
        if (!is_wp_error($info_response) && isset($info_response['data'])) {
            $user_info = $info_response['data'];
        }

        $address_response = $api->get_addresses();
        if (!is_wp_error($address_response) && isset($address_response['data'])) {
            $user_addresses = $address_response['data'];
        } elseif (!is_wp_error($address_response) && is_array($address_response)) {
            $user_addresses = $address_response;
        }
    }
}

$pre_name = isset($user_info['name']) ? $user_info['name'] : '';
$pre_email = isset($user_info['email']) ? $user_info['email'] : '';
$pre_phone = isset($user_info['mobile']) ? $user_info['mobile'] : '';
?>

<div class="container">
    <div class="main">
        <div class="top">
            <h1>تسویه حساب</h1>
            <p>تکمیل اطلاعات و پرداخت نهایی</p>
        </div>

        <div class="checkout-container">
            <div class="right">
                
                <div class="ship-info">
                    <div>
                        <p class="title">اطلاعات ارسال</p>
                        <p class="text">لطفا آدرس و مشخصات گیرنده را بررسی کنید.</p>
                        <?php if(is_array($user_addresses) && count($user_addresses) > 1): ?>
                            <button type="button" id="btn-change-address" style="font-size:12px; padding:5px 10px; background:#eee; border:none; cursor:pointer; margin-top:5px;">تغییر آدرس منتخب</button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form" id="checkout-form">
                        <div>
                            <div class="select input">
                                <input type="text" id="billing_name" placeholder="نام و نام خانوادگی *" value="<?php echo esc_attr($pre_name); ?>">
                            </div>
                            <div class="select input">
                                <input type="email" id="billing_email" placeholder="ایمیل (اختیاری)" value="<?php echo esc_attr($pre_email); ?>">
                            </div>
                        </div>
                        <div>
                            <div class="select input">
                                <input type="text" id="billing_phone" placeholder="‌شماره تماس (موبایل) *" value="<?php echo esc_attr($pre_phone); ?>">
                            </div>
                            <div class="select input">
                                <input type="text" id="billing_postcode" placeholder="کد پستی *">
                            </div>
                        </div>
                        <div>
                            <div class="select">
                                <select id="billing_state">
                                    <option value="">استان *</option>
                                    <option value="Tehran">تهران</option>
                                    <option value="East Azerbaijan">آذربایجان شرقی</option>
                                    <option value="Isfahan">اصفهان</option>
                                    <option value="Fars">فارس</option>
                                    <option value="Razavi Khorasan">خراسان رضوی</option>
                                    <option value="Khuzestan">خوزستان</option>
                                    <option value="Mazandaran">مازندران</option>
                                </select>
                            </div>
                            <div class="select">
                                <select id="billing_city">
                                    <option value="">شهر *</option>
                                    <option value="Tehran">تهران</option>
                                    <option value="Tabriz">تبریز</option>
                                    <option value="Isfahan">اصفهان</option>
                                    <option value="Shiraz">شیراز</option>
                                    <option value="Mashhad">مشهد</option>
                                    <option value="Ahvaz">اهواز</option>
                                    <option value="Sari">ساری</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="select input">
                                <input type="text" id="billing_address" placeholder="آدرس کامل پستی *">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ship-method">
                    <p class="title">روش ارسال</p>
                    <div class="methods" id="shipping-methods">
                        <div class="method-row" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                            <label style="cursor:pointer; display:flex; align-items:center; gap:10px;">
                                <input type="radio" name="ship-method" value="pishaz" data-cost="35000" checked>
                                <span>پست پیشتاز</span>
                            </label>
                            <div class="price">۳۵,۰۰۰ تومان</div>
                        </div>
                        <div class="hr" style="background:#eee; height:1px; margin-bottom:15px;"></div>
                        <div class="method-row" style="display:flex; justify-content:space-between; align-items:center;">
                            <label style="cursor:pointer; display:flex; align-items:center; gap:10px;">
                                <input type="radio" name="ship-method" value="tipax" data-cost="0">
                                <span>تیپاکس (پس‌کرایه)</span>
                            </label>
                            <div class="price">پس‌کرایه</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="left">
                <p>خلاصه صورت‌حساب</p>
                <div class="financial-container">
                    <div id="checkout-items-wrapper" style="margin-bottom:20px; max-height:200px; overflow-y:auto; border-bottom:1px solid #eee; padding-bottom:10px;"></div>

                    <div class="body">
                        <table style="width:100%; margin-bottom:10px;">
                            <tbody>
                                <tr>
                                    <td style="color:#666;">جمع کل اقلام</td>
                                    <td style="text-align:left;"><span class="total-cart-price">0</span> تومان</td>
                                </tr>
                                <tr>
                                    <td style="color:#666;">هزینه ارسال</td>
                                    <td style="text-align:left;"><span id="shipping-cost-display">35,000</span> تومان</td>
                                </tr>
                                <tr id="discount-row" style="display:none; color: green;">
                                    <td>تخفیف اعمال شده</td>
                                    <td style="text-align:left;">- <span id="discount-amount-display">0</span> تومان</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="hr" style="background:#111; height:1px; margin-bottom:15px;"></div>

                    <div class="bottom">
                        <table style="width:100%; margin-bottom:20px;">
                            <tbody>
                                <tr>
                                    <td style="font-weight:bold;">مبلغ قابل پرداخت</td>
                                    <td style="text-align:left; font-weight:bold; font-size:18px;"><span id="final-payable-price">0</span> تومان</td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="submit-purchase" id="btn-place-order" style="width:100%; cursor:pointer;">ثبت سفارش و پرداخت</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var wbs_checkout_params = {
        user_addresses: <?php echo json_encode(is_array($user_addresses) ? $user_addresses : []); ?>
    };
</script>