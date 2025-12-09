<?php
// -------------------------
// Shortcode: cambiar contraseña + eliminar cuenta (sin buttons)
add_shortcode('contenido_contrasena_eliminar', 'trekkium_password_delete_account_shortcode');

function trekkium_password_delete_account_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>Debes iniciar sesión para acceder a esta página.</p>';
    }

    ob_start();
    $user = wp_get_current_user();
    ?>

<div class="mc-ec-ce-contenedor">

    <div class="mc-ec-ce-seccion">
        
        <a href="#" class="mc-ec-ce-boton" data-modal="change-password-modal">
            Cambiar contraseña
        </a>   

        <?php if (array_intersect(['customer', 'administrator'], $user->roles)) : ?>   
    
        <a href="#" class="mc-ec-ce-boton" data-modal="delete-account-modal">
            Eliminar cuenta
        </a>

        <?php endif; ?>

    </div>

    <!-- Modal Cambiar Contraseña -->
    <div id="change-password-modal" class="mc-ec-ce-modal" style="display:none;">
        <div class="mc-ec-ce-modal-overlay"></div>
        <div class="mc-ec-ce-modal-content">
            <a href="#" class="mc-ec-ce-modal-close">&times;</a>
            <h2 class="mc-ec-ce-titulo">Cambiar contraseña</h2>
            
            <div id="password-change-message" style="display:none; padding: 10px; margin: 15px 0; border-radius: 4px;"></div>
            
            <div id="password-form-container">
                <form id="ajax-change-password-form" method="post">
                    <?php wp_nonce_field('ajax_change_password', 'ajax_change_password_nonce'); ?>

                    <div class="form-row mc-ec-ce-field-wrapper">
                        <label for="password_current">Contraseña actual <span class="required">*</span></label>
                        <div class="mc-ec-ce-input-container">
                            <input type="password" name="password_current" id="password_current" required placeholder="Introduce tu contraseña actual" />
                            <a href="#" class="mc-ec-ce-toggle" data-target="password_current">
                                <span class="eye-icon eye-open"><?php echo do_shortcode('[icon_ojo1]'); ?></span>
                                <span class="eye-icon eye-closed" style="display:none;"><?php echo do_shortcode('[icon_ojo2]'); ?></span>
                            </a>
                        </div>
                    </div>

                    <div class="form-row mc-ec-ce-field-wrapper">
                        <label for="password_1">Nueva contraseña <span class="required">*</span></label>
                        <div class="mc-ec-ce-input-container">
                            <input type="password" name="password_1" id="password_1" required placeholder="Introduce tu nueva contraseña" />
                            <a href="#" class="mc-ec-ce-toggle" data-target="password_1">
                                <span class="eye-icon eye-open"><?php echo do_shortcode('[icon_ojo1]'); ?></span>
                                <span class="eye-icon eye-closed" style="display:none;"><?php echo do_shortcode('[icon_ojo2]'); ?></span>
                            </a>
                        </div>
                    </div>

                    <div class="form-row mc-ec-ce-field-wrapper">
                        <label for="password_2">Repetir nueva contraseña <span class="required">*</span></label>
                        <div class="mc-ec-ce-input-container">
                            <input type="password" name="password_2" id="password_2" required placeholder="Repite tu nueva contraseña" />
                            <a href="#" class="mc-ec-ce-toggle" data-target="password_2">
                                <span class="eye-icon eye-open"><?php echo do_shortcode('[icon_ojo1]'); ?></span>
                                <span class="eye-icon eye-closed" style="display:none;"><?php echo do_shortcode('[icon_ojo2]'); ?></span>
                            </a>
                        </div>
                    </div>

                    <div class="micuenta-contrasena-eliminar-buton-section">
                        <a href="#" class="micuenta-contrasena-eliminar-trigger" id="submit-change-password">Cambiar contraseña</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Cuenta -->
    <div id="delete-account-modal" class="mc-ec-ce-modal" style="display:none;">
        <div class="mc-ec-ce-modal-overlay"></div>
        <div class="mc-ec-ce-modal-content">
            <a href="#" class="mc-ec-ce-modal-close">&times;</a>
            <h2 class="mc-ec-ce-titulo">Eliminar cuenta</h2>
            
            <div id="delete-account-message" style="display:none; padding: 10px; margin: 15px 0; border-radius: 4px;"></div>
            
            <div id="delete-account-content">
                <div class="woocommerce-billing-fields__field-wrapper trekkium-extra-fields">
                    <p class="texto-eliminar-cuenta">
                        Al eliminar tu cuenta se borrarán todos tus datos.  
                        Esta acción es irreversible.
                    </p>

                    <div class="micuenta-contrasena-eliminar-buton-section">
                        <a href="#" class="micuenta-contrasena-eliminar-trigger" id="confirm-delete-account">
                            Eliminar cuenta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
jQuery(function($){

    // Abrir modal (resetear mensajes al abrir)
    $('.mc-ec-ce-boton').on('click', function(e){
        e.preventDefault();
        var modalId = $(this).data('modal');
        
        // Resetear mensajes cuando se abre cualquier modal
        $('#password-change-message, #delete-account-message').hide().empty();
        
        // Mostrar el formulario/contenido principal
        $('#password-form-container, #delete-account-content').show();
        
        $('#' + modalId).fadeIn(300);
        $('body').css('overflow', 'hidden');
    });

    // Cerrar modal
    $('.mc-ec-ce-modal-close, .mc-ec-ce-modal-overlay').on('click', function(e){
        e.preventDefault();
        $(this).closest('.mc-ec-ce-modal').fadeOut(300);
        $('body').css('overflow','auto');
    });

    // Toggle ver contraseña
    $('.mc-ec-ce-toggle').on('click', function(e){
        e.preventDefault();
        const input = $('#' + $(this).data('target'));
        const open = $(this).find('.eye-open');
        const closed = $(this).find('.eye-closed');

        if(input.attr('type') === 'password'){
            input.attr('type','text'); open.hide(); closed.show();
        } else {
            input.attr('type','password'); open.show(); closed.hide();
        }
    });

    // Envío cambio contraseña
    $('#submit-change-password').on('click', function(e){
        e.preventDefault();
        $('#ajax-change-password-form').trigger('submit');
    });

    $('#ajax-change-password-form').on('submit', function(e){
        e.preventDefault();

        let data = $(this).serialize() + '&action=ajax_change_password';
        $('#password-change-message')
            .css({'color': '#ff6b6b', 'background-color': '#fff5f5', 'border': '1px solid #ffcccc'})
            .html('Procesando...')
            .show();
        
        // Ocultar formulario temporalmente
        $('#password-form-container').hide();

        $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response){
            if(response.success){
                $('#password-change-message')
                    .css({'color': '#2e7d32', 'background-color': '#edf7ed', 'border': '1px solid #c8e6c9'})
                    .html(response.data.message);
                
                // No mostrar más el formulario si fue exitoso
                $('#password-form-container').hide();
                
                setTimeout(()=>{ 
                    $('#change-password-modal').fadeOut(300);
                    $('body').css('overflow','auto');
                    location.reload();
                }, 1500);
            } else {
                $('#password-change-message')
                    .css({'color': '#ff6b6b', 'background-color': '#fff5f5', 'border': '1px solid #ffcccc'})
                    .html(response.data.message);
                
                // Volver a mostrar formulario para corregir errores
                $('#password-form-container').show();
            }
        });
    });

    // Eliminar cuenta
    $('#confirm-delete-account').on('click', function(e){
        e.preventDefault();
        if(!confirm('¿Confirmas que quieres eliminar tu cuenta?')) return;

        $('#delete-account-message')
            .css({'color': '#ff6b6b', 'background-color': '#fff5f5', 'border': '1px solid #ffcccc'})
            .html('Procesando...')
            .show();
        
        // Ocultar contenido principal
        $('#delete-account-content').hide();

        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
            action: 'ajax_delete_account',
            ajax_delete_account_nonce: '<?php echo wp_create_nonce('ajax_delete_account'); ?>'
        }, function(response){
            if(response.success){
                $('#delete-account-message')
                    .css({'color': '#2e7d32', 'background-color': '#edf7ed', 'border': '1px solid #c8e6c9'})
                    .html(response.data.message);
                
                // No mostrar más el contenido principal
                $('#delete-account-content').hide();
                
                setTimeout(()=>{ 
                    $('#delete-account-modal').fadeOut(300);
                    $('body').css('overflow','auto');
                    window.location.href = '<?php echo home_url(); ?>'; 
                }, 1500);
            } else {
                $('#delete-account-message')
                    .css({'color': '#ff6b6b', 'background-color': '#fff5f5', 'border': '1px solid #ffcccc'})
                    .html(response.data.message);
                
                // Volver a mostrar contenido principal
                $('#delete-account-content').show();
            }
        });
    });

});
</script>

<?php
return ob_get_clean();
}