<?php

// Shortcode: Información adicional para detalles del guía
add_shortcode('mc_ma_da_info_adicional', function ($atts) {

    $atts = shortcode_atts([
        'id' => 0
    ], $atts);

    $actividad_id = intval($atts['id']);
    if (!$actividad_id) return '<p>Actividad no válida.</p>';

    $actividad = get_post($actividad_id);
    if (!$actividad || $actividad->post_type !== 'product') return '<p>Actividad no válida.</p>';

    // Obtener información adicional actual
    $informacion_adicional = get_post_meta($actividad_id, 'informacion_adicional', true);

    // Procesar actualización si se envió el formulario
    if (isset($_POST['mc_actualizar_info_adicional']) && $_POST['mc_actividad_id'] == $actividad_id) {
        if (wp_verify_nonce($_POST['mc_nonce_info_adicional'], 'mc_actualizar_info_' . $actividad_id)) {
            $nueva_info = sanitize_textarea_field($_POST['informacion_adicional']);
            update_post_meta($actividad_id, 'informacion_adicional', $nueva_info);
            $informacion_adicional = $nueva_info;
            
            // Mostrar mensaje de éxito
            echo '<div class="mc-ma-da-mensaje-exito">¡Información actualizada correctamente!</div>';
        }
    }

    ob_start();
    ?>

    <div class="mc-ma-da-contenedor">

        <div class="mc-ma-da-titular">
            <h2>Información adicional</h2>
        </div>

        <div class="mc-ma-da-contenido">
            <form method="POST" class="mc-ma-da-form-info">
                <?php wp_nonce_field('mc_actualizar_info_' . $actividad_id, 'mc_nonce_info_adicional'); ?>
                <input type="hidden" name="mc_actividad_id" value="<?php echo esc_attr($actividad_id); ?>">
                
                <div class="mc-ma-da-campo-textarea">
                    <label for="informacion_adicional" class="mc-ma-da-label">Información adicional para la actividad:</label>
                    <textarea 
                        id="informacion_adicional" 
                        name="informacion_adicional" 
                        rows="8" 
                        class="mc-ma-da-textarea"
                        placeholder="Escribe aquí cualquier información adicional sobre la actividad..."
                    ><?php echo esc_textarea($informacion_adicional); ?></textarea>
                </div>
                
                <div class="mc-ma-da-boton-container">
                    <button type="submit" name="mc_actualizar_info_adicional" class="mc-ma-da-boton-actualizar">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php
    return ob_get_clean();
});