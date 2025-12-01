<?php
// Registrar metabox para Punto de Encuentro y Google Maps
add_action('add_meta_boxes', function() {
    add_meta_box(
        'encuentro_meta_box',
        'Punto de Encuentro y Google Maps',
        'render_encuentro_meta_box',
        'product',
        'normal',
        'default'
    );
});

// Renderizar metabox de Punto de Encuentro y Google Maps
function render_encuentro_meta_box($post) {
    $encuentro = get_post_meta($post->ID, 'encuentro', true);
    $google_maps = get_post_meta($post->ID, 'google_maps', true);
    wp_nonce_field('encuentro_nonce', 'encuentro_nonce_field');
    ?>
    <div style="margin:15px 0; display:flex; gap:20px;">
        <div style="flex:1; min-width:200px;">
            <label for="encuentro" style="display:block; margin-bottom:5px; font-weight:bold;">Punto de encuentro</label>
            <input type="text" id="encuentro" name="encuentro" value="<?php echo esc_attr($encuentro); ?>" placeholder="Ej: Plaza Mayor, entrada principal" style="width:100%; padding:8px;">
        </div>
        <div style="flex:1; min-width:200px;">
            <label for="google_maps" style="display:block; margin-bottom:5px; font-weight:bold;">Enlace a Google Maps</label>
            <input type="url" id="google_maps" name="google_maps" value="<?php echo esc_attr($google_maps); ?>" placeholder="https://maps.google.com/..." style="width:100%; padding:8px;">
        </div>
    </div>
    <?php
}

// Guardar Punto de Encuentro y Google Maps
add_action('save_post', function($post_id) {
    if (!isset($_POST['encuentro_nonce_field']) || !wp_verify_nonce($_POST['encuentro_nonce_field'], 'encuentro_nonce')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    foreach (['encuentro', 'google_maps'] as $campo) {
        if (isset($_POST[$campo])) {
            update_post_meta($post_id, $campo, sanitize_text_field($_POST[$campo]));
        }
    }
});
