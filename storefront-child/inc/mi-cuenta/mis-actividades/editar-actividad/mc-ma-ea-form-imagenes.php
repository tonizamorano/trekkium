<?php
/**
 * Shortcode para la sección de imágenes de GALERÍA en el formulario de EDITAR actividad
 * Incluye botones Editar/Eliminar y carga imágenes existentes
 */
function mc_ma_ea_form_imagenes_shortcode($atts) {
    ob_start();

    $atts = shortcode_atts([
        'edit_ids' => '', // IDs existentes separados por coma
    ], $atts, 'mc_ma_ea_form_imagenes');

    $edit_ids = $atts['edit_ids'] ? explode(',', $atts['edit_ids']) : [];

    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
    ?>
    
    <div class="mc-ma-na-grid-2col-imagenes">
        <?php for($i=1;$i<=4;$i++): 
            $image_id = isset($edit_ids[$i-1]) ? intval($edit_ids[$i-1]) : '';
        ?>
            <div class="mc-ma-na-grid-1col">

                <label class="edit-form-titular">Imagen de Galería <?php echo $i; ?></label>

                <div class="edit-form-image-box" onclick="abrirMediaUploaderGaleria(this, 'actividad_galeria_<?php echo $i; ?>')">
                    <?php if ($image_id): ?>
                        <?php echo wp_get_attachment_image($image_id, 'medium'); ?>
                        <div class="image-buttons">
                            <div class="btn edit">Editar</div>
                            <div class="btn delete">Eliminar</div>
                        </div>
                    <?php else: ?>
                        <span>Haz clic para seleccionar</span>
                    <?php endif; ?>
                </div>

                <input type="hidden" name="actividad_galeria_<?php echo $i; ?>" id="actividad_galeria_<?php echo $i; ?>" value="<?php echo esc_attr($image_id); ?>">

            </div>
        <?php endfor; ?>
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
    function abrirMediaUploaderGaleria(box, inputId){
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            console.error('WordPress media library no está disponible');
            alert('Error: La biblioteca de medios no está disponible. Recarga la página.');
            return;
        }

        var frame = wp.media({
            title: 'Seleccionar imagen para galería',
            button: { text: 'Usar esta imagen en galería' },
            multiple: false,
            library: { type: 'image' }
        });

        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            mostrarImagenGaleriaConBotones(box, inputId, attachment);
        });

        frame.open();
    }

    function mostrarImagenGaleriaConBotones(box, inputId, attachment) {
        box.innerHTML = `
            <img src="${attachment.url}" alt="Imagen de galería">
            <div class="image-buttons">
                <div class="btn edit">Editar</div>
                <div class="btn delete">Eliminar</div>
            </div>
        `;
        box.style.border = '2px solid #0073aa';

        var inputElement = document.getElementById(inputId);
        if (inputElement) inputElement.value = attachment.id;

        box.querySelector('.edit').addEventListener('click', function(e){
            e.stopPropagation();
            abrirMediaUploaderGaleria(box, inputId);
        });

        box.querySelector('.delete').addEventListener('click', function(e){
            e.stopPropagation();
            box.innerHTML = '<span>Haz clic para seleccionar</span>';
            box.style.border = '2px dashed #ccc';
            if (inputElement) inputElement.value = '';
        });
    }
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('mc_ma_ea_form_imagenes', 'mc_ma_ea_form_imagenes_shortcode');
