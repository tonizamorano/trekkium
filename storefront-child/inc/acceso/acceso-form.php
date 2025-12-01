<?php
/**
 * Shortcode acceso-form - Formulario combinado de login y registro
 */
add_shortcode('acceso-form', function () {
    ob_start();

    // Variables para mensajes
    $error_message = '';
    $success_message = '';
    $recovery_message = '';
    $recovery_error = '';

    // Inputs recordados
    $input_email = '';

    // Procesar formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action'])) {

        // === LOGIN ===
        if ($_POST['form_action'] === 'login') {
            $input_email = sanitize_email($_POST['username']);
            $password = $_POST['password'];

            if (!is_email($input_email)) {
                $error_message = 'Por favor, introduce un correo electrónico válido.';
            } else {
                $user = get_user_by('email', $input_email);

                if (!$user) {
                    $error_message = 'No existe ninguna cuenta con ese correo electrónico.';
                } else {
                    $creds = [
                        'user_login'    => $user->user_login,
                        'user_password' => $password,
                        'remember'      => isset($_POST['rememberme']),
                    ];

                    $login_user = wp_signon($creds, is_ssl());

                    if (is_wp_error($login_user)) {
                        $error_message = 'La contraseña no es correcta.';
                    } else {
                        wp_redirect(home_url());
                        exit;
                    }
                }
            }
        }

        // === REGISTRO ===
        if ($_POST['form_action'] === 'register') {
            $input_email = sanitize_email($_POST['reg_email']);
            $pass1 = $_POST['reg_password'];
            $pass2 = $_POST['reg_password2'];

            if (!is_email($input_email)) {
                $error_message = 'Por favor, introduce un correo electrónico válido.';
            } elseif (email_exists($input_email)) {
                $error_message = 'Este correo electrónico ya está registrado.';
            } elseif (empty($pass1) || empty($pass2)) {
                $error_message = 'Por favor, introduce y repite la contraseña.';
            } elseif ($pass1 !== $pass2) {
                $error_message = 'Las contraseñas no coinciden.';
            } else {
                $username = sanitize_user(current(explode('@', $input_email)));
                $original_username = $username;
                $i = 1;
                while (username_exists($username)) {
                    $username = $original_username . $i;
                    $i++;
                }

                $user_id = wp_create_user($username, $pass1, $input_email);

                if (is_wp_error($user_id)) {
                    $error_message = 'Hubo un error al registrar el usuario. Por favor, inténtalo más tarde.';
                } else {
                    // Login automático tras registro
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                    wp_redirect(home_url('/mi-cuenta/editar-cuenta'));
                    exit;
                }
            }
        }

        // === RECUPERACIÓN DE CONTRASEÑA ===
        if ($_POST['form_action'] === 'recovery') {
            $recovery_email = sanitize_email($_POST['recovery_email']);
            
            if (!is_email($recovery_email)) {
                $recovery_error = 'Por favor, introduce un correo electrónico válido.';
            } else {
                $user = get_user_by('email', $recovery_email);
                
                if (!$user) {
                    $recovery_error = 'Correo electrónico no válido, debe ser de una cuenta registrada.';
                } else {
                    // Usar la función de WordPress para recuperar contraseña
                    $result = retrieve_password($user->user_login);
                    
                    if (is_wp_error($result)) {
                        $recovery_error = 'Hubo un error al enviar el correo. Por favor, inténtalo de nuevo.';
                    } else {
                        $recovery_message = 'Te hemos enviado un correo electrónico con un enlace para restablecer tu contraseña. Por favor, revísalo.';
                    }
                }
            }
        }
    }
    ?>

    <div class="acceso-form-contenedor">
        <div class="acceso-form-grid">
            <!-- Columna 1 - Login para usuarios registrados -->
            <div class="acceso-form-columna">
                <?php if ($error_message): ?>
                    <div class="acceso-form-error"><?php echo esc_html($error_message); ?></div>
                <?php endif; ?>

                <div id="acceso-form-login">
                    <h2 class="acceso-form-title">Usuarios registrados</h2>
                    <div class="acceso-form-contenido">
                        <form class="acceso-form" method="post" autocomplete="off">
                            <div class="acceso-form-group">
                                <label for="username">Correo electrónico<span class="required">*</span></label>
                                <input class="acceso-form-input" type="email" name="username" id="username" required value="<?php echo esc_attr($input_email); ?>" autocomplete="email">
                            </div>

                            <div class="acceso-form-group">
                                <label for="password">Contraseña<span class="required">*</span></label>
                                <input type="password" name="password" id="password" class="acceso-form-input" required autocomplete="current-password">
                            </div>

                            <div class="acceso-form-group acceso-form-checkbox-label">
                                <input type="checkbox" name="rememberme" id="rememberme" value="forever">
                                <label for="rememberme">Recuérdame</label>
                            </div>

                            <div class="acceso-form-group-button">
                                <input class="acceso-form-button" type="submit" value="Entrar">
                                <input type="hidden" name="form_action" value="login">
                            </div>
                        </form>

                        <!-- Enlace para registro -->
                        <div class="acceso-form-links">
                            <div class="acceso-form-link">
                                <span class="acceso-form-toggle" id="toggle-register">¿Aún no te has registrado?</span>
                            </div>
                        </div>

                        <!-- Sección de registro -->
                        <div id="register-section" class="acceso-form-register">
                            <div class="acceso-form-group">
                                <p class="acceso-form-info">Crea tu cuenta de usuario, solo es un minuto.</p>
                            </div>
                            
                            <div class="acceso-form-group-button">
                                <a href="<?php echo home_url('/crear-cuenta/'); ?>" class="acceso-form-button">Crear cuenta</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna 2 - Recuperación de contraseña -->
            <div class="acceso-form-columna">
                <div id="acceso-form-recovery">
                    <h2 class="acceso-form-title">Contraseña perdida</h2>
                    <div class="acceso-form-contenido">
                        <form class="acceso-form" method="post" autocomplete="off">
                            <div class="acceso-form-group">
                                <p class="acceso-form-info">¿Has olvidado tu contraseña? Por favor, introduce tu correo electrónico. Te enviaremos un enlace para crear una contraseña nueva.</p>
                            </div>

                            <div class="acceso-form-group">
                                <label for="recovery_email">Correo electrónico<span class="required">*</span></label>
                                <input class="acceso-form-input" type="email" name="recovery_email" id="recovery_email" required autocomplete="email" value="<?php echo isset($_POST['recovery_email']) ? esc_attr($_POST['recovery_email']) : ''; ?>">
                            </div>

                            <div class="acceso-form-group-button">
                                <input class="acceso-form-button" type="submit" value="Restablecer contraseña">
                                <input type="hidden" name="form_action" value="recovery">
                            </div>

                            <?php if ($recovery_error): ?>
                                <div class="acceso-form-error"><?php echo esc_html($recovery_error); ?></div>
                            <?php elseif ($recovery_message): ?>
                                <div class="acceso-form-success"><?php echo esc_html($recovery_message); ?></div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleRegister = document.getElementById('toggle-register');
        const registerSection = document.getElementById('register-section');
        
        // Ocultar sección de registro al cargar
        if (registerSection) {
            registerSection.style.display = 'none';
        }
        
        // Toggle sección de registro
        if (toggleRegister && registerSection) {
            toggleRegister.addEventListener('click', function() {
                if (registerSection.style.display === 'none') {
                    registerSection.style.display = 'block';
                    toggleRegister.textContent = 'Ocultar opciones de registro';
                } else {
                    registerSection.style.display = 'none';
                    toggleRegister.textContent = '¿Aún no te has registrado?';
                }
            });
        }
    });
    </script>

<?php
    return ob_get_clean();
});