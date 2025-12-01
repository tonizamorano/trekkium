<?php
// Hook para registrar las taxonomías
add_action('init', 'crear_taxonomias_para_usuarios');

function crear_taxonomias_para_usuarios() {

    $roles_permitidos = array('guia');

    // Taxonomías a crear (modalidad eliminada)
    $taxonomias = array(
        'titulacion' => array(
            'label' => 'Titulación',
            'terms' => array('TD2 Media montaña','TD2 Escalada','TD2 Barrancos','TD3 Escalada','TD3 Alta montaña')
        ),
        'idiomas' => array(
            'label' => 'Idiomas',
            'terms' => array('Español','Català','Galego','Euskera','Inglés','Francés','Italiano','Portugués','Alemán')
        )
    );

    foreach ($taxonomias as $slug => $data) {
        register_taxonomy(
            $slug,
            'user',
            array(
                'public' => true,
                'labels' => array(
                    'name' => $data['label'],
                    'singular_name' => $data['label'],
                ),
                'hierarchical' => true,
                'show_ui' => false,
                'show_in_rest' => true,
            )
        );

        // Crear términos si no existen
        foreach ($data['terms'] as $term) {
            if (!term_exists($term, $slug)) {
                wp_insert_term($term, $slug);
            }
        }
    }
}


// Mostrar taxonomías en el perfil del usuario
add_action('show_user_profile', 'mostrar_taxonomias_usuario');
add_action('edit_user_profile', 'mostrar_taxonomias_usuario');

function mostrar_taxonomias_usuario($user) {

    $roles_permitidos = array('guia');
    if (!array_intersect($roles_permitidos, $user->roles)) return;

    echo '<h2>Información adicional</h2>';

    $editable_tax = array(
        'titulacion' => 'Titulación',
        'idiomas'    => 'Idiomas'
    );

    $orden_titulacion = array(
        'TD2 Media montaña',
        'TD2 Escalada',
        'TD2 Barrancos',
        'TD3 Escalada',
        'TD3 Alta montaña'
    );

    foreach ($editable_tax as $slug => $label) {

        $terms = get_terms(array(
            'taxonomy' => $slug,
            'hide_empty' => false
        ));

        if ($slug === 'titulacion') {
            usort($terms, function($a, $b) use ($orden_titulacion) {
                $pos_a = array_search($a->name, $orden_titulacion);
                $pos_b = array_search($b->name, $orden_titulacion);
                return $pos_a - $pos_b;
            });
        }

        $user_terms = wp_get_object_terms($user->ID, $slug, array('fields' => 'ids'));

        echo '<table class="form-table"><tr><th><label>'.$label.'</label></th><td>';
        echo '<div style="display:flex; gap:15px; flex-wrap:wrap;">';

        foreach ($terms as $term) {
            $checked = in_array($term->term_id, $user_terms) ? 'checked' : '';
            echo '<label style="display:flex; align-items:center;">
                    <input type="checkbox" name="'.$slug.'[]" value="'.$term->term_id.'" '.$checked.'>
                    '.$term->name.'
                  </label>';
        }

        echo '</div></td></tr></table>';
    }
}


// Guardar taxonomías de usuario al actualizar perfil
add_action('personal_options_update', 'guardar_taxonomias_usuario');
add_action('edit_user_profile_update', 'guardar_taxonomias_usuario');

function guardar_taxonomias_usuario($user_id) {

    if (!current_user_can('edit_user', $user_id)) return;

    // Guardar titulaciones
    if (isset($_POST['titulacion'])) {
        $tit_ids = array_map('intval', $_POST['titulacion']);
        wp_set_object_terms($user_id, $tit_ids, 'titulacion', false);
    } else {
        wp_set_object_terms($user_id, array(), 'titulacion', false);
    }

    // Guardar idiomas
    if (isset($_POST['idiomas'])) {
        $idiomas_ids = array_map('intval', $_POST['idiomas']);
        wp_set_object_terms($user_id, $idiomas_ids, 'idiomas', false);
    } else {
        wp_set_object_terms($user_id, array(), 'idiomas', false);
    }

}
