<div class="container">
    <div class="main">
        <div class="top">
            <h1>سبد خرید</h1>
            <p>لیست سفارش‌های شما</p>
        </div>

        <div class="cart-container">
            <div class="right">
                <div class="items" id="cart-items-wrapper">
                    <div style="display:flex; justify-content:center; padding:40px;">
                        <span style="color:#666;">در حال بارگذاری اقلام سبد...</span>
                    </div>
                </div>
            </div>
            
            <div class="left">
                <div class="discount-wrapper" style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px;">
                    <p style="margin-bottom: 10px; font-weight: bold;">کد تخفیف</p>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="discount_code_input" placeholder="کد تخفیف (مثلا: BAHAR)" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <button id="apply_discount_btn" style="padding: 8px 15px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">ثبت</button>
                    </div>
                    <div id="discount_message_box" style="margin-top: 10px; font-size: 13px;"></div>
                </div>
                <p>مجموع سبد خرید</p>
                <div class="financial-container">
                    <div class="body">
                        <table>
                            <tbody>
                                <tr>
                                    <td><p>مجموع اقلام</p></td>
                                    <td><p class="total-cart-price">0 تومان</p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="hr"></div>

                    <div class="bottom">
                        <table>
                            <tbody>
                                <tr>
                                    <td><p>مبلغ قابل پرداخت</p></td>
                                    <td><p class="final-total-price" style="font-weight:bold;">0 تومان</p></td>
                                </tr>
                            </tbody>
                        </table>

                        <a href="<?php echo home_url('/checkout'); ?>" class="submit-purchase">ادامه جهت تسویه حساب</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>