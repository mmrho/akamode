<?php

if (!defined('ABSPATH')) {
    exit;
}
get_header('single');
?>
<!-- Main -->
<main class="main-content blog-single">
    <?php wbsLoadBlogSingle(); ?>
</main>
<?php get_footer(); ?>