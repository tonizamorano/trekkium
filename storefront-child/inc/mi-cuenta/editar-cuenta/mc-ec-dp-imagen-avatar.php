<?php
/**
 * Shortcode para la IMAGEN AVATAR DEL USUARIO
 * Con botones Editar / Eliminar superpuestos
 * Visible para todos los usuarios
 */
add_shortcode('mc_ec_dp_imagen_avatar', 'mc_ec_dp_imagen_avatar_shortcode');
function mc_ec_dp_imagen_avatar_shortcode() {

    if (!is_user_logged_in()) {
        return '';
    }

    $current_user = wp_get_current_user();

    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }

    // Obtener avatar actual (ID)
    $avatar_id  = get_user_meta($current_user->ID, 'avatar_del_usuario', true);
    $avatar_url = '';

    if (is_numeric($avatar_id)) {
        $avatar_url = wp_get_attachment_image_url($avatar_id, 'medium');
    }

    ob_start();
    ?>

    <div style="margin-bottom: 15px;" class="mc-form-row mc-form-row-wide mc-ec-dp-avatar-wrap">
        <label class="edit-form-titular">Imagen de perfil</label>

        <div class="mc-ec-dp-avatar-box"
             style="<?php echo $avatar_url ? 'border:none;' : ''; ?>">

            <?php if ($avatar_url): ?>
                <img src="<?php echo esc_url($avatar_url); ?>" alt="Avatar del usuario">
                <div class="mc-ec-dp-image-buttons">
                    <div class="mc-ec-dp-btn edit">Editar</div>
                    <div class="mc-ec-dp-btn delete">Eliminar</div>
                </div>
            <?php else: ?>
                <span>Haz clic para seleccionar tu imagen de perfil</span>
            <?php endif; ?>

        </div>

        <input type="hidden" name="avatar_del_usuario" id="avatar_del_usuario" value="<?php echo esc_attr($avatar_id); ?>">
        <input type="hidden" name="avatar_del_usuario_delete" id="avatar_del_usuario_delete" value="">
    </div>

    <style>
    .mc-ec-dp-avatar-box {
        position: relative;
        cursor: pointer;
        text-align: center;
        border: 2px dashed #ccc;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .mc-ec-dp-avatar-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .mc-ec-dp-image-buttons {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        gap: 8px;
        z-index: 10;
    }

    .mc-ec-dp-btn {
        background-color: var(--azul2-100);
        color: #fff;
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        user-select: none;
        text-align: center;
    }

    .mc-ec-dp-btn.delete {
        background-color: var(--naranja1-100);
    }
    </style>

    <script>
    (function() {

        function abrirUploaderAvatar(box) {

            var frame = wp.media({
                title: 'Seleccionar imagen de perfil',
                button: { text: 'Usar como imagen de perfil' },
                multiple: false,
                library: { type: 'image' }
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                mostrarAvatar(box, attachment);
            });

            frame.open();
        }

        function mostrarAvatar(box, attachment) {

            let img = box.querySelector('img');

            if (!img) {
                box.innerHTML = `
                    <img src="" alt="Avatar del usuario">
                    <div class="mc-ec-dp-image-buttons">
                        <div class="mc-ec-dp-btn edit">Editar</div>
                        <div class="mc-ec-dp-btn delete">Eliminar</div>
                    </div>
                `;
                img = box.querySelector('img');
            }

            img.src = attachment.url;

            box.style.border = 'none';
            document.getElementById('avatar_del_usuario').value = attachment.id;
            document.getElementById('avatar_del_usuario_delete').value = '';
        }

        document.addEventListener('click', function(e) {

            const box = e.target.closest('.mc-ec-dp-avatar-box');
            if (!box) return;

            // EDITAR
            if (e.target.classList.contains('edit')) {
                e.preventDefault();
                e.stopPropagation();
                abrirUploaderAvatar(box);
                return;
            }

            // ELIMINAR
            if (e.target.classList.contains('delete')) {
                e.preventDefault();
                e.stopPropagation();

                box.innerHTML = '<span>Haz clic para seleccionar tu imagen de perfil</span>';
                box.style.border = '2px dashed #ccc';

                document.getElementById('avatar_del_usuario').value = '';
                document.getElementById('avatar_del_usuario_delete').value = '1';
                return;
            }

            // CLICK EN EL BOX
            abrirUploaderAvatar(box);
        });

    })();
    </script>

    <?php
    return ob_get_clean();
}
