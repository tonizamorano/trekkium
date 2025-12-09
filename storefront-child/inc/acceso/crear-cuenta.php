<?php
// ======= Shortcode [pagina_crear_cuenta] =======
add_shortcode('pagina_crear_cuenta', 'trekkium_pagina_crear_cuenta');

function trekkium_pagina_crear_cuenta() {
    $mensaje_error = get_transient('trekkium_registro_error') ?: '';
    $mensaje_exito = get_transient('trekkium_registro_success') ?: '';
    delete_transient('trekkium_registro_error');
    delete_transient('trekkium_registro_success');

    // CÁLCULO DE LA FECHA MÁXIMA PERMITIDA (18 años atrás)
    $max_date = date('Y-m-d', strtotime('-18 years'));
    
    // Procesar el teléfono del POST si existe (igual que en el otro snippet)
    $default_prefix = '+34';
    $phone_prefix = $default_prefix;
    $phone_number = '';
    $full_phone = isset($_POST['billing_phone']) ? sanitize_text_field($_POST['billing_phone']) : '';
    
    if (!empty($full_phone)) {
        $clean_phone = preg_replace('/\s+/', '', $full_phone);
        if (preg_match('/^(\+\d+)(\d{9})$/', $clean_phone, $matches)) {
            $phone_prefix = $matches[1];
            $phone_number = $matches[2];
        } else {
            $phone_number = $full_phone; 
            $phone_prefix = $default_prefix;
        }
    }
    $phone_number = preg_replace('/\s+/', '', $phone_number);

    ob_start(); ?>
    <div class="crear-cuenta-contenedor">

        <?php if ($mensaje_error || $mensaje_exito): ?>
            <div id="trekkium-modal" class="trekkium-modal">
                <div class="trekkium-modal-content <?php echo $mensaje_error ? 'error' : 'success'; ?>">
                    <span class="trekkium-modal-close">&times;</span>
                    <p><?php echo esc_html($mensaje_error ?: $mensaje_exito); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="crear-cuenta-titulo-seccion">
            <h2>Nueva cuenta de usuario</h2>
        </div>

        <div class="crear-cuenta-contenido">

            <form class="crear-cuenta-form" id="crear-cuenta-form" method="post">

                <div class="crear-cuenta-form-grid">

                    <div class="crear-cuenta-form-grid-col">
                        <label for="account_first_name">Nombre*</label>
                        <input type="text" id="account_first_name" name="account_first_name" value="<?php echo isset($_POST['account_first_name']) ? esc_attr($_POST['account_first_name']) : ''; ?>" required>
                    </div>

                    <div class="crear-cuenta-form-grid-col">
                        <label for="account_last_name">Apellidos*</label>
                        <input type="text" id="account_last_name" name="account_last_name" value="<?php echo isset($_POST['account_last_name']) ? esc_attr($_POST['account_last_name']) : ''; ?>" required>
                    </div>

                </div>

                <div class="crear-cuenta-form-grid">

                    <div class="crear-cuenta-form-grid-col">
                        <label for="account_display_name">Nombre visible*</label>
                        <input type="text" id="account_display_name" name="account_display_name" value="<?php echo isset($_POST['account_display_name']) ? esc_attr($_POST['account_display_name']) : ''; ?>" required>
                    </div>

                    <div class="crear-cuenta-form-grid-col">
                        <label for="fecha_nacimiento">Fecha de nacimiento*</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo isset($_POST['fecha_nacimiento']) ? esc_attr($_POST['fecha_nacimiento']) : ''; ?>" required max="<?php echo esc_attr($max_date); ?>" pattern="\d{4}-\d{2}-\d{2}">
                    </div>

                </div>

                <div class="crear-cuenta-form-grid">

                    <div class="crear-cuenta-form-grid-col">

                        <label for="billing_phone_number">Teléfono móvil*</label>

                        <div class="mc-phone-input-group">  
                            <input type="text" class="mc-phone-prefix" name="billing_phone_prefix" id="billing_phone_prefix" value="<?php echo esc_attr($phone_prefix); ?>" placeholder="+XX" pattern="\+\d+" title="Debe empezar con '+' seguido de dígitos." required aria-required="true"/>
                            <input type="tel" class="mc-phone-number" name="billing_phone_number" id="billing_phone_number" value="<?php echo esc_attr($phone_number); ?>" pattern="\d{9}" maxlength="9" title="9 dígitos sin espacios" required aria-required="true"/>
                        </div>

                        <input type="hidden" name="billing_phone" id="billing_phone" value="<?php echo esc_attr($full_phone); ?>" />

                    </div>

                    <div class="crear-cuenta-form-grid-col">
                        <label for="account_email">Correo electrónico*</label>
                        <input type="email" id="account_email" name="account_email" value="<?php echo isset($_POST['account_email']) ? esc_attr($_POST['account_email']) : ''; ?>" required>
                    </div>

                </div>

                <div class="crear-cuenta-form-grid">

                    <div class="crear-cuenta-form-grid-col">
                        <label for="billing_country">País de residencia*</label>
                        <?php
                        // Comprobar si WC está cargado antes de usarlo
                        if ( function_exists( 'WC' ) ) {
                            $countries = WC()->countries->get_allowed_countries();
                            $selected_country = isset($_POST['billing_country']) ? $_POST['billing_country'] : '';
                            echo '<select id="billing_country" name="billing_country" required>';
                            echo '<option value="">Selecciona un país</option>';
                            foreach ($countries as $code => $name) {
                                $selected = ($code === $selected_country) ? 'selected' : '';
                                echo '<option value="' . esc_attr($code) . '" ' . $selected . '>' . esc_html($name) . '</option>';
                            }
                            echo '</select>';
                        } else {
                            echo '<input type="text" id="billing_country" name="billing_country" value="' . (isset($_POST['billing_country']) ? esc_attr($_POST['billing_country']) : '') . '" required />';
                        }
                        ?>
                    </div>

                    <div class="crear-cuenta-form-grid-col">
                        <label for="billing_state">Provincia*</label>
                        <select id="billing_state" name="billing_state" required>
                            <option value="">Selecciona una provincia</option>
                        </select>
                    </div>

                </div>

                <div class="crear-cuenta-form-grid" style="margin-top: 15px;"> 

                    <div class="crear-cuenta-form-grid-col">
                        <label for="account_password">Contraseña*</label>
                        <input type="password" id="account_password" name="account_password" required minlength="8" autocomplete="new-password">
                        <small class="password-hint">Mínimo 8 caracteres, usa letras, números y símbolos.</small>
                    </div>

                    <div class="crear-cuenta-form-grid-col">
                        <label for="account_password_repeat">Repetir contraseña*</label>
                        <input type="password" id="account_password_repeat" name="account_password_repeat" required minlength="8" autocomplete="new-password">
                    </div>
                    
                </div>

                <div class="crear-cuenta-sugerencia" id="sugerir-container">
                    <button type="button" id="generar-password" class="crear-cuenta-boton-secundario">Sugerir contraseña segura</button>
                    <span id="password-sugerida" style="margin-left:10px;color:#0b568b;font-weight:600;"></span>
                </div>

                <div class="crear-cuenta-seccion-requisitos">
                    <label>
                        <input type="checkbox" name="terminos" <?php echo isset($_POST['terminos']) ? 'checked' : ''; ?> required>
                        He leído y acepto los <a href="/aviso-legal/" target="_blank">Términos y Condiciones Legales</a>.
                    </label>
                    <br>
                    <label>
                        <input type="checkbox" name="privacidad" <?php echo isset($_POST['privacidad']) ? 'checked' : ''; ?> required>
                        Acepto la <a href="/politica-privacidad/" target="_blank">Política de Privacidad</a>.
                    </label>
                </div>

                <div class="crear-cuenta-boton-seccion">
                    <button type="submit" class="crear-cuenta-boton">Crear cuenta</button>
                </div>

                <?php wp_nonce_field('crear_cuenta_nonce', 'crear_cuenta_nonce_field'); ?>
            </form>
        </div>
    </div>

    <script>
    (function(){
        const ajaxBase = '<?php echo admin_url('admin-ajax.php'); ?>';
        const countrySelect = document.getElementById('billing_country');
        const stateSelect = document.getElementById('billing_state');
        const generarBtn = document.getElementById('generar-password');
        const passwordSugerida = document.getElementById('password-sugerida');
        const sugerirContainer = document.getElementById('sugerir-container');
        const prefixField = document.getElementById('billing_phone_prefix');
        const numberField = document.getElementById('billing_phone_number');
        const hiddenPhoneField = document.getElementById('billing_phone');
        const form = document.getElementById('crear-cuenta-form');

        // Estado seleccionado (si viene de POST)
        const selectedState = '<?php echo isset($_POST['billing_state']) ? esc_js($_POST['billing_state']) : ''; ?>';

        // Función para actualizar el campo oculto del teléfono (IGUAL QUE EN EL OTRO SNIPPET)
        function updateHiddenPhoneField() {
            if (prefixField && numberField && hiddenPhoneField) {
                var prefix = prefixField.value.trim().replace(/\s/g, '');
                var number = numberField.value.trim().replace(/\s/g, '');
                hiddenPhoneField.value = prefix + number; 
            }
        }

        // Función para poblar estados/provincias
        function loadStates(country, preselect) {
            if (!country) {
                stateSelect.innerHTML = '<option value="">Selecciona una provincia</option>';
                return;
            }

            stateSelect.innerHTML = '<option value="">Cargando...</option>';

            fetch( ajaxBase + '?action=get_states_by_country&country=' + encodeURIComponent(country) )
                .then(function(response){ return response.json(); })
                .then(function(data){
                    stateSelect.innerHTML = '<option value="">Selecciona una provincia</option>';
                    if (!data || Object.keys(data).length === 0) {
                        // Si el país no tiene estados en WC, dejar vacío
                        return;
                    }
                    Object.entries(data).forEach(function(entry){
                        const code = entry[0], name = entry[1];
                        const opt = document.createElement('option');
                        opt.value = code;
                        opt.textContent = name;
                        if (preselect && preselect === code) opt.selected = true;
                        stateSelect.appendChild(opt);
                    });
                })
                .catch(function(err){
                    console.error('Error cargando provincias:', err);
                    stateSelect.innerHTML = '<option value="">Selecciona una provincia</option>';
                });
        }

        // Listener cambio de país
        if (countrySelect) {
            countrySelect.addEventListener('change', function(){
                loadStates(this.value, '');
            });
        }

        // Generador de contraseña manual
        if (generarBtn) {
            generarBtn.addEventListener('click', function() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+{}[]<>?';
                let password = '';
                for (let i = 0; i < 12; i++) {
                    password += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                const pw = document.getElementById('account_password');
                const pw2 = document.getElementById('account_password_repeat');
                if (pw) pw.value = password;
                if (pw2) pw2.value = password;
                if (passwordSugerida) passwordSugerida.textContent = password;
            });
        }

        // Detección razonable de gestor nativo de contraseñas para ocultar botón
        document.addEventListener('DOMContentLoaded', function() {
            const hasNativePasswordManager =
                ( !!window.PasswordCredential ) ||
                ( navigator.userAgent.includes('Safari') && !navigator.userAgent.includes('Chrome') ) ||
                navigator.userAgent.includes('Firefox');

            if (hasNativePasswordManager && sugerirContainer) {
                sugerirContainer.style.display = 'none';
            }

            // Cargar estados si ya hay un país seleccionado (p.e. tras submit con errores o valor prellenado)
            if (countrySelect && countrySelect.value) {
                loadStates(countrySelect.value, selectedState);
            }

            // Configurar listeners para el teléfono (IGUAL QUE EN EL OTRO SNIPPET)
            if (prefixField) prefixField.addEventListener('input', updateHiddenPhoneField);
            if (numberField) numberField.addEventListener('input', function() {
                this.value = this.value.replace(/\s/g, '');
                updateHiddenPhoneField();
            });
        });

        // Actualizar teléfono antes de enviar el formulario
        if (form) {
            form.addEventListener('submit', updateHiddenPhoneField);
        }
    })();
    </script>

    <script>
    (function(){
        const modal = document.getElementById('trekkium-modal');
        if (!modal) return;

        const closeBtn = modal.querySelector('.trekkium-modal-close');
        closeBtn.addEventListener('click', function(){
            modal.style.display = 'none';
        });

        // Cerrar modal al hacer clic fuera del contenido
        window.addEventListener('click', function(event){
            if (event.target === modal) modal.style.display = 'none';
        });
    })();
    </script>


    <?php
    return ob_get_clean();
}

// ======= Procesar registro y enviar email de verificación =======
add_action('init', 'trekkium_manejar_envio_formulario');

function trekkium_manejar_envio_formulario() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_cuenta_nonce_field'])) {
        $resultado = trekkium_procesar_registro_usuario();

        if (is_wp_error($resultado)) {
            set_transient('trekkium_registro_error', $resultado->get_error_message(), 30);
        } elseif ($resultado === true) {
            set_transient('trekkium_registro_success', 'Se ha enviado un correo de confirmación. Revisa tu email para activar tu cuenta.', 30);
        }
    }
}

function trekkium_procesar_registro_usuario() {
    if (!isset($_POST['crear_cuenta_nonce_field']) || !wp_verify_nonce($_POST['crear_cuenta_nonce_field'], 'crear_cuenta_nonce')) {
        return new WP_Error('security_error', 'Error de seguridad. Por favor, intenta de nuevo.');
    }

    $email = sanitize_email($_POST['account_email']);
    $first_name = sanitize_text_field($_POST['account_first_name']);
    $last_name = sanitize_text_field($_POST['account_last_name']);
    $display_name = sanitize_text_field($_POST['account_display_name']);
    
    // OBTENER EL TELÉFONO COMBINADO (IGUAL QUE EN EL OTRO SNIPPET)
    $full_phone_normalized = '';
    if (isset($_POST['billing_phone'])) {
        $full_phone_normalized = sanitize_text_field(preg_replace('/\s+/', '', $_POST['billing_phone']));
    }
    
    $country = sanitize_text_field($_POST['billing_country']);
    $state = sanitize_text_field($_POST['billing_state']);
    $fecha_nacimiento = sanitize_text_field($_POST['fecha_nacimiento']);
    $password = sanitize_text_field($_POST['account_password']);
    $password_repeat = sanitize_text_field($_POST['account_password_repeat']);

    if (!is_email($email)) return new WP_Error('invalid_email', 'El correo electrónico no es válido.');
    if (email_exists($email)) return new WP_Error('email_exists', 'Este correo electrónico ya está registrado.');
    if (strlen($password) < 8) return new WP_Error('password_short', 'La contraseña debe tener al menos 8 caracteres.');
    if ($password !== $password_repeat) return new WP_Error('password_mismatch', 'Las contraseñas no coinciden.');

    // VALIDACIÓN DEL TELÉFONO (IGUAL QUE EN EL OTRO SNIPPET)
    if (isset($_POST['billing_phone_prefix']) && isset($_POST['billing_phone_number'])) {
        $prefix = sanitize_text_field($_POST['billing_phone_prefix']);
        $number = sanitize_text_field($_POST['billing_phone_number']);
        $clean_number = preg_replace('/\s+/', '', $number);
        
        if (!preg_match('/^\+\d+$/', $prefix)) {
            return new WP_Error('billing_phone_prefix_format_error', __('El prefijo del teléfono debe empezar con "+" y contener solo números.', 'woocommerce'));
        }
        if (!preg_match('/^\d{9}$/', $clean_number)) {
            return new WP_Error('billing_phone_number_format_error', __('El número de teléfono debe tener exactamente 9 dígitos y no puede contener espacios.', 'woocommerce'));
        }
    }

    // VALIDACIÓN DE EDAD: El usuario debe tener al menos 18 años.
    $fecha_nac = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
    if (!$fecha_nac) return new WP_Error('invalid_date', 'Fecha de nacimiento no válida.');
    
    $fecha_minima_registro = new DateTime('-18 years');
    if ($fecha_nac > $fecha_minima_registro) return new WP_Error('underage', 'Debes tener al menos 18 años para registrarte.');

    global $wpdb;
    // Teléfono duplicado (USANDO EL TELÉFONO COMBINADO)
    $phone_exists = $wpdb->get_var($wpdb->prepare(
        "SELECT user_id FROM $wpdb->usermeta WHERE meta_key='billing_phone' AND meta_value=%s LIMIT 1",
        $full_phone_normalized
    ));
    if ($phone_exists) return new WP_Error('phone_exists', 'Este número de teléfono ya está registrado.');

    // Generar token y guardar datos temporalmente
    $token = wp_generate_password(20, false);
    $data = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'display_name' => $display_name,
        'email' => $email,
        'phone' => $full_phone_normalized, // GUARDAR EL TELÉFONO COMBINADO
        'country' => $country,
        'state' => $state,
        'fecha_nacimiento' => $fecha_nacimiento,
        'password' => base64_encode($password)
    ];
    set_transient('trekkium_email_verify_' . $token, $data, DAY_IN_SECONDS);

    // Enviar email de confirmación
    $verify_url = site_url("/confirmar-cuenta/?token=$token");
    $subject = "Confirma tu cuenta en Trekkium";
    $message = "
    <p>Hola $first_name,</p>
    <p>Gracias por registrarte en Trekkium. Para completar tu registro, haz clic en el botón:</p>
    <p><a href='$verify_url' style='padding:10px 20px;background:#0b568b;color:#fff;text-decoration:none;border-radius:4px;'>Confirmar cuenta</a></p>
    <p>Si no creaste esta solicitud, ignora este mensaje.</p>
    ";
    wp_mail($email, $subject, $message, ['Content-Type: text/html; charset=UTF-8']);

    return true;
}

add_action('template_redirect', function() {
    if (is_page('confirmar-cuenta') && isset($_GET['token'])) {
        $token = sanitize_text_field($_GET['token']);
        $data = get_transient('trekkium_email_verify_' . $token);

        if ($data) {
            $user_id = wp_create_user($data['email'], base64_decode($data['password']), $data['email']);
            wp_update_user([
                'ID' => $user_id,
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'display_name' => $data['display_name'],
                'role' => 'customer'
            ]);

            // Guardar el teléfono combinado en los metadatos
            update_user_meta($user_id, 'billing_phone', $data['phone']);
            
            foreach (['country','state','fecha_nacimiento'] as $meta) {
                if ($meta === 'fecha_nacimiento') {
                    update_user_meta($user_id, 'fecha_nacimiento', $data[$meta]);
                } else {
                    update_user_meta($user_id, 'billing_' . $meta, $data[$meta]);
                    update_user_meta($user_id, 'shipping_' . $meta, $data[$meta]);
                }
            }

            delete_transient('trekkium_email_verify_' . $token);

            $subject = "Bienvenido a Trekkium";
            $message = "
            <p>Hola {$data['first_name']},</p>
            <p>Tu cuenta en Trekkium ha sido creada correctamente.</p>
            <p>Ya puedes iniciar sesión con tu correo y la contraseña que elegiste.</p>
            <p>¡Disfruta de tus aventuras!</p>
            ";
            wp_mail($data['email'], $subject, $message, ['Content-Type: text/html; charset=UTF-8']);

            wp_redirect(site_url('/acceso/'));
            exit;
        } else {
            wp_die('El enlace de verificación no es válido o ha expirado.');
        }
    }
});

// ======= Handler AJAX faltante para cargar provincias =======
add_action('wp_ajax_get_states_by_country', 'trekkium_get_states_by_country');
add_action('wp_ajax_nopriv_get_states_by_country', 'trekkium_get_states_by_country');
function trekkium_get_states_by_country() {
    if ( empty($_REQUEST['country']) ) {
        wp_send_json([]);
    }

    $country = sanitize_text_field($_REQUEST['country']);

    // Asegurarse de que WooCommerce esté activo
    if ( ! function_exists( 'WC' ) ) {
        wp_send_json([]);
    }

    $states = WC()->countries->get_states( $country );

    // Enviar array (code => name) o array vacío
    wp_send_json( $states ? $states : [] );
}