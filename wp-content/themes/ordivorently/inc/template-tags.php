<?php
/**
 * Custom template tags for Ordivorently
 */

if ( ! function_exists( 'ordivorently_posted_on' ) ) {
    function ordivorently_posted_on() {
        printf( '<span class="posted-on">%s</span>',
            esc_html( get_the_date() )
        );
    }
}
