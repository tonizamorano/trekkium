<?php
// 1️⃣ Toggle tipo switch en el perfil de usuario (solo admins, solo guías)
add_action('show_user_profile', 'trekkium_toggle_guia_activo');
add_action('edit_user_profile', 'trekkium_toggle_guia_activo');

function trekkium_toggle_guia_activo($user) {
    if( in_array('guia', (array) $user->roles, true) && current_user_can('manage_options') ) {
        $activo = get_user_meta($user->ID, 'guia_activo', true);
        ?>
        <h2>Estado del guía</h2>
        <table class="form-table">
            <tr>
                <th>Guía activo</th>
                <td>
                    <label class="trekkium-switch">
                        <input type="checkbox" name="guia_activo" value="1" <?php checked($activo, 1); ?> />
                        <span class="slider"></span>
                    </label>
                    <p class="description">Solo los administradores pueden cambiar este estado.</p>
                </td>
            </tr>
        </table>
        <style>
            .trekkium-switch { position: relative; display: inline-block; width: 60px; height: 28px; }
            .trekkium-switch input { display:none; }
            .trekkium-switch .slider { position: absolute; cursor: pointer; top:0; left:0; right:0; bottom:0; background-color:#ccc; transition:0.4s; border-radius:34px; }
            .trekkium-switch .slider:before { position:absolute; content:""; height:22px; width:22px; left:3px; bottom:3px; background-color:white; transition:0.4s; border-radius:50%; }
            .trekkium-switch input:checked + .slider { background-color:#0b568b; }
            .trekkium-switch input:checked + .slider:before { transform:translateX(32px); }
        </style>
        <?php
    }
}

// 2️⃣ Guardar toggle
add_action('personal_options_update', 'trekkium_guardar_guia_activo');
add_action('edit_user_profile_update', 'trekkium_guardar_guia_activo');

function trekkium_guardar_guia_activo($user_id) {
    $user = get_userdata($user_id);
    if( !current_user_can('manage_options') || !in_array('guia', (array) $user->roles, true) ) return;
    $activo = isset($_POST['guia_activo']) ? 1 : 0;
    update_user_meta($user_id, 'guia_activo', $activo);
}

// 3️⃣ Columna de estado en la tabla de usuarios (solo guías)
add_filter( 'manage_users_columns', 'trekkium_agregar_columna_estado' );
function trekkium_agregar_columna_estado( $columns ) {
    $columns['guia_estado'] = 'Estado';
    return $columns;
}

add_action( 'manage_users_custom_column', 'trekkium_mostrar_estado_columna', 10, 3 );
function trekkium_mostrar_estado_columna( $value, $column_name, $user_id ) {
    if ( 'guia_estado' === $column_name ) {
        $user = get_userdata($user_id);
        if ( in_array('guia', (array) $user->roles, true) ) {
            $activo = get_user_meta( $user_id, 'guia_activo', true );
            return $activo ? 'ACTIVO' : 'INACTIVO';
        } else {
            return ''; // Para otros roles no mostrar nada
        }
    }
    return $value;
}
