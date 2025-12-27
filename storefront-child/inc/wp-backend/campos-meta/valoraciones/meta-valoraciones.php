<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Campos meta de valoraciones (custom, sin ACF)
 */
function trekkium_register_meta_valoraciones() {
    $campos = array(
        // Datos automáticos
        'usuario_id',
        'nombre_cliente',
        'avatar_del_usuario',
        'actividad_id',
        'nombre_actividad',
        'guia_id',
        'nombre_guia',
        'fecha_actividad',
        'fecha_envio_formulario',
        'pedido_id',

        // Valoraciones internas (no públicas)
        'valor_actividad_general',
        'actividad_se_ajusta_descripcion',
        'organizacion_actividad',
        'experiencia_trekkium',
        'proceso_reserva',
        'sensacion_seguridad',

        // Valoraciones públicas del guía
        'valoracion_guia_general',
        'comentario_guia',
    );

    foreach ( $campos as $campo ) {
        register_post_meta( 'valoraciones', $campo, array(
            'type' => 'string', // usamos string genérico, algunas pueden ser integers
            'single' => true,
            'show_in_rest' => false,
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            },
        ));
    }
}
add_action( 'init', 'trekkium_register_meta_valoraciones' );
