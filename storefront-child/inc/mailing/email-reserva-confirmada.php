<?php
// Enviar email de reserva confirmada al cliente tras pago completado (Stripe compatible)
add_action( 'woocommerce_order_status_pending_to_processing', 'trekkium_enviar_email_reserva_confirmada', 10, 1 );
add_action( 'woocommerce_order_status_on-hold_to_processing', 'trekkium_enviar_email_reserva_confirmada', 10, 1 );

function trekkium_enviar_email_reserva_confirmada( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    // --- 1️⃣ Datos del cliente ---
    $cliente_nombre   = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
    $cliente_email    = $order->get_billing_email();
    $cliente_telefono = $order->get_billing_phone();

    // --- 2️⃣ Tomar primer item del pedido ---
    $items = $order->get_items();
    if ( empty($items) ) return;

    $item = array_shift($items);
    $actividad_nombre  = $item->get_name();
    $fecha             = $item->get_meta('fecha') ?: 'No disponible';
    $hora              = $item->get_meta('hora') ?: 'No disponible';
    $enlace_whatsapp   = $item->get_meta('enlace_whatsapp') ?: '#';
    $precio_actividad  = floatval( $item->get_meta('precio') ?: 0 );
    $precio_reserva    = floatval( $item->get_total() ?: 0 );
    $plazas            = $item->get_quantity() ?: 1;
    $total_reserva     = $precio_reserva * $plazas;
    $total_pendiente   = max( 0, ($precio_actividad - $precio_reserva) * $plazas );

    // --- 3️⃣ Contenido del email ---
    $email_content = '
    <p>Hola <strong>'.$cliente_nombre.'</strong>,</p>
    <p>Tu reserva <strong>Nº '.$order->get_id().'</strong> se ha realizado correctamente y la estamos procesando. A continuación encontrarás todos los detalles:</p>

    <hr>
    <h3>Titular de la reserva</h3>
    <p>Nombre y apellidos: '.$cliente_nombre.'<br>
    Teléfono: '.$cliente_telefono.'<br>
    Email: '.$cliente_email.'</p>

    <hr>
    <h3>Actividad</h3>
    <p>'.$actividad_nombre.'</p>
    <p>Fecha y hora: '.$fecha.' a las '.$hora.' h</p>

    <hr>
    <h3>Detalle económico</h3>
    <p>
    Precio de la actividad: '.$precio_actividad.' €<br>
    Importe de la reserva: '.$precio_reserva.' €<br>
    Plazas reservadas: '.$plazas.'<br>
    <strong>Total reserva pagado:</strong> '.$total_reserva.' €<br>
    <strong>Importe pendiente de pago:</strong> '.$total_pendiente.' €
    </p>

    <p>Puedes consultar el estado de tu reserva y todos sus detalles desde tu área personal:</p>
    <a href="'.get_permalink( wc_get_page_id('myaccount') ).'" class="mail-button">Ver mi reserva</a>

    <hr>
    <h3>Información importante</h3>
    <ul>
    <li>Puedes cancelar tu reserva gratuitamente hasta 24 horas antes de la actividad.</li>
    <li>El importe de la reserva no se cargará en tu tarjeta hasta que finalice ese plazo.</li>
    <li>El día de la actividad deberás abonar directamente al guía el importe pendiente.</li>
    </ul>

    <hr>
    <h3>Grupo de WhatsApp</h3>
    <p>Accede al grupo de WhatsApp de esta actividad para recibir información actualizada y comunicarte con el guía:</p>
    <a href="'.esc_url($enlace_whatsapp).'" class="mail-button">Unirme al grupo de WhatsApp</a>

    <hr>
    <p>Si tienes cualquier duda antes de la actividad, puedes consultar las condiciones y la información completa desde tu cuenta en Trekkium.</p>

    <p>¡Gracias por confiar en nosotros y nos vemos en la montaña!</p>
    <p>—<br>Equipo Trekkium</p>
    ';

    // --- 4️⃣ Título del email ---
    $email_title = 'Reserva confirmada';

    // --- 5️⃣ Generar el HTML final de la plantilla de email (sin imprimir) ---
    // Usar helper para evitar que la plantilla se muestre accidentalmente en el frontend.
    // Asegúrate de que `mailing-functions.php` está disponible.
    $helpers_path = dirname(__FILE__) . '/mailing-functions.php';
    if ( file_exists( $helpers_path ) ) {
        include_once $helpers_path;
    }

    if ( function_exists('trekkium_get_email_html') ) {
        $final_email_content = trekkium_get_email_html( $email_title, $email_content );
    } else {
        // Fallback simple
        $final_email_content = '<html><body>' . $email_content . '</body></html>';
    }

    error_log('Trekkium: Longitud del contenido generado: ' . strlen($final_email_content));

    // --- 6️⃣ Enviar email ---
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    if( ! wp_mail( $cliente_email, $email_title, $final_email_content, $headers ) ) {
        error_log( 'Trekkium: fallo al enviar email a ' . $cliente_email );
    } else {
        error_log( 'Trekkium: email enviado correctamente a ' . $cliente_email );
    }
}