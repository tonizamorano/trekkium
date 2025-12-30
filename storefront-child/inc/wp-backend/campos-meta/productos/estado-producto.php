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

    $product_id = $product->get_id();

    // Estado anterior (antes de procesar la petición)
    $old_estado = get_post_meta( $product_id, 'estado_producto', true );

    // Determinar nuevo estado desde POST (si viene) o dejar el existente
    if ( isset( $_POST['estado_producto'] ) ) {
        $new_estado = sanitize_text_field( wp_unslash( $_POST['estado_producto'] ) );
        $product->update_meta_data( 'estado_producto', $new_estado );
    } else {
        $new_estado = get_post_meta( $product_id, 'estado_producto', true );
    }

    // Si existe una solicitud de cambio de tipo "Cancelación" y la solicitud está "Aprobada",
    // forzamos el estado del producto a "cancelado".
    $tipo_cambio = isset( $_POST['tipo_cambio'] ) ? sanitize_text_field( wp_unslash( $_POST['tipo_cambio'] ) ) : get_post_meta( $product_id, 'tipo_cambio', true );
    $estado_solicitud = isset( $_POST['estado_solicitud'] ) ? sanitize_text_field( wp_unslash( $_POST['estado_solicitud'] ) ) : get_post_meta( $product_id, 'estado_solicitud', true );

    if ( $tipo_cambio === 'Cancelación' && $estado_solicitud === 'Aprobada' ) {
        $new_estado = 'cancelado';
        $product->update_meta_data( 'estado_producto', 'cancelado' );
    }

    // Si hemos cambiado a "cancelado" (y antes no estaba cancelado),
    // cancelar todos los pedidos que contengan este producto y poner el producto en borrador.
    if ( $new_estado === 'cancelado' && $old_estado !== 'cancelado' ) {
        // Obtener pedidos (puede ser costoso si hay muchos; intentamos usar la API de WC)
        $orders = array();
        if ( function_exists( 'wc_get_orders' ) ) {
            try {
                $orders = wc_get_orders( array( 'limit' => -1 ) );
            } catch ( Exception $e ) {
                $orders = array();
            }
        } elseif ( class_exists( 'WC_Order_Query' ) ) {
            try {
                $q = new WC_Order_Query( array( 'limit' => -1 ) );
                $orders = $q->get_orders();
            } catch ( Exception $e ) {
                $orders = array();
            }
        }

        if ( ! empty( $orders ) ) {
            foreach ( $orders as $order ) {
                // Aceptamos tanto objetos WC_Order como IDs
                if ( is_numeric( $order ) ) {
                    $order = wc_get_order( $order );
                }
                if ( ! $order ) {
                    continue;
                }

                $items = $order->get_items();
                foreach ( $items as $item ) {
                    $item_product_id = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : ( isset( $item['product_id'] ) ? $item['product_id'] : null );
                    if ( $item_product_id && intval( $item_product_id ) === intval( $product_id ) ) {
                        // Cambiar estado del pedido a 'cancelled'
                        try {
                            $order->update_status( 'cancelled', sprintf( 'Pedido cancelado automáticamente porque el producto %d fue marcado como cancelado.', $product_id ) );
                        } catch ( Exception $e ) {
                            error_log( 'Error cambiando estado de pedido ' . $order->get_id() . ': ' . $e->getMessage() );
                        }
                        // No necesitamos seguir comprobando items de este pedido
                        break;
                    }
                }
            }
        }

        // Forzar estado del producto a 'draft'
        try {
            if ( method_exists( $product, 'set_status' ) ) {
                $product->set_status( 'draft' );
                $product->save();
            } else {
                wp_update_post( array( 'ID' => $product_id, 'post_status' => 'draft' ) );
            }
        } catch ( Exception $e ) {
            error_log( 'Error poniendo el producto ' . $product_id . ' en borrador: ' . $e->getMessage() );
        }
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