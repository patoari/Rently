<?php
/**
 * Single template for properties and other post types
 */
get_header();
?>
<main>
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            if ( 'property' === get_post_type() ) {
                get_template_part( 'template-parts/content', 'single-property' );
            } else {
                the_title( '<h1>', '</h1>' );
                the_content();
            }
        endwhile;
    endif;
    ?>
</main>
<?php
get_footer();
