<?php
/**
 * Añadir campo "estado_producto" en la edición de productos WooCommerce
 */
add_action( 'woocommerce_product_options_general_product_data', function () {

    woocommerce_wp_select( array(
        'id'          => 'estado_producto',
        'label'       => 'Estado del producto',
        'description' => 'Define el estado actual del producto.',
        'desc_tip'    => true,
        'options'     => array(
            'activo'     => 'Activo',
            'cancelado'  => 'Cancelado',
            'finalizado' => 'Finalizado',
        ),
    ) );

} );

/**
 * Guardar el campo "estado_producto"
 */
add_action( 'woocommerce_admin_process_product_object', function ( $product ) {

    if ( isset( $_POST['estado_producto'] ) ) {
        $product->update_meta_data(
            'estado_producto',
            sanitize_text_field( $_POST['estado_producto'] )
        );
    }

} );

/**
 * Mostrar columna "Estado" en el listado de productos
 */

add_filter( 'manage_edit-product_columns', function ( $columns ) {

    $columns['estado_producto'] = 'Estado';

    return $columns;
} );

/**
 * Rellenar columna "Estado" en el listado de productos
 */

add_action( 'manage_posts_custom_column', function ( $column, $post_id ) {

    if ( $column !== 'estado_producto' ) {
        return;
    }

    $estado = get_post_meta( $post_id, 'estado_producto', true );

    if ( ! $estado ) {
        echo '—';
        return;
    }

    $labels = array(
        'activo'     => 'Activo',
        'cancelado'  => 'Cancelado',
        'finalizado' => 'Finalizado',
    );

    echo esc_html( $labels[ $estado ] ?? $estado );

}, 10, 2 );


