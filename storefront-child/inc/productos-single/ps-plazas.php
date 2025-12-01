<?php
/*
Plugin Name: Sección Plazas y Disponibilidad
Description: Muestra la sección de plazas y disponibilidad de un producto WooCommerce mediante shortcode.
Version: 1.1
Author: Tu Nombre
*/

// Función para generar la sección plazas y disponibilidad
function seccion_plazas_shortcode() {
    if (!is_singular('product')) return '';

    global $product;
    $product_id = $product->get_id();

    // Obtener campos meta
    $edad_minima = get_post_meta($product_id, 'edad_minima', true);
    $plazas_totales = get_post_meta($product_id, 'plazas_totales', true);
    $plazas_minimas = get_post_meta($product_id, 'plazas_minimas', true);
    $stock = $product->get_stock_quantity();
    $estado_actividad = get_post_meta($product_id, 'estado_actividad', true);

    // Calcular plazas reservadas
    $plazas_reservadas = $plazas_totales - $stock;

    // Determinar mensaje según estado_actividad
    $mensaje_estado = '';
    switch ($estado_actividad) {
        case 'SIN CONFIRMAR':
            $mensaje_estado = 'Aún no se ha completado el grupo mínimo requerido para garantizar la salida.';
            break;
        case 'PLAZAS DISPONIBLES':
        case 'ÚLTIMAS PLAZAS':
            $mensaje_estado = 'El grupo mínimo se ha completado y la salida está garantizada.';
            break;
        case 'COMPLETA':
            $mensaje_estado = 'Esta actividad está completa, la salida está garantizada.';
            break;
        case 'CANCELADA':
            $mensaje_estado = 'Esta actividad ha sido cancelada.';
            break;
        default:
            $mensaje_estado = 'Estado no definido.';
            break;
    }

    ob_start(); ?>

    <div class="ps-contenedor">

        <div class="ps-titular">
            <h5>Plazas y disponibilidad</h5>
        </div>

        <div class="ps-contenido">

            <!-- Edad mínima -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">

                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_edad]'); ?>
                    </span>
                    <span>Edad mínima:</span>
                </div>

                <div class="ps-fila-col2">
                    <?= esc_html($edad_minima) ?> años
                </div>

            </div>

            <!-- Grupo máximo -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_grupo_max]'); ?>
                    </span>
                    <span>Grupo máximo:</span>
                </div>

                <div class="ps-fila-col2">
                    <?= esc_html($plazas_totales) ?>
                </div>

            </div>

            <!-- Grupo mínimo -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_grupo_min]'); ?>
                    </span>
                    <span>Grupo mínimo:</span>
                </div>

                <div class="ps-fila-col2">
                    <?= esc_html($plazas_minimas) ?>
                </div>

            </div>

            <!-- Plazas reservadas -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_plazas_res]'); ?>
                    </span>
                    <span>Plazas reservadas:</span>
                </div>

                <div class="ps-fila-col2">
                    <?= esc_html($plazas_reservadas) ?>
                </div>

            </div>

            <!-- Estado de la actividad -->
            <div class="ps-estado-actividad">
                <?= esc_html($mensaje_estado) ?>
            </div>

        </div>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('seccion_plazas', 'seccion_plazas_shortcode');