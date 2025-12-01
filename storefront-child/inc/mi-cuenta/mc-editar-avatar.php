<?php
/*
Plugin Name: Editor de Avatar de Usuario (Meta WP)
Description: Permite a los usuarios cambiar o eliminar su avatar desde su perfil usando user meta.
Version: 1.5.2
Author: Toni (Trekkium)
*/

// === Shortcode ===
add_shortcode('editar-avatar-usuario', function () {
    if (!is_user_logged_in()) return '';

    $user_id = get_current_user_id();
    $avatar_id = get_user_meta($user_id, 'avatar_del_usuario', true);

    // Obtener URL del avatar - FIX PRINCIPAL
    if ($avatar_id && is_numeric($avatar_id)) {
        $current_avatar_url = wp_get_attachment_image_url($avatar_id, 'thumbnail');
    } else {
        // Imagen por defecto
        $current_avatar_url = 'https://trekkium.com/wp-content/uploads/2025/11/icon_user.png';
    }

    // Verificar si estamos en la página específica de edición de cuenta
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $is_edit_account_page = (strpos($current_url, 'https://trekkium.com/mi-cuenta/editar-cuenta/') !== false);

    ob_start();
    ?>
    <div id="avatar-editor">
        <img id="user-avatar-preview" src="<?php echo esc_url($current_avatar_url); ?>" alt="Avatar del usuario"/>
    <?php if ($is_edit_account_page): ?>
    <div class="avatar-buttons">
        <input type="file" id="avatar-file-input" accept="image/*" style="display: none;" />
        <button type="button" id="avatar-change-btn" title="Cambiar avatar">
            <svg viewBox="0 0 512 512" class="avatar-buttons-svg" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M290.74 93.24l128.02 128.02-277.99 277.99-114.14 12.6C11.35 513.54-1.56 500.62.14 485.34l12.7-114.22 277.9-277.88zm207.2-19.06l-60.11-60.11c-18.75-18.75-49.16-18.75-67.91 0l-56.55 56.55 128.02 128.02 56.55-56.55c18.75-18.76 18.75-49.16 0-67.91z"></path>
            </svg>
        </button>
        <button type="button" id="avatar-delete-btn" title="Eliminar avatar">
            <svg viewBox="0 0 8 8" class="avatar-buttons-svg" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 0c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm-1.5 1.78l1.5 1.5 1.5-1.5.72.72-1.5 1.5 1.5 1.5-.72.72-1.5-1.5-1.5 1.5-.72-.72 1.5-1.5-1.5-1.5.72-.72z"></path>
            </svg>
        </button>
    </div>
    <?php endif; ?>
</div>

<?php
    return ob_get_clean();
});// === AJAX: subir avatar usando Media Library ===
add_action('wp_ajax_subir_avatar_usuario', function () {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Usuario no autenticado']);
    }

    // Verificar nonce - más tolerante
    $nonce = isset($_POST['security']) ? sanitize_text_field($_POST['security']) : '';
    if (!$nonce || !wp_verify_nonce($nonce, 'subir_avatar_nonce')) {
        wp_send_json_error(['message' => 'Error de seguridad: nonce inválido', 'debug' => $nonce ? 'nonce_mismatch' : 'nonce_empty']);
    }

    if (!isset($_FILES['avatar_file']) || $_FILES['avatar_file']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(['message' => 'No se ha subido un archivo válido']);
    }

    $file = $_FILES['avatar_file'];

    // Validaciones básicas
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        wp_send_json_error(['message' => 'Tipo de archivo no permitido. Solo JPEG, PNG o GIF']);
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        wp_send_json_error(['message' => 'El archivo es demasiado grande. Máximo 2MB']);
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    // Subir archivo a la librería de medios
    $overrides = ['test_form' => false];
    $movefile = wp_handle_upload($file, $overrides);

    if (!$movefile || isset($movefile['error'])) {
        wp_send_json_error(['message' => 'Error al subir archivo: ' . ($movefile['error'] ?? '')]);
    }

    // Crear attachment
    $wp_filetype = wp_check_filetype($movefile['file'], null);
    $attachment = [
        'guid'           => $movefile['url'],
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => sanitize_file_name($file['name']),
        'post_content'   => '',
        'post_status'    => 'inherit'
    ];

    $attach_id = wp_insert_attachment($attachment, $movefile['file']);
    $attach_data = wp_generate_attachment_metadata($attach_id, $movefile['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);

    // Guardar ID del attachment en user meta - FIX: Guardamos el ID, no la URL
    $current_user_id = get_current_user_id();
    $old_attach_id = get_user_meta($current_user_id, 'avatar_del_usuario', true);

    // Eliminar el avatar anterior si existe
    if ($old_attach_id && $old_attach_id != $attach_id && is_numeric($old_attach_id)) {
        wp_delete_attachment($old_attach_id, true);
    }

    // Guardar el ID del attachment
    update_user_meta($current_user_id, 'avatar_del_usuario', $attach_id);

    // Obtener URL para la respuesta
    $avatar_url = wp_get_attachment_image_url($attach_id, 'thumbnail');

    wp_send_json_success(['url' => $avatar_url]);
});

// === AJAX: eliminar avatar ===
add_action('wp_ajax_eliminar_avatar_usuario', function () {
    // Verificar que el usuario esté logueado
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Usuario no autenticado']);
    }

    // Verificar nonce
    if (!wp_verify_nonce($_POST['security'], 'eliminar_avatar_nonce')) {
        wp_send_json_error(['message' => 'Error de seguridad: nonce inválido']);
    }

    $current_user_id = get_current_user_id();

    // Eliminar attachment si existe - FIX: Buscar por ID
    $avatar_id = get_user_meta($current_user_id, 'avatar_del_usuario', true);
    if ($avatar_id && is_numeric($avatar_id)) {
        wp_delete_attachment($avatar_id, true);
    }

    // Eliminar el meta del usuario
    delete_user_meta($current_user_id, 'avatar_del_usuario');

    $default_avatar_url = 'https://trekkium.com/wp-content/uploads/2025/11/icon_user.png';
    wp_send_json_success(['url' => $default_avatar_url]);
});

// === Limpiar avatares cuando se elimine un usuario ===
add_action('delete_user', function($user_id) {
    $avatar_id = get_user_meta($user_id, 'avatar_del_usuario', true);
    if ($avatar_id && is_numeric($avatar_id)) {
        wp_delete_attachment($avatar_id, true);
    }
});