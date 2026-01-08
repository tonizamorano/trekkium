<?php
/**
 * Procesa un producto cuyo estado ha pasado a "finalizado":
 * - Busca pedidos COMPLETED de ese producto
 * - Envía email de valoración
 * - Marca el pedido para evitar duplicados
 */
function procesar_estado_producto_finalizado( int $product_id ) {

    $orders = trekkium_get_orders_finalizado( $product_id );

    if ( empty( $orders ) ) {
        trekkium_write_log(
            'email-finalizados',
            "procesar_estado_producto_finalizado: producto={$product_id} - sin pedidos encontrados"
        );
        return;
    }

    $emails_sent = 0;

    foreach ( $orders as $order ) {

        // Evitar duplicados
        if ( $order->get_meta( 'email_valoracion_enviado' ) ) {
            trekkium_write_log(
                'email-finalizados',
                "Pedido {$order->get_id()} ya tiene email_valoracion_enviado, se omite."
            );
            continue;
        }

        foreach ( $order->get_items() as $item ) {
            if ( (int) $item->get_product_id() === $product_id ) {

                try {
                    trekkium_enviar_email_reserva_finalizada( $order->get_id() );

                    // Marcar pedido
                    $order->update_meta_data( 'email_valoracion_enviado', true );
                    $order->save();

                    $emails_sent++;

                } catch ( Throwable $e ) {
                    trekkium_write_log(
                        'email-finalizados',
                        "Error enviando email pedido {$order->get_id()}: " . $e->getMessage(),
                        'ERROR'
                    );
                }

                break; // un email por pedido
            }
        }
    }

    trekkium_write_log(
        'email-finalizados',
        "procesar_estado_producto_finalizado: producto={$product_id} - emails enviados={$emails_sent}"
    );
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

add_action( 'updated_postmeta', 'trekkium_estado_producto_meta_changed', 10, 4 );
function trekkium_estado_producto_meta_changed( $meta_id, $post_id, $meta_key, $meta_value ) {

    if ( $meta_key !== 'estado_producto' ) return;
    if ( strtolower( $meta_value ) !== 'finalizado' ) return;

    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'product' ) return;

    // Comprobar valor anterior
    $prev_value = get_metadata_by_mid( 'post', $meta_id );
    if (
        $prev_value &&
        isset( $prev_value->meta_value ) &&
        strtolower( (string) $prev_value->meta_value ) === 'finalizado'
    ) {
        // No hay cambio real
        return;
    }

    trekkium_write_log(
        'email-finalizados',
        "Hook updated_postmeta: producto={$post_id} pasó a finalizado"
    );

    procesar_estado_producto_finalizado( (int) $post_id );
}


/**
 * Recupera pedidos COMPLETED para un producto dentro de la ventana
 * relativa al inicio de la actividad (-2 días a +10 horas).
 * Siempre paginado.
 */
function trekkium_get_orders_finalizado( int $product_id ) {

    $component = 'email-finalizados';

    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        trekkium_write_log( $component, "Producto {$product_id} no encontrado.", 'ERROR' );
        return array();
    }

    $fecha = $product->get_meta( 'fecha' );
    $hora  = $product->get_meta( 'hora' ) ?: '00:00';

    if ( ! $fecha ) {
        trekkium_write_log( $component, "Producto {$product_id} sin meta fecha.", 'ERROR' );
        return array();
    }

    $tz = wp_timezone();
    try {
        $start = new DateTime( "{$fecha} {$hora}", $tz );
    } catch ( Throwable $e ) {
        trekkium_write_log( $component, "Fecha/hora inválida producto {$product_id}.", 'ERROR' );
        return array();
    }

    $after  = ( clone $start )->modify( '-2 days' );
    $before = ( clone $start )->modify( '+10 hours' );

    $after_str  = $after->format( 'Y-m-d H:i:s' );
    $before_str = $before->format( 'Y-m-d H:i:s' );

    $per_page = 50;
    $page     = 1;
    $found    = array();

    while ( true ) {

        $orders = wc_get_orders( array(
            'status'         => 'completed',
            'limit'          => $per_page,
            'page'           => $page,
            'date_completed' => array(
                'after'  => $after_str,
                'before' => $before_str,
            ),
            'return' => 'objects',
        ) );

        if ( empty( $orders ) ) {
            break;
        }

        foreach ( $orders as $order ) {
            $items = $order->get_items();
            if ( empty( $items ) ) continue;

            $item = array_shift( $items );
            if ( (int) $item->get_product_id() === $product_id ) {
                $found[ $order->get_id() ] = $order;
            }
        }

        // Si la página devuelve menos que el límite, no hay más
        if ( count( $orders ) < $per_page ) {
            break;
        }

        $page++;
    }

    trekkium_write_log(
        $component,
        "trekkium_get_orders_finalizado: producto={$product_id} pedidos=" . count( $found )
    );

    return array_values( $found );
}