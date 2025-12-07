document.addEventListener("DOMContentLoaded", function() {
    
   
    const userAddresses = (typeof wbs_checkout_params !== 'undefined' && wbs_checkout_params.user_addresses) 
                          ? wbs_checkout_params.user_addresses 
                          : [];
    
    const formFields = {
        full_name: document.getElementById('billing_name'),
        address:   document.getElementById('billing_address'),
        city:      document.getElementById('billing_city'),
        state:     document.getElementById('billing_state'),
        postcode:  document.getElementById('billing_postcode'),
        phone:     document.getElementById('billing_phone')
    };

    function fillAddressForm(addr) {
        if(!addr) return;
        if(formFields.full_name) formFields.full_name.value = addr.full_name || '';
        if(formFields.address) formFields.address.value = addr.address || '';
        if(formFields.city) formFields.city.value = addr.city || '';
        if(formFields.state) formFields.state.value = addr.state || '';
        if(formFields.postcode) formFields.postcode.value = addr.zip_code || '';
        if(formFields.phone) formFields.phone.value = addr.phone || '';
    }

    if (Array.isArray(userAddresses) && userAddresses.length > 0) {
        if (userAddresses.length === 1) {
            fillAddressForm(userAddresses[0]);
        } else {
            setTimeout(showAddressModal, 500);
        }
    }

    function showAddressModal() {
        if(typeof Swal !== 'undefined') {
            let options = {};
            userAddresses.forEach((addr, idx) => {
                options[idx] = `<b>${addr.city}</b> - ${addr.address}`;
            });

            Swal.fire({
                title: 'انتخاب آدرس ارسال',
                input: 'radio',
                inputOptions: options,
                inputValue: 0,
                confirmButtonText: 'تایید آدرس',
                allowOutsideClick: false,
                customClass: {
                    container: 'akamode-popup-container',
                    title: 'akamode-popup-title',
                    content: 'akamode-popup-content',
                    confirmButton: 'akamode-popup-confirm'
                },
                width: '400px'
            }).then((result) => {
                if (result.isConfirmed) {
                    fillAddressForm(userAddresses[result.value]);
                }
            });
        } else {
            fillAddressForm(userAddresses[0]);
        }
    }
    
    const changeBtn = document.getElementById('btn-change-address');
    if(changeBtn) changeBtn.addEventListener('click', showAddressModal);


    const btn = document.getElementById('btn-place-order');
    const shippingInputs = document.querySelectorAll('input[name="ship-method"]');
    
    function updateFinalPrice() {
        if (typeof window.cartManager === 'undefined') return;
        
        let cartTotal = window.cartManager.getTotalPrice();
        const selectedShipping = document.querySelector('input[name="ship-method"]:checked');
        const shippingCost = selectedShipping ? parseInt(selectedShipping.getAttribute('data-cost')) : 0;
        
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

        let finalTotal = (cartTotal + shippingCost) - discountAmount;
        if(finalTotal < 0) finalTotal = 0;
        
        document.querySelectorAll('.total-cart-price').forEach(el => el.innerText = cartTotal.toLocaleString());
        const shipDisplay = document.getElementById('shipping-cost-display');
        if(shipDisplay) shipDisplay.innerText = shippingCost === 0 ? '۶۰,۰۰۰ تومان' : shippingCost.toLocaleString();
        const finalDisplay = document.getElementById('final-payable-price');
        if(finalDisplay) finalDisplay.innerText = finalTotal.toLocaleString();
    }

    if(shippingInputs.length > 0) {
        shippingInputs.forEach(input => input.addEventListener('change', updateFinalPrice));
    }
    setTimeout(updateFinalPrice, 500);
    window.addEventListener('cartUpdated', updateFinalPrice);

    
    if(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (typeof window.cartManager === 'undefined') return;
            const cart = window.cartManager.getCart();
            if(cart.length === 0) { alert("سبد خرید خالی است."); return; }

            const fullName = document.getElementById('billing_name').value;
            const phone = document.getElementById('billing_phone').value;
            const address = document.getElementById('billing_address').value;
            const state = document.getElementById('billing_state').value;
            const city = document.getElementById('billing_city').value;
            const zip = document.getElementById('billing_postcode').value;

            if (!fullName || !phone || !address || !state || !city) {
                if(typeof Swal !== 'undefined') Swal.fire('خطا', 'لطفا تمام فیلدهای ستاره‌دار را تکمیل کنید.', 'error');
                else alert("خطای غیر منتظره");
                return;
            }

            let appliedDiscountCode = null;
            const storedDiscount = localStorage.getItem('active_discount');
            if (storedDiscount) {
                try {
                    const dData = JSON.parse(storedDiscount);
                    appliedDiscountCode = dData.code;
                } catch(e) {}
            }

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
                "packaging_id": 0, 
                "discount_code": appliedDiscountCode,
                "payment_method": "card",
                "transaction_code": "1234567890",
                "items": cart.map(item => ({
                    "variant_id": item.variant_id ? parseInt(item.variant_id) : parseInt(item.id),
                    "quantity": parseInt(item.quantity)
                }))
            };

            const originalBtnText = btn.innerText;
            btn.innerText = "در حال ثبت...";
            btn.disabled = true;

            if(typeof wbs_ajax === 'undefined') {
                alert('خطای سیستمی: تنظیمات ارتباطی یافت نشد.');
                btn.innerText = originalBtnText;
                btn.disabled = false;
                return;
            }

            fetch(wbs_ajax.ajax_url + '?action=akamode_process_checkout', {
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
                            title: 'موفق!',
                            text: response.data.message || 'سفارش شما ثبت شد.',
                            icon: 'success',
                            confirmButtonText: 'باشه',
                            customClass: { confirmButton: 'akamode-popup-confirm' }
                        }).then(() => { window.location.href = response.data.redirect_url; });
                    } else {
                        window.location.href = response.data.redirect_url;
                    }
                } else {
                    let msg = response.data.message || "مشکلی پیش آمد";
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