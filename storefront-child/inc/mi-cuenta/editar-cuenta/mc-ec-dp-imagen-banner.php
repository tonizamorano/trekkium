<?php
/**
 * Shortcode para la IMAGEN DEL BANNER DEL GUÍA
 * Con botones Editar / Eliminar superpuestos
 */
add_shortcode('mc_ec_dp_imagen_banner', 'mc_ec_dp_imagen_banner_shortcode');
function mc_ec_dp_imagen_banner_shortcode() {

    $current_user = wp_get_current_user();

    // Solo para guías
    if (!in_array('guia', $current_user->roles)) {
        return '';
    }

    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }

    // Obtener imagen actual (ID o URL antigua)
    $banner_id  = get_user_meta($current_user->ID, 'imagen_banner', true);
    $banner_url = '';

    if (is_numeric($banner_id)) {
        $banner_url = wp_get_attachment_image_url($banner_id, 'full');
    } else {
        $legacy_url = get_user_meta($current_user->ID, 'imagen_banner_guia', true);
        if (filter_var($legacy_url, FILTER_VALIDATE_URL)) {
            $banner_url = $legacy_url;
        }
    }

    ob_start();
    ?>

    <div class="mc-form-row mc-form-row-wide mc-ec-dp-banner-wrap">
        <label class="edit-form-titular">Imagen del banner</label>

        <div class="mc-ec-dp-banner-box"
             style="<?php echo $banner_url ? 'border:none;' : ''; ?>">

            <?php if ($banner_url): ?>
                <img src="<?php echo esc_url($banner_url); ?>" alt="Banner del guía">
                <div class="mc-ec-dp-image-buttons">
                    <div class="mc-ec-dp-btn edit">Editar</div>
                    <div class="mc-ec-dp-btn delete">Eliminar</div>
                </div>
            <?php else: ?>
                <span>Haz clic para seleccionar la imagen del banner</span>
            <?php endif; ?>

        </div>

        <input type="hidden" name="imagen_banner" id="imagen_banner" value="<?php echo esc_attr($banner_id); ?>">
        <input type="hidden" name="imagen_banner_delete" id="imagen_banner_delete" value="">
    </div>

    <style>
    .mc-ec-dp-banner-box {
        position: relative;
        cursor: pointer;
        text-align: center;
        border: 2px dashed #ccc;
        aspect-ratio: 16 / 7;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .mc-ec-dp-banner-box img {
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
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        user-select: none;
    }

    .mc-ec-dp-btn.delete {
        background-color: var(--naranja1-100);
    }
    </style>

    <script>
    (function() {

        function abrirUploaderBanner(box) {

            var frame = wp.media({
                title: 'Seleccionar imagen del banner',
                button: { text: 'Usar como banner' },
                multiple: false,
                library: { type: 'image' }
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                mostrarBanner(box, attachment);
            });

            frame.open();
        }

        function mostrarBanner(box, attachment) {

            let img = box.querySelector('img');

            if (!img) {
                box.innerHTML = `
                    <img src="" alt="Banner del guía">
                    <div class="mc-ec-dp-image-buttons">
                        <div class="mc-ec-dp-btn edit">Editar</div>
                        <div class="mc-ec-dp-btn delete">Eliminar</div>
                    </div>
                `;
                img = box.querySelector('img');
            }

            img.src = attachment.url;

            box.style.border = 'none';
            document.getElementById('imagen_banner').value = attachment.id;
            document.getElementById('imagen_banner_delete').value = '';
        }

        document.addEventListener('click', function(e) {

            const box = e.target.closest('.mc-ec-dp-banner-box');
            if (!box) return;

            // EDITAR
            if (e.target.classList.contains('edit')) {
                e.preventDefault();
                e.stopPropagation();
                abrirUploaderBanner(box);
                return;
            }

            // ELIMINAR
            if (e.target.classList.contains('delete')) {
                e.preventDefault();
                e.stopPropagation();

                box.innerHTML = '<span>Haz clic para seleccionar la imagen del banner</span>';
                box.style.border = '2px dashed #ccc';

                document.getElementById('imagen_banner').value = '';
                document.getElementById('imagen_banner_delete').value = '1';
                return;
            }

            // CLICK EN EL BOX (solo si no hay botones)
            abrirUploaderBanner(box);
        });

    })();
    </script>


    <?php
    return ob_get_clean();
}
