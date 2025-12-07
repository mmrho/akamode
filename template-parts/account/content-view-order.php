<?php
$api = get_query_var('api_client');
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$base_url = get_permalink();
$back_url = add_query_arg('tab', 'orders', $base_url);

// آدرس پایه استوریج لاراول (باید مطابق با دامنه API شما باشد)
// تصاویر معمولا در public/storage هستند
define('API_STORAGE_BASE', 'https://api.akamode.com/storage/'); 

$order = $api->get_order_single($order_id);

if (is_wp_error($order) || !$order) {
    echo '<div class="error">سفارش یافت نشد.</div>';
    echo '<a href="'.$back_url.'">بازگشت</a>';
    return;
}
?>

<div class="order-details-container">
    
    <div class="details-header">        
        <a href="<?php echo esc_url($back_url); ?>" class="back-btn">
            <i class="icon-up-left-arrow"></i> <span>بازگشت به لیست سفارش‌ها</span> 
        </a>
    </div>

    <div class="order-info">
        <div class="right">
            <div class="top">آیتم های سفارش (<?php echo count($order['items']); ?>)</div>
            <div class="items">
                <?php foreach($order['items'] as $item): 
                    $product = $item['product_variant']['product'];
                    $variant = $item['product_variant'];
                    
                    // پیدا کردن تصویر
                    $img_path = '';
                    if(!empty($product['images'])){
                        $img_path = API_STORAGE_BASE . $product['images'][0]['path'];
                    } else {
                        $img_path = THEME_IMG . 'placeholder.png'; // Placeholder
                    }
                ?>
                <div class="item">
                    <div class="img">
                        <img src="<?php echo esc_url($img_path); ?>" alt="<?php echo esc_attr($item['product_name']); ?>">
                    </div>
                    <div class="item-details">
                        <p class="title"><?php echo esc_html($item['product_name']); ?></p>
                        <p class="color">
                            <?php if(isset($variant['color'])) echo 'رنگ: ' . esc_html($variant['color']); ?> 
                            <?php if(isset($variant['size'])) echo ' - سایز: ' . esc_html($variant['size']); ?>
                        </p>
                        <p class="price"><?php echo number_format($item['price']); ?> تومان</p>
                        <p class="qty">تعداد: <?php echo $item['quantity']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="left">
            <div class="top">جزئیات سفارش</div>
            <table>
                <tbody>
                    <tr>
                        <td><p>شناسه سفارش</p></td>
                        <td><p><?php echo esc_html($order['order_code']); ?></p></td>
                    </tr>
                    <tr>
                        <td><p>وضعیت</p></td>
                        <td><p>
                            <?php
                            if($order['status'] == 'pending') {
                                echo 'در انتظار تایید';
                            } elseif($order['status'] == 'completed') {
                                echo 'تحویل شده';
                            } elseif($order['status'] == 'cancelled') {
                                echo 'لغو شده';
                            } elseif($order['status'] == 'processing') {
                                echo 'در حال پردازش';
                            } elseif($order['status'] == 'shipped') {
                                echo 'ارسال شده';
                            } else {
                                echo $order['status'];
                            } ?>
                        </p></td>
                    </tr>
                    <tr>
                        <td><p>روش ارسال</p></td>
                        <td><p><?php echo esc_html($order['shipping_method']); ?></p></td>
                    </tr>
                    <tr>
                        <td><p>روش پرداخت</p></td>
                        <td><p><?php 
                        if($order['payment_method'] == 'online') {
                            echo 'پرداخت آنلاین';
                        } elseif($order['payment_method'] == 'cod') {
                            echo 'پرداخت در محل';
                        }elseif($order['payment_method'] == 'card') {
                            echo 'کارت به کارت';
                        } else {
                            echo esc_html($order['payment_method']); 
                        }
                        ?></p></td>
                    </tr>
                    <tr>
                        <td><p>تاریخ ثبت</p></td>
                        <td style="direction:ltr"><p><?php echo get_persian_date($order['created_at']); ?></p></td>
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
                        <td><p>مبلغ کل آیتم‌ها</p></td>
                        <td><p><?php echo number_format($order['subtotal']); ?> تومان</p></td>
                    </tr>
                    <tr>
                        <td><p>هزینه ارسال</p></td>
                        <td><p><?php echo number_format($order['shipping_cost']); ?> تومان</p></td>
                    </tr>
                    <?php if($order['discount_amount'] > 0): ?>
                    <tr>
                        <td><p>تخفیف</p></td>
                        <td><p style="color:red">- <?php echo number_format($order['discount_amount']); ?> تومان</p></td>
                    </tr>
                    <?php endif; ?>
                    <tr style="font-weight:bold; background:#fafafa;">
                        <td><p>مبلغ نهایی پرداختی</p></td>
                        <td><p><?php echo number_format($order['total']); ?> تومان</p></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
