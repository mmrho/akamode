<?php
/* Template Name: blog */

if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<!-- Main -->
<main class="main-content blog-main">
    <?php wbsLoadBlog(); ?>
</main>
<?php get_footer(); ?>