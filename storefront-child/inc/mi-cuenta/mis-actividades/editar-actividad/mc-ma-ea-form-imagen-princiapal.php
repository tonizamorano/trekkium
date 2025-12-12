<?php
/**
 * Shortcode para la IMAGEN PRINCIPAL del formulario de EDITAR PRODUCTO con botones Editar/Eliminar
 * Acepta atributo: edit_id (ID de la imagen principal)
 */
function mc_ma_ea_form_imagen_principal_shortcode($atts) {
    ob_start();

    // Encolar media uploader si no está
    if (!did_action('wp_enqueue_media')) {
        wp_enqueue_media();
    }

    // Obtener ID de la imagen principal si se pasa por shortcode
    $atts = shortcode_atts([
        'edit_id' => 0,
    ], $atts, 'mc_ma_ea_form_imagen_principal');

    $imagen_id = intval($atts['edit_id']);
    $imagen_url = $imagen_id ? wp_get_attachment_url($imagen_id) : '';

    ?>
    <div class="mc-ma-na-grid-1col">
        <label class="edit-form-titular">Imagen Principal*</label>

        <div class="edit-form-image-box" onclick="abrirMediaUploaderEA(this, 'actividad_imagen_1')">
            <?php if ($imagen_url): ?>
                <img src="<?php echo esc_url($imagen_url); ?>" alt="Imagen principal">
                <div class="image-buttons">
                    <div class="btn edit">Editar</div>
                    <div class="btn delete">Eliminar</div>
                </div>
            <?php else: ?>
                <span>Haz clic para seleccionar la imagen principal</span>
            <?php endif; ?>
        </div>

        <input type="hidden" name="actividad_imagen_1" id="actividad_imagen_1" required value="<?php echo esc_attr($imagen_id); ?>">
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
    function abrirMediaUploaderEA(box, inputId){
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
            mostrarImagenConBotonesEA(box, inputId, attachment);
        });

        frame.open();
    }

    function mostrarImagenConBotonesEA(box, inputId, attachment) {
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

        // Botón Editar
        box.querySelector('.edit').addEventListener('click', function(e){
            e.stopPropagation();
            abrirMediaUploaderEA(box, inputId);
        });

        // Botón Eliminar
        box.querySelector('.delete').addEventListener('click', function(e){
            e.stopPropagation();
            box.innerHTML = '<span>Haz clic para seleccionar la imagen principal</span>';
            box.style.border = '2px dashed #ccc';
            if (inputElement) inputElement.value = '';
        });
    }

    // Inicializar la imagen existente al cargar la página
    jQuery(document).ready(function($){
        var input = document.getElementById('actividad_imagen_1');
        var box = input ? input.previousElementSibling : null;

        if (input && input.value && box) {
            var attachment = { id: input.value, url: '<?php echo esc_url($imagen_url); ?>' };
            mostrarImagenConBotonesEA(box, 'actividad_imagen_1', attachment);
        }
    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('mc_ma_ea_form_imagen_principal', 'mc_ma_ea_form_imagen_principal_shortcode');
