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
                                    <td><p class="total-cart-price" style="font-weight:bold;">0 تومان</p></td>
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