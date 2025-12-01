<?php
/**
 * Mostrar y autocompletar campo "Comunidad autónoma" para todos los usuarios
 */

// Mostrar campo en el perfil y en el formulario de nuevo usuario
add_action('show_user_profile', 'mostrar_comunidad_autonoma');
add_action('edit_user_profile', 'mostrar_comunidad_autonoma');
add_action('user_new_form', 'mostrar_comunidad_autonoma');

function mostrar_comunidad_autonoma($user) {
    $user_id = isset($user->ID) ? $user->ID : 0;
    $comunidad_autonoma = get_user_meta($user_id, 'comunidad_autonoma', true);
    ?>
    <h2>Ubicación profesional</h2>
    <table class="form-table">
        <tr>
            <th><label for="comunidad_autonoma">Comunidad autónoma</label></th>
            <td>
                <input type="text" 
                       id="comunidad_autonoma" 
                       name="comunidad_autonoma_display" 
                       value="<?php echo esc_attr($comunidad_autonoma); ?>" 
                       readonly 
                       style="background-color: #f5f5f5; border: 1px solid #ddd; padding: 8px; color: #666; width: 100%;">
                <p class="description">Calculado automáticamente según la provincia o país</p>
                <input type="hidden" name="comunidad_autonoma" value="<?php echo esc_attr($comunidad_autonoma); ?>">
            </td>
        </tr>
    </table>
    <?php
}

// Autocompletar automáticamente según billing_country / billing_state
add_action('profile_update', 'autofill_comunidad_autonoma', 20, 2);
add_action('user_register', 'autofill_comunidad_autonoma', 20, 1);

function autofill_comunidad_autonoma($user_id, $old_user_data = null) {
    $billing_country = get_user_meta($user_id, 'billing_country', true);
    if (empty($billing_country)) return;

    if ($billing_country !== 'ES') {
        if (class_exists('WC_Countries')) {
            $countries = new WC_Countries();
            $country_name = $countries->countries[$billing_country] ?? $billing_country;
        } else {
            $country_name = $billing_country;
        }
        update_user_meta($user_id, 'comunidad_autonoma', $country_name);
        return;
    }

    $billing_state = get_user_meta($user_id, 'billing_state', true);
    if (empty($billing_state)) return;

    $map = array(
        'AL'=>'Andalucía','CA'=>'Andalucía','CO'=>'Andalucía','GR'=>'Andalucía','HU'=>'Aragón','JA'=>'Andalucía',
        'MA'=>'Andalucía','SE'=>'Andalucía','TE'=>'Aragón','Z'=>'Aragón','O'=>'Asturias','PM'=>'Illes Balears',
        'GC'=>'Canarias','TF'=>'Canarias','S'=>'Cantabria','AV'=>'Castilla y León','BU'=>'Castilla y León','LE'=>'Castilla y León',
        'P'=>'Castilla y León','SA'=>'Castilla y León','SG'=>'Castilla y León','SO'=>'Castilla y León','VA'=>'Castilla y León','ZA'=>'Castilla y León',
        'AB'=>'Castilla-La Mancha','CR'=>'Castilla-La Mancha','CU'=>'Castilla-La Mancha','GU'=>'Castilla-La Mancha','TO'=>'Castilla-La Mancha',
        'B'=>'Catalunya','GI'=>'Catalunya','L'=>'Catalunya','T'=>'Catalunya','CE'=>'Ceuta',
        'A'=>'Comunidad Valenciana','CS'=>'Comunidad Valenciana','V'=>'Comunidad Valenciana',
        'BA'=>'Extremadura','CC'=>'Extremadura','C'=>'Galicia','LU'=>'Galicia','OR'=>'Galicia','PO'=>'Galicia',
        'M'=>'Madrid','ML'=>'Melilla','MU'=>'Murcia','NA'=>'Navarra',
        'BI'=>'Euskadi','SS'=>'Euskadi','VI'=>'Euskadi','LO'=>'La Rioja',
    );

    if (isset($map[$billing_state])) {
        update_user_meta($user_id, 'comunidad_autonoma', $map[$billing_state]);
    }
}
