<?php
/**
 * Snippet: Campo DNI / NIE / Pasaporte para usuarios
 * Estado: INACTIVO (guardado en la base de datos pero no se muestra ni se usa)
 * Uso futuro: Trekkium como agencia de viajes
 */

// ---------------------------
// 1. Función para mostrar el campo en el perfil de usuario (admin)
// ---------------------------
function mostrar_dni_en_admin($user) {
    // Esta función genera el HTML del campo DNI en el perfil de usuario
    // Actualmente NO está enganchada a ningún hook, por lo que no se muestra
    ?>
    <h2>Información adicional</h2>
    <table class="form-table">
        <tr>
            <th><label for="billing_dni">DNI / NIE / Pasaporte</label></th>
            <td>
                <input type="text" name="billing_dni" id="billing_dni" 
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'billing_dni', true)); ?>" 
                       class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}

// ---------------------------
// 2. Función para guardar el campo desde el admin
// ---------------------------
function guardar_dni_en_admin($user_id) {
    // Comprueba si el usuario tiene permisos para editar este perfil
    if (current_user_can('edit_user', $user_id) && isset($_POST['billing_dni'])) {
        // Guarda el valor de manera segura en la meta del usuario
        update_user_meta($user_id, 'billing_dni', sanitize_text_field($_POST['billing_dni']));
    }
}

// ---------------------------
// 3. Hooks desactivados por ahora
// ---------------------------
// Para mostrar y guardar el campo en el perfil de usuario,
// simplemente descomenta estas líneas en el futuro:

// add_action('show_user_profile', 'mostrar_dni_en_admin');
// add_action('edit_user_profile', 'mostrar_dni_en_admin');
// add_action('personal_options_update', 'guardar_dni_en_admin');
// add_action('edit_user_profile_update', 'guardar_dni_en_admin');

// ---------------------------
// 4. Notas importantes
// ---------------------------
// - El meta 'billing_dni' sigue existiendo en la base de datos, seguro para uso futuro.
// - Actualmente el campo no es obligatorio, no rompe registros ni reservas.
// - Se puede rellenar manualmente desde código o importaciones si es necesario.
// - Activar solo cuando Trekkium sea agencia de viajes y necesites recopilar DNI/NIE/Pasaporte.
