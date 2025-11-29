<?php
/* Template Name: single-product  */

if (!defined('ABSPATH')) {
    exit;
}
get_header('single');
?>
<!-- Main -->
<main class="main-content singleProduct-main">
    <?php wbsLoadSingleProduct(); ?>
</main>
<?php get_footer(); ?>
