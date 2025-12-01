<?php
function ut_form_shortcode() {
    ob_start();

    $mensaje = ''; // Variable para almacenar mensajes

    // Procesar formulario al enviar
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ut_form_nonce']) && wp_verify_nonce($_POST['ut_form_nonce'], 'ut_form_action')) {

        // Sanitizar y recoger datos
        $nombre     = sanitize_text_field($_POST['nombre']);
        $apellidos  = sanitize_text_field($_POST['apellidos']);
        $telefono   = preg_replace('/\D/', '', $_POST['telefono']); // Solo números
        $email      = sanitize_email($_POST['email']);
        $provincia  = sanitize_text_field($_POST['provincia']);
        $titulacion = sanitize_text_field($_POST['titulacion']);
        $terminos   = isset($_POST['terminos']) ? true : false;

        $errores = [];

        // Validar teléfono: debe tener 9 dígitos
        if (strlen($telefono) !== 9) {
            $errores[] = 'El teléfono debe contener 9 cifras válidas.';
        }

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        }

        // Validar términos y condiciones
        if (!$terminos) {
            $errores[] = 'Debes aceptar los Términos y condiciones legales.';
        }

        if (empty($errores)) {
            // Crear post
            $post_id = wp_insert_post([
                'post_type'   => 'candidato',
                'post_title'  => $nombre . ' ' . $apellidos,
                'post_status' => 'publish'
            ]);

            if ($post_id) {
                update_post_meta($post_id, 'nombre', $nombre);
                update_post_meta($post_id, 'apellidos', $apellidos);
                update_post_meta($post_id, 'telefono', $telefono);
                update_post_meta($post_id, 'email', $email);
                update_post_meta($post_id, 'provincia', $provincia);
                update_post_meta($post_id, 'estado', 'pendiente');
                update_post_meta($post_id, 'pais', 'ES');
                update_post_meta($post_id, 'titulacion_array', [$titulacion]);
                update_post_meta($post_id, 'titulacion', $titulacion);

                // Redirigir a la misma página con flag de éxito
                $redirect_url = add_query_arg('ut_form_success', '1', get_permalink());
                wp_redirect($redirect_url);
                exit;
            } else {
                $errores[] = 'Ha ocurrido un error. Por favor, inténtalo de nuevo.';
            }
        }

        if (!empty($errores)) {
            $mensaje = '<div class="ut-form-error">' . implode('<br>', $errores) . '</div>';
        }
    }

    // Mensaje de éxito desde URL
    if (isset($_GET['ut_form_success']) && $_GET['ut_form_success'] == '1') {
        $mensaje = '<div class="ut-form-success">Tu solicitud se ha enviado correctamente. Nos pondremos en contacto contigo pronto.</div>';
    }

    // Obtener datos para selects
    $provincias_españa = function_exists('trekkium_get_spanish_states') ? trekkium_get_spanish_states() : [];
    $titulaciones = function_exists('trekkium_get_taxonomy_terms') ? trekkium_get_taxonomy_terms('titulacion') : [];

    ?>

    <div class="ut-form-contenedor">
        <div class="ut-form-seccion-titular">
            <div class="ut-form-titular">Formulario de solicitud</div>
        </div>
        <div class="ut-form-seccion-contenido">
            <form method="POST" class="ut-form">
                <?php wp_nonce_field('ut_form_action', 'ut_form_nonce'); ?>

                <div class="ut-form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>

                <div class="ut-form-group">
                    <label for="apellidos">Apellidos *</label>
                    <input type="text" name="apellidos" id="apellidos" required>
                </div>

                <div class="ut-form-group">
                    <label for="telefono">Teléfono *</label>
                    <input type="tel" name="telefono" id="telefono" pattern="[0-9]{9}" maxlength="9" required>
                </div>

                <div class="ut-form-group">
                    <label for="email">Correo electrónico *</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="ut-form-group">
                    <label for="provincia">Provincia *</label>
                    <select name="provincia" id="provincia" required>
                        <option value="">Selecciona una provincia</option>
                        <?php foreach ($provincias_españa as $code => $name): ?>
                            <option value="<?php echo esc_attr($code); ?>"><?php echo esc_html($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="ut-form-group">
                    <label for="titulacion">Titulación *</label>
                    <select name="titulacion" id="titulacion" required>
                        <option value="">Selecciona una titulación</option>
                        <?php foreach ($titulaciones as $key => $label): ?>
                            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="ut-form-group">
                    <label class="ut-form-checkbox-label">
                        <input type="checkbox" name="terminos" id="terminos" required>
                        He leído y acepto los <a href="/terminos-y-condiciones/" target="_blank">Términos y condiciones legales</a> *
                    </label>
                </div>

                <div class="ut-form-submit">
                    <button type="submit" class="ut-btn-enviar">Enviar solicitud</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    // Mostrar mensaje debajo del contenedor
    echo $mensaje;

    return ob_get_clean();
}
add_shortcode('ut_form', 'ut_form_shortcode');