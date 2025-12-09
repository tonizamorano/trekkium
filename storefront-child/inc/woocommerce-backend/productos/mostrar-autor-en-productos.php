<?php
// Habilitar campo Autor y asignar autor automáticamente al crear productos desde frontend

// 1️⃣ Activar soporte de Autor en productos (backend)
function trekkium_habilitar_autor_en_productos() {
    add_post_type_support('product', 'author');
}
add_action('init', 'trekkium_habilitar_autor_en_productos');

// 2️⃣ Asignar automáticamente autor al guía al crear producto desde frontend (ACF)
add_action('acf/save_post', function($post_id) {

    // Solo afecta a productos
    if (get_post_type($post_id) !== 'product') return;

    // Evitar que afecte a autosaves o revisiones
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;

    // Solo para usuarios conectados
    if (!is_user_logged_in()) return;

    // Solo asignar si el autor aún no es otro usuario (evita sobreescribir admins)
    $post = get_post($post_id);
    if ($post->post_author == 0 || $post->post_author != get_current_user_id()) {
        wp_update_post(array(
            'ID' => $post_id,
            'post_author' => get_current_user_id()
        ));
    }

}, 20); // Prioridad 20 para asegurarse de que ACF haya guardado los campos
