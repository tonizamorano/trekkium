<?php
// Registrar campo meta para Dificultad técnica
add_action('add_meta_boxes', function() {
    add_meta_box(
        'dificultad_tecnica_meta_box',           // ID del meta box
        'Dificultad técnica',                   // Título del meta box
        'render_dificultad_tecnica_meta_box',   // Función callback
        'product',                              // Post type (productos)
        'normal',                               // Contexto
        'default'                               // Prioridad
    );
});

// Función para renderizar el contenido del meta box
function render_dificultad_tecnica_meta_box($post) {
    // Obtener el valor actual del campo meta
    $dificultad_tecnica = get_post_meta($post->ID, 'dificultad_tecnica', true);
    
    // Nonce para seguridad
    wp_nonce_field('dificultad_tecnica_nonce', 'dificultad_tecnica_nonce_field');
    
    // Configurar editor wysiwyg
    $settings = array(
        'textarea_name' => 'dificultad_tecnica',
        'textarea_rows' => 10,
        'teeny' => false,
        'media_buttons' => false,      // Ocultar botón de medios
        'tinymce' => array(
            'toolbar1' => 'bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,undo,redo',
            'toolbar2' => '',
        ),
        'quicktags' => false,          // Deshabilitar HTML quicktags
    );
    
    wp_editor($dificultad_tecnica, 'dificultad_tecnica_editor', $settings);
}

// Guardar el campo meta
add_action('save_post', function($post_id) {
    // Verificar nonce
    if (!isset($_POST['dificultad_tecnica_nonce_field']) || 
        !wp_verify_nonce($_POST['dificultad_tecnica_nonce_field'], 'dificultad_tecnica_nonce')) {
        return;
    }
    
    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Verificar autoguardado
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Guardar el valor del campo
    if (isset($_POST['dificultad_tecnica'])) {
        update_post_meta(
            $post_id, 
            'dificultad_tecnica', 
            wp_kses_post($_POST['dificultad_tecnica'])  // Sanitizar contenido HTML
        );
    }
});

// Opcional: Función para obtener el valor del campo meta
function get_dificultad_tecnica($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'dificultad_tecnica', true);
}