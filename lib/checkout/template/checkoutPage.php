<?php
// فایل: template/checkoutPage.php

// 1. محافظت قدرتمند از صفحه (PHP + JS Fallback)
if (!is_user_logged_in()) {
    
    // آدرس صفحه لاگین + آدرس بازگشت به چک‌اوت
    $target_url = home_url('/login') . '?redirect_to=' . urlencode(home_url('/checkout'));
    
    // اگر هنوز هیچ خروجی HTML ارسال نشده، با PHP ریدایرکت کن (روش استاندارد)
    if (!headers_sent()) {
        wp_redirect($target_url);
        exit;
    } else {
        // اگر هدر ارسال شده بود، با جاوااسکریپت ریدایرکت کن (روش تضمینی)
        echo '<script>window.location.href = "' . $target_url . '";</script>';
        // توقف اجرای بقیه صفحه
        exit('لطفا وارد شوید...');
    }
}

// 2. دریافت اطلاعات کاربر از API (برای پر کردن فرم)
$user_info = [];
$user_address = [];
$prefill_name = ''; $prefill_email = ''; $prefill_phone = ''; 
$prefill_address = ''; $prefill_zip = ''; $prefill_city = ''; $prefill_state = '';

// فقط اگر کلاس API وجود داشت تلاش کن
if (class_exists('Laravel_API_Client')) {
    $api = Laravel_API_Client::get_instance();
    
    // دریافت اطلاعات اکانت
    $info_response = $api->get_user_info();
    if (!is_wp_error($info_response) && isset($info_response['data'])) {
        $user_info = $info_response['data'];
    }

    // دریافت آدرس‌ها
    $address_response = $api->get_addresses();
    if (!is_wp_error($address_response) && isset($address_response['data']) && count($address_response['data']) > 0) {
        $user_address = $address_response['data'][0]; // اولین آدرس
    }
}

// جایگذاری متغیرها (با چک کردن وجود کلیدها)
$prefill_name    = isset($user_info['name']) ? $user_info['name'] : '';
$prefill_email   = isset($user_info['email']) ? $user_info['email'] : '';
$prefill_phone   = isset($user_info['mobile']) ? $user_info['mobile'] : '';
$prefill_address = isset($user_address['address']) ? $user_address['address'] : '';
$prefill_zip     = isset($user_address['postal_code']) ? $user_address['postal_code'] : '';
$prefill_city    = isset($user_address['city']) ? $user_address['city'] : '';
$prefill_state   = isset($user_address['state']) ? $user_address['state'] : '';
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
                        <p class="text">
                            <?php echo !empty($prefill_name) ? 'اطلاعات شما از حساب کاربری فراخوانی شد.' : 'لطفا اطلاعات زیر را تکمیل کنید.'; ?>
                        </p>
                    </div>
                    
                    <div class="form" id="checkout-form">
                        <div>
                            <div class="select input">
                                <input type="text" id="billing_name" placeholder="نام و نام خانوادگی *" value="<?php echo esc_attr($prefill_name); ?>">
                            </div>
                            <div class="select input">
                                <input type="email" id="billing_email" placeholder="ایمیل (اختیاری)" value="<?php echo esc_attr($prefill_email); ?>">
                            </div>
                        </div>
                        <div>
                            <div class="select input">
                                <input type="text" id="billing_phone" placeholder="‌شماره تماس (موبایل) *" value="<?php echo esc_attr($prefill_phone); ?>">
                            </div>
                            <div class="select input">
                                <input type="text" id="billing_postcode" placeholder="کد پستی *" value="<?php echo esc_attr($prefill_zip); ?>">
                            </div>
                        </div>
                        <div>
                            <div class="select">
                                <select id="billing_state">
                                    <option value="">استان *</option>
                                    <option value="Tehran" <?php selected($prefill_state, 'Tehran'); ?>>تهران</option>
                                    <option value="East Azerbaijan" <?php selected($prefill_state, 'East Azerbaijan'); ?>>آذربایجان شرقی</option>
                                    <option value="Isfahan" <?php selected($prefill_state, 'Isfahan'); ?>>اصفهان</option>
                                    <option value="Fars" <?php selected($prefill_state, 'Fars'); ?>>فارس</option>
                                    <option value="Razavi Khorasan" <?php selected($prefill_state, 'Razavi Khorasan'); ?>>خراسان رضوی</option>
                                </select>
                            </div>
                            <div class="select">
                                <select id="billing_city">
                                    <option value="">شهر *</option>
                                    <option value="Tehran" <?php selected($prefill_city, 'Tehran'); ?>>تهران</option>
                                    <option value="Tabriz" <?php selected($prefill_city, 'Tabriz'); ?>>تبریز</option>
                                    <option value="Isfahan" <?php selected($prefill_city, 'Isfahan'); ?>>اصفهان</option>
                                    <option value="Shiraz" <?php selected($prefill_city, 'Shiraz'); ?>>شیراز</option>
                                    <option value="Mashhad" <?php selected($prefill_city, 'Mashhad'); ?>>مشهد</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="select input">
                                <input type="text" id="billing_address" placeholder="آدرس کامل پستی *" value="<?php echo esc_attr($prefill_address); ?>">
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
                    
                    <div id="checkout-items-wrapper" style="margin-bottom:20px; max-height:200px; overflow-y:auto; border-bottom:1px solid #eee; padding-bottom:10px;">
                        </div>

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
/**
 * Checkout Logic Script
 * Handles final price calculation and order submission.
 */
document.addEventListener("DOMContentLoaded", function() {
    const btn = document.getElementById('btn-place-order');
    const shippingInputs = document.querySelectorAll('input[name="ship-method"]');
    
    // --- 1. Calculate Final Price (Cart Total + Shipping) ---
    function updateFinalPrice() {
        // Ensure CartManager is loaded
        if (typeof window.cartManager === 'undefined') return;
        
        const cartTotal = window.cartManager.getTotalPrice();
        
        // Get selected shipping cost
        const selectedShipping = document.querySelector('input[name="ship-method"]:checked');
        const shippingCost = selectedShipping ? parseInt(selectedShipping.getAttribute('data-cost')) : 0;
        
        const finalTotal = cartTotal + shippingCost;
        
        // Update DOM elements
        const cartPriceEls = document.querySelectorAll('.total-cart-price');
        cartPriceEls.forEach(el => el.innerText = cartTotal.toLocaleString());
        
        const shipDisplay = document.getElementById('shipping-cost-display');
        if(shipDisplay) shipDisplay.innerText = shippingCost === 0 ? 'پس‌کرایه' : shippingCost.toLocaleString();
        
        const finalDisplay = document.getElementById('final-payable-price');
        if(finalDisplay) finalDisplay.innerText = finalTotal.toLocaleString();
    }

    // Listen for changes in shipping method or cart updates
    shippingInputs.forEach(input => {
        input.addEventListener('change', updateFinalPrice);
    });

    setTimeout(updateFinalPrice, 500);
    window.addEventListener('cartUpdated', updateFinalPrice);


    // --- 2. Order Submission Logic ---
    if(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Validation: Check Cart
            if (typeof window.cartManager === 'undefined') return;
            const cart = window.cartManager.getCart();
            if(cart.length === 0) {
                alert("Cart is empty.");
                return;
            }

            // Validation: Get Form Data
            const fullName = document.getElementById('billing_name').value;
            const phone = document.getElementById('billing_phone').value;
            const address = document.getElementById('billing_address').value;
            const state = document.getElementById('billing_state').value;
            const city = document.getElementById('billing_city').value;
            const zip = document.getElementById('billing_postcode').value;

            if (!fullName || !phone || !address || !state || !city) {
                alert("لطفا تمام فیلدهای ستاره‌دار را تکمیل کنید.");
                return;
            }

            // Prepare Data Object
            const formData = {
                full_name: fullName,
                phone: phone,
                zip_code: zip,
                state: state,
                city: city,
                address: address,
                shipping_method: document.querySelector('input[name="ship-method"]:checked')?.value || 'pishaz',
                items: cart.map(item => ({
                    variant_id: item.variant_id ? item.variant_id : item.id,
                    quantity: item.quantity
                }))
            };

            // UI: Disable button and show loading state
            const originalBtnText = btn.innerText;
            btn.innerText = "در حال بررسی و ثبت...";
            btn.disabled = true;
            btn.style.opacity = "0.7";

            // Send AJAX Request
            fetch(wbs_data.ajax_url + '?action=akamode_process_checkout', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(response => {
                if(response.success) {
                    // Success: Clear cart and redirect to gateway
                    window.cartManager.clearCart(); 
                    window.location.href = response.data.redirect_url;
                } else {
                    // Error: Show message (e.g., Stock issues)
                    const errorMsg = response.data.message || "مشکلی در ثبت سفارش پیش آمد.";
                    
                    // Simple confirm dialog to guide user back to cart if needed
                    if(confirm("خطا: " + errorMsg + "\n\nآیا می‌خواهید به سبد خرید برگردید و تعداد را اصلاح کنید؟")) {
                        window.location.href = wbs_data.cart_url;
                    }

                    // Reset button state
                    btn.innerText = originalBtnText;
                    btn.disabled = false;
                    btn.style.opacity = "1";
                }
            })
            .catch(err => {
                console.error(err);
                alert("خطای ارتباط با سرور. لطفا مجدد تلاش کنید.");
                
                // Reset button state
                btn.innerText = originalBtnText;
                btn.disabled = false;
                btn.style.opacity = "1";
            });
        });
    }
});
</script>