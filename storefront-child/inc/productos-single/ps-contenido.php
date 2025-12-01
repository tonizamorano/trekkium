<?php
// Shortcode para mostrar contenido de la actividad con slider
function trekkium_seccion_contenido_shortcode() {
    global $product;
    if ( ! $product ) return '';

    ob_start();



    $product_id = $product->get_id();

    $provincias = wp_get_post_terms(get_the_ID(), 'provincia');
    $regiones   = wp_get_post_terms(get_the_ID(), 'region');

    $provincia_name  = (!empty($provincias) && !is_wp_error($provincias)) ? esc_html($provincias[0]->name) : '';
    $region_name     = (!empty($regiones) && !is_wp_error($regiones)) ? esc_html($regiones[0]->name) : '';


    // Imagen destacada
    $featured_img_url = get_the_post_thumbnail_url($product_id, 'large');

    // Galería de WooCommerce
    $gallery_ids = $product->get_gallery_image_ids();

    $imgs = [];

    // Primero la destacada
    if ($featured_img_url) {
        $imgs[] = $featured_img_url;
    }

    // Luego las de la galería
    if (!empty($gallery_ids)) {
        foreach ($gallery_ids as $img_id) {
            $img_url = wp_get_attachment_image_url($img_id, 'large');
            if ($img_url) {
                $imgs[] = $img_url;
            }
        }
    }

    if (empty($imgs)) {
        return '';
    }

    // Enqueue Swiper y CSS solo en páginas de producto
    if (is_product()) {
        wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css', [], '9.0');
        wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js', ['jquery'], '9.0', true);
        wp_enqueue_style('slider-actividad-css', get_stylesheet_directory_uri() . '/css/slider-actividad.css', [], '1.0');
    }
    ?>

    <div class="ps-contenedor-contenido">

        <!-- Slider -->
        <?php echo do_shortcode('[productos_single_slider]'); ?> 

        <!-- Información principal -->
        <div class="ps-info">

            <!-- Modalidad -->
            <div class="ps-modalidad">
                <div class="ps-modalidad-item">
                    <?php
                    $modalidad_terms = wp_get_post_terms( get_the_ID(), 'modalidad' );
                    if ( ! empty( $modalidad_terms ) && ! is_wp_error( $modalidad_terms ) ) {
                        echo esc_html( $modalidad_terms[0]->name );
                    }
                    ?>
                </div>
            </div>

            <!-- Título de la actividad -->
            <div class="ps-titulo">
                <h2><?php the_title(); ?></h2>
            </div>

            <!-- Ubicación -->
            <?php 
            $espacio_natural = get_post_meta(get_the_ID(), 'espacio_natural', true);
            $provincias = wp_get_post_terms(get_the_ID(), 'provincia');
            $regiones   = wp_get_post_terms(get_the_ID(), 'region');

            $provincia_name  = (!empty($provincias) && !is_wp_error($provincias)) ? esc_html($provincias[0]->name) : '';
            $region_name     = (!empty($regiones) && !is_wp_error($regiones)) ? esc_html($regiones[0]->name) : '';

            if ($espacio_natural || $provincia_name || $region_name) : ?>
                <div class="ps-ubicacion">
                    <?php if ($espacio_natural) : ?>
                        <?php echo esc_html($espacio_natural); ?>
                    <?php endif; ?>
                    
                    <?php 
                    $ubicacion = '';
                    if ($provincia_name && $region_name) {
                        $ubicacion = $provincia_name . ', ' . $region_name;
                    } elseif ($provincia_name) {
                        $ubicacion = $provincia_name;
                    } elseif ($region_name) {
                        $ubicacion = $region_name;
                    }

                    if ($ubicacion) {
                        echo ' (' . $ubicacion . ')';
                    }
                    ?>
                </div>
            <?php endif; ?>


             <!-- Descripción del producto -->
            <div class="ps-descripcion">
                <?php
                $descripcion = $product->get_description();
                echo apply_filters( 'the_content', $descripcion );
                ?>
            </div>

        </div>       

       

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.slider-actividad-container', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            }
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('seccion_contenido', 'trekkium_seccion_contenido_shortcode');
