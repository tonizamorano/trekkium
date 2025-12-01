<?php
function productos_single_slider_shortcode() {
    ob_start();

    global $product;

    // Verificar que estamos en una página de producto individual
    if (!is_a($product, 'WC_Product')) {
        return '';
    }

    // Obtener la imagen destacada y las imágenes de la galería
    $featured_image_id = $product->get_image_id();
    $gallery_image_ids = $product->get_gallery_image_ids();

    // Combinar todas las imágenes (destacada + galería)
    $all_image_ids = array();
    if ($featured_image_id) {
        $all_image_ids[] = $featured_image_id;
    }
    if (!empty($gallery_image_ids)) {
        $all_image_ids = array_merge($all_image_ids, $gallery_image_ids);
    }

    // Si no hay imágenes, retornar vacío
    if (empty($all_image_ids)) {
        return '';
    }

    $slider_id = 'ps-slider-' . esc_attr($product->get_id());
    ?>
    <div class="ps-slider-container">
        <div class="ps-slider-wrapper">
            <div class="ps-slider" id="<?php echo $slider_id; ?>">
                <?php foreach ($all_image_ids as $index => $image_id): ?>
                    <div class="ps-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo esc_attr($index); ?>">
                        <?php echo wp_get_attachment_image($image_id, 'large'); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($all_image_ids) > 1): ?>
                <button class="ps-slider-btn prev" aria-label="Anterior">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </button>

                <button class="ps-slider-btn next" aria-label="Siguiente">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                    </svg>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php

    return ob_get_clean();
}
add_shortcode('productos_single_slider', 'productos_single_slider_shortcode');
?>
