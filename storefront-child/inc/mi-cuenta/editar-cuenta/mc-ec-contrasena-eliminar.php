<?php
// -------------------------
// Shortcode: cambiar contraseña + eliminar cuenta (1 columna)
add_shortcode('contenido_contrasena_eliminar', 'trekkium_password_delete_account_shortcode');

function trekkium_password_delete_account_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>Debes iniciar sesión para acceder a esta página.</p>';
    }

    ob_start();
    
    $user = wp_get_current_user();
    ?>

    <div class="trekkium-cambiar-contrasena-section">

        <!-- COLUMNA ÚNICA -->

        <!-- Cambiar Contraseña -->
        <div class="micuenta-contrasena-eliminar-columna">

            <div class="titular-contrasena-section accordion-header">
                <h2 class="cambiar-contrasena-titulo">Cambiar contraseña</h2>
            </div>

            <div class="accordion-content" style="display: none;">
                <div class="woocommerce-billing-fields__field-wrapper trekkium-extra-fields">
                    <form id="ajax-change-password-form" method="post">
                        <?php wp_nonce_field('ajax_change_password', 'ajax_change_password_nonce'); ?>

                        <div class="form-row password-field-wrapper">
                            <label for="password_current">Contraseña actual <span class="required">*</span></label>
                            <div class="password-input-container">
                                <input type="password" name="password_current" id="password_current" class="input-text" autocomplete="current-password" required placeholder="Introduce tu contraseña actual" />
                                <span class="toggle-password" data-target="password_current">
                                    <svg class="eye-icon eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-icon eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="form-row password-field-wrapper">
                            <label for="password_1">Nueva contraseña <span class="required">*</span></label>
                            <div class="password-input-container">
                                <input type="password" name="password_1" id="password_1" class="input-text" autocomplete="new-password" required placeholder="Introduce tu nueva contraseña" />
                                <span class="toggle-password" data-target="password_1">
                                    <svg class="eye-icon eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-icon eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="form-row password-field-wrapper">
                            <label for="password_2">Repetir nueva contraseña <span class="required">*</span></label>
                            <div class="password-input-container">
                                <input type="password" name="password_2" id="password_2" class="input-text" autocomplete="new-password" required placeholder="Repite tu nueva contraseña" />
                                <span class="toggle-password" data-target="password_2">
                                    <svg class="eye-icon eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-icon eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="micuenta-contrasena-eliminar-buton-section">                            
                            <button class="micuenta-contrasena-eliminar-buton" type="submit">Cambiar contraseña</button>
                        </div>
                        
                        <div id="password-change-message"></div>
                    </form>
                </div>
            </div>

        </div>

        <?php if (array_intersect(['customer', 'administrator'], $user->roles)) : ?>

        <!-- Eliminar Cuenta -->
        <div class="micuenta-contrasena-eliminar-columna">

            <div class="titular-contrasena-section accordion-header">
                <h2 class="cambiar-contrasena-titulo">Eliminar cuenta</h2>
            </div>

            <div class="accordion-content" style="display: none;">
                <div class="woocommerce-billing-fields__field-wrapper trekkium-extra-fields">
                    <p class="texto-eliminar-cuenta">Al eliminar tu cuenta se borrarán todos tus datos de contacto, contraseñas, reservas, cupones, etc. ¿Estás seguro de que quieres eliminar tu cuenta? Esta acción es irreversible.</p>
                    
                    <div class="micuenta-contrasena-eliminar-buton-section">
                        <button class="micuenta-contrasena-eliminar-buton" id="confirm-delete-account" type="button">Eliminar cuenta</button>
                    </div>

                    <div id="delete-account-message"></div>
                </div>
            </div>

        </div>
        <?php endif; ?>

    </div>
    
    <!-- JS funcionalidad acordeón y AJAX -->
    <script type="text/javascript">
        jQuery(function($) {
            $('.accordion-header').on('click', function() {
                $(this).toggleClass('active');
                $(this).next('.accordion-content').slideToggle(300);
            });

            $('.toggle-password').on('click', function() {
                const target = $(this).data('target');
                const input = $('#' + target);
                const eyeOpen = $(this).find('.eye-open');
                const eyeClosed = $(this).find('.eye-closed');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    eyeOpen.hide(); eyeClosed.show();
                } else {
                    input.attr('type', 'password');
                    eyeOpen.show(); eyeClosed.hide();
                }
            });

            $('#ajax-change-password-form').on('submit', function(e) {
                e.preventDefault();
                let data = $(this).serialize() + '&action=ajax_change_password';
                $('#password-change-message').css('color', 'red').html('Procesando...');
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                    if (response.success) {
                        $('#password-change-message').css('color', 'green').html(response.data.message);
                        $('#ajax-change-password-form')[0].reset();
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        $('#password-change-message').css('color', 'red').html(response.data.message);
                    }
                });
            });

            $('#confirm-delete-account').on('click', function() {
                if (!confirm('¿Confirmas que quieres eliminar tu cuenta? Esta acción no se puede deshacer.')) return;
                $('#delete-account-message').css('color', 'red').html('Procesando...');
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                    action: 'ajax_delete_account',
                    ajax_delete_account_nonce: '<?php echo wp_create_nonce('ajax_delete_account'); ?>'
                }, function(response) {
                    if (response.success) {
                        $('#delete-account-message').css('color', 'green').html(response.data.message);
                        setTimeout(() => { window.location.href = '<?php echo esc_url(home_url()); ?>'; }, 2000);
                    } else {
                        $('#delete-account-message').css('color', 'red').html(response.data.message);
                    }
                });
            });
        });
    </script>

<?php
    return ob_get_clean();
}
