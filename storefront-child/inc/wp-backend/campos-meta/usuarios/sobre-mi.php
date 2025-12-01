<?php
/**
 * Mostrar y guardar campo "Sobre mí" para usuarios con rol guia
 */

// Mostrar campo en el perfil y en el formulario de nuevo usuario
add_action('show_user_profile', 'mostrar_sobre_mi');
add_action('edit_user_profile', 'mostrar_sobre_mi');
add_action('user_new_form', 'mostrar_sobre_mi');

function mostrar_sobre_mi($user) {
    $user_id = isset($user->ID) ? $user->ID : 0;
    $user_roles = $user->roles ?? (isset($_POST['role']) ? array($_POST['role']) : array());
    
    if (!$user_id && isset($_POST['role'])) {
        $user_roles = array($_POST['role']);
    }

    if (!array_intersect($user_roles, array('guia'))) return;

    $sobre_mi = get_user_meta($user_id, 'sobre_mi', true);

    wp_nonce_field('sobre_mi_nonce', 'sobre_mi_nonce_field');
    ?>
    <h2>Datos profesionales</h2>
    <table class="form-table">
        <tr>
            <th><label for="sobre_mi">Sobre mí</label></th>
            <td>
                <?php
                wp_editor($sobre_mi, 'sobre_mi_editor', array(
                    'textarea_name' => 'sobre_mi',
                    'textarea_rows' => 10,
                    'teeny' => false,
                    'media_buttons' => false,
                    'tinymce' => array(
                        'toolbar1' => 'bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,undo,redo',
                    ),
                ));
                ?>
            </td>
        </tr>
    </table>
    <?php
}

// Guardar campo
add_action('personal_options_update', 'guardar_sobre_mi');
add_action('edit_user_profile_update', 'guardar_sobre_mi');
add_action('user_register', 'guardar_sobre_mi');

function guardar_sobre_mi($user_id) {
    if (!current_user_can('edit_user', $user_id)) return false;
    if (!isset($_POST['sobre_mi_nonce_field']) || !wp_verify_nonce($_POST['sobre_mi_nonce_field'], 'sobre_mi_nonce')) return;

    $user = get_userdata($user_id);
    if (!$user || !array_intersect($user->roles, array('guia'))) return;

    if (isset($_POST['sobre_mi'])) {
        update_user_meta($user_id, 'sobre_mi', wp_kses_post($_POST['sobre_mi']));
    }
}
