<?php
/* Template Name: faq */

if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<!-- Main -->
<main class="main-content faq-main">
    <?php wbsLoadFaq(); ?>
</main>
<?php get_footer(); ?>