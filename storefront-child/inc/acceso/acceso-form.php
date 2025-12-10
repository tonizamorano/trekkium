<?php
add_shortcode('acceso-form', function () {
    ob_start();

    // Variables para mensajes
    $modal_message = '';
    $modal_title = '';
    $show_modal = false;
    $is_error = false;
    
    // Inputs recordados
    $input_email = '';

    // Procesar formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action'])) {

        /* === LOGIN === */
        if ($_POST['form_action'] === 'login') {
            $input_email = sanitize_email($_POST['username']);
            $password = $_POST['password'];

            if (!is_email($input_email)) {
                $modal_title = 'Error en el formulario';
                $modal_message = 'Por favor, introduce un correo electrónico válido.';
                $show_modal = true;
                $is_error = true;
            } else {
                $user = get_user_by('email', $input_email);

                if (!$user) {
                    $modal_title = 'Error de acceso';
                    $modal_message = 'No existe ninguna cuenta con ese correo electrónico.';
                    $show_modal = true;
                    $is_error = true;
                } else {
                    $creds = [
                        'user_login'    => $user->user_login,
                        'user_password' => $password,
                        'remember'      => false,
                    ];

                    $login_user = wp_signon($creds, is_ssl());

                    if (is_wp_error($login_user)) {
                        $modal_title = 'Error de acceso';
                        $modal_message = 'La contraseña no es correcta.';
                        $show_modal = true;
                        $is_error = true;
                    } else {
                        wp_redirect(home_url());
                        exit;
                    }
                }
            }
        }

        /* === REGISTRO === */
        if ($_POST['form_action'] === 'register') {
            $input_email = sanitize_email($_POST['reg_email']);
            $pass1 = $_POST['reg_password'];
            $pass2 = $_POST['reg_password2'];

            if (!is_email($input_email)) {
                $modal_title = 'Error en el formulario';
                $modal_message = 'Por favor, introduce un correo electrónico válido.';
                $show_modal = true;
                $is_error = true;
            } elseif (email_exists($input_email)) {
                $modal_title = 'Error de registro';
                $modal_message = 'Este correo electrónico ya está registrado.';
                $show_modal = true;
                $is_error = true;
            } elseif (empty($pass1) || empty($pass2)) {
                $modal_title = 'Error en el formulario';
                $modal_message = 'Por favor, introduce y repite la contraseña.';
                $show_modal = true;
                $is_error = true;
            } elseif ($pass1 !== $pass2) {
                $modal_title = 'Error en el formulario';
                $modal_message = 'Las contraseñas no coinciden.';
                $show_modal = true;
                $is_error = true;
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
                    $modal_title = 'Error de sistema';
                    $modal_message = 'Hubo un error al registrar el usuario. Por favor, inténtalo más tarde.';
                    $show_modal = true;
                    $is_error = true;
                } else {
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                    wp_redirect(home_url('/mi-cuenta/editar-cuenta'));
                    exit;
                }
            }
        }

        /* === RECUPERACIÓN === */
        if ($_POST['form_action'] === 'recovery') {
            $recovery_email = sanitize_email($_POST['recovery_email']);
            
            if (!is_email($recovery_email)) {
                $modal_title = 'Error en el formulario';
                $modal_message = 'Por favor, introduce un correo electrónico válido.';
                $show_modal = true;
                $is_error = true;
            } else {
                $user = get_user_by('email', $recovery_email);
                
                if (!$user) {
                    $modal_title = 'Error de recuperación';
                    $modal_message = 'Correo electrónico no válido, debe ser de una cuenta registrada.';
                    $show_modal = true;
                    $is_error = true;
                } else {
                    $result = retrieve_password($user->user_login);
                    
                    if (is_wp_error($result)) {
                        $modal_title = 'Error de sistema';
                        $modal_message = 'Hubo un error al enviar el correo. Por favor, inténtalo de nuevo.';
                        $show_modal = true;
                        $is_error = true;
                    } else {
                        $modal_title = 'Correo enviado';
                        $modal_message = 'Te hemos enviado un correo para restablecer tu contraseña.';
                        $show_modal = true;
                        $is_error = false;
                    }
                }
            }
        }
    }

    ?>

    <!-- ===================== FORMULARIO ===================== -->
    <div class="acceso-form-contenedor">
        <div class="acceso-form-grid">

            <!-- LOGIN -->
            <div class="acceso-form-columna">
                <h2 class="acceso-form-title">Usuarios registrados</h2>
                <div class="acceso-form-contenido">

                    <form class="acceso-form" method="post" autocomplete="off">
                        <div class="acceso-form-group">
                            <label for="username">Correo electrónico<span class="required">*</span></label>
                            <input class="acceso-form-input" type="email" name="username" id="username"
                                required value="<?php echo esc_attr($input_email); ?>" autocomplete="email">
                        </div>

                        <div class="acceso-form-group">
                            <label for="password">Contraseña<span class="required">*</span></label>
                            <input type="password" name="password" id="password" class="acceso-form-input" required autocomplete="current-password">
                        </div>

                        <div class="acceso-form-group-button">
                            <input class="acceso-form-button" type="submit" value="Acceder a Trekkium">
                            <input type="hidden" name="form_action" value="login">
                        </div>
                    </form>

                    <div class="acceso-form-links">
                        <span class="acceso-form-toggle" id="openRecoveryModal">Contraseña perdida</span>
                    </div>
                </div>
            </div>

            <!-- NUEVOS USUARIOS -->
            <div class="acceso-form-columna">
                <h2 class="acceso-form-title">Nuevos usuarios</h2>
                <div class="acceso-form-contenido">
                    <p class="acceso-form-info">¿Aún no te has registrado? Crea tu cuenta y disfruta de todas las ventajas.</p>
                    <div class="acceso-form-group-button">
                        <a href="<?php echo home_url('/crear-cuenta/'); ?>" class="acceso-form-button">Crear cuenta</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ===================== MODAL RECUPERACIÓN ===================== -->
    <div id="recoveryModal" class="acceso-form-modal">
        <div class="acceso-form-modal-content">
            <span class="acceso-form-close" id="closeRecoveryModal">&times;</span>
            <h2>Recuperar contraseña</h2>
            <p>Introduce tu correo electrónico. Te enviaremos un enlace para restablecerla.</p>

            <form class="acceso-form" method="post" autocomplete="off">
                <div class="acceso-form-group">
                    <label for="recovery_email">Correo electrónico<span class="required">*</span></label>
                    <input class="acceso-form-input" type="email" name="recovery_email" id="recovery_email" required>
                </div>
                <div class="acceso-form-group-button">
                    <input class="acceso-form-button" type="submit" value="Restablecer contraseña">
                    <input type="hidden" name="form_action" value="recovery">
                </div>
            </form>
        </div>
    </div>

    <!-- ===================== MODAL MENSAJE ===================== -->
    <div id="messageModal" class="acceso-form-modal <?php echo $show_modal ? 'show' : ''; ?>">
        <div class="acceso-form-modal-content">
            <span class="acceso-form-close" id="closeMessageModal">&times;</span>
            <h2 style="color: <?php echo $is_error ? 'var(--rojo-100)' : 'var(--verde-100)'; ?>;">
                <?php echo esc_html($modal_title); ?>
            </h2>
            <p><?php echo esc_html($modal_message); ?></p>
            <div class="acceso-form-group-button">
                <button class="acceso-form-button" id="closeMessageModalBtn">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- ===================== SCRIPT ===================== -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        const recoveryModal = document.getElementById('recoveryModal');
        const messageModal = document.getElementById('messageModal');

        const openRecoveryModal = document.getElementById('openRecoveryModal');
        const closeRecoveryModal = document.getElementById('closeRecoveryModal');

        const closeMessageModal = document.getElementById('closeMessageModal');
        const closeMessageModalBtn = document.getElementById('closeMessageModalBtn');

        /* Abrir recuperación */
        if (openRecoveryModal) {
            openRecoveryModal.addEventListener('click', () => {
                recoveryModal.classList.add('show');
            });
        }

        /* Cerrar recuperación */
        if (closeRecoveryModal) {
            closeRecoveryModal.addEventListener('click', () => {
                recoveryModal.classList.remove('show');
            });
        }

        /* Cerrar mensaje */
        if (closeMessageModal) {
            closeMessageModal.addEventListener('click', () => {
                messageModal.classList.remove('show');
            });
        }

        if (closeMessageModalBtn) {
            closeMessageModalBtn.addEventListener('click', () => {
                messageModal.classList.remove('show');
            });
        }

        /* Cerrar haciendo click fuera */
        window.addEventListener('click', event => {
            if (event.target === recoveryModal) recoveryModal.classList.remove('show');
            if (event.target === messageModal) messageModal.classList.remove('show');
        });

        /* Autocerrar mensaje si es éxito */
        <?php if ($show_modal && !$is_error): ?>
        setTimeout(() => { messageModal.classList.remove('show'); }, 5000);
        <?php endif; ?>

    });
    </script>

<?php
    return ob_get_clean();
});
