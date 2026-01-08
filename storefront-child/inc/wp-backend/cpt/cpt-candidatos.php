<?php
/**
 * CPT "Candidatos" - Estructura básica
 * Autor: Toni - Trekkium
 */

// Registrar el CPT
add_action('init', 'trekkium_register_candidatos_cpt');
function trekkium_register_candidatos_cpt() {

    $labels = array(
        'name'               => 'Candidatos',
        'singular_name'      => 'Candidato',
        'menu_name'          => 'Candidatos',
        'add_new'            => 'Añadir nuevo',
        'add_new_item'       => 'Añadir nuevo candidato',
        'edit_item'          => 'Editar candidato',
        'new_item'           => 'Nuevo candidato',
        'view_item'          => 'Ver candidato',
        'all_items'          => 'Todos los candidatos',
        'search_items'       => 'Buscar candidatos',
        'not_found'          => 'No se encontraron candidatos',
        'not_found_in_trash' => 'No hay candidatos en la papelera'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-id-alt',
        'supports'           => array('title'),
        'show_in_rest'       => false,
    );

    register_post_type('candidato', $args);
}

// Columnas personalizadas en la lista
add_filter('manage_candidato_posts_columns', 'trekkium_add_candidato_columns');
function trekkium_add_candidato_columns($columns) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => 'Título',
        'provincia' => 'Provincia',
        'titulacion' => 'Titulación',
        'estado' => 'Estado',
        'date' => 'Fecha'
    );
    return $columns;
}

// Mostrar datos en columnas personalizadas
add_action('manage_candidato_posts_custom_column', 'trekkium_show_candidato_columns', 10, 2);
function trekkium_show_candidato_columns($column, $post_id) {
    switch ($column) {
        case 'provincia':
            $provincia = get_post_meta($post_id, 'provincia', true);
            if ($provincia) {
                $states = trekkium_get_spanish_states();
                if ($states && isset($states[$provincia])) {
                    echo esc_html($states[$provincia]);
                } else {
                    echo esc_html($provincia);
                }
            } else {
                echo '—';
            }
            break;
            
        case 'titulacion':
            // CORRECCIÓN: Cambiar 'titulacion' por 'titulacion_array'
            $titulaciones = get_post_meta($post_id, 'titulacion_array', true);
            $titulaciones_nombres = trekkium_get_taxonomy_terms('titulacion');
            
            if (!empty($titulaciones) && is_array($titulaciones)) {
                $nombres = array();
                foreach ($titulaciones as $titulacion) {
                    if (isset($titulaciones_nombres[$titulacion])) {
                        $nombres[] = $titulaciones_nombres[$titulacion];
                    } else {
                        $nombres[] = $titulacion;
                    }
                }
                echo esc_html(implode(', ', $nombres));
            } else {
                echo '—';
            }
            break;
            
        case 'estado':
            $estado = get_post_meta($post_id, 'estado', true);
            $estados = array(
                'pendiente' => 'Pendiente de revisión',
                'aprobado' => 'Aprobado',
                'rechazado' => 'Rechazado'
            );
            if ($estado && isset($estados[$estado])) {
                $clase = '';
                switch ($estado) {
                    case 'aprobado': $clase = 'estado-aprobado'; break;
                    case 'rechazado': $clase = 'estado-rechazado'; break;
                    case 'pendiente': $clase = 'estado-pendiente'; break;
                }
                echo '<span class="' . esc_attr($clase) . '">' . esc_html($estados[$estado]) . '</span>';
            } else {
                echo '—';
            }
            break;
    }
}

// Columnas ordenables
add_filter('manage_edit-candidato_sortable_columns', 'trekkium_make_candidato_columns_sortable');
function trekkium_make_candidato_columns_sortable($columns) {
    $columns['provincia'] = 'provincia';
    $columns['estado'] = 'estado';
    return $columns;
}

// Ordenar por columnas personalizadas
add_action('pre_get_posts', 'trekkium_sort_candidato_columns');
function trekkium_sort_candidato_columns($query) {
    if (!is_admin() || !$query->is_main_query()) return;
    if ($query->get('post_type') !== 'candidato') return;
    
    $orderby = $query->get('orderby');
    switch ($orderby) {
        case 'provincia':
            $query->set('meta_key', 'provincia');
            $query->set('orderby', 'meta_value');
            break;
        case 'estado':
            $query->set('meta_key', 'estado');
            $query->set('orderby', 'meta_value');
            break;
    }
}

// Estilos para la administración
add_action('admin_head', 'trekkium_candidato_admin_styles');
function trekkium_candidato_admin_styles() {
    echo '<style>
        .estado-aprobado { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 3px; font-weight: 600; font-size: 12px; }
        .estado-rechazado { background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 3px; font-weight: 600; font-size: 12px; }
        .estado-pendiente { background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 3px; font-weight: 600; font-size: 12px; }
        .fixed .column-provincia, .fixed .column-titulacion, .fixed .column-estado { width: 20%; }
        .fixed .column-title { width: 40%; }
    </style>';
}