<?php
$user = get_query_var('user_data');
?>
<div class="details-content">
    <form method="post">
        <?php wp_nonce_field('wbs_profile_update'); ?>
        <input type="hidden" name="wbs_action" value="update_profile">

        <div>
            <label style="font-size:12px; color:#666;">نام و نام خانوادگی</label>
            <input type="text" name="account_name" value="<?php echo esc_attr($user['name'] ?? ''); ?>" placeholder="نام *">
        </div>
        
        <div style="margin-top:15px;">
            <label style="font-size:12px; color:#666;">ایمیل</label>
            <input type="email" name="account_email" value="<?php echo esc_attr($user['email'] ?? ''); ?>" placeholder="ایمیل *">
        </div>
        
        <div style="margin-top:15px;">
            <label style="font-size:12px; color:#666;">شماره موبایل (غیرقابل تغییر)</label>
            <input type="text" value="<?php echo esc_attr($user['mobile'] ?? ''); ?>" disabled style="opacity:0.6; cursor:not-allowed;">
        </div>

        <div style="margin-top:30px;">
            <input type="submit" value="ذخیره تغییرات" class="submit">
        </div>
    </form>
</div>