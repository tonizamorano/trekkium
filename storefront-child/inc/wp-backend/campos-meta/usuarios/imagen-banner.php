<?php

/**
 * Campo meta: Imagen del banner para usuarios con rol "guia"
 */
add_action('show_user_profile', 'trekkium_imagen_banner_metabox');
add_action('edit_user_profile', 'trekkium_imagen_banner_metabox');

function trekkium_imagen_banner_metabox($user) {
    // Solo mostrar si es guía
    if (!in_array('guia', (array) $user->roles)) {
        return;
    }

    $banner_id = get_user_meta($user->ID, 'imagen_banner_id', true);
    $banner_url = get_user_meta($user->ID, 'imagen_banner', true);
    
    // Si no hay ID pero sí hay URL, intentar obtener el ID
    if (!$banner_id && $banner_url) {
        $banner_id = attachment_url_to_postid($banner_url);
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
                <button type="button" class="button" id="imagen_banner_remove_btn" style="margin-left:10px;">Eliminar</button>

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
            
            // Si ya hay una imagen seleccionada, marcarla
            if ($('#imagen_banner').val()) {
                frame.on('open', function() {
                    var selection = frame.state().get('selection');
                    var attachment = wp.media.attachment($('#imagen_banner').val());
                    selection.add(attachment ? [attachment] : []);
                });
            }
            
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#imagen_banner').val(attachment.id);
                $('#imagen_banner_preview').html('<img src="'+attachment.url+'" style="max-width:400px;width:100%;aspect-ratio:16/7;object-fit:cover;border:1px solid #ccc;border-radius:0;" />');
            });
            frame.open();
        });

        $('#imagen_banner_remove_btn').on('click', function(){
            $('#imagen_banner').val('');
            $('#imagen_banner_preview').html('');
        });
    });
    </script>
    <?php
}