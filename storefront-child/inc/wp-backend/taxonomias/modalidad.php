<?php
/**
 * TAXONOMÍA GLOBAL: MODALIDAD
 * Productos + Usuarios (guía y cliente)
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
        'show_admin_column' => true,
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
 * REGLAS MODALIDAD POR TITULACIÓN (GUÍAS)
 */
function trekkium_modalidades_por_titulacion($titulacion) {
    $map = array(
        'TD2 Media montaña' => array('Senderismo','Trekking','Raquetas de nieve'),
        'TD2 Barrancos'     => array('Senderismo','Trekking','Barranquismo','Vía ferrata'),
        'TD2 Escalada'      => array('Senderismo','Trekking','Escalada','Vía ferrata'),
        'TD3 Escalada'      => array('Senderismo','Trekking','Escalada','Vía ferrata'),
        'TD3 Alta montaña'  => array('Senderismo','Trekking','Escalada','Alpinismo','Raquetas de nieve','Vía ferrata')
    );
    return $map[$titulacion] ?? array();
}

/**
 * ASIGNAR AUTOMÁTICAMENTE MODALIDAD AL GUÍA SEGÚN TITULACIÓN
 */
add_action('edit_user_profile_update', 'trekkium_autocompletar_modalidad_guia', 30);
add_action('personal_options_update', 'trekkium_autocompletar_modalidad_guia', 30);
function trekkium_autocompletar_modalidad_guia($user_id) {
    $user = get_userdata($user_id);
    if (!$user || !in_array('guia', $user->roles)) return;

    $titulaciones = wp_get_object_terms($user_id, 'titulacion', array('fields' => 'names'));
    if (empty($titulaciones)) return;

    $modalidades_final = [];
    foreach ($titulaciones as $tit) {
        $modalidades_final = array_merge($modalidades_final, trekkium_modalidades_por_titulacion($tit));
    }
    $modalidades_final = array_unique($modalidades_final);

    if (!empty($modalidades_final)) {
        wp_set_object_terms($user_id, $modalidades_final, 'modalidad', false);
    }
}

/**
 * BLOQUEAR edición manual de modalidad para guías
 */
add_action('admin_head', function(){
    $user = wp_get_current_user();
    if (in_array('author', $user->roles)) {
        echo '<style>#modalidaddiv { display:none !important; }</style>';
    }
});

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
 * CLIENTES: Modalidad obligatoria múltiple (nunca vacío) y metabox de selección
 */
add_action('show_user_profile', 'trekkium_modalidad_user_profile');
add_action('edit_user_profile', 'trekkium_modalidad_user_profile');
function trekkium_modalidad_user_profile($user) {
    $user_roles = $user->roles;
    $terms = wp_get_object_terms($user->ID, 'modalidad', array('fields' => 'names'));
    $all_terms = get_terms(array('taxonomy' => 'modalidad','hide_empty' => false));

    echo '<h2>Modalidad</h2><table class="form-table"><tbody>';

    if (in_array('guia', $user_roles)) {
        echo '<tr><th>Modalidades asignadas</th><td>';
        echo !empty($terms) ? implode(', ', $terms) : 'Sin asignar';
        echo '</td></tr>';
    }

    if (in_array('customer', $user_roles)) {
        echo '<tr><th>Seleccionar modalidades</th><td>';
        foreach ($all_terms as $term) {
            $checked = in_array($term->name, $terms) ? 'checked' : '';
            echo '<label style="display:block;margin-bottom:4px;">';
            echo '<input type="checkbox" name="modalidad_user[]" value="' . esc_attr($term->term_id) . '" ' . $checked . '> ';
            echo esc_html($term->name);
            echo '</label>';
        }
        echo '</td></tr>';
    }

    echo '</tbody></table>';
}

add_action('personal_options_update', 'trekkium_save_modalidad_user');
add_action('edit_user_profile_update', 'trekkium_save_modalidad_user');
function trekkium_save_modalidad_user($user_id) {
    $user = get_userdata($user_id);
    if (!$user || !in_array('customer', $user->roles)) return;

    if (isset($_POST['modalidad_user']) && is_array($_POST['modalidad_user'])) {
        $term_ids = array_map('intval', $_POST['modalidad_user']);
        wp_set_object_terms($user_id, $term_ids, 'modalidad', false);
    } else {
        wp_set_object_terms($user_id, array('Senderismo'), 'modalidad', false);
    }
}
