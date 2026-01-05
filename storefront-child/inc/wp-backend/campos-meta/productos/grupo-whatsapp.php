<?php
// Registrar campo meta para Grupo de WhatsApp
add_action('add_meta_boxes', function() {
    add_meta_box(
        'grupo_whatsapp_meta_box',           // ID del meta box
        'Enlace al grupo de WhatsApp',       // Título del meta box
        'render_grupo_whatsapp_meta_box',    // Función callback
        'product',                           // Post type (productos)
        'normal',                            // Contexto
        'default'                            // Prioridad
    );
});

// Función para renderizar el contenido del meta box
function render_grupo_whatsapp_meta_box($post) {
    // Obtener el valor actual del campo meta
    $grupo_whatsapp = get_post_meta($post->ID, 'grupo_whatsapp', true);
    
    // Nonce para seguridad
    wp_nonce_field('grupo_whatsapp_nonce', 'grupo_whatsapp_nonce_field');
    
    echo '<p>Introduce el enlace al grupo de WhatsApp asociado a este producto (por ejemplo, una excursión):</p>';
    echo '<input type="url" 
                 id="grupo_whatsapp" 
                 name="grupo_whatsapp" 
                 value="' . esc_attr($grupo_whatsapp) . '" 
                 style="width:100%; max-width:600px;" 
                 placeholder="https://chat.whatsapp.com/tu-enlace-aqui" />';
}

// Guardar el campo meta
add_action('save_post', function($post_id) {
    // Solo para productos
    $post_type = get_post_type($post_id);
    if ($post_type !== 'product') {
        return;
    }

    // Evitar autoguardados y revisiones
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }

    // Permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Caso REST/Gutenberg: las peticiones REST no incluyen el nonce del meta box.
    // Si estamos en una REST request, aceptamos el valor si viene en REQUEST.
    if (defined('REST_REQUEST') && REST_REQUEST) {
        if (isset($_REQUEST['grupo_whatsapp'])) {
            update_post_meta($post_id, 'grupo_whatsapp', esc_url_raw($_REQUEST['grupo_whatsapp']));
        }
        return;
    }

    // Verificar nonce en guardado clásico (admin)
    if (!isset($_POST['grupo_whatsapp_nonce_field']) || 
        !wp_verify_nonce($_POST['grupo_whatsapp_nonce_field'], 'grupo_whatsapp_nonce')) {
        return;
    }

    // Guardar el valor del campo si se envió desde el formulario clásico
    if (isset($_POST['grupo_whatsapp'])) {
        update_post_meta($post_id, 'grupo_whatsapp', esc_url_raw($_POST['grupo_whatsapp']));
    }
});

// Función para obtener el valor del campo meta
function get_grupo_whatsapp($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'grupo_whatsapp', true);
}
