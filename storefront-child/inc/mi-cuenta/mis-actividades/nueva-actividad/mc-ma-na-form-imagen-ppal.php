<?php

/**
 * Shortcode para la IMAGEN PRINCIPAL del formulario con "botones" Editar/Eliminar
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
    
    <style>
    .edit-form-image-box {
        position: relative;
        cursor: pointer;
        text-align: center;
        min-height: 100px;
        border: 2px dashed #ccc;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .edit-form-image-box img {
        max-width: 100%;
        height: auto;
    }
    .image-buttons {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        flex-direction: column;
        gap: 5px;
        z-index: 10;
    }
    .image-buttons .btn {
        display: inline-block;
        background-color: var(--azul2-100);
        color: #fff;
        padding: 5px 15px;
        cursor: pointer;
        font-size: 16px;
        border-radius: 50px;
        font-weight: 500;
        text-align: center;
        user-select: none;
    }
    .image-buttons .btn.delete {
        background-color: var(--naranja1-100);
    }
    </style>
    
    <script>
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
            library: { type: 'image' }
        });
        
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            mostrarImagenConBotones(box, inputId, attachment);
        });
        
        frame.open();
    }

    function mostrarImagenConBotones(box, inputId, attachment) {
        box.innerHTML = `
            <img src="${attachment.url}" alt="Imagen principal">
            <div class="image-buttons">
                <div class="btn edit">Editar</div>
                <div class="btn delete">Eliminar</div>
            </div>
        `;
        box.style.border = '2px solid #0073aa';
        var inputElement = document.getElementById(inputId);
        if (inputElement) inputElement.value = attachment.id;

        // Función del "botón" Editar
        box.querySelector('.edit').addEventListener('click', function(e){
            e.stopPropagation();
            abrirMediaUploaderPrincipal(box, inputId);
        });

        // Función del "botón" Eliminar
        box.querySelector('.delete').addEventListener('click', function(e){
            e.stopPropagation();
            box.innerHTML = '<span>Haz clic para seleccionar la imagen principal</span>';
            box.style.border = '2px dashed #ccc';
            if (inputElement) inputElement.value = '';
        });
    }
    
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
                        mostrarImagenConBotones(box, 'actividad_imagen_1', { id: input.value, url: response.data.url });
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
