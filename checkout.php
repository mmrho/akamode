<?php
/* Template Name: checkout */

if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<!-- Main -->
<main class="main-content checkout-main">
    <?php wbsLoadCheckout(); ?>
</main>
<?php get_footer(); ?>