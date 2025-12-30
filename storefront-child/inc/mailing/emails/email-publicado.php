<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Enviar email al autor cuando un producto pase a estado "publish".
 */
add_action( 'transition_post_status', 'trekkium_enviar_email_publicado', 10, 3 );
function trekkium_enviar_email_publicado( $new_status, $old_status, $post ) {
    if ( $post->post_type !== 'product' ) return;
    if ( $new_status !== 'publish' ) return;
    if ( $old_status === 'publish' ) return; // evitar duplicados

    $post_id = $post->ID;
    $actividad_nombre = get_the_title( $post_id );
    $product_url = get_permalink( $post_id );

    $author_id = $post->post_author;
    $author = get_userdata( $author_id );
    if ( ! $author || empty( $author->user_email ) ) return;

    $to_email = $author->user_email;
    $to_name = $author->display_name ?: '';

    $email_title = 'Actividad publicada — ' . $actividad_nombre;

    $email_content = '
    <div class="mail-content-contenedor">
        <div class="mail-content-seccion">
            <p>Hola <strong>' . esc_html( $to_name ) . '</strong>,</p>
            <p>La actividad <strong>' . esc_html( $actividad_nombre ) . '</strong> se ha publicado con éxito.</p>
            <br>
            <p><a href="' . esc_url( $product_url ) . '" class="mail-button">Ver actividad</a></p>
            <br>
            <p>—<br>El equipo de Trekkium</p>
        </div>
    </div>
    ';

    if ( function_exists('trekkium_get_email_html') ) {
        $final = trekkium_get_email_html( $email_title, $email_content );
    } else {
        $final = '<html><body>' . $email_content . '</body></html>';
    }

    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail( $to_email, $email_title, $final, $headers );
}
