<?php
$user = get_query_var('user_data');
?>
<div class="details-content">
    <form method="post" id="profile-details-form" novalidate>
        <?php wp_nonce_field('wbs_profile_update'); ?>
        <input type="hidden" name="wbs_action" value="update_profile">

        <div>
            <label style="font-size:12px; color:#666;">نام و نام خانوادگی</label>
            <input type="text" name="account_name" class="wbs-required" data-name="نام و نام خانوادگی" value="<?php echo esc_attr($user['name'] ?? ''); ?>" placeholder="نام *">
        </div>

        <div style="margin-top:15px;">
            <label style="font-size:12px; color:#666;">ایمیل</label>
            <input type="email" name="account_email" value="<?php echo esc_attr($user['email'] ?? ''); ?>" placeholder="ایمیل">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profile-details-form');
    if(profileForm) {
        profileForm.addEventListener('submit', function(e) {
            let hasError = false;
            let firstErrorField = null;
            let errorMsg = "";

            const requiredFields = profileForm.querySelectorAll('.wbs-required');

            requiredFields.forEach(function(field) {
                if (field.value.trim() === "") {
                    if (!hasError) {
                        hasError = true;
                        firstErrorField = field;
                        errorMsg = "لطفا فیلد " + field.getAttribute('data-name') + " را تکمیل کنید.";
                    }
                    field.style.borderColor = "red";
                } else {
                    field.style.borderColor = "#ddd";
                }
            });

            if (hasError) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'خطا',
                    text: errorMsg,
                    confirmButtonText: 'باشه',
                    target: 'body',
                    customClass: { container: 'wbs-popup-panel' }
                });
                if(firstErrorField) firstErrorField.focus();
            }
        });
    }
});
</script>