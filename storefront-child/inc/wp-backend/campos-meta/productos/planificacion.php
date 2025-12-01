<?php
// Registrar meta box para Planificación
add_action('add_meta_boxes', function() {
    add_meta_box(
        'planificacion_meta_box',       // ID del meta box
        'Planificación',               // Título del meta box
        'render_planificacion_meta_box', // Función callback
        'product',                     // Post type (productos)
        'normal',                      // Contexto
        'default'                      // Prioridad
    );
});

// Función para renderizar el contenido del meta box
function render_planificacion_meta_box($post) {
    // Obtener el valor actual del campo meta
    $planificacion = get_post_meta($post->ID, 'planificacion', true);
    
    // Nonce para seguridad
    wp_nonce_field('planificacion_nonce', 'planificacion_nonce_field');
    
    // Configurar editor wysiwyg
    $settings = array(
        'textarea_name' => 'planificacion',
        'textarea_rows' => 10,
        'teeny' => false,
        'media_buttons' => false,      // Ocultar botón de medios
        'tinymce' => array(
            'toolbar1' => 'bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,undo,redo',
            'toolbar2' => '',
        ),
        'quicktags' => false,          // Deshabilitar HTML quicktags
    );
    
    wp_editor($planificacion, 'planificacion_editor', $settings);
}

// Guardar el campo meta
add_action('save_post', function($post_id) {
    // Verificar nonce
    if (!isset($_POST['planificacion_nonce_field']) || 
        !wp_verify_nonce($_POST['planificacion_nonce_field'], 'planificacion_nonce')) {
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
    if (isset($_POST['planificacion'])) {
        update_post_meta(
            $post_id, 
            'planificacion', 
            wp_kses_post($_POST['planificacion'])  // Sanitizar contenido HTML
        );
    }
});

// Función para obtener el valor del campo meta
function get_planificacion_producto($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'planificacion', true);
}

// Función para mostrar el contenido formateado
function mostrar_planificacion_producto($post_id = null) {
    $planificacion = get_planificacion_producto($post_id);
    if ($planificacion) {
        return apply_filters('the_content', $planificacion);
    }
    return '';
}