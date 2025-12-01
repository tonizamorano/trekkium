<?php

/* =========================================================
   METABOX: Experiencia y requisitos (WYSIWYG sin medios)
   Meta key: experiencia_requisitos
========================================================= */

function trekkium_add_metabox_experiencia_requisitos() {
    add_meta_box(
        'trekkium_experiencia_requisitos',
        'Experiencia y requisitos',
        'trekkium_render_metabox_experiencia_requisitos',
        'product', // <-- Cambia por tu CPT si no es product
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'trekkium_add_metabox_experiencia_requisitos');


function trekkium_render_metabox_experiencia_requisitos($post) {

    wp_nonce_field('trekkium_guardar_experiencia_requisitos', 'trekkium_experiencia_requisitos_nonce');

    $contenido = get_post_meta($post->ID, 'experiencia_requisitos', true);

    wp_editor($contenido, 'experiencia_requisitos_editor', array(
        'textarea_name' => 'experiencia_requisitos',
        'media_buttons' => false,
        'teeny' => false,
        'tinymce' => true,         // Solo editor visual
        'quicktags' => false,      // ❌ Sin pestaña Texto / HTML
        'editor_height' => 180
    ));
}


function trekkium_guardar_experiencia_requisitos($post_id) {

    if (!isset($_POST['trekkium_experiencia_requisitos_nonce'])) return;
    if (!wp_verify_nonce($_POST['trekkium_experiencia_requisitos_nonce'], 'trekkium_guardar_experiencia_requisitos')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['experiencia_requisitos'])) {
        update_post_meta(
            $post_id,
            'experiencia_requisitos',
            wp_kses_post($_POST['experiencia_requisitos'])
        );
    }
}
add_action('save_post', 'trekkium_guardar_experiencia_requisitos');
