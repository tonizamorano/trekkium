<?php
/**
 * Crear taxonomía personalizada "Dificultad" para productos
 */

// Hook para registrar la taxonomía
add_action('init', 'crear_taxonomia_dificultad');

function crear_taxonomia_dificultad() {
    // Etiquetas para la taxonomía - CORREGIDAS para mostrar "Dificultad" en admin
    $labels = array(
        'name'              => _x('Dificultades', 'taxonomy general name', 'text-domain'),
        'singular_name'     => _x('Dificultad', 'taxonomy singular name', 'text-domain'),
        'search_items'      => __('Buscar Dificultades', 'text-domain'),
        'all_items'         => __('Todas las Dificultades', 'text-domain'),
        'parent_item'       => __('Dificultad Padre', 'text-domain'),
        'parent_item_colon' => __('Dificultad Padre:', 'text-domain'),
        'edit_item'         => __('Editar Dificultad', 'text-domain'),
        'update_item'       => __('Actualizar Dificultad', 'text-domain'),
        'add_new_item'      => __('Añadir Nueva Dificultad', 'text-domain'),
        'new_item_name'     => __('Nombre de Nueva Dificultad', 'text-domain'),
        'menu_name'         => __('Dificultad', 'text-domain'), // Esta línea controla el nombre en el menú
        'popular_items'     => __('Dificultades Populares', 'text-domain'),
        'separate_items_with_commas' => __('Separar dificultades con comas', 'text-domain'),
        'add_or_remove_items'        => __('Añadir o eliminar dificultades', 'text-domain'),
        'choose_from_most_used'      => __('Elegir de las más usadas', 'text-domain'),
        'not_found'         => __('No se encontraron dificultades', 'text-domain'),
    );

    // Argumentos para registrar la taxonomía
    $args = array(
        'hierarchical'      => true, // Jerárquico: sí
        'labels'            => $labels,
        'show_ui'           => true, // Mostrar en IU: sí
        'show_admin_column' => true, // Mostrar columna de administración: sí
        'query_var'         => true, // Consultable públicamente: sí
        'show_in_rest'      => true, // Mostrar en REST API: sí
        'public'            => true, // Público: sí
        'show_in_nav_menus' => true, // Compatibilidad con menús de apariencia: sí
        'show_tagcloud'     => true, // Nube de etiquetas: sí
        'show_in_quick_edit'=> true, // Edición rápida: sí
        'rewrite'           => array('slug' => 'dificultad'),
    );

    // Registrar la taxonomía para el tipo de contenido "product"
    register_taxonomy('dificultad', array('product'), $args);
}

/**
 * Crear términos predeterminados para la taxonomía
 */
add_action('init', 'crear_terminos_dificultad');

function crear_terminos_dificultad() {
    $terminos = array(
        'Muy fácil' => array(
            'slug' => 'muy-facil',
            'description' => 'Nivel de dificultad muy fácil'
        ),
        'Fácil' => array(
            'slug' => 'facil',
            'description' => 'Nivel de dificultad fácil'
        ),
        'Moderado' => array(
            'slug' => 'moderado',
            'description' => 'Nivel de dificultad moderado'
        ),
        'Exigente' => array(
            'slug' => 'exigente',
            'description' => 'Nivel de dificultad exigente'
        ),
        'Muy exigente' => array(
            'slug' => 'muy-exigente',
            'description' => 'Nivel de dificultad muy exigente'
        )
    );

    foreach ($terminos as $termino => $detalles) {
        // Verificar si el término ya existe
        if (!term_exists($termino, 'dificultad')) {
            wp_insert_term(
                $termino,
                'dificultad',
                array(
                    'slug' => $detalles['slug'],
                    'description' => $detalles['description']
                )
            );
        }
    }
}

/**
 * Asegurar que la taxonomía se muestre en el menú de administración como "Dificultad"
 */
add_action('admin_menu', 'mostrar_taxonomia_en_menu_admin');

function mostrar_taxonomia_en_menu_admin() {
    // Agregar el submenú con el nombre "Dificultad" en singular
    add_submenu_page(
        'edit.php?post_type=product',
        __('Dificultades', 'text-domain'), // Título de la página
        __('Dificultad', 'text-domain'),   // Nombre en el menú - EN SINGULAR
        'manage_categories',
        'edit-tags.php?taxonomy=dificultad&post_type=product'
    );
}