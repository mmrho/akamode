<?php
$user = get_query_var('user_data');
$base_url = get_permalink();
?>
<p>
    سلام <?php echo isset($user['name']) ? esc_html($user['name']) : 'کاربر عزیز'; ?>!
    (<?php echo isset($user['name']) ? esc_html($user['name']) : ''; ?> نیستید؟)
    <a href="#" class="logout-link" onclick="document.querySelector('.tab.logout').click(); return false;">خارج شوید!</a>
</p>
<p>
    شما در حساب کاربری خود میتوانید
    <a href="<?php echo esc_url(add_query_arg('tab', 'orders', $base_url)); ?>">لیست سفارش ها</a>
    را ببینید و یا
    <a href="<?php echo esc_url(add_query_arg('tab', 'address', $base_url)); ?>">آدرس ها</a>
    را مدیریت کنید. همچنین میتوانید
    <a href="<?php echo esc_url(add_query_arg('tab', 'details', $base_url)); ?>">اطلاعات اکانت</a>
    خود را مدیریت کنید.
</p>