<?php
/* Template Name: login */
if (is_user_logged_in()) {
    wp_redirect(home_url('/userdashboard/'));
    exit();
}
?>

<div class="wrapper">
    <?php get_header('login'); ?>
    <main id="site-main">
        <div class="container-fluid loginContainer">
            <?php wbsLoadLoginForm(); ?>
        </div>
    </main>
    <?php get_footer('login'); ?>
</div>