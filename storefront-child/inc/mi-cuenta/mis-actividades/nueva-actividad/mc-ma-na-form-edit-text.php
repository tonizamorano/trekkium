<?php
/**
 * Shortcode Formulario Campos de Texto Avanzados (Editor)
 * Campos: Dificultad técnica, Requisitos y experiencia, Planificación, Material necesario, Incluido en la actividad.   
 */

function mc_ma_na_form_edit_text_shortcode() {
    ob_start();

    $campos_editor = [
        'dificultad_tecnica' => 'Dificultad técnica*',
        'experiencia_requisitos' => 'Requisitos y experiencia*',
        'planificacion'      => 'Planificación*',
        'material'           => 'Material necesario*',
        'incluye'            => 'Incluido en la actividad*',
    ];
    
    // Definir contenidos por defecto para cada campo
    $default_contents = [
        'dificultad_tecnica' => 'Describe en este apartado todo lo relacionado con las características técnicas de la actividad, como tipo de terreno, pasos equipados, exposición al vacío, grimpadas, graduación de vías ferratas, de escalada o alpinismo, etc...',

        'experiencia_requisitos' => 'Describe en este apartado cuales son los requisitos o experiencia previa necesaria de los participantes para realizar esta actividad, por ejemplo: "No se necesita experiencia previa", "Estar habituado a realizar actividades de senderismo", etc...',
                         
        'planificacion' => '08:00 Encuentro con el guía<br>'
                         . '09:00 Inicio de la actividad<br>'
                         . '10:30 Pico del Mirador (descanso 15 min)<br>'
                         . '12:00 Collado del Rebeco (descanso 15 min)<br>'
                         . '13:30 Mirador del Valle (picnic 30 min)<br>'
                         . '15:30 Pico Bastiselles (descanso 15 min)<br>'
                         . '17:00 Fin de la actividad',
        
        'material' => '✔︎ Material 1<br>'
                    . '✔︎ Material 2<br>'
                    . '✔︎ Material 3<br>'
                    . '✔︎ Material 4<br>'
                    . '✔︎ Material 5<br>'
                    . '✔︎ Material 6<br>'
                    . '✔︎ Material 7',
        
        'incluye' => '✔︎ Guia oficial titulado<br>'
                   . '✔︎ Organización y planificación<br>'
                   . '✔︎ Grupo de Whatsapp (consultas al guía, organización de transportes…)<br>'
                   . '✔︎ Seguros RC y accidentes<br>'
                   . '✔︎ Tasas e impuestos<br><br>'
                   . 'NO INCLUYE:<br>'
                   . '✔︎ Desplazamientos<br>'
                   . '✔︎ Picnic',
    ];

    foreach($campos_editor as $slug => $label){
        echo '<div class="mc-ma-na-grid-1col"><label class="edit-form-titular">'.$label.'</label>';
        
        // Obtener contenido por defecto si existe
        $default_content = isset($default_contents[$slug]) ? $default_contents[$slug] : '';
        
        wp_editor(
            $default_content,  // Contenido por defecto
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
add_shortcode('mc_ma_na_form_edit_text', 'mc_ma_na_form_edit_text_shortcode');