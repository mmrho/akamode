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
            <p>صورت حساب شما</p>
        </div>

        <div class="checkout-container">
            <div class="right">

                <div class="top">
                    <div>
                        <span>ثبت نام نکرده اید؟</span>
                        <a href="#">وارد شوید</a>
                    </div>
                    <div>
                        <span>آیا کد تخفیف دارید؟</span>
                        <a href="#">کد تخفیف را وارد کنید</a>
                    </div>
                    <div class="dis">
                        <p>در صورت داشتن کد تخفیف در باکس زیر وارد کنید</p>
                        <div class="discount">
                            <div class="select input">
                                <input type="text" placeholder="کد تخفیف">
                            </div>
                            <a href="#" class="sdiscount">ثبت کد</a>
                        </div>
                    </div>
                    
                </div>

                <div class="ship-info">
                    <div>
                        <p class="title">اطلاعات ارسال</p>
                        <p class="text">لطفا اطلاعات زیر را تکمیل کنید</p>
                    </div>
                    
                    <div class="form">
                        <div>
                            <div class="select input">
                                <input type="text" placeholder="نام *">
                            </div>
                            <div class="select input">
                                <input type="text" placeholder="ایمیل *">
                            </div>
                        </div>
                        <div>
                            <div class="select input">
                                <input type="text" placeholder="‌شماره تماس *">
                            </div>
                            <div class="select input">
                                <input type="text" placeholder="کد پستی *">
                            </div>
                        </div>
                        <div>
                            <div class="select">
                                <select name="" id="">
                                    <option value="">استان *</option>
                                </select>
                            </div>
                            <div class="select">
                                <select name="" id="">
                                    <option value="">شهر *</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="select input">
                                <input type="text" placeholder="آدرس *">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ship-method">
                    <div>
                        <p class="title">روش ارسال</p>
                        <p class="text">روش ارسال را انتخاب کنید</p>
                    </div>

                    <div class="methods">
                        <div>
                            <label>
                                <input type="radio" name="ship-method" value="pishaz" checked>
                                <span>پست پیشتاز</span>
                            </label>
                            <div class="price">۲۳ میلیون تومان</div>
                        </div>
                        <div class="hr"></div>
                        <div>
                            <label>
                                <input type="radio" name="ship-method" value="tipax">
                                <span>تیپاکس</span>
                            </label>
                            <div class="price">۲۳ میلیون تومان</div>
                        </div>
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
                                <tr>
                                    <td><p>هزینه ارسال</p></td>
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