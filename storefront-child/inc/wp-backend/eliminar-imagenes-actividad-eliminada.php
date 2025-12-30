<?php

/**
 * Eliminar imágenes asociadas a un producto cuando este es eliminado.
 */


add_action( 'before_delete_post', 'trekkium_delete_product_images', 10, 1 );

function trekkium_delete_product_images( $post_id ) {

    // Asegurarnos de que es un producto
    if ( get_post_type( $post_id ) !== 'product' ) {
        return;
    }

    // 1. Imagen destacada
    $featured_image_id = get_post_thumbnail_id( $post_id );
    if ( $featured_image_id ) {
        wp_delete_attachment( $featured_image_id, true );
    }

    // 2. Imágenes de la galería
    $product = wc_get_product( $post_id );
    if ( $product ) {
        $gallery_image_ids = $product->get_gallery_image_ids();

        if ( ! empty( $gallery_image_ids ) ) {
            foreach ( $gallery_image_ids as $image_id ) {
                wp_delete_attachment( $image_id, true );
            }
        }
    }
}
