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
    $edad_minima = get_post_meta($product_id, 'edad_minima', true); // Nueva línea añadida

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
            <h5>Características</h5>
        </div>

        <!-- CONTENIDO -->
        <div class="ps-contenido-momento">

            <!-- Distancia -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_distancia1]'); ?>
                    </span>
                    <span>Distancia total:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($distancia); ?> km
                </div>

            </div>

            <!-- Desnivel positivo -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_desnivel_pos]'); ?>
                    </span>
                    <span>Desnivel positivo:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($desnivel_positivo); ?> m
                </div>

            </div>

            <!-- Desnivel negativo -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_desnivel_neg]'); ?>
                    </span>
                    <span>Desnivel negativo:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($desnivel_negativo); ?> m
                </div>

            </div>

            <!-- Duración -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_duracion]'); ?>
                    </span>
                    <span>Duración:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($duracion); ?> h
                </div>

            </div>

            <!-- Dificultad -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_dificultad1]'); ?>
                    </span>
                    <span>Dificultad:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($dificultad); ?>
                </div>

            </div>

            <!-- Edad mínima - AÑADIDA DESPUÉS DE LA ÚLTIMA FILA -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_edad]'); ?>
                    </span>
                    <span>Edad mínima:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($edad_minima); ?> años
                </div>

            </div>

        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('seccion_fichatecnica', 'seccion_fichatecnica_shortcode');