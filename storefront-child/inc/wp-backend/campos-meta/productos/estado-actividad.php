<?php
/**
 * ESTADO AUTOMÁTICO DE ACTIVIDAD (WooCommerce)
 * - Lee estado_producto (manual)
 * - Calcula estado_actividad + mensaje_actividad (automático)
 */

/* ---------------------------------------------------------
 * META BOX SOLO LECTURA EN ADMIN
 * --------------------------------------------------------- */
add_action( 'add_meta_boxes', function () {

    add_meta_box(
        'estado_actividad_box',
        'Estado de la actividad',
        'mostrar_estado_actividad_meta_box',
        'product',
        'side',
        'high'
    );

} );

function mostrar_estado_actividad_meta_box( $post ) {

    $estado  = get_post_meta( $post->ID, 'estado_actividad', true ) ?: 'Sin definir';
    $mensaje = get_post_meta( $post->ID, 'mensaje_actividad', true ) ?: '';

    echo '<input type="text" value="' . esc_attr( $estado ) . '" readonly
        style="width:100%; background:#f8f8f8; border:1px solid #ccc; margin-bottom:6px;" />';

    if ( $mensaje ) {
        echo '<textarea readonly
            style="width:100%; background:#f8f8f8; border:1px solid #ccc;">'
            . esc_textarea( $mensaje ) .
        '</textarea>';
    }
}

/* ---------------------------------------------------------
 * FUNCIÓN PRINCIPAL
 * --------------------------------------------------------- */
function actualizar_estado_actividad( $product_id ) {

    if ( wp_is_post_revision( $product_id ) || wp_is_post_autosave( $product_id ) ) {
        return;
    }

    $post = get_post( $product_id );
    if ( ! $post || $post->post_type !== 'product' ) {
        return;
    }

    static $doing = false;
    if ( $doing ) return;
    $doing = true;

    /* --- DATOS --- */
    $estado_producto = get_post_meta( $product_id, 'estado_producto', true );
    $post_status     = $post->post_status;

    $plazas_totales = (int) get_post_meta( $product_id, 'plazas_totales', true );
    $plazas_min     = (int) get_post_meta( $product_id, 'plazas_minimas', true );
    $stock_total    = (int) get_post_meta( $product_id, '_stock', true );

    $plazas_ocupadas = max( 0, $plazas_totales - $stock_total );

    $estado  = 'Sin definir';
    $mensaje = '';

    /* -----------------------------------------------------
     * LÓGICA DE ESTADOS
     * ----------------------------------------------------- */

    // ESTADOS MANUALES (prioridad absoluta)
    if ( $estado_producto === 'cancelado' ) {

        $estado  = 'Cancelada';
        $mensaje = 'Esta actividad ha sido cancelada.';

    } elseif ( $estado_producto === 'finalizado' ) {

        $estado  = 'Finalizada';
        $mensaje = 'Esta actividad ha finalizado.';

    } elseif ( $estado_producto === 'activo' && $post_status === 'publish' ) {

        // ESTADOS AUTOMÁTICOS
        if ( $stock_total === 0 ) {

            $estado  = 'Completa';
            $mensaje = 'No quedan plazas disponibles, salida confirmada.';

        } elseif ( $plazas_ocupadas < $plazas_min ) {

            $estado  = 'Plazas disponibles';
            $mensaje = 'Grupo mínimo insuficiente, salida sin confirmar.';

        } elseif ( $stock_total <= ( $plazas_totales - $plazas_min ) ) {

            $estado  = 'Salida confirmada';
            $mensaje = 'Grupo mínimo suficiente, salida confirmada.';

        } else {

            $estado  = 'Últimas plazas';
            $mensaje = 'Quedan pocas plazas, salida confirmada.';
        }
    }

    update_post_meta( $product_id, 'estado_actividad', $estado );
    update_post_meta( $product_id, 'mensaje_actividad', $mensaje );

    $doing = false;
}

/* ---------------------------------------------------------
 * HOOKS CORRECTOS
 * --------------------------------------------------------- */

// Al guardar el producto (después de guardar metas Woo)
add_action( 'woocommerce_process_product_meta', function( $post_id ) {
    actualizar_estado_actividad( $post_id );
}, 20 );

// Al cambiar stock
add_action( 'woocommerce_product_set_stock', function ( $product ) {
    actualizar_estado_actividad( $product->get_id() );
} );

add_action( 'woocommerce_variation_set_stock', function ( $product ) {
    actualizar_estado_actividad( $product->get_id() );
} );

// Al cambiar estado de pedido
add_action( 'woocommerce_order_status_changed', function ( $order_id ) {

    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    foreach ( $order->get_items() as $item ) {
        actualizar_estado_actividad( $item->get_product_id() );
    }

}, 20 );

/* ---------------------------------------------------------
 * MOSTRAR EN FRONTEND
 * --------------------------------------------------------- */
add_action( 'woocommerce_single_product_summary', function () {

    global $post;

    $estado  = get_post_meta( $post->ID, 'estado_actividad', true );
    $mensaje = get_post_meta( $post->ID, 'mensaje_actividad', true );

    if ( ! $estado ) return;

    echo '<div class="estado-actividad" style="margin-top:15px;">
        <strong>Estado de la actividad:</strong> ' . esc_html( $estado ) . '<br>
        <span>' . esc_html( $mensaje ) . '</span>
    </div>';

}, 20 );
