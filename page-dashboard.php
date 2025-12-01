<?php
/* Template Name: User Dashboard */

if (!defined('ABSPATH')) exit;

if (!is_user_logged_in()) {
    // Redirect to YOUR custom login page instead of default WP login
    wp_redirect(home_url('/login/')); // فرض بر اینکه آدرس صفحه لاگین /login/ است
    exit;
}


get_header();
?>
<!-- Main -->
<main class="main-content account-main">
    <?php wbsLoadAccount(); ?>
</main>
<?php get_footer(); ?>