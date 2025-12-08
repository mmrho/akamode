document.addEventListener("DOMContentLoaded", function() {
    
// 1. دیتابیس استان‌ها و شهرهای ایران
const iranProvinces = {
    "Alborz": "البرز", "Ardabil": "اردبیل", "Bushehr": "بوشهر", "Chaharmahal and Bakhtiari": "چهارمحال و بختیاری",
    "East Azerbaijan": "آذربایجان شرقی", "Isfahan": "اصفهان", "Fars": "فارس", "Gilan": "گیلان", "Golestan": "گلستان",
    "Hamadan": "همدان", "Hormozgan": "هرمزگان", "Ilam": "ایلام", "Kerman": "کرمان", "Kermanshah": "کرمانشاه",
    "Khuzestan": "خوزستان", "Kohgiluyeh and Boyer-Ahmad": "کهگیلویه و بویراحمد", "Kurdistan": "کردستان", "Lorestan": "لرستان",
    "Markazi": "مرکزی", "Mazandaran": "مازندران", "North Khorasan": "خراسان شمالی", "Qazvin": "قزوین", "Qom": "قم",
    "Razavi Khorasan": "خراسان رضوی", "Semnan": "سمنان", "Sistan and Baluchestan": "سیستان و بلوچستان", "South Khorasan": "خراسان جنوبی",
    "Tehran": "تهران", "West Azerbaijan": "آذربایجان غربی", "Yazd": "یزد", "Zanjan": "زنجان"
};

const iranCities = {
    "Alborz": ["کرج", "هشتگرد", "نظرآباد", "محمدشهر", "اشتهارد", "ماهدشت", "مشکین دشت", "گرمدره"],
    "Ardabil": ["اردبیل", "پارس آباد", "مشگین شهر", "خلخال", "گرمی", "نمین", "بیله سوار", "کوثر"],
    "Bushehr": ["بوشهر", "برازجان", "گناوه", "خورموج", "کنگان", "عسلویه", "دیر", "جم"],
    "Chaharmahal and Bakhtiari": ["شهرکرد", "بروجن", "لردگان", "فرخ شهر", "فارسان", "هفشجان", "جونقان"],
    "East Azerbaijan": ["تبریز", "مراغه", "مرند", "میانه", "اهر", "بناب", "سراب", "آذرشهر", "هادیشهر", "عجب شیر"],
    "Isfahan": ["اصفهان", "کاشان", "خمینی شهر", "نجف آباد", "شاهین شهر", "شهرضا", "فولادشهر", "مبارکه", "زرین شهر", "گلپایگان"],
    "Fars": ["شیراز", "مرودشت", "جهرم", "فسا", "کازرون", "داراب", "فیروزآباد", "لار", "آباده", "نورآباد"],
    "Gilan": ["رشت", "بندر انزلی", "لاهیجان", "لنگرود", "هشتپر", "آستارا", "صومعه سرا", "آستانه اشرفیه", "رودسر", "فومن"],
    "Golestan": ["گرگان", "گنبد کاووس", "علی آباد کتول", "بندر ترکمن", "آزادشهر", "آق قلا", "کلاله", "کردکوی"],
    "Hamadan": ["همدان", "ملایر", "نهاوند", "تویسرکان", "اسدآباد", "کبودرآهنگ", "بهار", "رزن"],
    "Hormozgan": ["بندرعباس", "میناب", "دهبارز", "بندر لنگه", "قشم", "کیش", "حاجی آباد", "بندر کنگ"],
    "Ilam": ["ایلام", "دهلران", "ایوان", "آبدانان", "دره شهر", "مهران", "سرابله"],
    "Kerman": ["کرمان", "سیرجان", "رفسنجان", "جیرفت", "بم", "زرند", "کهنوج", "شهربابک"],
    "Kermanshah": ["کرمانشاه", "اسلام آباد غرب", "جوانرود", "kangavar", "سرپل ذهاب", "سنقر", "هرسین", "صحنه"],
    "Khuzestan": ["اهواز", "دزفول", "آبادان", "ماهشه", "اندیمشک", "خرمشهر", "بهبهان", "ایذه", "شوشتر", "مسجدسلیمان"],
    "Kohgiluyeh and Boyer-Ahmad": ["یاسوج", "دوگنبدان", "دهدشت", "لیکک", "چرام", "لنده"],
    "Kurdistan": ["سنندج", "سقز", "مریوان", "بانه", "قروه", "کامیاران", "بیجار", "دیواندره"],
    "Lorestan": ["خرم آباد", "بروجرد", "دورود", "کوهدشت", "الیگودرز", "نورآباد", "ازنا", "الشتر"],
    "Markazi": ["اراک", "ساوه", "خمین", "محلات", "دلیجان", "شازند", "مامونیه", "تفرش"],
    "Mazandaran": ["ساری", "بابل", "آمل", "قائم شهر", "بهشهر", "چالوس", "نکا", "بابلسر", "تنکابن", "نوشهر"],
    "North Khorasan": ["بجنورد", "شیروان", "اسفراین", "آشخانه", "جاجرم", "فاروج"],
    "Qazvin": ["قزوین", "الوند", "تاکستان", "بوئین زهرا", "بیدستان", "محمدیه"],
    "Qom": ["قم", "قنوات", "جعفریه", "کهک"],
    "Razavi Khorasan": ["مشهد", "نیشابور", "سبزوار", "تربت حیدریه", "کاشمر", "قوچان", "تربت جام", "تایباد", "چناران", "سرخس"],
    "Semnan": ["سمنان", "شاهرود", "دامغان", "گرمسار", "مهدی شهر", "ایوانکی"],
    "Sistan and Baluchestan": ["زاهدان", "زابل", "ایرانشهر", "چابهار", "سراوان", "خاش", "کنارک", "میرجاوه"],
    "South Khorasan": ["بیرجند", "قائن", "طبس", "فردوس", "نهبندان", "سرایان"],
    "Tehran": ["تهران", "اسلامشهر", "شهریار", "قدس", "ملارد", "پاکدشت", "ورامین", "گلستان", "ری", "اندیشه", "پرند", "پردیس", "دماوند"],
    "West Azerbaijan": ["ارومیه", "خوی", "بوکان", "مهاباد", "میاندوآب", "سلماس", "پیرانشهر", "نقده"],
    "Yazd": ["یزد", "میبد", "اردکان", "بافق", "مهریز", "ابرکوه", "اشکذر"],
    "Zanjan": ["زنجان", "ابهر", "خرمدره", "قیدار", "هیدج", "صائین قلعه"]
};

// 2. مقداردهی متغیرها
const userAddresses = (typeof wbs_checkout_data !== 'undefined' && wbs_checkout_data.addresses) 
                      ? wbs_checkout_data.addresses 
                      : [];

const formFields = {
    full_name: document.getElementById('billing_name'),
    address:   document.getElementById('billing_address'),
    city:      document.getElementById('billing_city'),
    state:     document.getElementById('billing_state'),
    postcode:  document.getElementById('billing_postcode'),
    phone:     document.getElementById('billing_phone')
};

// 3. پر کردن لیست استان‌ها
function populateStates() {
    if(!formFields.state) return;
    formFields.state.innerHTML = '<option value="">استان *</option>';
    for (const [key, value] of Object.entries(iranProvinces)) {
        let option = document.createElement("option");
        option.value = key; // مقدار انگلیسی (مثلا Tehran)
        option.text = value; // نمایش فارسی (مثلا تهران)
        formFields.state.appendChild(option);
    }
}

// 4. پر کردن لیست شهرها بر اساس استان
function populateCities(selectedStateKey) {
    if(!formFields.city) return;
    formFields.city.innerHTML = '<option value="">شهر *</option>';
    
    // اگر کلید استان معتبر باشد، شهرها را پر کن
    if (selectedStateKey && iranCities[selectedStateKey]) {
        iranCities[selectedStateKey].forEach(city => {
            let option = document.createElement("option");
            option.value = city; // شهر معمولا فارسی ذخیره می‌شود
            option.text = city;
            formFields.city.appendChild(option);
        });
    }
}

// رویداد تغییر استان توسط کاربر
if(formFields.state) {
    populateStates();
    formFields.state.addEventListener('change', function() {
        populateCities(this.value);
    });
}

// 5. تابع کمکی برای پیدا کردن کلید استان (انگلیسی) از روی نام (فارسی یا انگلیسی)
function findStateKey(inputState) {
    if (!inputState) return "";
    // اگر ورودی دقیقاً یکی از کلیدهای انگلیسی باشد
    if (iranProvinces[inputState]) return inputState;
    
    // اگر ورودی فارسی باشد، کلید انگلیسی متناظر را پیدا کن
    for (const [key, value] of Object.entries(iranProvinces)) {
        if (value === inputState) {
            return key;
        }
    }
    return "";
}

// 6. پر کردن فرم با آدرس انتخاب شده (اصلاح شده)
function fillAddressForm(addr) {
    if(!addr) return;
    if(formFields.full_name) formFields.full_name.value = addr.full_name || '';
    if(formFields.address) formFields.address.value = addr.address || '';
    if(formFields.postcode) formFields.postcode.value = addr.zip_code || '';
    if(formFields.phone) formFields.phone.value = addr.phone || '';

    // انتخاب هوشمند استان و شهر
    if(formFields.state && addr.state) {
        // پیدا کردن کلید صحیح استان (چه فارسی بیاید چه انگلیسی)
        const correctStateKey = findStateKey(addr.state);
        
        if (correctStateKey) {
            formFields.state.value = correctStateKey;
            // بلافاصله شهرها را لود کن
            populateCities(correctStateKey);
            
            // انتخاب شهر با کمی تاخیر برای اطمینان از رندر شدن
            if(formFields.city && addr.city) {
                setTimeout(() => {
                    formFields.city.value = addr.city;
                }, 10);
            }
        }
    }
}

// اگر فقط یک آدرس وجود دارد، خودکار پر شود
if (Array.isArray(userAddresses) && userAddresses.length === 1) {
    fillAddressForm(userAddresses[0]);
}

// 7. لاجیک پاپ‌آپ آدرس
function showAddressModal() {
    if(typeof Swal !== 'undefined' && userAddresses.length > 0) {
        let options = {};
        userAddresses.forEach((addr, idx) => {
            options[idx] = `<div style="text-align:right; line-height:1.5;">
                                <b>${addr.state || ''} - ${addr.city || ''}</b><br>
                                <span style="font-size:12px; color:#555;">${addr.address}</span>
                            </div>`;
        });

        Swal.fire({
            title: 'انتخاب آدرس ارسال',
            input: 'radio',
            inputOptions: options,
            inputValue: 0, 
            confirmButtonText: 'تایید و انتخاب',
            showCloseButton: true,
            customClass: {
                container: 'akamode-popup-container',
                title: 'akamode-popup-title',
                confirmButton: 'akamode-popup-confirm'
            },
            width: '700px'
        }).then((result) => {
            if (result.isConfirmed) {
                fillAddressForm(userAddresses[result.value]);
            }
        });
    } else if (userAddresses.length === 0) {
        if(typeof Swal !== 'undefined') Swal.fire('توجه', 'آدرسی یافت نشد. لطفا فرم را دستی پر کنید.', 'info');
        else alert('آدرسی یافت نشد.');
    }
}

const changeBtn = document.getElementById('btn-change-address');
if(changeBtn) changeBtn.addEventListener('click', showAddressModal);

    // 7. محاسبه قیمت
    const btn = document.getElementById('btn-place-order');
    const shippingInputs = document.querySelectorAll('input[name="ship-method"]');
    
    function updateFinalPrice() {
        if (typeof window.cartManager === 'undefined') return;
        
        let cartTotal = window.cartManager.getTotalPrice();
        const selectedShipping = document.querySelector('input[name="ship-method"]:checked');
        
        // تبدیل هزینه به عدد صحیح (اگر صفر بود یعنی پس کرایه)
        const shippingCost = selectedShipping ? parseInt(selectedShipping.getAttribute('data-cost')) : 0;
        
        // محاسبه تخفیف
        let discountAmount = 0;
        const storedDiscount = localStorage.getItem('active_discount');
        if (storedDiscount) {
            try {
                const discountData = JSON.parse(storedDiscount);
                discountAmount = parseInt(discountData.amount) || 0;
                const discountRow = document.getElementById('discount-row');
                if(discountRow) {
                    discountRow.style.display = 'table-row';
                    document.getElementById('discount-amount-display').innerText = discountAmount.toLocaleString();
                }
            } catch(e) {}
        } else {
            const discountRow = document.getElementById('discount-row');
            if(discountRow) discountRow.style.display = 'none';
        }

        // جمع نهایی
        let finalTotal = (cartTotal + shippingCost) - discountAmount;
        if(finalTotal < 0) finalTotal = 0;
        
        // به‌روزرسانی UI
        document.querySelectorAll('.total-cart-price').forEach(el => el.innerText = cartTotal.toLocaleString() + ' تومان');
        
        const shipDisplay = document.getElementById('shipping-cost-display');
        if(shipDisplay) {
            shipDisplay.innerText = shippingCost === 0 ? 'پس‌کرایه' : shippingCost.toLocaleString() + ' تومان';
        }
        
        const finalDisplay = document.getElementById('final-payable-price');
        if(finalDisplay) finalDisplay.innerText = finalTotal.toLocaleString() + ' تومان';
    }

    if(shippingInputs.length > 0) {
        shippingInputs.forEach(input => input.addEventListener('change', updateFinalPrice));
    }
    setTimeout(updateFinalPrice, 500);
    window.addEventListener('cartUpdated', updateFinalPrice); // اگر سبد خرید تغییر کرد

    
    // 8. ثبت سفارش (Ajax)
    if(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (typeof window.cartManager === 'undefined') return;
            const cart = window.cartManager.getCart();
            if(cart.length === 0) { alert("سبد خرید خالی است."); return; }

            // دریافت مقادیر فرم
            const fullName = document.getElementById('billing_name').value;
            const phone = document.getElementById('billing_phone').value;
            const address = document.getElementById('billing_address').value;
            const state = document.getElementById('billing_state').value;
            const city = document.getElementById('billing_city').value;
            const zip = document.getElementById('billing_postcode').value;
            const paymentMethodInput = document.querySelector('input[name="payment-method"]:checked');

            // اعتبارسنجی
            if (!fullName || !phone || !address || !state || !city || !zip) {
                if(typeof Swal !== 'undefined') Swal.fire('خطا', 'لطفا تمام فیلدهای ستاره‌دار آدرس را تکمیل کنید.', 'error');
                else alert("لطفا تمام فیلدهای آدرس را تکمیل کنید.");
                return;
            }

            if(!paymentMethodInput) {
                if(typeof Swal !== 'undefined') Swal.fire('خطا', 'لطفا یک روش پرداخت انتخاب کنید.', 'error');
                else alert("لطفا روش پرداخت را انتخاب کنید.");
                return;
            }

            // کد تخفیف
            let appliedDiscountCode = null;
            const storedDiscount = localStorage.getItem('active_discount');
            if (storedDiscount) {
                try {
                    const dData = JSON.parse(storedDiscount);
                    appliedDiscountCode = dData.code;
                } catch(e) {}
            }

            // آماده‌سازی داده‌ها برای ارسال
            const formData = {
                "address": {
                    "full_name": fullName,
                    "address": address,
                    "city": city,
                    "state": state,
                    "zip_code": zip,
                    "phone": phone,
                    "country": "Iran"
                },
                "shipping_method": document.querySelector('input[name="ship-method"]:checked')?.value || 'pishaz',
                "payment_method": paymentMethodInput.value, // اضافه شدن متد پرداخت داینامیک
                "packaging_id": 0, 
                "discount_code": appliedDiscountCode,
                "transaction_code": "pending", // سمت سرور هندل می‌شود
                "items": cart.map(item => ({
                    "variant_id": item.variant_id ? parseInt(item.variant_id) : parseInt(item.id),
                    "quantity": parseInt(item.quantity)
                }))
            };

            const originalBtnText = btn.innerText;
            btn.innerText = "در حال ثبت...";
            btn.disabled = true;

            // آدرس Ajax
            const ajaxUrl = (typeof wbs_checkout_data !== 'undefined' && wbs_checkout_data.ajax_url) 
                            ? wbs_checkout_data.ajax_url 
                            : (typeof wbs_ajax !== 'undefined' ? wbs_ajax.ajax_url : '/wp-admin/admin-ajax.php');

            fetch(ajaxUrl + '?action=akamode_process_checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData),
                credentials: 'include' 
            })
            .then(res => res.json())
            .then(response => {
                if(response.success) {
                    window.cartManager.clearCart(); 
                    localStorage.removeItem('active_discount'); 
                    
                    if(typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'ثبت شد!',
                            text: response.data.message || 'در حال انتقال به درگاه...',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => { window.location.href = response.data.redirect_url; });
                    } else {
                        window.location.href = response.data.redirect_url;
                    }
                } else {
                    let msg = response.data.message || "مشکلی در ثبت سفارش پیش آمد";
                    if(typeof Swal !== 'undefined') Swal.fire('خطا', msg, 'error');
                    else alert("خطا: " + msg);
                    btn.innerText = originalBtnText;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert("خطای ارتباط با سرور");
                btn.innerText = originalBtnText;
                btn.disabled = false;
            });
        });
    }
});