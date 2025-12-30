<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Enviar email informando de cambio de ubicación a un destinatario.
 * @param string $to_email
 * @param string $to_name
 * @param int $product_id
 * @param string $entorno (espacio natural)
 * @param string $provincia
 * @param string $region
 */
function trekkium_send_email_cambio_ubicacion( $to_email, $to_name, $product_id, $entorno = '', $provincia = '', $region = '' ) {
    if ( empty( $to_email ) ) return false;

    $helpers = dirname(__FILE__) . '/../mailing-functions.php';
    if ( file_exists( $helpers ) ) include_once $helpers;

    $actividad_nombre = get_the_title( $product_id );
    $product_url = get_permalink( $product_id );

    $to_name_safe = $to_name ?: 'amigo/a';

    $entorno_html = $entorno ? esc_html( $entorno ) : '';
    $prov_html = $provincia ? esc_html( $provincia ) : '';
    $reg_html = $region ? esc_html( $region ) : '';

    $ubicacion_line = '';
    if ( $entorno_html ) {
        $ubicacion_line .= $entorno_html;
    }
    $parts = array_filter( array( $prov_html, $reg_html ) );
    if ( ! empty( $parts ) ) {
        $ubicacion_line .= ( $ubicacion_line ? ' (' : '' ) . implode(', ', $parts) . ( $ubicacion_line ? ')' : '' );
    }

    $email_content = '
    <div class="mail-content-contenedor">
        <div class="mail-content-seccion">
            <p>Hola <strong>'.esc_html($to_name_safe).'</strong>,</p>
            <p>&nbsp;</p>
            <p>La actividad <strong>'.esc_html($actividad_nombre).'</strong> ha cambiado de ubicación.</p>
            <br>
            <p><strong>Nueva ubicación:</strong></p>
            <p>'.( $ubicacion_line ? esc_html($ubicacion_line) : 'Información no disponible' ).'</p>
            <p><a href="'.esc_url($product_url).'" class="mail-button">Ver actividad</a></p>
            <p>—<br>El equipo de Trekkium</p>
        </div>
    </div>
    ';

    $email_title = 'Cambio de ubicación — '. $actividad_nombre;

    if ( function_exists('trekkium_get_email_html') ) {
        $final = trekkium_get_email_html( $email_title, $email_content );
    } else {
        $final = '<html><body>'.$email_content.'</body></html>';
    }

    $headers = array('Content-Type: text/html; charset=UTF-8');
    return wp_mail( $to_email, $email_title, $final, $headers );
}

