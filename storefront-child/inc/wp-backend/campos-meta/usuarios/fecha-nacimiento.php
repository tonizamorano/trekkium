<?php
// Añadir el campo "Fecha de nacimiento" al perfil de usuario en WP Admin
add_action('show_user_profile', 'agregar_fecha_nacimiento');
add_action('edit_user_profile', 'agregar_fecha_nacimiento');
add_action('admin_enqueue_scripts', 'cargar_scripts_datepicker');

function cargar_scripts_datepicker($hook) {
    if ($hook === 'profile.php' || $hook === 'user-edit.php') {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css');
    }
}

function agregar_fecha_nacimiento($user) {
    $fecha = get_user_meta($user->ID, 'fecha_nacimiento', true);
    ?>
    <h2>Información adicional</h2>
    <table class="form-table">
        <tr>
            <th><label for="fecha_nacimiento">Fecha de nacimiento</label></th>
            <td>
                <input type="text" name="fecha_nacimiento" id="fecha_nacimiento" 
                       value="<?php echo esc_attr($fecha); ?>" 
                       class="regular-text" placeholder="dd/mm/aaaa" />
                <p class="description">Haga clic para seleccionar la fecha</p>
                
                <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('#fecha_nacimiento').datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: '-100:+0',
                        maxDate: new Date()
                    });
                });
                </script>
                
                <style>
                .ui-datepicker {
                    font-size: 12px;
                }
                </style>
            </td>
        </tr>
    </table>
    <?php
}

// Guardar el campo al actualizar perfil
add_action('personal_options_update', 'guardar_fecha_nacimiento');
add_action('edit_user_profile_update', 'guardar_fecha_nacimiento');

function guardar_fecha_nacimiento($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    if (isset($_POST['fecha_nacimiento']) && !empty($_POST['fecha_nacimiento'])) {
        $fecha = sanitize_text_field($_POST['fecha_nacimiento']);
        update_user_meta($user_id, 'fecha_nacimiento', $fecha);
    } else {
        delete_user_meta($user_id, 'fecha_nacimiento');
    }
}