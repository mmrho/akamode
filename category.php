<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// 1. Define template selection logic BEFORE the loop (Efficiency improvement)
$category = get_queried_object();
$slug     = $category->slug;

// Define the path to your template parts
$template_path    = get_template_directory() . '/category-parts/';
$template_to_load = '';

// Check for a custom template based on the current category slug
if ( file_exists( $template_path . $slug . '.php' ) ) {
    $template_to_load = $template_path . $slug . '.php';
} 
// If no custom template, check if the category has a parent and look for parent's template
elseif ( $category->parent ) {
    $parent = get_category( $category->parent );
    if ( file_exists( $template_path . $parent->slug . '.php' ) ) {
        $template_to_load = $template_path . $parent->slug . '.php';
    } else {
        // Fallback if parent template doesn't exist
        $template_to_load = $template_path . 'default.php';
    }
} 
// Fallback to default template for all other cases
else {
    $template_to_load = $template_path . 'default.php';
}

// 2. Load the selected template file
// IMPORTANT: The files inside 'category-parts/' must contain the WordPress Loop (while have_posts...)
if ( file_exists( $template_to_load ) ) {
    include $template_to_load;
} else {
    // Fallback UI if even default.php is missing
    if ( have_posts() ) : 
        echo '<ul>';
        while ( have_posts() ) : the_post();
            ?>
            <li>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                (<?php echo get_post_type(); ?>)
            </li>
            <?php
        endwhile; 
        echo '</ul>';
    else :
        echo '<p>No content found.</p>';
    endif;
}

get_footer();