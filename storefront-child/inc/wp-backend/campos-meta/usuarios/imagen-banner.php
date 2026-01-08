<?php

/**
 * Campo meta: Imagen del banner para usuarios con rol "guia"
 */
add_action('admin_enqueue_scripts', function() {
    $screen = get_current_screen();
    if ($screen && in_array($screen->id, ['profile', 'user-edit'])) {
        wp_enqueue_media();
    }
});

add_action('show_user_profile', 'trekkium_imagen_banner_metabox');
add_action('edit_user_profile', 'trekkium_imagen_banner_metabox');

function trekkium_imagen_banner_metabox($user) {
    if (!in_array('guia', (array) $user->roles)) {
        return;
    }

    // Normalizar: usamos la clave 'imagen_banner' para almacenar el ID del attachment
    $banner_meta = get_user_meta($user->ID, 'imagen_banner', true);
    $banner_id = is_numeric($banner_meta) ? intval($banner_meta) : 0;
    $banner_url = '';

    if ($banner_id) {
        $banner_url = wp_get_attachment_image_url($banner_id, 'full');
    } elseif (!empty($banner_meta)) {
        // Si hay algo y no es ID, puede ser una URL antigua: intentamos obtener el ID
        $banner_url = esc_url_raw($banner_meta);
        $maybe_id = attachment_url_to_postid($banner_url);
        if ($maybe_id) {
            $banner_id = $maybe_id;
        }
    }
    ?>
    <h2>Imagen del banner</h2>
    <table class="form-table">
        <tr>
            <th><label for="imagen_banner">Subir imagen</label></th>
            <td>
                <input type="hidden" name="imagen_banner" id="imagen_banner" value="<?php echo esc_attr($banner_id); ?>" />
                <div id="imagen_banner_preview" style="margin-bottom:10px;">
                    <?php if ($banner_url): ?>
                        <img src="<?php echo esc_url($banner_url); ?>" style="max-width:400px;width:100%;aspect-ratio:16/7;object-fit:cover;border:1px solid #ccc;border-radius:0;" />
                    <?php endif; ?>
                </div>
                <button type="button" class="button" id="imagen_banner_upload_btn">Subir / Cambiar imagen</button>
                <button type="button" class="button" id="imagen_banner_remove_btn" style="margin-left:10px;<?php echo !$banner_id ? 'display:none;' : ''; ?>">Eliminar</button>

                <p class="description">Sube una imagen horizontal para usar como banner en la ficha del guía (máx. 1600 px de ancho recomendado).</p>
            </td>
        </tr>
    </table>

    <script>
    jQuery(document).ready(function($){
        var frame;
        $('#imagen_banner_upload_btn').on('click', function(e){
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'Seleccionar imagen del banner',
                button: { text: 'Usar esta imagen' },
                multiple: false,
                library: { type: 'image' }
            });

            frame.on('open', function() {
                var id = $('#imagen_banner').val();
                if (id) {
                    var selection = frame.state().get('selection');
                    var attachment = wp.media.attachment(id);
                    if (attachment) selection.add([attachment]);
                }
            });

            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#imagen_banner').val(attachment.id);
                var url = attachment.sizes && attachment.sizes.full ? attachment.sizes.full.url : attachment.url;
                $('#imagen_banner_preview').html('<img src="'+url+'" style="max-width:400px;width:100%;aspect-ratio:16/7;object-fit:cover;border:1px solid #ccc;border-radius:0;" />');
                $('#imagen_banner_remove_btn').show();
            });
            frame.open();
        });

        $('#imagen_banner_remove_btn').on('click', function(){
            $('#imagen_banner').val('');
            $('#imagen_banner_preview').html('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

// Guardar el campo meta del banner como el ID del attachment en 'imagen_banner'
add_action('personal_options_update', 'trekkium_guardar_imagen_banner');
add_action('edit_user_profile_update', 'trekkium_guardar_imagen_banner');

function trekkium_guardar_imagen_banner($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (isset($_POST['imagen_banner'])) {
        $val = sanitize_text_field($_POST['imagen_banner']);
        // Guardar vacío o ID
        update_user_meta($user_id, 'imagen_banner', $val);
    }
}