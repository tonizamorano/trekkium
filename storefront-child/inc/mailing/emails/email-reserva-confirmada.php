<?php
// Enviar email de reserva confirmada al cliente tras pago completado (Stripe compatible)
add_action( 'woocommerce_order_status_pending_to_processing', 'trekkium_enviar_email_reserva_confirmada', 10, 1 );
add_action( 'woocommerce_order_status_on-hold_to_processing', 'trekkium_enviar_email_reserva_confirmada', 10, 1 );

function trekkium_enviar_email_reserva_confirmada( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    // --- 1️⃣ Datos del cliente ---
    $cliente_nombre   = $order->get_billing_first_name();
    $cliente_email    = $order->get_billing_email();
    
    // OBTENER TELÉFONO DE MÚLTIPLES FUENTES
    $cliente_telefono = $order->get_billing_phone(); // Primero del pedido
    
    // Si no hay teléfono en billing, intentar del usuario asociado
    if ( empty($cliente_telefono) ) {
        $user_id = $order->get_customer_id();
        if ( $user_id ) {
            $user_phone = get_user_meta( $user_id, 'billing_phone', true );
            if ( !empty($user_phone) ) {
                $cliente_telefono = $user_phone;
            }
        }
    }
    
    // Si sigue vacío, intentar de los metadatos del pedido directamente
    if ( empty($cliente_telefono) ) {
        $cliente_telefono = $order->get_meta('_billing_phone');
    }
    
    // Debug más detallado
    error_log('=== Trekkium Debug Teléfono ===');
    error_log('Order ID: ' . $order_id);
    error_log('Teléfono billing: ' . ($order->get_billing_phone() ?: 'VACÍO'));
    error_log('User ID: ' . $order->get_customer_id());
    error_log('User meta phone: ' . (get_user_meta($order->get_customer_id(), 'billing_phone', true) ?: 'VACÍO'));
    error_log('Order meta phone: ' . ($order->get_meta('_billing_phone') ?: 'VACÍO'));
    error_log('=== Fin Debug ===');

    // --- 2️⃣ Tomar primer item del pedido ---
    $items = $order->get_items();
    if ( empty($items) ) return;

    $item = array_shift($items);
    $product_id = $item->get_product_id();
    $product = wc_get_product($product_id);
    
    // Obtener los metadatos del producto
    $fecha_meta = $product ? $product->get_meta('fecha') : '';
    $hora = $product ? $product->get_meta('hora') : 'No disponible';
    $grupo_whatsapp = $product ? $product->get_meta('grupo_whatsapp') : '#';
    $precio_actividad = $product ? floatval($product->get_meta('precio')) : 0;
    
    // Formatear fecha a "25 de enero de 2026"
    $fecha_formateada = 'No disponible';
    if ($fecha_meta) {
        $timestamp = strtotime($fecha_meta);
        if ($timestamp !== false) {
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
    
    $actividad_nombre = $item->get_name();
    $precio_reserva  = floatval( $item->get_total() ?: 0 );
    $plazas          = $item->get_quantity() ?: 1;
    $total_reserva   = $precio_reserva * $plazas;
    $total_pendiente = max( 0, ($precio_actividad - $precio_reserva) * $plazas );

    // URL para ver la reserva específica
    $ver_reserva_url = 'https://staging2.trekkium.com/mi-cuenta/ver-reservas/' . $order_id . '/';
    
    // URL de la página del producto
    $product_url = get_permalink($product_id);
    
    // Verificar que el enlace de WhatsApp no esté vacío
    if (empty($grupo_whatsapp) || $grupo_whatsapp == '#') {
        $grupo_whatsapp_display = '#';
        $grupo_whatsapp_href = '#';
    } else {
        if (!preg_match('/^https?:\/\//', $grupo_whatsapp)) {
            $grupo_whatsapp_href = 'https://' . ltrim($grupo_whatsapp, '/');
        } else {
            $grupo_whatsapp_href = $grupo_whatsapp;
        }
        $grupo_whatsapp_display = $grupo_whatsapp_href;
    }

    // --- 3️⃣ Contenido del email ---
    $email_content = '

    <div class="mail-content-contenedor">  

        <div class="mail-content-seccion">

            <p style="margin-bottom:10px !important;">Hola <strong>'.$cliente_nombre.'</strong>,</p>
            <p>Tu reserva <strong>Nº '.$order->get_id().'</strong> se ha realizado correctamente y se encuentra pendiente de pago. A continuación encontrarás todos los detalles:</p>

        </div>

        <div class="mail-content-seccion">
       
            <h4>Titular de la reserva</h4>
            <p style="font-weight:600 !important;">'.$order->get_billing_first_name() . ' ' . $order->get_billing_last_name().'</p>
            <p>'.($cliente_telefono ?: 'No proporcionado').'</p>
            <p>'.$cliente_email.'</p>
        
        </div>

        <div class="mail-content-seccion">
        
            <h4>Actividad</h4>
            <p style="font-weight:600;">'.$actividad_nombre.'</p>
            <p>'.$fecha_formateada.' a las '.$hora.' h</p>
            <a href="'.esc_url($product_url).'" class="mail-button" target="_blank">Ver actividad</a>

        </div>


        <div class="mail-content-seccion">
       
            <h4>Detalle económico</h4>
            <p>
            Precio de la actividad: '.number_format($precio_actividad, 2, ',', '.').' €<br>
            Importe de la reserva: '.number_format($precio_reserva, 2, ',', '.').' €<br>
            Plazas reservadas: '.$plazas.'<br>
            <strong>Total reserva:</strong> '.number_format($total_reserva, 2, ',', '.').' €<br>
            <strong>Importe pendiente:</strong> '.number_format($total_pendiente, 2, ',', '.').' €
            </p>

            <p style="margin-top:10px !important;">Puedes consultar el estado de tu reserva y todos sus detalles desde tu área personal:</p>
            <a href="'.esc_url($ver_reserva_url).'" class="mail-button">Ver mi reserva</a>

        </div>

        <div class="mail-content-seccion">
       
            <h4>Información importante</h4>
            
            <p style="margin-bottom:5px !important;">El importe de la reserva se cargará automáticamente en tu tarjeta una vez finalice el plazo de cancelación gratuita.</p>
            <p>El día de la actividad deberás abonar directamente al guía el importe pendiente.</p>           

        </div>

        <div class="mail-content-seccion">

            <h4>Grupo de WhatsApp</h4>
            <p>Accede al grupo de WhatsApp de esta actividad para recibir información actualizada y comunicarte con el guía:</p>
            <a href="'.esc_url($grupo_whatsapp_href).'" class="mail-button" target="_blank">Unirme al grupo de WhatsApp</a>

        </div>

        <div class="mail-content-seccion">

            <p style="margin-bottom:10px !important;">¡Gracias por confiar en nosotros y disfruta de la aventura!</p>
            <p>—<br>El equipo de Trekkium</p>

        </div>

    </div>

    ';

    // --- 4️⃣ Título del email ---
    $email_title = 'RESERVA CONFIRMADA';

    // --- 5️⃣ Generar el HTML final de la plantilla de email (sin imprimir) ---
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