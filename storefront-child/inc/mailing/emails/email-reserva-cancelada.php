<?php
// Enviar email de reserva cancelada al cliente cuando el pedido pasa a cancelado
add_action( 'woocommerce_order_status_cancelled', 'trekkium_enviar_email_reserva_cancelada', 10, 1 );

function trekkium_enviar_email_reserva_cancelada( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    // --- 1️⃣ Datos del cliente ---
    $cliente_nombre = $order->get_billing_first_name();
    $cliente_email  = $order->get_billing_email();

    // Obtener teléfono desde múltiples fuentes
    $cliente_telefono = $order->get_billing_phone();

    if ( empty( $cliente_telefono ) ) {
        $user_id = $order->get_customer_id();
        if ( $user_id ) {
            $user_phone = get_user_meta( $user_id, 'billing_phone', true );
            if ( ! empty( $user_phone ) ) {
                $cliente_telefono = $user_phone;
            }
        }
    }

    if ( empty( $cliente_telefono ) ) {
        $cliente_telefono = $order->get_meta( '_billing_phone' );
    }

    // --- 2️⃣ Contenido del email ---
    $email_content = '

    <div class="mail-content-contenedor">  

        <div class="mail-content-seccion">

            <p style="margin-bottom:10px !important;">Hola <strong>' . esc_html( $cliente_nombre ) . '</strong>,</p>
            <p>Tu reserva <strong>Nº ' . $order->get_id() . '</strong> ha sido cancelada.</p>

        </div>

        <div class="mail-content-seccion">
       
            <h4>Titular de la reserva</h4>
            <p style="font-weight:600 !important;">' . esc_html( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) . '</p>
            <p>' . ( $cliente_telefono ?: 'No proporcionado' ) . '</p>
            <p>' . esc_html( $cliente_email ) . '</p>
        
        </div>

        <div class="mail-content-seccion">

            <p>—<br>El equipo de Trekkium</p>

        </div>

    </div>
    ';

    // --- 3️⃣ Título del email ---
    $email_title = 'RESERVA CANCELADA';

    // --- 4️⃣ Plantilla HTML ---
    $helpers_path = dirname(__FILE__) . '/mailing-functions.php';
    if ( file_exists( $helpers_path ) ) {
        include_once $helpers_path;
    }

    if ( function_exists( 'trekkium_get_email_html' ) ) {
        $final_email_content = trekkium_get_email_html( $email_title, $email_content );
    } else {
        $final_email_content = '<html><body>' . $email_content . '</body></html>';
    }

    // --- 5️⃣ Envío ---
    $headers = [ 'Content-Type: text/html; charset=UTF-8' ];

    if ( ! wp_mail( $cliente_email, $email_title, $final_email_content, $headers ) ) {
        error_log( 'Trekkium: fallo al enviar email de cancelación a ' . $cliente_email );
    } else {
        error_log( 'Trekkium: email de cancelación enviado correctamente a ' . $cliente_email );
    }
}
