<?php

/**
 * Campo meta: Imagen del banner para usuarios con rol "guia"
 */
add_action('show_user_profile', 'trekkium_imagen_banner_guia_metabox');
add_action('edit_user_profile', 'trekkium_imagen_banner_guia_metabox');

function trekkium_imagen_banner_guia_metabox($user) {
    // Solo mostrar si es guía
    if (!in_array('guia', (array) $user->roles)) {
        return;
    }

    $banner_url = get_user_meta($user->ID, 'imagen_banner_guia', true);
    ?>
    <h2>Imagen del banner</h2>
    <table class="form-table">
        <tr>
            <th><label for="imagen_banner_guia">Subir imagen</label></th>
            <td>
                <input type="hidden" name="imagen_banner_guia" id="imagen_banner_guia" value="<?php echo esc_attr($banner_url); ?>" />
                <div id="imagen_banner_guia_preview" style="margin-bottom:10px;">
                        <?php if ($banner_url): ?>
                            <img src="<?php echo esc_url($banner_url); ?>" style="max-width:400px;width:100%;aspect-ratio:16/7;object-fit:cover;border:1px solid #ccc;border-radius:0;" />
                        <?php endif; ?>
                </div>
                <button type="button" class="button" id="imagen_banner_guia_upload_btn">Subir / Cambiar imagen</button>
                <button type="button" class="button" id="imagen_banner_guia_remove_btn" style="margin-left:10px;">Eliminar</button>

                <p class="description">Sube una imagen horizontal para usar como banner en la ficha del guía (máx. 1600 px de ancho recomendado).</p>
            </td>
        </tr>
    </table>

    <script>
    jQuery(document).ready(function($){
        var frame;
        $('#imagen_banner_guia_upload_btn').on('click', function(e){
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'Seleccionar imagen del banner',
                button: { text: 'Usar esta imagen' },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#imagen_banner_guia').val(attachment.id);
                $('#imagen_banner_guia_preview').html('<img src="'+attachment.url+'" style="max-width:400px;height:auto;border:1px solid #ccc;" />');
            });
            frame.open();
        });

        $('#imagen_banner_guia_remove_btn').on('click', function(){
            $('#imagen_banner_guia').val('');
            $('#imagen_banner_guia_preview').html('');
        });
    });
    </script>
    <?php
}

/**
 * Guardar y optimizar imagen del banner
 */
add_action('personal_options_update', 'trekkium_guardar_imagen_banner_guia');
add_action('edit_user_profile_update', 'trekkium_guardar_imagen_banner_guia');

function trekkium_guardar_imagen_banner_guia($user_id) {
    if (!current_user_can('edit_user', $user_id)) return false;

    if (!empty($_POST['imagen_banner_guia'])) {
        $attachment_id = intval($_POST['imagen_banner_guia']);
        $image_path = get_attached_file($attachment_id);
        $image_editor = wp_get_image_editor($image_path);

        if (!is_wp_error($image_editor)) {
            // Redimensionar y optimizar para web
            $image_editor->resize(1600, 900, false);
            $image_editor->set_quality(80); // compresión moderada
            $optimized_path = $image_editor->generate_filename('optimized');
            $image_editor->save($optimized_path);

            // Obtener URL optimizada
            $upload_dir = wp_upload_dir();
            $optimized_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $optimized_path);

            update_user_meta($user_id, 'imagen_banner_guia', esc_url($optimized_url));
        }
    } else {
        delete_user_meta($user_id, 'imagen_banner_guia');
    }
}
