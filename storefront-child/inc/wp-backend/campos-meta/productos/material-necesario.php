<?php
// Registrar meta box para Material necesario
add_action('add_meta_boxes', function() {
    add_meta_box(
        'material_necesario_meta_box',    // ID del meta box
        'Material necesario',             // Título del meta box
        'render_material_necesario_meta_box', // Función callback
        'product',                        // Post type (productos)
        'normal',                         // Contexto
        'default'                         // Prioridad
    );
});

// Función para renderizar el contenido del meta box
function render_material_necesario_meta_box($post) {
    // Obtener el valor actual del campo meta
    $material = get_post_meta($post->ID, 'material', true);
    
    // Nonce para seguridad
    wp_nonce_field('material_necesario_nonce', 'material_necesario_nonce_field');
    
    // Configurar editor wysiwyg
    $settings = array(
        'textarea_name' => 'material',
        'textarea_rows' => 10,
        'teeny' => false,
        'media_buttons' => false,      // Ocultar botón de medios
        'tinymce' => array(
            'toolbar1' => 'bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,undo,redo',
            'toolbar2' => '',
        ),
        'quicktags' => false,          // Deshabilitar HTML quicktags
    );
    
    wp_editor($material, 'material_editor', $settings);
}

// Guardar el campo meta
add_action('save_post', function($post_id) {
    // Verificar nonce
    if (!isset($_POST['material_necesario_nonce_field']) || 
        !wp_verify_nonce($_POST['material_necesario_nonce_field'], 'material_necesario_nonce')) {
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
    if (isset($_POST['material'])) {
        update_post_meta(
            $post_id, 
            'material', 
            wp_kses_post($_POST['material'])  // Sanitizar contenido HTML
        );
    }
});

// Función para obtener el valor del campo meta
function get_material_necesario($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'material', true);
}

// Función para mostrar el contenido formateado
function mostrar_material_necesario($post_id = null) {
    $material = get_material_necesario($post_id);
    if ($material) {
        return apply_filters('the_content', $material);
    }
    return '';
}