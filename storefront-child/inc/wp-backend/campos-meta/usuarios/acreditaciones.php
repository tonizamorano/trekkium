<?php
// Añadir metabox de Acreditaciones para usuarios con rol "guia"
add_action('show_user_profile', 'guia_acreditaciones_metabox');
add_action('edit_user_profile', 'guia_acreditaciones_metabox');

function guia_acreditaciones_metabox($user) {
    if (!in_array('guia', (array) $user->roles)) {
        return;
    }

    // Recuperar valores guardados
    $acreditaciones = get_user_meta($user->ID, 'acreditaciones', true);
    if (!is_array($acreditaciones)) {
        $acreditaciones = [];
    }

    // Opciones disponibles
    $opciones = [
        'AEGM' => 'AEGM',
        'AEGM - Alta montaña' => 'AEGM - Alta montaña',
        'AEGM - Escalada' => 'AEGM - Escalada',
        'AEGM - Barrancos' => 'AEGM - Barrancos',
        'UIMLA' => 'UIMLA',
        'UIAGM' => 'UIAGM',
    ];
    ?>
    <h2>Acreditaciones</h2>
    <table class="form-table">
        <tr>
            <th><label for="acreditaciones">Seleccione acreditaciones</label></th>
            <td>
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <?php foreach ($opciones as $key => $label): ?>
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="checkbox" name="acreditaciones[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $acreditaciones)); ?>>
                            <?php echo esc_html($label); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </td>
        </tr>
    </table>
    <?php
}

// Guardar las acreditaciones al actualizar el perfil
add_action('personal_options_update', 'guia_acreditaciones_save');
add_action('edit_user_profile_update', 'guia_acreditaciones_save');

function guia_acreditaciones_save($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (isset($_POST['acreditaciones']) && is_array($_POST['acreditaciones'])) {
        // Sanitizar y guardar
        $acreditaciones = array_map('sanitize_text_field', $_POST['acreditaciones']);
        update_user_meta($user_id, 'acreditaciones', $acreditaciones);
    } else {
        // Si no hay ninguna seleccionada, borrar
        delete_user_meta($user_id, 'acreditaciones');
    }
}
