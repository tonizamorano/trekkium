<?php
/**
 * TAXONOMÍA GLOBAL: MODALIDAD
 * Productos + Usuarios (todos)
 */

add_action('init', 'trekkium_registrar_taxonomia_modalidad_global', 20);
function trekkium_registrar_taxonomia_modalidad_global() {

    $labels = array(
        'name'          => 'Modalidad',
        'singular_name' => 'Modalidad',
        'menu_name'     => 'Modalidad'
    );

    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => false, // No mostrar columna en lista de usuarios
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'modalidad'),
        'capabilities'      => array(
            'manage_terms' => 'manage_modalidades',
            'edit_terms'   => 'manage_modalidades',
            'delete_terms' => 'manage_modalidades',
            'assign_terms' => 'read'
        )
    );

    register_taxonomy('modalidad', array('product', 'user'), $args);
}

/**
 * SOLO ADMIN puede gestionar términos
 */
add_action('init', function() {
    $admin = get_role('administrator');
    if ($admin && !$admin->has_cap('manage_modalidades')) {
        $admin->add_cap('manage_modalidades');
    }
});

/**
 * Creación automática de términos base
 */
add_action('init', 'trekkium_crear_terminos_modalidad_global', 25);
function trekkium_crear_terminos_modalidad_global() {

    $modalidades = array(
        'Senderismo',
        'Trekking',
        'Alpinismo',
        'Barranquismo',
        'Escalada',
        'Esquí de montaña',
        'Raquetas de nieve',
        'Vía ferrata'
    );

    foreach ($modalidades as $modalidad) {
        if (!term_exists($modalidad, 'modalidad')) {
            wp_insert_term($modalidad, 'modalidad');
        }
    }
}

/**
 * PRODUCTOS: Modalidad obligatoria (1 mínimo)
 */
add_action('save_post_product', function($post_id){
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    $terms = wp_get_object_terms($post_id, 'modalidad');
    if (empty($terms)) {
        wp_set_object_terms($post_id, 'Senderismo', 'modalidad');
    }
});

/**
 * USUARIOS: Metabox de selección múltiple para TODOS los usuarios
 */
add_action('show_user_profile', 'trekkium_modalidad_user_profile');
add_action('edit_user_profile', 'trekkium_modalidad_user_profile');
function trekkium_modalidad_user_profile($user) {
    // Obtener términos asignados al usuario
    $terms = wp_get_object_terms($user->ID, 'modalidad', array('fields' => 'ids'));
    
    // Obtener todos los términos disponibles
    $all_terms = get_terms(array(
        'taxonomy' => 'modalidad',
        'hide_empty' => false,
        'orderby' => 'name'
    ));

    ?>
    <h2>Modalidad</h2>
    <table class="form-table">
        <tbody>
            <tr>
                <th><label>Modalidades</label></th>
                <td>
                    <div class="modalidades-container" style="display: flex; flex-wrap: wrap; gap: 5px 5px; margin: 3px 0;">
                        <?php
                        if (!empty($all_terms) && !is_wp_error($all_terms)) {
                            foreach ($all_terms as $term) {
                                $checked = in_array($term->term_id, $terms) ? 'checked' : '';
                                ?>
                                <label style="display: flex; align-items: center; white-space: nowrap; margin: 0;">
                                    <input type="checkbox" 
                                           name="modalidad_user[]" 
                                           value="<?php echo esc_attr($term->term_id); ?>" 
                                           <?php echo $checked; ?>
                                           style="margin: 0 6px 0 0;">
                                    <?php echo esc_html($term->name); ?>
                                </label>
                                <?php
                            }
                        } else {
                            echo '<p>No hay modalidades disponibles.</p>';
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    
    <style>
        .modalidades-container label {
            padding: 4px 8px;
            border-radius: 3px;
            transition: background-color 0.2s;
        }
        .modalidades-container label:hover {
            background-color: #f0f0f1;
        }
    </style>
    <?php
}

/**
 * Guardar modalidades para TODOS los usuarios
 */
add_action('personal_options_update', 'trekkium_save_modalidad_user');
add_action('edit_user_profile_update', 'trekkium_save_modalidad_user');

function trekkium_save_modalidad_user($user_id) {
    // Verificar permisos
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }
    
    // Guardar modalidades si se enviaron
    if (isset($_POST['modalidad_user']) && is_array($_POST['modalidad_user'])) {
        $term_ids = array_map('intval', $_POST['modalidad_user']);
        wp_set_object_terms($user_id, $term_ids, 'modalidad', false);
    } else {
        // Si no se selecciona ninguna, asignar Senderismo por defecto
        $default_term = get_term_by('name', 'Senderismo', 'modalidad');
        if ($default_term) {
            wp_set_object_terms($user_id, array($default_term->term_id), 'modalidad', false);
        } else {
            // Si no existe el término por defecto, limpiar las asignaciones
            wp_set_object_terms($user_id, array(), 'modalidad', false);
        }
    }
}

/**
 * Añadir estilos CSS para el admin
 */
add_action('admin_head', 'trekkium_modalidad_admin_styles');
function trekkium_modalidad_admin_styles() {
    ?>
    <style>
        /* Estilos específicos para el perfil de usuario */
        .user-edit-php .modalidades-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 20px;
            margin: 8px 0;
            align-items: center;
        }
        
        .user-edit-php .modalidades-container label {
            display: flex;
            align-items: center;
            white-space: nowrap;
            margin: 0;
            padding: 4px 8px;
            border-radius: 3px;
            transition: background-color 0.2s;
        }
        
        .user-edit-php .modalidades-container label:hover {
            background-color: #f0f0f1;
        }
        
        .user-edit-php .modalidades-container input[type="checkbox"] {
            margin: 0 6px 0 0;
        }
        
        /* Estilos para el listado de usuarios (opcional) */
        .users-php #modalidad {
            width: 200px;
        }
    </style>
    <?php
}

/**
 * (OPCIONAL) Mostrar columna en listado de usuarios
 */
add_filter('manage_users_columns', 'trekkium_add_modalidad_user_column');
function trekkium_add_modalidad_user_column($columns) {
    $columns['modalidad'] = 'Modalidad';
    return $columns;
}

add_action('manage_users_custom_column', 'trekkium_show_modalidad_user_column', 10, 3);
function trekkium_show_modalidad_user_column($value, $column_name, $user_id) {
    if ($column_name === 'modalidad') {
        $terms = wp_get_object_terms($user_id, 'modalidad', array('fields' => 'names'));
        if (!empty($terms) && !is_wp_error($terms)) {
            return implode(', ', $terms);
        }
        return '—';
    }
    return $value;
}

/**
 * (OPCIONAL) Permitir filtrado por modalidad en listado de usuarios
 */
add_action('restrict_manage_users', 'trekkium_add_modalidad_user_filter');
function trekkium_add_modalidad_user_filter() {
    $taxonomy = 'modalidad';
    $terms = get_terms($taxonomy, array('hide_empty' => false));
    
    if (!empty($terms)) {
        ?>
        <select name="<?php echo $taxonomy; ?>" id="<?php echo $taxonomy; ?>" class="postform">
            <option value="">Todas las modalidades</option>
            <?php foreach ($terms as $term) : ?>
                <option value="<?php echo $term->slug; ?>" <?php selected(isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '', $term->slug); ?>>
                    <?php echo $term->name; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
}

add_filter('pre_get_users', 'trekkium_filter_users_by_modalidad');
function trekkium_filter_users_by_modalidad($query) {
    global $pagenow;
    
    if (is_admin() && $pagenow == 'users.php' && isset($_GET['modalidad']) && $_GET['modalidad'] != '') {
        $taxonomy = 'modalidad';
        $term_slug = sanitize_text_field($_GET[$taxonomy]);
        $term = get_term_by('slug', $term_slug, $taxonomy);
        
        if ($term) {
            $user_ids = get_objects_in_term($term->term_id, $taxonomy);
            if (!empty($user_ids)) {
                $query->set('include', $user_ids);
            }
        }
    }
}