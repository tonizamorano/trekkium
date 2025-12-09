<?php

/**
 * Shortcode para la IMAGEN PRINCIPAL del formulario
 */
function mc_ma_na_form_imagen_principal_shortcode() {
    ob_start();
    
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
    ?>
    
    <div class="mc-ma-na-grid-1col">
        <label class="edit-form-titular">Imagen Principal*</label>
        
        <div class="edit-form-image-box" onclick="abrirMediaUploaderPrincipal(this, 'actividad_imagen_1')">
            <span>Haz clic para seleccionar la imagen principal</span>
        </div>
        
        <input type="hidden" name="actividad_imagen_1" id="actividad_imagen_1" required>
    </div>
    
    <script>
    // Función específica para la imagen principal
    function abrirMediaUploaderPrincipal(box, inputId){
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            console.error('WordPress media library no está disponible');
            alert('Error: La biblioteca de medios no está disponible. Recarga la página.');
            return;
        }
        
        var frame = wp.media({
            title: 'Seleccionar imagen principal',
            button: { text: 'Usar como imagen principal' },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            if (box) {
                box.innerHTML = '<img src="' + attachment.url + '" alt="Imagen principal" style="max-width:100%;height:auto;">';
                box.style.border = '2px solid #0073aa';
            }
            var inputElement = document.getElementById(inputId);
            if (inputElement) {
                inputElement.value = attachment.id;
            }
        });
        
        frame.open();
    }
    
    // Inicializar si hay imagen principal precargada
    jQuery(document).ready(function($){
        var input = document.getElementById('actividad_imagen_1');
        var box = input ? input.previousElementSibling : null;
        
        if (input && input.value && box) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                method: 'POST',
                data: {
                    action: 'mc_get_image_url',
                    image_id: input.value
                },
                success: function(response) {
                    if (response.success && response.data.url) {
                        box.innerHTML = '<img src="' + response.data.url + '" alt="Imagen principal cargada" style="max-width:100%;height:auto;">';
                        box.style.border = '2px solid #0073aa';
                    }
                }
            });
        }
    });
    </script>
    
    <?php
    return ob_get_clean();
}
add_shortcode('mc_ma_na_form_imagen_principal', 'mc_ma_na_form_imagen_principal_shortcode');