<?php
/**
 * Snippet: checkout-seccion-detalles-facturacion
 * Shortcode: [checkout-detalles-facturacion]
 */

function shortcode_checkout_detalles_facturacion() {
    if (!class_exists('WC_Countries')) {
        return 'WooCommerce no está activo';
    }

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $billing_first_name = get_user_meta($user_id, 'billing_first_name', true);
    $billing_last_name  = get_user_meta($user_id, 'billing_last_name', true);
    $billing_phone      = get_user_meta($user_id, 'billing_phone', true);
    $billing_email      = get_user_meta($user_id, 'billing_email', true) ?: $current_user->user_email;
    $billing_country    = get_user_meta($user_id, 'billing_country', true);
    $billing_state      = get_user_meta($user_id, 'billing_state', true);
    $billing_city       = get_user_meta($user_id, 'billing_city', true);
    $billing_postcode   = get_user_meta($user_id, 'billing_postcode', true);

    if (empty($billing_first_name)) {
        $billing_first_name = get_user_meta($user_id, 'first_name', true);
    }
    if (empty($billing_last_name)) {
        $billing_last_name = get_user_meta($user_id, 'last_name', true);
    }

    $wc_countries = new WC_Countries();
    $countries    = $wc_countries->get_countries();
    $states       = $wc_countries->get_states();

    ob_start();
    ?>
    <div class="checkout-contenedor-seccion">
        
        <div class="checkout-seccion-titulo">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 24 24">
                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
            </svg>
            <span class="checkout-seccion-titulo-texto">DETALLES DE FACTURACION</span>
        </div>
        
        <div class="checkout-seccion-contenido">
            <form id="facturacion-form" class="facturacion-form">
                <!-- Primera fila: Nombre y Apellidos -->
                <div class="facturacion-grid-2">
                    <div class="facturacion-columna">
                        <div class="facturacion-titular">Nombre</div>
                        <input type="text" name="billing_first_name" class="facturacion-input" value="<?php echo esc_attr($billing_first_name); ?>" required>
                    </div>
                    <div class="facturacion-columna">
                        <div class="facturacion-titular">Apellidos</div>
                        <input type="text" name="billing_last_name" class="facturacion-input" value="<?php echo esc_attr($billing_last_name); ?>" required>
                    </div>
                </div>
                
                <!-- Segunda fila: Teléfono y Email -->
                <div class="facturacion-grid-2">
                    <div class="facturacion-columna">
                        <div class="facturacion-titular">Teléfono</div>
                        <input type="text" name="billing_phone" class="facturacion-input" value="<?php echo esc_attr($billing_phone); ?>" required>
                    </div>
                    <div class="facturacion-columna">
                        <div class="facturacion-titular">Correo electrónico</div>
                        <input type="email" name="billing_email" class="facturacion-input" value="<?php echo esc_attr($billing_email); ?>" required>
                    </div>
                </div>
                
                <!-- Tercera fila: País y Provincia/Región -->
                <div class="facturacion-grid-2">
                    <div class="facturacion-columna">
                        <div class="facturacion-titular">País</div>
                        <select name="billing_country" id="billing_country" class="facturacion-select" required>
                            <option value="">Selecciona un país</option>
                            <?php foreach ($countries as $code => $name): ?>
                                <option value="<?php echo esc_attr($code); ?>" <?php selected($billing_country, $code); ?>>
                                    <?php echo esc_html($name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="facturacion-columna">
                        <div class="facturacion-titular">Provincia/Región</div>
                        <select name="billing_state" id="billing_state" class="facturacion-select" required>
                            <option value="">Selecciona un país primero</option>
                            <?php if ($billing_country && isset($states[$billing_country])): ?>
                                <?php foreach ($states[$billing_country] as $code => $name): ?>
                                    <option value="<?php echo esc_attr($code); ?>" <?php selected($billing_state, $code); ?>>
                                        <?php echo esc_html($name); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Cuarta fila: Población y CP -->
                <div class="facturacion-grid-2">
                    <div class="facturacion-columna">
                        <div class="facturacion-titular">Población</div>
                        <input type="text" name="billing_city" class="facturacion-input" value="<?php echo esc_attr($billing_city); ?>" required>
                    </div>
                    <div class="facturacion-columna">
                        <div class="facturacion-titular">CP</div>
                        <input type="text" name="billing_postcode" class="facturacion-input" value="<?php echo esc_attr($billing_postcode); ?>" required>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        const statesData = <?php echo json_encode($states); ?>;
        
        function updateStates() {
            const country = $('#billing_country').val();
            const stateSelect = $('#billing_state');
            const currentState = '<?php echo esc_js($billing_state); ?>';
            
            stateSelect.empty();
            
            if (country && statesData[country]) {
                stateSelect.append($('<option>', {
                    value: '',
                    text: 'Selecciona una provincia/región'
                }));
                
                $.each(statesData[country], function(code, name) {
                    stateSelect.append($('<option>', {
                        value: code,
                        text: name,
                        selected: (code === currentState)
                    }));
                });
            } else {
                stateSelect.append($('<option>', {
                    value: '',
                    text: 'Selecciona un país primero'
                }));
            }
        }
        
        $('#billing_country').change(updateStates);
        
        <?php if (!empty($billing_country)): ?>
        updateStates();
        <?php endif; ?>
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('checkout_detalles_facturacion', 'shortcode_checkout_detalles_facturacion');
