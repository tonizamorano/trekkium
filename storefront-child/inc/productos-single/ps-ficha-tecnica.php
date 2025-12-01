<?php
/*
Plugin Name: Sección Ficha Técnica Producto
Description: Muestra la sección de ficha técnica de un producto WooCommerce mediante shortcode.
Version: 1.2
Author: Tu Nombre
*/

// Función para formatear números
function format_num($num, $decimals = 1) {
    if (floor($num) == $num) { // es entero
        return number_format($num, 0, ',', '.');
    } else {
        return number_format($num, $decimals, ',', '.');
    }
}

// Shortcode principal
function seccion_fichatecnica_shortcode() {
    if (!is_singular('product')) return '';

    global $product;
    $product_id = $product->get_id();

    // Obtener campos meta
    $distancia = get_post_meta($product_id, 'distancia', true);
    $desnivel_positivo = get_post_meta($product_id, 'desnivel_positivo', true);
    $desnivel_negativo = get_post_meta($product_id, 'desnivel_negativo', true);
    $duracion = get_post_meta($product_id, 'duracion', true);

    // Obtener taxonomía dificultad
    $dificultad_terms = get_the_terms($product_id, 'dificultad');
    $dificultad = $dificultad_terms ? $dificultad_terms[0]->name : '';

    // Aplicar formateo
    $distancia = $distancia ? format_num((float)$distancia, 1) : '';
    $desnivel_positivo = $desnivel_positivo ? format_num((float)$desnivel_positivo, 0) : '';
    $desnivel_negativo = $desnivel_negativo ? format_num((float)$desnivel_negativo, 0) : '';
    $duracion = $duracion ? format_num((float)$duracion, 1) : '';

    ob_start(); ?>

    <div class="ps-contenedor">

        <!-- TITULAR -->
        <div class="ps-titular">
            <h5>Características técnicas</h5>
        </div>

        <!-- CONTENIDO -->
        <div class="ps-contenido-momento">


            <!-- Distancia -->

            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <svg viewBox="0 0 512 512" fill="currentColor"><path d="M256 8c137 0 248 111 248 248S393 504 256 504 8 393 8 256 119 8 256 8zm-28.9 143.6l75.5 72.4H120c-13.3 0-24 10.7-24 24v16c0 13.3 10.7 24 24 24h182.6l-75.5 72.4c-9.7 9.3-9.9 24.8-.4 34.3l11 10.9c9.4 9.4 24.6 9.4 33.9 0L404.3 273c9.4-9.4 9.4-24.6 0-33.9L271.6 106.3c-9.4-9.4-24.6-9.4-33.9 0l-11 10.9c-9.5 9.6-9.3 25.1.4 34.4z"/></svg>
                    <span>Distancia total:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($distancia); ?> km
                </div>

            </div>


            <!-- Desnivel positivo -->

            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <svg viewBox="0 0 512 512" fill="currentColor"><path d="M8 256C8 119 119 8 256 8s248 111 248 248-111 248-248 248S8 393 8 256zm143.6 28.9l72.4-75.5V392c0 13.3 10.7 24 24 24h16c13.3 0 24-10.7 24-24V209.4l72.4 75.5c9.3 9.7 24.8 9.9 34.3.4l10.9-11c9.4-9.4 9.4-24.6 0-33.9L273 107.7c-9.4-9.4-24.6-9.4-33.9 0L106.3 240.4c-9.4 9.4-9.4 24.6 0 33.9l10.9 11c9.6 9.5 25.1 9.3 34.4-.4z"/></svg>
                    <span>Desnivel positivo:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($desnivel_positivo); ?> m
                </div>

            </div>


            <!-- Desnivel negativo -->

            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <svg viewBox="0 0 512 512" fill="currentColor"><path d="M504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-143.6-28.9L288 302.6V120c0-13.3-10.7-24-24-24h-16c-13.3 0-24 10.7-24 24v182.6l-72.4-75.5c-9.3-9.7-24.8-9.9-34.3-.4l-10.9 11c-9.4 9.4-9.4 24.6 0 33.9L239 404.3c9.4 9.4 24.6 9.4 33.9 0l132.7-132.7c9.4-9.4 9.4-24.6 0-33.9l-10.9-11c-9.5-9.5-25-9.3-34.3.4z"/></svg>
                    <span>Desnivel negativo:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($desnivel_negativo); ?> m
                </div>

            </div>

            <!-- Duración -->

            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">

                    <?php 
                    $svg_path = get_stylesheet_directory() . '/svg/duracion1.svg'; 
                    if (file_exists($svg_path)) {
                        include $svg_path;
                    } 
                    
                    ?>


                    
                    <span>Duración:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($duracion); ?> h
                </div>

            </div>

            <!-- Dificultad -->

            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <svg viewBox="0 0 504 512" fill="currentColor"><path d="M456 128c26.5 0 48-21 48-47 0-20-28.5-60.4-41.6-77.8-3.2-4.3-9.6-4.3-12.8 0C436.5 20.6 408 61 408 81c0 26 21.5 47 48 47zm0 32c-44.1 0-80-35.4-80-79 0-4.4.3-14.2 8.1-32.2C345 23.1 298.3 8 248 8 111 8 0 119 0 256s111 248 248 248 248-111 248-248c0-35.1-7.4-68.4-20.5-98.6-6.3 1.5-12.7 2.6-19.5 2.6zm-128-8c23.8 0 52.7 29.3 56 71.4.7 8.6-10.8 12-14.9 4.5l-9.5-17c-7.7-13.7-19.2-21.6-31.5-21.6s-23.8 7.9-31.5 21.6l-9.5 17c-4.1 7.4-15.6 4-14.9-4.5 3.1-42.1 32-71.4 55.8-71.4zm-160 0c23.8 0 52.7 29.3 56 71.4.7 8.6-10.8 12-14.9 4.5l-9.5-17c-7.7-13.7-19.2-21.6-31.5-21.6s-23.8 7.9-31.5 21.6l-9.5 17c-4.2 7.4-15.6 4-14.9-4.5 3.1-42.1 32-71.4 55.8-71.4zm80 280c-60.6 0-134.5-38.3-143.8-93.3-2-11.8 9.3-21.6 20.7-17.9C155.1 330.5 200 336 248 336s92.9-5.5 123.1-15.2c11.5-3.7 22.6 6.2 20.7 17.9-9.3 55-83.2 93.3-143.8 93.3z"/></svg>
                    <span>Dificultad:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($dificultad); ?>
                </div>

            </div>

        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('seccion_fichatecnica', 'seccion_fichatecnica_shortcode');
