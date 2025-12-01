<?php
/**
 * Plantilla personalizada para mostrar un single product reducido
 *
 * Solo muestra el header normal, un shortcode y el footer normal.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header(); // Header normal

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        echo do_shortcode('[pagina_single_productos]');
    endwhile;
endif;

get_footer(); // Footer normal
