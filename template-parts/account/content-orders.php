<?php 
    // Ensure base_url is defined in this file too
    $base_url = get_permalink(); 
    $order_id = '23234234'; // This would come from your database loop normally
?>

<div class="order-content">
    سفارشی وجود ندارد.
</div>

<br>

<table class="orders">
    <thead>
        <tr>
            <th>شناسه سفارش</th>
            <th>تاریخ</th>
            <th>وضعیت</th>
            <th>مبلغ</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>23234234</td>
            <td>۱ آبان ۱۴۰۴</td>
            <td>در حال پردازش</td>
            <td>مبلغ</td>
            <td><a href="<?php echo esc_url( add_query_arg( ['tab' => 'view-order', 'id' => $order_id], $base_url ) ); ?>" 
           class="view-btn">
           مشاهده سفارش
        </a></td>
        </tr>
    </tbody>
</table>

<br>
<br>

<div class="pagination-container">
    <div class="pagination">
        <div class="page active">1</div>
        <div class="page">2</div>
        <div class="page">></div>
    </div>
</div>
