<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header('single');
?>
<!-- Main -->
<main class="main-content search-main">
    <?php  wbsLoadSearch(); ?>
</main>

<?php get_footer(); ?>
