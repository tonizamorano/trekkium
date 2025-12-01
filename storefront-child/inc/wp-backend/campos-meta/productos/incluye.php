<?php
// Registrar meta box para Incluye
add_action('add_meta_boxes', function() {
    add_meta_box(
        'incluye_meta_box',           // ID del meta box
        'Incluye',                   // Título del meta box
        'render_incluye_meta_box',   // Función callback
        'product',                   // Post type (productos)
        'normal',                    // Contexto
        'default'                    // Prioridad
    );
});

// Función para renderizar el contenido del meta box
function render_incluye_meta_box($post) {
    // Obtener el valor actual del campo meta
    $incluye = get_post_meta($post->ID, 'incluye', true);
    
    // Si no hay contenido guardado, usar el contenido predeterminado
    if (empty($incluye)) {
        $incluye = '✔︎ Guía de montaña oficial<br>
✔︎ Organización y planificación<br>
✔︎ Grupo de Whatsapp<br>
✔︎ Seguros RC y accidentes<br>
✔︎ Tasas e impuestos<br>
<br>
NO INCLUYE:<br>
✔︎ Desplazamientos<br>
✔︎ Picnic';
    }
    
    // Nonce para seguridad
    wp_nonce_field('incluye_nonce', 'incluye_nonce_field');
    
    // Configurar editor wysiwyg
    $settings = array(
        'textarea_name' => 'incluye',
        'textarea_rows' => 10,
        'teeny' => false,
        'media_buttons' => false,      // Ocultar botón de medios
        'tinymce' => array(
            'toolbar1' => 'bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,undo,redo',
            'toolbar2' => '',
        ),
        'quicktags' => false,          // Deshabilitar HTML quicktags
    );
    
    wp_editor($incluye, 'incluye_editor', $settings);
}

// Guardar el campo meta
add_action('save_post', function($post_id) {
    // Verificar nonce
    if (!isset($_POST['incluye_nonce_field']) || 
        !wp_verify_nonce($_POST['incluye_nonce_field'], 'incluye_nonce')) {
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
    if (isset($_POST['incluye'])) {
        update_post_meta(
            $post_id, 
            'incluye', 
            wp_kses_post($_POST['incluye'])  // Sanitizar contenido HTML
        );
    }
});

// Función para obtener el valor del campo meta
function get_incluye_producto($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $incluye = get_post_meta($post_id, 'incluye', true);
    
    // Si no hay contenido guardado, devolver el contenido predeterminado
    if (empty($incluye)) {
        $incluye = '✔︎ Guía de montaña oficial<br>
✔︎ Organización y planificación<br>
✔︎ Grupo de Whatsapp<br>
✔︎ Seguros RC y accidentes<br>
✔︎ Tasas e impuestos<br>
<br>
NO INCLUYE:<br>
✔︎ Desplazamientos<br>
✔︎ Picnic';
    }
    
    return $incluye;
}

// Función para mostrar el contenido formateado
function mostrar_incluye_producto($post_id = null) {
    $incluye = get_incluye_producto($post_id);
    if ($incluye) {
        return apply_filters('the_content', $incluye);
    }
    return '';
}