<?php
// Shortcode: Resumen del pago
add_shortcode('mc_vr_resumen_pago', 'mc_vr_resumen_pago_callback');

function mc_vr_resumen_pago_callback($atts) {

    $atts = shortcode_atts([
        'order_id' => ''
    ], $atts);

    $order_id = intval($atts['order_id']);
    if (!$order_id) return '';

    $order = wc_get_order($order_id);
    if (!$order) return '';

    // Obtener el primer ítem del pedido (la actividad)
    $items = $order->get_items();
    $actividad = reset($items);
    if (!$actividad) return '';

    $product_id = $actividad->get_product_id();
    $parent_product_id = wp_get_post_parent_id($product_id);
    if ($parent_product_id) $product_id = $parent_product_id;

    // Datos necesarios
    $precio_unitario_con_iva = ($actividad->get_total() + $actividad->get_total_tax()) / $actividad->get_quantity();
    $precio_total_actividad = floatval(get_post_meta($product_id, 'precio', true));
    $plazas = intval($actividad->get_quantity());
    $total_formatted = $order->get_formatted_order_total();

    // Falta por pagar
    $falta_por_pagar = ($precio_total_actividad - $precio_unitario_con_iva) * $plazas;

    ob_start();
    ?>

    <div class="contenedor-detalles">

        <h1>Resumen del pago</h1>

        <div class="contenido-detalles">

            <div class="campo-detalle">
                <span class="etiqueta">Precio actividad</span>
                <span class="valor"><?php echo wc_price($precio_total_actividad); ?></span>
            </div>

            <div class="campo-detalle">
                <span class="etiqueta">Importe reserva</span>
                <span class="valor"><?php echo wc_price($precio_unitario_con_iva); ?></span>
            </div>

            <div class="campo-detalle">
                <span class="etiqueta">Plazas reservadas</span>
                <span class="valor">x <?php echo esc_html($plazas); ?></span>
            </div>

            <div class="linea-divisoria" style="height: 2px;background-color: var(--azul1-100);margin: 7px 0px;border-radius: 2px;"></div>

            <div class="campo-detalle">
                <span class="etiqueta">Total actividad</span>
                <span class="valor"><?php echo wc_price($precio_total_actividad * $plazas); ?></span>
            </div>

            <div class="campo-detalle">
                <span class="etiqueta">Total reserva</span>
                <span class="valor"><?php echo $total_formatted; ?></span>
            </div>

            <p class="campo-detalle">
                <span class="etiqueta">Importe pendiente</span>
                <span class="valor"><?php echo wc_price($falta_por_pagar); ?></span>
            </p>

            <div class="campo-detalle-texto">
                IVA incluido en todos los precios.<br>
                El día de la actividad deberás pagar al guía al contado el importe pendiente.
            </div>

            <div class="campo-detalle-texto">
                Tu pago se hará efectivo en tu tarjeta cuando finalice el plazo límite de cancelación gratuita.
            </div>

        </div>
    </div>

    <?php
    return ob_get_clean();
}
