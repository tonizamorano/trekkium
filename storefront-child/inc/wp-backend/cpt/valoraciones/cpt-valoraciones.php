<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function trekkium_register_cpt_valoraciones() {

    $labels = array(
        'name'                  => 'Valoraciones',
        'singular_name'         => 'Valoración',
        'menu_name'             => 'Valoraciones',
        'name_admin_bar'        => 'Valoración',
        'add_new'               => 'Añadir nueva',
        'add_new_item'          => 'Añadir nueva valoración',
        'new_item'              => 'Nueva valoración',
        'edit_item'             => 'Editar valoración',
        'view_item'             => 'Ver valoración',
        'all_items'             => 'Todas las valoraciones',
        'search_items'          => 'Buscar valoraciones',
        'not_found'             => 'No se encontraron valoraciones',
        'not_found_in_trash'    => 'No hay valoraciones en la papelera',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'supports'           => array( 'title' ),
        'menu_icon'          => 'dashicons-star-filled',
        'menu_position'      => 26,
    );

    register_post_type( 'valoraciones', $args );
}
add_action( 'init', 'trekkium_register_cpt_valoraciones' );
