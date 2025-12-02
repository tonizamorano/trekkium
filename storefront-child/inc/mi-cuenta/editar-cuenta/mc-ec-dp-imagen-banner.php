<?php
// Shortcode para la imagen del banner
add_shortcode('mc_ec_dp_imagen_banner', 'mc_ec_dp_imagen_banner_shortcode');
function mc_ec_dp_imagen_banner_shortcode() {
    $current_user = wp_get_current_user();
    
    // Solo mostrar para guías
    if (!in_array('guia', $current_user->roles)) {
        return '';
    }
    
    // Compatibilidad: algunos usuarios tienen 'imagen_banner_guia' (URL), otros el nuevo 'imagen_banner' (attach ID)
    $imagen_banner_meta = get_user_meta($current_user->ID, 'imagen_banner', true);
    if (empty($imagen_banner_meta)) {
        $imagen_banner_meta = get_user_meta($current_user->ID, 'imagen_banner_guia', true);
    }
    $imagen_banner_url = '';
    $imagen_banner_id = 0;
    if (is_numeric($imagen_banner_meta) && $imagen_banner_meta > 0) {
        $imagen_banner_id = intval($imagen_banner_meta);
        $imagen_banner_url = wp_get_attachment_image_url($imagen_banner_id, 'full');
    } elseif (filter_var($imagen_banner_meta, FILTER_VALIDATE_URL)) {
        $imagen_banner_url = $imagen_banner_meta;
    }
    
    ob_start(); ?>
    
    <div class="mc-form-row mc-form-row-wide mc-banner-container" style="margin-top:20px;">
        <label for="imagen_banner_file">Imagen del banner</label>
        <?php if ($imagen_banner_url): ?>
            <div class="mc-banner-preview" style="margin:10px 0;">
                <img src="<?php echo esc_url($imagen_banner_url); ?>" alt="Imagen banner" style="width:100%; max-width:100%; aspect-ratio:16/7; object-fit:cover; border-radius:0;" />
            </div>
        <?php endif; ?>

        <input type="file" name="imagen_banner_file" id="imagen_banner_file" accept="image/*" />
        <div style="margin-top:8px;">
            <label style="font-weight:normal; font-size:14px;">
                <input type="checkbox" name="imagen_banner_delete" value="1" /> Eliminar imagen actual
            </label>
        </div>
    </div>
    
    <?php return ob_get_clean();
}

// Procesar la imagen del banner en el guardado de la cuenta
add_action('woocommerce_save_account_details', 'mc_ec_dp_process_imagen_banner', 105, 1);
function mc_ec_dp_process_imagen_banner($user_id) {
    $user = get_userdata($user_id);
    $is_guia = in_array('guia', $user->roles);
    
    if (!$is_guia) {
        return;
    }
    
    // Verificar nonce
    if (isset($_POST['save-account-details-nonce']) && !wp_verify_nonce($_POST['save-account-details-nonce'], 'save_account_details')) {
        return;
    }
    
    // Eliminar imagen si se solicitó
    if (!empty($_POST['imagen_banner_delete'])) {
        delete_user_meta($user_id, 'imagen_banner');
        delete_user_meta($user_id, 'imagen_banner_guia');
    }

    // Procesar subida de archivo
    if (!empty($_FILES['imagen_banner_file']) && !empty($_FILES['imagen_banner_file']['tmp_name'])) {
        // Cargar utilidades necesarias
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $file = $_FILES['imagen_banner_file'];
        $overrides = ['test_form' => false];
        $uploaded = wp_handle_upload($file, $overrides);

        if (!empty($uploaded) && empty($uploaded['error'])) {
            $file_path = $uploaded['file'];
            $filetype = wp_check_filetype($file_path, null);
            $attachment = [
                'post_mime_type' => $filetype['type'],
                'post_title'     => sanitize_file_name($file['name']),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ];

            $attach_id = wp_insert_attachment($attachment, $file_path);
            if (!is_wp_error($attach_id)) {
                $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
                wp_update_attachment_metadata($attach_id, $attach_data);
                // Guardar ID y URL optimizada en metas para compatibilidad
                update_user_meta($user_id, 'imagen_banner', $attach_id);
                $attach_url = wp_get_attachment_image_url($attach_id, 'full');
                if ($attach_url) {
                    update_user_meta($user_id, 'imagen_banner_guia', esc_url($attach_url));
                }
            }
        }
    }
}
?>