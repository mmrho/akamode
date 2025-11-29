<?php

if (!defined('ABSPATH')) {
    exit;
}
get_header('single');
?>
<!-- Main -->
<main class="main-content category-main">
    <?php wbsLoadCategory(); ?>
</main>

<?php get_footer(); ?>
