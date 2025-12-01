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

                    <svg viewBox="0 0 448 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M448 384c-28.02 0-31.26-32-74.5-32-43.43 0-46.825 32-74.75 32-27.695 0-31.454-32-74.75-32-42.842 0-47.218 32-74.5 32-28.148 0-31.202-32-74.75-32-43.547 0-46.653 32-74.75 32v-80c0-26.5 21.5-48 48-48h16V112h64v144h64V112h64v144h64V112h64v144h16c26.5 0 48 21.5 48 48v80zm0 128H0v-96c43.356 0 46.767-32 74.75-32 27.951 0 31.253 32 74.75 32 42.843 0 47.217-32 74.5-32 28.148 0 31.201 32 74.75 32 43.357 0 46.767-32 74.75-32 27.488 0 31.252 32 74.5 32v96zM96 96c-17.75 0-32-14.25-32-32 0-31 32-23 32-64 12 0 32 29.5 32 56s-14.25 40-32 40zm128 0c-17.75 0-32-14.25-32-32 0-31 32-23 32-64 12 0 32 29.5 32 56s-14.25 40-32 40zm128 0c-17.75 0-32-14.25-32-32 0-31 32-23 32-64 12 0 32 29.5 32 56s-14.25 40-32 40z"></path></svg>
                    <span>Edad mínima:</span>
                </div>

                <div class="ps-fila-col2">
                    <?= esc_html($edad_minima) ?> años
                </div>

            </div>

            <!-- Grupo máximo -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <svg viewBox="0 0 640 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z"></path></svg>
                    <span>Grupo máximo:</span>
                </div>

                <div class="ps-fila-col2">
                    <?= esc_html($plazas_totales) ?>
                </div>

            </div>

            <!-- Grupo mínimo -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <svg viewBox="0 0 640 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M192 256c61.9 0 112-50.1 112-112S253.9 32 192 32 80 82.1 80 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C51.6 288 0 339.6 0 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zM480 256c53 0 96-43 96-96s-43-96-96-96-96 43-96 96 43 96 96 96zm48 32h-3.8c-13.9 4.8-28.6 8-44.2 8s-30.3-3.2-44.2-8H432c-20.4 0-39.2 5.9-55.7 15.4 24.4 26.3 39.7 61.2 39.7 99.8v38.4c0 2.2-.5 4.3-.6 6.4H592c26.5 0 48-21.5 48-48 0-61.9-50.1-112-112-112z"></path></svg>
                    <span>Grupo mínimo:</span>
                </div>

                <div class="ps-fila-col2">
                    <?= esc_html($plazas_minimas) ?>
                </div>

            </div>

            <!-- Plazas reservadas -->
            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <svg viewBox="0 0 640 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4zm323-128.4l-27.8-28.1c-4.6-4.7-12.1-4.7-16.8-.1l-104.8 104-45.5-45.8c-4.6-4.7-12.1-4.7-16.8-.1l-28.1 27.9c-4.7 4.6-4.7 12.1-.1 16.8l81.7 82.3c4.6 4.7 12.1 4.7 16.8.1l141.3-140.2c4.6-4.7 4.7-12.2.1-16.8z"></path></svg>
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