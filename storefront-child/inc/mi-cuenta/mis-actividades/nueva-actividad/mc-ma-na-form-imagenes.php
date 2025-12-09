<?php
/**
 * Shortcode para la sección de imágenes de GALERÍA del formulario de nueva actividad
 * Incluye tanto el HTML como el JavaScript relacionado
 */
function mc_ma_na_form_imagenes_shortcode() {
    ob_start();
    
    // Encolar media si no está ya encolado (para asegurar que funcione standalone)
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
    ?>
    
    <!-- GALERÍA de Imágenes (excluyendo imagen principal) -->
    <div class="mc-ma-na-grid-2col-imagenes">
        
        
        <?php for($i=1;$i<=4;$i++): ?>
            <div class="mc-ma-na-grid-1col">

                <label class="edit-form-titular">Imagen de Galería <?php echo $i; ?></label>

                <div class="edit-form-image-box" onclick="abrirMediaUploaderGaleria(this, 'actividad_galeria_<?php echo $i; ?>')">
                    <span>Haz clic para seleccionar</span>
                </div>

                <input type="hidden" name="actividad_galeria_<?php echo $i; ?>" id="actividad_galeria_<?php echo $i; ?>">

            </div>
        <?php endfor; ?>
    </div>
    
    <script>
    // Función específica para el uploader de imágenes de GALERÍA
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
            library: {
                type: 'image' // Solo imágenes
            }
        });
        
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            if (box) {
                box.innerHTML = '<img src="' + attachment.url + '" alt="Imagen de galería" style="max-width:100%;height:auto;">';
                // Agregar indicador visual de que es imagen de galería
                box.style.border = '2px dashed #0073aa';
            }
            var inputElement = document.getElementById(inputId);
            if (inputElement) {
                inputElement.value = attachment.id;
            }
        });
        
        frame.on('close', function() {
            // Liberar recursos si es necesario
        });
        
        frame.open();
    }
    
    // Inicializar si hay imágenes de galería precargadas (para edición)
    jQuery(document).ready(function($){
        // Si hay valores en los campos ocultos, mostrar las imágenes de galería
        for(var i=1; i<=4; i++) {
            var inputId = 'actividad_galeria_' + i;
            var input = document.getElementById(inputId);
            var box = input ? input.previousElementSibling : null;
            
            if (input && input.value && box) {
                // Obtener la URL de la imagen desde el ID
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    method: 'POST',
                    data: {
                        action: 'mc_get_image_url',
                        image_id: input.value
                    },
                    success: function(response) {
                        if (response.success && response.data.url) {
                            box.innerHTML = '<img src="' + response.data.url + '" alt="Imagen de galería cargada" style="max-width:100%;height:auto;">';
                            box.style.border = '2px dashed #0073aa';
                        }
                    }
                });
            }
        }
    });
    </script>
    
    <?php
    return ob_get_clean();
}
add_shortcode('mc_ma_na_form_imagenes', 'mc_ma_na_form_imagenes_shortcode');

/**
 * Función auxiliar para procesar imágenes de GALERÍA al guardar
 */
function mc_ma_na_procesar_imagenes_actividad($product_id) {
    $galeria_ids = array();
    
    // Recoger solo imágenes de galería (no la imagen principal)
    for ($i=1; $i<=4; $i++) {
        $meta_key = 'actividad_galeria_' . $i;
        if (isset($_POST[$meta_key]) && intval($_POST[$meta_key])) {
            $imagen_id = intval($_POST[$meta_key]);
            
            // Verificar que sea una imagen válida
            if (wp_attachment_is_image($imagen_id)) {
                $galeria_ids[] = $imagen_id;
            }
        }
    }
    
    // Si estamos trabajando con WooCommerce, actualizar la galería del producto
    if (function_exists('wc_update_product_gallery_attachment_ids') && $product_id) {
        // Convertir array a string separado por comas (formato de WooCommerce)
        $galeria_string = !empty($galeria_ids) ? implode(',', $galeria_ids) : '';
        
        // Actualizar metadato de galería
        update_post_meta($product_id, '_product_image_gallery', $galeria_string);
    }
    
    // También guardar como metadatos individuales para fácil acceso
    if (!empty($galeria_ids)) {
        update_post_meta($product_id, '_galeria_imagenes_ids', $galeria_ids);
        update_post_meta($product_id, '_galeria_imagenes_count', count($galeria_ids));
    } else {
        delete_post_meta($product_id, '_galeria_imagenes_ids');
        delete_post_meta($product_id, '_galeria_imagenes_count');
    }
    
    return $galeria_ids;
}

/**
 * AJAX handler para obtener URL de imagen por ID
 */
function mc_get_image_url_ajax() {/**
 * Shortcode para la sección de imágenes de GALERÍA del formulario de nueva actividad
 * Incluye tanto el HTML como el JavaScript relacionado
 */
function mc_ma_na_form_imagenes_shortcode() {
    ob_start();
    
    // Encolar media si no está ya encolado (para asegurar que funcione standalone)
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
    ?>
    
    <!-- GALERÍA de Imágenes (excluyendo imagen principal) -->
    <div class="mc-ma-na-grid-2col-imagenes">
        
        <?php for($i=2;$i<=4;$i++): ?>
            <div class="mc-ma-na-grid-1col">

                <label class="edit-form-titular">Imagen de Galería <?php echo $i; ?></label>

                <div class="edit-form-image-box" onclick="abrirMediaUploaderGaleria(this, 'actividad_imagen_<?php echo $i; ?>')">
                    <span>Haz clic para seleccionar</span>
                </div>

                <input type="hidden" name="actividad_imagen_<?php echo $i; ?>" id="actividad_imagen_<?php echo $i; ?>">

            </div>
        <?php endfor; ?>
    </div>
    
    <script>
    // Función específica para el uploader de imágenes de GALERÍA
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
            library: {
                type: 'image' // Solo imágenes
            }
        });
        
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            if (box) {
                box.innerHTML = '<img src="' + attachment.url + '" alt="Imagen de galería" style="max-width:100%;height:auto;">';
                // Agregar indicador visual de que es imagen de galería
                box.style.border = '2px dashed #0073aa';
            }
            var inputElement = document.getElementById(inputId);
            if (inputElement) {
                inputElement.value = attachment.id;
            }
        });
        
        frame.on('close', function() {
            // Liberar recursos si es necesario
        });
        
        frame.open();
    }
    
    // Inicializar si hay imágenes de galería precargadas (para edición)
    jQuery(document).ready(function($){
        // Si hay valores en los campos ocultos, mostrar las imágenes de galería
        for(var i=2; i<=4; i++) {
            var inputId = 'actividad_imagen_' + i;
            var input = document.getElementById(inputId);
            var box = input ? input.previousElementSibling : null;
            
            if (input && input.value && box) {
                // Obtener la URL de la imagen desde el ID
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    method: 'POST',
                    data: {
                        action: 'mc_get_image_url',
                        image_id: input.value
                    },
                    success: function(response) {
                        if (response.success && response.data.url) {
                            box.innerHTML = '<img src="' + response.data.url + '" alt="Imagen de galería cargada" style="max-width:100%;height:auto;">';
                            box.style.border = '2px dashed #0073aa';
                        }
                    }
                });
            }
        }
    });
    </script>
    
    <?php
    return ob_get_clean();
}
add_shortcode('mc_ma_na_form_imagenes', 'mc_ma_na_form_imagenes_shortcode');
    $image_id = intval($_POST['image_id']);
    
    if ($image_id) {
        $image_url = wp_get_attachment_url($image_id);
        if ($image_url) {
            wp_send_json_success(array('url' => $image_url));
        }
    }
    
    wp_send_json_error('Imagen no encontrada');
}
add_action('wp_ajax_mc_get_image_url', 'mc_get_image_url_ajax');
add_action('wp_ajax_nopriv_mc_get_image_url', 'mc_get_image_url_ajax');