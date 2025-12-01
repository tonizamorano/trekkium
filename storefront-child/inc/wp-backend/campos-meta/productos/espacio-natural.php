<?php
/*************************************************
 METABOX: Espacio natural
 Meta key: espacio_natural
**************************************************/

function trekkium_add_metabox_espacio_natural() {
    add_meta_box(
        'trekkium_espacio_natural',
        'Espacio natural',
        'trekkium_render_metabox_espacio_natural',
        'product', // <-- Cambia si tu CPT no es product
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'trekkium_add_metabox_espacio_natural');


function trekkium_render_metabox_espacio_natural($post) {

    wp_nonce_field('trekkium_guardar_espacio_natural', 'trekkium_espacio_natural_nonce');

    $valor = get_post_meta($post->ID, 'espacio_natural', true);

    echo '<input type="text"
        name="espacio_natural"
        value="' . esc_attr($valor) . '"
        style="width:100%;"
        placeholder="Ej: Parque Natural del Cadí-Moixeró" />';
}


function trekkium_guardar_espacio_natural($post_id) {

    if (!isset($_POST['trekkium_espacio_natural_nonce'])) return;
    if (!wp_verify_nonce($_POST['trekkium_espacio_natural_nonce'], 'trekkium_guardar_espacio_natural')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['espacio_natural'])) {
        update_post_meta(
            $post_id,
            'espacio_natural',
            sanitize_text_field($_POST['espacio_natural'])
        );
    }
}
add_action('save_post', 'trekkium_guardar_espacio_natural');
