<?php
/*
Plugin Name: Sección Información Adicional Producto
Description: Muestra la sección de información adicional de un producto solo si hay contenido.
Version: 1.1
Author: Toni
*/

function seccion_informacion_adicional_shortcode() {
    if (!is_singular('product')) return;

    $informacion_adicional = get_post_meta(get_the_ID(), 'informacion_adicional', true);

    // Si está vacío, no mostramos nada
    if (empty($informacion_adicional)) return;

    ob_start();
    ?>

    <div class="ps-contenedor">

        <div class="ps-titular">
            <h5>Información adicional</h5>
        </div>

        <div class="ps-contenido">
            <?php echo wp_kses_post($informacion_adicional); ?>
        </div>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('seccion_informacion_adicional', 'seccion_informacion_adicional_shortcode');
