<?php

/* =========================================================
   METABOX: Información adicional del guía (WYSIWYG sin medios)
   Meta key: informacion_adicional
========================================================= */

function trekkium_add_metabox_informacion_adicional() {
    add_meta_box(
        'trekkium_info_adicional',
        'Información adicional',
        'trekkium_render_metabox_informacion_adicional',
        'product', // <-- Cambia por tu CPT si no es product
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'trekkium_add_metabox_informacion_adicional');


function trekkium_render_metabox_informacion_adicional($post) {

    wp_nonce_field('trekkium_guardar_info_adicional', 'trekkium_info_adicional_nonce');

    $contenido = get_post_meta($post->ID, 'informacion_adicional', true);

    add_filter('user_can_richedit', '__return_true');

    wp_editor($contenido, 'informacion_adicional_editor', array(
        'textarea_name' => 'informacion_adicional',
        'media_buttons' => false,
        'teeny' => false,
        'tinymce' => true,
        'quicktags' => false,
        'editor_height' => 180
    ));
}


function trekkium_guardar_informacion_adicional($post_id) {

    if (!isset($_POST['trekkium_info_adicional_nonce'])) return;
    if (!wp_verify_nonce($_POST['trekkium_info_adicional_nonce'], 'trekkium_guardar_info_adicional')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['informacion_adicional'])) {
        update_post_meta(
            $post_id,
            'informacion_adicional',
            wp_kses_post($_POST['informacion_adicional'])
        );
    }
}
add_action('save_post', 'trekkium_guardar_informacion_adicional');
