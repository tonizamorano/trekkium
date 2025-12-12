<?php
/**
 * Shortcode Formulario Campos de Texto Avanzados (Editor) para edición de actividad
 * Muestra los valores guardados en la actividad y permite editarlos respetando los párrafos.
 * Uso: [mc_ma_ea_form_edit_text post_id="123"]
 */
function mc_ma_ea_form_edit_text_shortcode($atts) {
    ob_start();

    $atts = shortcode_atts([
        'post_id' => 0,
    ], $atts, 'mc_ma_ea_form_edit_text');

    $post_id = intval($atts['post_id']);
    if (!$post_id) {
        return '<p style="color:red;">⚠️ No se ha especificado un ID de actividad válido.</p>';
    }

    $campos_editor = [
        'dificultad_tecnica' => 'Dificultad técnica*',
        'experiencia_requisitos' => 'Requisitos y experiencia*',
        'planificacion'      => 'Planificación*',
        'material'           => 'Material necesario*',
        'incluye'            => 'Incluido en la actividad*',
    ];

    foreach($campos_editor as $slug => $label){
        echo '<div class="mc-ma-na-grid-1col"><label class="edit-form-titular">'.$label.'</label>';

        // Obtener contenido guardado desde la meta del post
        $contenido_guardado = get_post_meta($post_id, $slug, true);

        wp_editor(
            $contenido_guardado,
            'actividad_'.$slug,
            [
                'textarea_name' => 'actividad_'.$slug,
                'textarea_rows' => 8,
                'media_buttons' => false,
                'teeny'         => false,
                'quicktags'     => false,
            ]
        );
        echo '</div>';
    }

    return ob_get_clean();
}
add_shortcode('mc_ma_ea_form_edit_text', 'mc_ma_ea_form_edit_text_shortcode');
