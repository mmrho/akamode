<?php
/* Template Name: 404 */

if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<!-- Main -->
<main class="main-content the404-main">
    <?php wbsLoad404(); ?>
</main>
<?php get_footer(); ?>