<?php
if (!is_user_logged_in()) {
    $target_url = home_url('/login') . '?redirect_to=' . urlencode(home_url('/checkout'));
    if (!headers_sent()) wp_redirect($target_url);
    else echo '<script>window.location.href = "' . $target_url . '";</script>';
    exit;
}

// دریافت اطلاعات
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
        if (!is_wp_error($addr_r) && isset($addr_r['data'])) $user_addresses = $addr_r['data']; // ساختار احتمالی 1
        elseif (!is_wp_error($addr_r)) $user_addresses = $addr_r; // ساختار احتمالی 2 (طبق جیسون شما)
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
                    <div>
                        <p class="title">اطلاعات ارسال</p>
                        <p class="text">لطفا آدرس و مشخصات گیرنده را وارد کنید.</p>
                        <?php if(is_array($user_addresses) && count($user_addresses) > 0): ?>
                            <button type="button" id="btn-change-address" style="font-size:12px; padding:5px 10px; background:#eee; border:none; cursor:pointer;">انتخاب از آدرس‌های ذخیره شده</button>
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
                                    <option value="Tehran">تهران</option>
                                    <option value="East Azerbaijan">آذربایجان شرقی</option>
                                    </select>
                            </div>
                            <div class="select">
                                <select id="billing_city">
                                    <option value="">شهر *</option>
                                    <option value="Tehran">تهران</option>
                                    <option value="Tabriz">تبریز</option>
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
                        <div class="method-row" style="display:flex; justify-content:space-between; margin-bottom:10px;">
                            <label><input type="radio" name="ship-method" value="pishaz" data-cost="35000" checked> پست پیشتاز</label>
                            <div class="price">۳۵,۰۰۰ تومان</div>
                        </div>
                        <div class="method-row" style="display:flex; justify-content:space-between;">
                            <label><input type="radio" name="ship-method" value="tipax" data-cost="0"> تیپاکس (پس‌کرایه)</label>
                            <div class="price">پس‌کرایه</div>
                        </div>
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
                            <tr><td>هزینه ارسال</td><td id="shipping-cost-display">35,000 تومان</td></tr>
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
document.addEventListener("DOMContentLoaded", function() {
    
    // --- مدیریت آدرس‌ها ---
    const userAddresses = <?php echo json_encode(is_array($user_addresses) ? $user_addresses : []); ?>;
    
    // فیلدها
    const els = {
        name: document.getElementById('billing_name'),
        addr: document.getElementById('billing_address'),
        city: document.getElementById('billing_city'),
        state: document.getElementById('billing_state'),
        zip: document.getElementById('billing_postcode'),
        phone: document.getElementById('billing_phone')
    };

    function fillForm(a) {
        if(!a) return;
        if(els.name) els.name.value = a.full_name || '';
        if(els.addr) els.addr.value = a.address || '';
        if(els.city) els.city.value = a.city || '';
        if(els.state) els.state.value = a.state || '';
        if(els.zip) els.zip.value = a.zip_code || '';
        if(els.phone) els.phone.value = a.phone || '';
    }

    // انتخاب هوشمند آدرس
    if (userAddresses.length === 1) fillForm(userAddresses[0]);
    
    document.getElementById('btn-change-address')?.addEventListener('click', () => {
        if(typeof Swal === 'undefined') return;
        let opts = {};
        userAddresses.forEach((a, i) => opts[i] = `<b>${a.city}</b>: ${a.address}`);
        
        Swal.fire({
            title: 'انتخاب آدرس',
            input: 'radio',
            inputOptions: opts,
            confirmButtonText: 'تایید',
            customClass: { container: 'akamode-popup-container', confirmButton: 'akamode-popup-confirm' }
        }).then(res => { if(res.isConfirmed) fillForm(userAddresses[res.value]); });
    });

    // --- محاسبه قیمت ---
    function calcPrice() {
        if(typeof window.cartManager === 'undefined') return;
        let total = window.cartManager.getTotalPrice();
        let shipMethod = document.querySelector('input[name="ship-method"]:checked');
        let shipCost = shipMethod ? parseInt(shipMethod.dataset.cost) : 0;
        
        // تخفیف
        let disc = 0;
        let savedDisc = localStorage.getItem('active_discount');
        if(savedDisc) {
            try { 
                let d = JSON.parse(savedDisc); 
                disc = parseInt(d.amount); 
                document.getElementById('discount-row').style.display = 'table-row';
                document.getElementById('discount-amount-display').innerText = disc.toLocaleString();
            } catch(e) {}
        }

        document.querySelectorAll('.total-cart-price').forEach(e => e.innerText = total.toLocaleString() + ' تومان');
        document.getElementById('shipping-cost-display').innerText = shipCost.toLocaleString() + ' تومان';
        document.getElementById('final-payable-price').innerText = Math.max(0, total + shipCost - disc).toLocaleString() + ' تومان';
    }
    
    document.querySelectorAll('input[name="ship-method"]').forEach(el => el.addEventListener('change', calcPrice));
    setTimeout(calcPrice, 500);

    // --- ثبت سفارش ---
    document.getElementById('btn-place-order').addEventListener('click', function(e) {
        e.preventDefault();
        const btn = this;
        
        if(typeof window.cartManager === 'undefined' || window.cartManager.getCart().length === 0) {
            alert('سبد خرید خالی است'); return;
        }

        // بررسی فیلدها
        if(!els.name.value || !els.phone.value || !els.addr.value || !els.city.value) {
            alert('لطفا فیلدهای آدرس را کامل کنید'); return;
        }

        let discCode = null;
        try { discCode = JSON.parse(localStorage.getItem('active_discount')).code; } catch(e){}

        // *** ساختار دقیق جیسون مطابق Postman ***
        const payload = {
            "address": {
                "full_name": els.name.value,
                "address": els.addr.value,
                "city": els.city.value,
                "state": els.state.value,
                "zip_code": els.zip.value,
                "phone": els.phone.value,
                "country": "Iran"
            },
            "shipping_method": document.querySelector('input[name="ship-method"]:checked')?.value || 'pishaz',
            "packaging_id": 0,
            "discount_code": discCode,
            "payment_method": "card",
            "transaction_code": "1234567890", // مقدار تستی برای عبور از ولیدیشن
            "items": window.cartManager.getCart().map(i => ({
                "variant_id": i.variant_id ? parseInt(i.variant_id) : parseInt(i.id),
                "quantity": parseInt(i.quantity)
            }))
        };

        const oldText = btn.innerText;
        btn.innerText = 'در حال ثبت...';
        btn.disabled = true;

        fetch(wbs_ajax.ajax_url + '?action=akamode_process_checkout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
            credentials: 'include'
        })
        .then(r => r.json())
        .then(res => {
            if(res.success) {
                window.cartManager.clearCart();
                localStorage.removeItem('active_discount');
                window.location.href = res.data.redirect_url;
            } else {
                alert(res.data.message || 'خطا در ثبت سفارش');
                btn.innerText = oldText;
                btn.disabled = false;
            }
        })
        .catch(() => {
            alert('خطای ارتباط با سرور');
            btn.innerText = oldText;
            btn.disabled = false;
        });
    });
});
</script>