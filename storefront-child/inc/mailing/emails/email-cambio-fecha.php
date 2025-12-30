<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Función reutilizable para enviar el email de cambio de fecha a un destinatario.
 * @param string $to_email
 * @param string $to_name
 * @param int $product_id
 * @param string $nueva_fecha (YYYY-mm-dd o cualquier formato válido)
 */
function trekkium_send_email_cambio_fecha( $to_email, $to_name, $product_id, $nueva_fecha ) {
    if ( empty( $to_email ) ) return false;

    // Incluir helpers
    $helpers = dirname(__FILE__) . '/../mailing-functions.php';
    if ( file_exists( $helpers ) ) include_once $helpers;

    // Obtener título y URL producto
    $actividad_nombre = get_the_title( $product_id );
    $product_url = get_permalink( $product_id );

    // Formatear fecha tipo "25 de enero de 2026"
    $fecha_formateada = $nueva_fecha;
    $timestamp = strtotime( $nueva_fecha );
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

    $to_name_safe = $to_name ?: 'amigo/a';

    // Motivo del cambio (campo meta del producto)
    $motivo_cambio = get_post_meta( $product_id, 'motivo_cambio', true );
    $motivo_cambio_html = $motivo_cambio ? nl2br( esc_html( $motivo_cambio ) ) : '';

    $email_content = '
    <div class="mail-content-contenedor">
        <div class="mail-content-seccion">
            <p>Hola <strong>'.esc_html($to_name_safe).'</strong>,</p>
            <p>&nbsp;</p>
            <p>La actividad <strong>'.esc_html($actividad_nombre).'</strong> se ha cambiado para el día <strong>'.esc_html($fecha_formateada).'</strong>.</p>
            <br>
            <p><strong>Motivos del cambio:</strong></p>
            '.( $motivo_cambio_html ? '<p>'.$motivo_cambio_html.'</p>' : '' ).'
            <p><a href="'.esc_url($product_url).'" class="mail-button">Ver actividad</a></p>
            <p>—<br>El equipo de Trekkium</p>
        </div>
    </div>
    ';

    $email_title = 'Cambio de fecha — '. $actividad_nombre;

    if ( function_exists('trekkium_get_email_html') ) {
        $final = trekkium_get_email_html( $email_title, $email_content );
    } else {
        $final = '<html><body>'.$email_content.'</body></html>';
    }

    $headers = array('Content-Type: text/html; charset=UTF-8');
    return wp_mail( $to_email, $email_title, $final, $headers );
}
