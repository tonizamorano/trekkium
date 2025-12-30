<?php
/**
 * Email de notificación de cancelación de actividad a reservas y autor
 * Se envía cuando el campo meta 'estado_producto' pasa a 'cancelado'.
 */

add_action( 'updated_postmeta', 'trekkium_enviar_email_cancelacion_actividad', 20, 4 );
function trekkium_enviar_email_cancelacion_actividad( $meta_id, $post_id, $meta_key, $meta_value ) {
    if ( $meta_key !== 'estado_producto' ) return;
    if ( $meta_value !== 'cancelado' ) return;

    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'product' ) return;

    // Obtener datos del producto
    $actividad_nombre = get_the_title( $post_id );
    $fecha_meta = get_post_meta( $post_id, 'fecha', true );
    $fecha_formateada = 'No disponible';
    if ( $fecha_meta ) {
        $timestamp = strtotime( $fecha_meta );
        if ( $timestamp !== false ) {
            $fecha_formateada = date('d/m/Y', $timestamp);
        }
    }

    // Motivos de la cancelación: usar el campo meta 'motivo_cambio' si el tipo de cambio es Cancelación y la solicitud está aprobada
    $tipo_cambio = get_post_meta($post_id, 'tipo_cambio', true);
    $estado_solicitud = get_post_meta($post_id, 'estado_solicitud', true);
    $motivos = '';
    if ($tipo_cambio === 'Cancelación' && $estado_solicitud === 'Aprobada') {
        $motivos = get_post_meta($post_id, 'motivo_cambio', true);
    }
    if (empty($motivos)) $motivos = 'No especificados.';

    // Obtener autor del producto
    $autor_id = $post->post_author;
    $autor = get_userdata( $autor_id );
    $autor_nombre = $autor ? $autor->display_name : 'Autor';
    $autor_email = $autor ? $autor->user_email : '';

    // Buscar todos los pedidos que contengan este producto, sin filtrar por estado
    $orders = wc_get_orders([
        'limit' => -1,
        'status' => array_keys(wc_get_order_statuses()), // todos los estados
    ]);
    $enviados = [];
    foreach ( $orders as $order ) {
        foreach ( $order->get_items() as $item ) {
            if ( (int) $item->get_product_id() === (int) $post_id ) {
                // Email al cliente
                trekkium_mail_cancelacion_actividad(
                    $order->get_billing_email(),
                    $order->get_billing_first_name(),
                    $actividad_nombre,
                    $fecha_formateada,
                    $motivos
                );
                $enviados[] = $order->get_billing_email();
                break;
            }
        }
    }
    // Email al autor (si no es uno de los clientes)
    if ( $autor_email && !in_array($autor_email, $enviados) ) {
        trekkium_mail_cancelacion_actividad(
            $autor_email,
            $autor_nombre,
            $actividad_nombre,
            $fecha_formateada,
            $motivos
        );
    }
}

/**
 * Envía el email de cancelación de actividad
 */
function trekkium_mail_cancelacion_actividad($dest_email, $nombre, $actividad, $fecha, $motivos) {
    $email_title = 'ACTIVIDAD CANCELADA';
    $email_content = '
    <div class="mail-content-contenedor">
        <div class="mail-content-seccion">
            <p>Hola <strong>' . esc_html($nombre) . '</strong>,</p>
            <p>Te informamos de que la actividad <strong>' . esc_html($actividad) . '</strong> con fecha <strong>' . esc_html($fecha) . '</strong> ha sido cancelada.</p>
            <h4>Motivos de la cancelación:</h4>
            <div style="background:#f8d7da;padding:10px;border-radius:5px;margin-bottom:10px;">' . nl2br(esc_html($motivos)) . '</div>
            <p>El equipo de Trekkium</p>
        </div>
    </div>';
    if ( function_exists('trekkium_get_email_html') ) {
        $final_email_content = trekkium_get_email_html($email_title, $email_content);
    } else {
        $final_email_content = '<html><body>' . $email_content . '</body></html>';
    }
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    wp_mail($dest_email, $email_title, $final_email_content, $headers);
}
