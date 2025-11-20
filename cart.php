<?php
/* Template Name: cart */

if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<!-- Main -->
<main class="main-content cart-main">
    <?php wbsLoadCart(); ?>
</main>
<?php get_footer(); ?>