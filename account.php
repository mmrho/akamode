<?php
/* Template Name: account */

if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<!-- Main -->
<main class="main-content account-main">
    <?php wbsLoadAccount(); ?>
</main>
<?php get_footer(); ?>