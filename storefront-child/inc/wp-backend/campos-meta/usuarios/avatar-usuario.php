<?php
// ================================
// Campo personalizado meta "Avatar del usuario"
// ================================

// Asegurar que se carguen los scripts de WordPress Media
add_action('admin_enqueue_scripts', function() {
    if (in_array(get_current_screen()->id, ['profile', 'user-edit'])) {
        wp_enqueue_media();
    }
});

add_action('show_user_profile', 'mostrar_campo_avatar_usuario');
add_action('edit_user_profile', 'mostrar_campo_avatar_usuario');

function mostrar_campo_avatar_usuario($user) {
    ?>
    <h3>Avatar del usuario</h3>
    <table class="form-table">
        <tr>
            <th><label for="avatar_del_usuario">Avatar del usuario</label></th>
            <td>
                <?php
                $avatar_id = get_user_meta($user->ID, 'avatar_del_usuario', true);
                $avatar_url = $avatar_id ? wp_get_attachment_image_url($avatar_id, 'thumbnail') : '';
                ?>
                
                <div id="avatar-preview" style="margin-bottom: 10px;">
                    <?php if ($avatar_url): ?>
                        <img src="<?php echo esc_url($avatar_url); ?>" />
                    <?php endif; ?>
                </div>
                
                <input type="hidden" name="avatar_del_usuario" id="avatar_del_usuario" value="<?php echo esc_attr($avatar_id); ?>" />
                <button type="button" class="button" id="subir-avatar">Subir avatar</button>
                <button type="button" class="button" id="quitar-avatar" style="<?php echo !$avatar_id ? 'display:none;' : ''; ?>">Quitar avatar</button>
                
                <p class="description">Sube aquí tu avatar personalizado (300x300px recomendado)</p>
                
                <script>
                jQuery(document).ready(function($) {
                    var frame;
                    
                    $('#subir-avatar').on('click', function(e) {
                        e.preventDefault();
                        
                        // Si ya existe el frame, ábrelo
                        if (frame) {
                            frame.open();
                            return;
                        }
                        
                        // Crear nuevo frame de WordPress Media
                        frame = wp.media({
                            title: 'Seleccionar Avatar',
                            button: {
                                text: 'Usar este avatar'
                            },
                            multiple: false,
                            library: {
                                type: 'image'
                            }
                        });
                        
                        // Cuando se selecciona una imagen
                        frame.on('select', function() {
                            var attachment = frame.state().get('selection').first().toJSON();
                            
                            // Actualizar el campo oculto con el ID del attachment
                            $('#avatar_del_usuario').val(attachment.id);
                            
                            // Mostrar la imagen preview
                            if (attachment.sizes && attachment.sizes.thumbnail) {
                                $('#avatar-preview').html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 150px; height: auto;" />');
                            } else {
                                $('#avatar-preview').html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto;" />');
                            }
                            
                            // Mostrar el botón de quitar
                            $('#quitar-avatar').show();
                        });
                        
                        // Abrir el frame
                        frame.open();
                    });
                    
                    // Quitar avatar
                    $('#quitar-avatar').on('click', function(e) {
                        e.preventDefault();
                        $('#avatar_del_usuario').val('');
                        $('#avatar-preview').html('');
                        $(this).hide();
                    });
                });
                </script>
            </td>
        </tr>
    </table>
    <?php
}

// Guardar el campo meta del avatar
add_action('personal_options_update', 'guardar_campo_avatar_usuario');
add_action('edit_user_profile_update', 'guardar_campo_avatar_usuario');

function guardar_campo_avatar_usuario($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    if (isset($_POST['avatar_del_usuario'])) {
        update_user_meta($user_id, 'avatar_del_usuario', sanitize_text_field($_POST['avatar_del_usuario']));
    }
}

// ================================
// Usar el avatar del meta field en lugar del de Gravatar - CORREGIDO
// ================================
add_filter('get_avatar_url', function ($url, $id_or_email, $args) {
    $user = false;

    if (is_numeric($id_or_email)) {
        $user = get_user_by('id', (int) $id_or_email);
    } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
        $user = get_user_by('id', (int) $id_or_email->user_id);
    } elseif (is_string($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
    }

    if ($user) {
        $avatar_id = get_user_meta($user->ID, 'avatar_del_usuario', true);

        if ($avatar_id && is_numeric($avatar_id)) {
            // Usar el tamaño solicitado o 300x300 por defecto
            $size = isset($args['size']) ? [$args['size'], $args['size']] : [300, 300];
            $avatar_url = wp_get_attachment_image_url($avatar_id, $size);
            
            if ($avatar_url) {
                return $avatar_url;
            }
        }
    }

    return $url;
}, 10, 3);

// FILTRO CORREGIDO - MÁS ROBUSTO:
add_filter('get_avatar', function ($avatar, $id_or_email, $size, $default, $alt, $args) {
    // Solo modificar si tenemos un usuario válido
    $user = false;
    
    if (is_numeric($id_or_email)) {
        $user = get_user_by('id', (int) $id_or_email);
    } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
        $user = get_user_by('id', (int) $id_or_email->user_id);
    } elseif (is_string($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
    }
    
    // Si no hay usuario, dejar que WordPress maneje el avatar
    if (!$user) {
        return $avatar;
    }
    
    // Verificar si tiene avatar personalizado
    $avatar_id = get_user_meta($user->ID, 'avatar_del_usuario', true);
    
    if ($avatar_id && is_numeric($avatar_id)) {
        // Obtener la URL del avatar personalizado
        $avatar_url = get_avatar_url($id_or_email, $args);
        
        $class = isset($args['class']) ? esc_attr($args['class']) : 'avatar';
        $size_attr = "width='{$size}' height='{$size}'";

        return "<img alt='" . esc_attr($alt) . "' src='" . esc_url($avatar_url) . "' class='{$class}' {$size_attr} />";
    }
    
    // Si no tiene avatar personalizado, devolver el avatar normal
    return $avatar;
}, 10, 6);