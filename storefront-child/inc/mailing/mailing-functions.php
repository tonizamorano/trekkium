<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Devuelve el HTML completo de la plantilla de email pasándole título y contenido.
 * No imprime nada, solo retorna el HTML (seguro para usar desde frontend o procesos cron).
 *
 * @param string $email_title
 * @param string $email_content
 * @return string
 */
function trekkium_get_email_html( $email_title, $email_content ) {
    // Localizar la plantilla base: preferencia tema hijo -> tema padre -> inc/mailing local
    $paths = array(
        get_stylesheet_directory() . '/inc/mailing/mailing-plantilla-base.php',
        get_template_directory() . '/inc/mailing/mailing-plantilla-base.php',
        dirname(__FILE__) . '/mailing-plantilla-base.php',
    );

    $template = '';
    foreach ( $paths as $p ) {
        if ( file_exists( $p ) ) { $template = $p; break; }
    }

    // Si no hay plantilla, devolver solo el contenido simple envuelto en HTML mínimo
    if ( ! $template ) {
        return '<!doctype html><html><head><meta charset="utf-8"><title>' . esc_html( $email_title ) . '</title></head><body>' . $email_content . '</body></html>';
    }

    // Bufferizar la salida de la plantilla pasando variables locales.
    ob_start();
    // Variables que la plantilla espera
    $email_title_local   = $email_title;
    $email_content_local = $email_content;

    // Para compatibilidad con plantillas que usan $email_title/$email_content directamente,
    // las exponemos con esos nombres en el scope de include.
    $email_title   = $email_title_local;
    $email_content = $email_content_local;

    include $template;
    $html = ob_get_clean();
    return $html;
}
