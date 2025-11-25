<?php
/**
 * Template part for displaying single order details
 */

// 1. Get the Order ID from the URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. (Optional) Security check: Ensure this order belongs to the current user
// if ( ! is_user_owner_of_order($order_id) ) { echo 'Access Denied'; return; }

// 3. URL to go back to the list
$base_url = get_permalink();
$back_url = add_query_arg('tab', 'orders', $base_url);
?>

<div class="order-details-container">
    
    <div class="details-header">        
        <a href="<?php echo esc_url($back_url); ?>" class="back-btn">
            <i class="icon-up-left-arrow"></i> <span>بازگشت به لیست سفارش‌ها</span> 
        </a>
    </div>

    <div class="order-info">
        <div class="right">
            <div class="top">آیتم های سفارش</div>
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
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="left">
            <div class="top">جزئیات سفارش</div>
            <table>
                <tbody>
                    <tr>
                        <td><p>شناسه سفارش</p></td>
                        <td><p>تست تست</p></td>
                    </tr>
                    <tr>
                        <td><p>نام و نام خانوادگی</p></td>
                        <td><p>تست تست</p></td>
                    </tr>
                    <tr>
                        <td><p>شماره تماس</p></td>
                        <td><p>تست تست</p></td>
                    </tr>
                    <tr>
                        <td><p>ایمیل</p></td>
                        <td><p>تست تست</p></td>
                    </tr>
                    <tr>
                        <td><p>آدرس</p></td>
                        <td><p>تست تست</p></td>
                    </tr>
                    <tr>
                        <td><p>کد پستی</p></td>
                        <td><p>تست تست</p></td>
                    </tr>
                    <tr>
                        <td><p>روش ارسال</p></td>
                        <td><p>تست تست</p></td>
                    </tr>
                    <tr>
                        <td><p>وضعیت</p></td>
                        <td><p>تست تست</p></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="financial-container">
        <div class="top">بخش مالی</div>
        <div class="body">
            <table>
                <tbody>
                    <tr>
                        <td><p>هزینه مالیات بر ارزش افزوده</p></td>
                        <td><p>۲۳ میلیون تومان</p></td>
                    </tr>
                    <tr>
                        <td><p>هزینه ارسال</p></td>
                        <td><p>۲۳ میلیون تومان</p></td>
                    </tr>
                    <tr>
                        <td><p>مجموع مبلغ</p></td>
                        <td><p>۲۳ میلیون تومان</p></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>