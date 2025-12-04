jQuery(document).ready(function($) {
    
    // --- Discount Logic ---
    $('#apply_discount_btn').on('click', function(e) {
        e.preventDefault();
        
        const code = $('#discount_code_input').val().trim();
        const $msgBox = $('#discount_message_box');
        const $btn = $(this);

        if (!code) {
            $msgBox.text('لطفا کد تخفیف را وارد کنید.').css('color', 'red');
            return;
        }

        // Get items from CartManager (global variable)
        if (typeof window.cartManager === 'undefined') {
             console.error("CartManager not found");
             return;
        }

        const cart = window.cartManager.getCart();
        
        if(cart.length === 0) {
            $msgBox.text('سبد خرید خالی است.').css('color', 'red');
            return;
        }

        // Prepare items for API
        const apiItems = cart.map(item => ({
            variant_id: item.variant_id || item.id,
            quantity: item.quantity
        }));

        $btn.prop('disabled', true).text('...');

        $.ajax({
            url: wbs_ajax.ajax_url, 
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'apply_discount_code',
                code: code,
                items: apiItems
            },
            success: function(response) {
                $btn.prop('disabled', false).text('ثبت');
                
                if (response.success) {
                    const data = response.data;
                    $msgBox.html(`<span style="color:green">${data.message} <br> تخفیف: ${data.discount_display}</span>`);
                    
                    // Update Total Price in DOM
                    if(data.new_total) {
                        $('.final-total-price').text(new Intl.NumberFormat('fa-IR').format(data.new_total) + ' تومان');
                    }

                    // *** FIX: Save Discount Info for Checkout Page ***
                    const discountData = {
                        code: data.discount_code,
                        amount: data.discount_amount,
                        display: data.discount_display
                    };
                    localStorage.setItem('active_discount', JSON.stringify(discountData));
                    // *************************************************

                } else {
                    $msgBox.text(response.data.message || 'کد نامعتبر است.').css('color', 'red');
                    localStorage.removeItem('active_discount'); // Clear if invalid
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('ثبت');
                $msgBox.text('خطا در ارتباط با سرور.').css('color', 'red');
            }
        });
    });
});