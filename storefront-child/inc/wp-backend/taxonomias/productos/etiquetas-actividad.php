<?php

/**
 * 1) Registrar taxonomía Etiquetas de Actividad
 */
function trekkium_register_etiquetas_actividad() {
    if (taxonomy_exists('etiquetas_actividad')) {
        return;
    }

    $labels = array(
        'name'                       => 'Etiquetas de actividad',
        'singular_name'              => 'Etiqueta de actividad',
        'menu_name'                  => 'Etiquetas de actividad',
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => false,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'etiqueta-actividad'),
        'show_in_rest'      => true,
        'public'            => true,

        // 👇 CLAVE
        'meta_box_cb'       => 'post_categories_meta_box',

        'capabilities' => array(
            'manage_terms' => 'edit_users',
            'edit_terms'   => 'edit_users',
            'delete_terms' => 'edit_users',
            'assign_terms' => 'edit_users',
        ),
    );


    register_taxonomy( 'etiquetas_actividad', array( 'product', 'user' ), $args );
}
add_action( 'init', 'trekkium_register_etiquetas_actividad' );

/**
 * METABOX PARA USUARIOS - CON DISPLAY EN FILAS/COLUMNAS
 */
add_action( 'show_user_profile', 'trekkium_user_etiquetas_metabox_grid' );
add_action( 'edit_user_profile', 'trekkium_user_etiquetas_metabox_grid' );

function trekkium_user_etiquetas_metabox_grid( $user ) {
    
    // TEMPORAL: Comentar para probar con todos los usuarios
    // if ( ! in_array( 'cliente', (array) $user->roles ) ) {
    //     return;
    // }
    
    $taxonomy = 'etiquetas_actividad';
    $terms = get_terms( array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));

    if ( is_wp_error( $terms ) ) {
        echo '<p>Error cargando etiquetas.</p>';
        return;
    }

    $user_terms = wp_get_object_terms( $user->ID, $taxonomy, array( 'fields' => 'ids' ) );
    if ( is_wp_error( $user_terms ) ) {
        $user_terms = array();
    }

    ?>
    <h3>🏷️ Etiquetas de Actividad</h3>
    
    <table class="form-table">
        <tr>
            <th><label>Etiquetas asignadas</label></th>
            <td>
                <div class="trekkium-etiquetas-grid" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 15px; background: #f9f9f9; border-radius: 4px;">
                    <?php if ( empty( $terms ) ) : ?>
                        <p>No hay etiquetas disponibles. <a href="<?php echo admin_url('edit-tags.php?taxonomy=etiquetas_actividad'); ?>">Crear etiquetas</a></p>
                    <?php else : ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                            <?php foreach ( $terms as $term ) : ?>
                                <?php $checked = in_array( $term->term_id, $user_terms ) ? 'checked' : ''; ?>
                                <label style="display: flex; align-items: center; cursor: pointer; transition: all 0.2s;">
                                    <input type="checkbox" 
                                           name="user_etiquetas_actividad[]" 
                                           value="<?php echo esc_attr( $term->term_id ); ?>" 
                                           <?php echo $checked; ?>
                                           style="margin: 0 8px 0 0;" />
                                    <?php echo esc_html( $term->name ); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <p class="description">Selecciona las etiquetas de actividad para este usuario.</p>
            </td>
        </tr>
    </table>
    
    <style>
    .trekkium-etiquetas-grid label:hover {
        background: #e8f4fd !important;
        border-color: #007cba !important;
    }
    .trekkium-etiquetas-grid input[type="checkbox"]:checked + label {
        background: #e8f4fd !important;
        border-color: #007cba !important;
    }
    </style>
    <?php
}

/**
 * Guardar etiquetas de usuarios
 */
add_action( 'personal_options_update', 'trekkium_save_user_etiquetas_grid' );
add_action( 'edit_user_profile_update', 'trekkium_save_user_etiquetas_grid' );

function trekkium_save_user_etiquetas_grid( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    $taxonomy = 'etiquetas_actividad';

    if ( isset( $_POST['user_etiquetas_actividad'] ) && is_array( $_POST['user_etiquetas_actividad'] ) ) {
        $terms = array_map( 'intval', $_POST['user_etiquetas_actividad'] );
        wp_set_object_terms( $user_id, $terms, $taxonomy, false );
    } else {
        wp_set_object_terms( $user_id, array(), $taxonomy, false );
    }
    
    return true;
}

/**
 * COLUMNA EN LISTA DE USUARIOS
 */
add_filter( 'manage_users_columns', 'trekkium_add_etiquetas_column' );
function trekkium_add_etiquetas_column( $columns ) {
    $columns['etiquetas_actividad'] = 'Etiquetas';
    return $columns;
}

add_filter( 'manage_users_custom_column', 'trekkium_show_etiquetas_column', 10, 3 );
function trekkium_show_etiquetas_column( $value, $column_name, $user_id ) {
    if ( 'etiquetas_actividad' === $column_name ) {
        $terms = wp_get_object_terms( $user_id, 'etiquetas_actividad' );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $term_names = wp_list_pluck( $terms, 'name' );
            return implode( ', ', $term_names );
        }
        return '—';
    }
    return $value;
}