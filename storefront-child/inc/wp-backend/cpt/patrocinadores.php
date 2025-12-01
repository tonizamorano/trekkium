<?php

/**
 * Register Custom Post Type: Patrocinadores
 */
function trekkium_register_cpt_patrocinadores() {

    $labels = array(
        'name'                  => 'Patrocinadores',
        'singular_name'         => 'Patrocinador',
        'menu_name'             => 'Patrocinadores',
        'name_admin_bar'        => 'Patrocinador',
        'add_new'               => 'Añadir nuevo',
        'add_new_item'          => 'Añadir nuevo patrocinador',
        'edit_item'             => 'Editar patrocinador',
        'new_item'              => 'Nuevo patrocinador',
        'view_item'             => 'Ver patrocinador',
        'search_items'          => 'Buscar patrocinadores',
        'not_found'             => 'No se han encontrado patrocinadores',
        'not_found_in_trash'    => 'No hay patrocinadores en la papelera',
        'all_items'             => 'Todos los patrocinadores',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => false,           // No público, pero accesible en admin
        'show_ui'               => true,            // Visible en el panel WP
        'show_in_menu'          => true,
        'menu_position'         => 25,
        'menu_icon'             => 'dashicons-megaphone',

        // Permite título y editor si los quieres usar para algo.
        'supports'              => array( 'title' ),

        // Si más adelante quieres listarlos públicamente, solo cambia aquí:
        'has_archive'           => false,
        'rewrite'               => false,

        // Gestión de capacidades (puedes cambiarlo luego si hay roles especiales)
        'capability_type'       => 'post',
        'map_meta_cap'          => true,
    );

    register_post_type( 'patrocinador', $args );
}
add_action( 'init', 'trekkium_register_cpt_patrocinadores' );
