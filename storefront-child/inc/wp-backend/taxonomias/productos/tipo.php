<?php
// Registrar la taxonomía "Tipo" para productos WooCommerce
function crear_taxonomia_tipo() {

    $labels = array(
        'name'                       => _x( 'Tipos', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Tipo', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Tipos', 'text_domain' ),
        'all_items'                  => __( 'Todos los Tipos', 'text_domain' ),
        'parent_item'                => __( 'Tipo Padre', 'text_domain' ),
        'parent_item_colon'          => __( 'Tipo Padre:', 'text_domain' ),
        'new_item_name'              => __( 'Nuevo Tipo', 'text_domain' ),
        'add_new_item'               => __( 'Añadir Nuevo Tipo', 'text_domain' ),
        'edit_item'                  => __( 'Editar Tipo', 'text_domain' ),
        'update_item'                => __( 'Actualizar Tipo', 'text_domain' ),
        'view_item'                  => __( 'Ver Tipo', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separar tipos con comas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Añadir o eliminar tipos', 'text_domain' ),
        'choose_from_most_used'      => __( 'Elegir de los más usados', 'text_domain' ),
        'popular_items'              => __( 'Tipos populares', 'text_domain' ),
        'search_items'               => __( 'Buscar Tipos', 'text_domain' ),
        'not_found'                  => __( 'No encontrado', 'text_domain' ),
        'no_terms'                   => __( 'No hay tipos', 'text_domain' ),
        'items_list'                 => __( 'Lista de tipos', 'text_domain' ),
        'items_list_navigation'      => __( 'Navegación de la lista de tipos', 'text_domain' ),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_in_rest'               => true,
        'show_tagcloud'              => true,
        'show_in_quick_edit'         => true,
    );

    register_taxonomy( 'tipo', array( 'product' ), $args );

    // Crear automáticamente los tipos
    $tipos = array(
        'Actividad'
        // 'Viaje'
    );

    foreach ( $tipos as $tipo ) {
        if ( ! term_exists( $tipo, 'tipo' ) ) {
            wp_insert_term( $tipo, 'tipo' );
        }
    }

}
add_action( 'init', 'crear_taxonomia_tipo', 0 );
