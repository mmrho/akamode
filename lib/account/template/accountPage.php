<?php
// detect what tab is active
$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';

// url of this page
$base_url = get_permalink(); 
?>

<!-- modal -->
 <div class="modal-container">
    <div class="modal">
        <div class="top">
            <div>خروج از حساب کاربری</div>
            <span class="times">&times;</span>
        </div>
        <div class="message">
            آیا برای خروج از حساب کاربری خود اطمینان دارید؟
        </div>
        <div class="buttons">
            <button class="close">انصراف</button>
            <a href="#" class="logout">خروج</a>
        </div>
    </div>
 </div>

<div class="container">
    <div class="main">

        <div class="top">
            <h1>حساب کاربری</h1>
            <p>سفارش های شما</p>
        </div>
        
        <?php 
        // --- SCENARIO 1: VIEWING A SINGLE ORDER (No Tabs) ---
        if ($active_tab == 'view-order') : 
            $back_url = add_query_arg('tab', 'orders', $base_url);
        ?>

            <div class="single-view-container">
                

                <div class="single-view-body">
                    <?php get_template_part('template-parts/account/content-view-order'); ?>
                </div>
            </div>

        <?php 
        // --- SCENARIO 2: STANDARD TABS VIEW ---
        else : 
        ?>

            

            <div class="tabs-container">
                <div class="tabs">
                    <a href="<?php echo esc_url($base_url); ?>" 
                       class="tab <?php echo ($active_tab == 'dashboard') ? 'active' : ''; ?>">
                        داشبورد
                    </a>
                    <a href="<?php echo esc_url(add_query_arg('tab', 'orders', $base_url)); ?>" 
                       class="tab <?php echo ($active_tab == 'orders') ? 'active' : ''; ?>">
                        سفارش ها
                    </a>
                    <a href="<?php echo esc_url(add_query_arg('tab', 'address', $base_url)); ?>" 
                       class="tab <?php echo ($active_tab == 'address') ? 'active' : ''; ?>">
                        آدرس ها
                    </a>
                    <a href="<?php echo esc_url(add_query_arg('tab', 'details', $base_url)); ?>" 
                       class="tab <?php echo ($active_tab == 'details') ? 'active' : ''; ?>">
                       جزئیات اکانت
                    </a>
                    <a href="#" class="tab logout">خروج</a>
                </div>

                <div class="body">
                <?php
                    if ($active_tab == 'orders') {
                        get_template_part('template-parts/account/content-orders');
                    } elseif ($active_tab == 'address') {
                        get_template_part('template-parts/account/content-address');
                    } elseif ($active_tab == 'details') {
                        get_template_part('template-parts/account/content-details');
                    } else {
                        get_template_part('template-parts/account/content-dashboard');
                    }
                ?>
                </div>
            </div>

        <?php endif; // End of main If/Else ?>
        
    </div>
</div>