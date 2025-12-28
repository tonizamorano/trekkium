<?php
// Enviar email al cliente cuando el pedido pasa a estado "finalizado"
add_action( 'woocommerce_order_status_finalizado', 'trekkium_enviar_email_reserva_finalizada', 10, 1 );

function trekkium_enviar_email_reserva_finalizada( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    // Datos cliente
    $cliente_nombre = $order->get_billing_first_name();
    $cliente_email  = $order->get_billing_email();

    // Tomar primer item del pedido
    $items = $order->get_items();
    if ( empty( $items ) ) return;
    $item = array_shift($items);
    $product_id = $item->get_product_id();
    $product = wc_get_product( $product_id );

    $actividad_nombre = $item->get_name();
    $fecha_meta = $product ? $product->get_meta('fecha') : '';
    $hora = $product ? $product->get_meta('hora') : 'No disponible';

    // Formatear fecha legible
    $fecha_formateada = 'No disponible';
    if ( $fecha_meta ) {
        $timestamp = strtotime( $fecha_meta );
        if ( $timestamp !== false ) {
            $meses = array(
                1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
            );
            $dia = date('j', $timestamp);
            $mes_num = date('n', $timestamp);
            $ano = date('Y', $timestamp);
            $fecha_formateada = $dia . ' de ' . $meses[$mes_num] . ' de ' . $ano;
        }
    }

    // Generar token para formulario de satisfacción
    if ( function_exists('trekkium_generar_token_valoracion') ) {
        $token = trekkium_generar_token_valoracion( $order_id );
    } else {
        $token = '';
    }

    // URL al formulario de valoraciones
    $valoraciones_url = esc_url( site_url( '/valoraciones/' ) . '?pedido=' . $order_id . '&token=' . $token );

    // Contenido del email
    $email_content = '

    <div class="mail-content-contenedor">

        <div class="mail-content-seccion">
            <p style="margin-bottom:10px !important;">Hola <strong>' . esc_html( $cliente_nombre ) . '</strong>,</p>
            <p>Tu reserva <strong>Nº ' . $order->get_id() . '</strong> ha finalizado. Nos gustaría conocer tu opinión sobre la actividad y el/la guía.</p>
        </div>

        <div class="mail-content-seccion">
            <h4>Actividad</h4>
            <p style="font-weight:600;">' . esc_html( $actividad_nombre ) . '</p>
            <p>' . esc_html( $fecha_formateada ) . ' a las ' . esc_html( $hora ) . ' h</p>
            <a href="' . $valoraciones_url . '" class="mail-button" target="_blank">Formulario de satisfacción</a>
        </div>

        <div class="mail-content-seccion">
            <p>Tu opinión nos ayuda a mejorar. Gracias por dedicar unos minutos.</p>
            <p>—<br>El equipo de Trekkium</p>
        </div>

    </div>

    ';

    $email_title = 'RESERVA FINALIZADA - VALORACIÓN';

    // Generar HTML con plantilla
    $helpers_path = dirname(__DIR__) . '/mailing-functions.php';
    if ( file_exists( $helpers_path ) ) {
        include_once $helpers_path;
    }

    if ( function_exists('trekkium_get_email_html') ) {
        $final_email_content = trekkium_get_email_html( $email_title, $email_content );
    } else {
        $final_email_content = '<html><body>' . $email_content . '</body></html>';
    }

    $headers = array( 'Content-Type: text/html; charset=UTF-8' );

    if ( ! wp_mail( $cliente_email, $email_title, $final_email_content, $headers ) ) {
        error_log( 'Trekkium: fallo al enviar email de valoración a ' . $cliente_email );
    } else {
        error_log( 'Trekkium: email de valoración enviado a ' . $cliente_email );
    }
}


/**
 * Cuando el campo meta `estado_producto` pase a `finalizado`, buscar pedidos
 * que contengan ese producto y enviar el email de valoración a cada cliente.
 */
add_action( 'updated_postmeta', 'trekkium_estado_producto_meta_changed', 10, 4 );
function trekkium_estado_producto_meta_changed( $meta_id, $post_id, $meta_key, $meta_value ) {
    if ( $meta_key !== 'estado_producto' ) return;
    if ( $meta_value !== 'finalizado' ) return;

    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'product' ) return;

    // Buscar pedidos donde aparezca este producto. Consideramos estados comunes de reservas.
    $orders = wc_get_orders( array(
        'limit'  => -1,
        'status' => array( 'processing', 'completed', 'on-hold' ),
    ) );

    if ( empty( $orders ) ) return;

    foreach ( $orders as $order ) {
        $items = $order->get_items();
        foreach ( $items as $item ) {
            $product_id = $item->get_product_id();
            if ( $product_id == $post_id ) {
                try {
                    trekkium_enviar_email_reserva_finalizada( $order->get_id() );
                } catch ( Exception $e ) {
                    error_log( 'Trekkium: error enviando email de valoración para pedido ' . $order->get_id() . ' - ' . $e->getMessage() );
                }
                break;
            }
        }
    }
}
