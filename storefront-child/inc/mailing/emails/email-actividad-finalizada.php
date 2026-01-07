<?php
// Enviar email al cliente cuando el pedido pasa a estado "finalizado"
// realmente cuándo se produce? la function ya es llamada desde el hook de trekkium_estado_producto_meta_changed
// add_action( 'woocommerce_order_status_finalizado', 'trekkium_enviar_email_reserva_finalizada', 10, 1 );

function trekkium_write_log( $component, $message, $level = 'INFO' ) {
    $base = get_stylesheet_directory();
    // Directorio unificado para logs (solo web path): storefront-child/logs
    $log_dir = $base . '/logs';

    if ( ! is_dir( $log_dir ) ) {
        if ( function_exists( 'wp_mkdir_p' ) ) {
            @wp_mkdir_p( $log_dir );
        } else {
            @mkdir( $log_dir, 0755, true );
        }
    }

    $line = sprintf( "[%s] [%s] %s\n", date( 'Y-m-d H:i:s' ), $level, $message );

    if ( is_dir( $log_dir ) && is_writable( $log_dir ) ) {
        $file = $log_dir . '/' . $component . '-' . strtolower( $level ) . '-' . date( 'Y-m-d' ) . '.log';
        error_log( $line, 3, $file );
    } else {
        // Fallback a error_log del sistema
        error_log( $line );
    }
}

function trekkium_enviar_email_reserva_finalizada( $order_id ) {
    try {
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            trekkium_write_log( 'email-finalizados', "Pedido {$order_id} no encontrado.", 'ERROR' );
            return;
        }

        // Datos cliente
        $cliente_nombre = $order->get_billing_first_name();
        $cliente_email  = $order->get_billing_email();

        // Tomar primer item del pedido
        $items = $order->get_items();
        if ( empty( $items ) ) {
            trekkium_write_log( 'email-finalizados', "Pedido {$order_id} sin items.", 'ERROR' );
            return;
        }
        $item = array_shift( $items );
        $product_id = $item->get_product_id();
        $product = wc_get_product( $product_id );

        trekkium_write_log( 'email-finalizados', "Enviar email: pedido={$order_id}, cliente={$cliente_email}, producto={$product_id}" );

        $actividad_nombre = $item->get_name();
        $fecha_meta = $product ? $product->get_meta('fecha') : '';
        $hora = $product ? $product->get_meta('hora') : 'No disponible';

        // Formatear fecha legible
        $fecha_formateada = 'No disponible';
        if ( $fecha_meta ) {
            $timestamp = strtotime( (string) $fecha_meta );
            if ( $timestamp !== false ) {
                $meses = array(
                    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
                );
                $dia = date( 'j', $timestamp );
                $mes_num = date( 'n', $timestamp );
                $ano = date( 'Y', $timestamp );
                $fecha_formateada = $dia . ' de ' . $meses[ $mes_num ] . ' de ' . $ano;
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

        if ( function_exists( 'trekkium_get_email_html' ) ) {
            $final_email_content = trekkium_get_email_html( $email_title, $email_content );
        } else {
            $final_email_content = '<html><body>' . $email_content . '</body></html>';
        }

        $headers = array( 'Content-Type: text/html; charset=UTF-8' );

        trekkium_write_log( 'email-finalizados', "Intentando enviar email a {$cliente_email} para pedido {$order_id}" );
        $sent = false;
        try {
            $sent = wp_mail( $cliente_email, $email_title, $final_email_content, $headers );
        } catch ( Throwable $e ) {
            trekkium_write_log( 'email-finalizados', "Excepción en wp_mail para pedido {$order_id}: " . $e->getMessage(), 'ERROR' );
        }

        if ( $sent ) {
            trekkium_write_log( 'email-finalizados', 'Trekkium: email de valoración enviado a ' . $cliente_email );
        } else {
            trekkium_write_log( 'email-finalizados', 'Trekkium: fallo al enviar email de valoración a ' . $cliente_email, 'ERROR' );
        }

    } catch ( Throwable $e ) {
        trekkium_write_log( 'email-finalizados', 'Excepción en trekkium_enviar_email_reserva_finalizada: ' . $e->getMessage(), 'ERROR' );
        if ( method_exists( $e, 'getTraceAsString' ) ) {
            trekkium_write_log( 'email-finalizados', $e->getTraceAsString(), 'ERROR' );
        }
    }
}


/**
 * Cuando el campo meta `estado_producto` pase a `finalizado`,
 * enviar email SOLO a los clientes con pedidos COMPLETED
 * que contengan ese producto.
 */
add_action( 'updated_postmeta', 'trekkium_estado_producto_meta_changed', 10, 4 );
function trekkium_estado_producto_meta_changed( $meta_id, $post_id, $meta_key, $meta_value ) {

    // Solo cuando cambia estado_producto a finalizado
    if ( $meta_key !== 'estado_producto' ) return;
    if ( strtolower($meta_value) !== 'finalizado' ) return;

    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'product' ) return;

    // Obtener pedidos COMPLETED recientes y filtrar por fecha y por si contienen este producto.
    // Evitamos pasar los parámetros de fecha directamente a WC_Order_Query porque
    // algunas versiones de WooCommerce pueden procesarlos y llamar a strtotime() con valores no esperados.
    $after  = ( new DateTime( '-2 days' ) )->setTimezone( wp_timezone() );
    $before = ( new DateTime( '-12 hours' ) )->setTimezone( wp_timezone() );

    $after_ts  = $after->getTimestamp();
    $before_ts = $before->getTimestamp();

    // Traer pedidos completed recientes (objetos) y luego filtrar en PHP por fecha y producto
    $orders = wc_get_orders( array(
        'status' => 'completed',
        'limit'  => 50,
        'return' => 'objects',
    ) );

    // Logging: registrar número de pedidos recuperados
    trekkium_write_log( 'email-finalizados', "trekkium_estado_producto_meta_changed: producto={$post_id} - pedidos recuperados: " . count( $orders ) );

    if ( empty( $orders ) ) return;

    $emails_sent = 0;
    foreach ( $orders as $order ) {
        // Fecha de finalización del pedido (puede ser WC_DateTime / DateTime o null)
        $date_completed = $order->get_date_completed();
        if ( ! $date_completed ) {
            continue;
        }

        if ( is_object( $date_completed ) && method_exists( $date_completed, 'getTimestamp' ) ) {
            $completed_ts = $date_completed->getTimestamp();
        } else {
            $completed_ts = strtotime( (string) $date_completed );
        }

        if ( $completed_ts === false ) {
            continue;
        }

        // Solo pedidos cuya fecha de completion esté entre after y before
        if ( $completed_ts < $after_ts || $completed_ts > $before_ts ) {
            continue;
        }

        // Comprobar items del pedido para ver si contienen este producto
        foreach ( $order->get_items() as $item ) {
            if ( (int) $item->get_product_id() === (int) $post_id ) {
                try {
                    trekkium_enviar_email_reserva_finalizada( $order->get_id() );
                    $emails_sent++;
                } catch ( Throwable $e ) {
                    trekkium_write_log( 'email-finalizados', 'Trekkium: error enviando email de valoración para pedido ' . $order->get_id() . ' - ' . $e->getMessage(), 'ERROR' );
                }

                break; // evitar doble envío por pedido
            }
        }
    }

    trekkium_write_log( 'email-finalizados', "trekkium_estado_producto_meta_changed: emails enviados={$emails_sent} para producto={$post_id}" );
}