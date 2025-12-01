<?php 
    $api = get_query_var('api_client');
    $base_url = get_permalink(); 
    
    // دریافت شماره صفحه فعلی
    $paged = isset($_GET['page_num']) ? intval($_GET['page_num']) : 1;
    
    // درخواست به API
    $orders_response = $api->get_orders($paged); // متد get_orders باید آرگومان page بگیرد
    
    // مدیریت خطا یا خالی بودن
    $orders = [];
    $pagination = [];
    
    if (!is_wp_error($orders_response) && isset($orders_response['data'])) {
        $orders = $orders_response['data'];
        $pagination = [
            'current_page' => $orders_response['current_page'],
            'last_page'    => $orders_response['last_page']
        ];
    }
?>

<?php if (empty($orders)): ?>
    <div class="order-content">
        سفارشی وجود ندارد.
    </div>
<?php else: ?>

    <table class="orders">
        <thead>
            <tr>
                <th>شناسه سفارش</th>
                <th>تاریخ</th>
                <th>وضعیت</th>
                <th>مبلغ کل</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $order): ?>
            <tr>
                <td><?php echo esc_html($order['order_code']); ?></td>
                
                <td style="direction:ltr"><?php echo date('Y/m/d', strtotime($order['created_at'])); ?></td>
                
                <td>
                    <?php 
                        // ترجمه ساده وضعیت‌ها
                        $statuses = [
                            'pending' => 'در حال پردازش',
                            'completed' => 'تکمیل شده',
                            'cancelled' => 'لغو شده'
                        ];
                        echo isset($statuses[$order['status']]) ? $statuses[$order['status']] : $order['status'];
                    ?>
                </td>
                
                <td><?php echo number_format($order['total']); ?> تومان</td>
                
                <td>
                    <a href="<?php echo esc_url( add_query_arg( ['tab' => 'view-order', 'id' => $order['id']], $base_url ) ); ?>" class="view-btn">
                       مشاهده سفارش
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>

    <?php if($pagination['last_page'] > 1): ?>
    <div class="pagination-container">
        <div class="pagination">
            <?php for($i = 1; $i <= $pagination['last_page']; $i++): ?>
                <a href="<?php echo esc_url(add_query_arg(['tab'=>'orders', 'page_num'=>$i], $base_url)); ?>" 
                   class="page <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                   <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>

<?php endif; ?>