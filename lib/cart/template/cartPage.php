<?php
// detect what tab is active
$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';

// url of this page
$base_url = get_permalink(); 
?>

<div class="container">
    <div class="main">

        <div class="top">
            <h1>حساب کاربری</h1>
            <p>سفارش های شما</p>
        </div>

        <div class="cart-container">
            <div class="right">
                <div class="items">
                    <?php for($i = 0; $i < 3; $i++){ ?>
                    <div class="item">
                        <div class="img">
                            <img src="<?php echo THEME_IMG; ?>temp/bag.png" alt="">
                        </div>
                        <div class="item-details">
                            <p class="title">کت چرم کالکشن بهار</p>
                            <p class="color">رنگ قهوه ای - سایز ۴۴</p>
                            <p class="price">۲۳۰۰۰ تومان</p>
                        </div>
                        <div class="quantity">
                            <div class="counter">
                                <div class="plus">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <g clip-path="url(#clip0_1421_5449)">
                                        <path d="M3.75 12H20.25" stroke="#111111" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 3.75V20.25" stroke="#111111" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_1421_5449">
                                        <rect width="24" height="24" fill="white"/>
                                        </clipPath>
                                    </defs>
                                    </svg>
                                </div>
                                <div class="number">2</div>
                                <div class="minus">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <g clip-path="url(#clip0_1421_5445)">
                                        <path d="M3.75 12H20.25" stroke="#111111" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_1421_5445">
                                        <rect width="24" height="24" fill="white"/>
                                        </clipPath>
                                    </defs>
                                    </svg>
                                </div>
                            </div>
                            <div class="remove">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <g clip-path="url(#clip0_1421_5432)">
                                    <path d="M18.75 5.25L5.25 18.75" stroke="#111111" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.75 18.75L5.25 5.25" stroke="#111111" stroke-linecap="round" stroke-linejoin="round"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_1421_5432">
                                    <rect width="24" height="24" fill="white"/>
                                    </clipPath>
                                </defs>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="hr"></div>
                    <?php } ?>
                    <div class="discount">
                        <div class="select input">
                            <input type="text" placeholder="کد تخفیف">
                        </div>
                        <a href="#" class="sdiscount">ثبت کد</a>
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
                                    <td><p>مجموع مبلغ</p></td>
                                    <td><p>۲۳ میلیون تومان</p></td>
                                </tr>
                                <tr>
                                    <td><p>هزینه مالیات بر ارزش افزوده</p></td>
                                    <td><p>۲۳ میلیون تومان</p></td>
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
                                    <td><p>۲۳ میلیون تومان</p></td>
                                </tr>
                            </tbody>
                        </table>

                        <a href="#" class="submit-purchase">ادامه خرید</a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>