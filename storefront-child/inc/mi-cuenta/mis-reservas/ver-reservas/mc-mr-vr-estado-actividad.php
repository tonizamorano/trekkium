<?php
// Shortcode: Detalles de la actividad
add_shortcode('mc_vr_estado_actividad', function ($atts) {

    $atts = shortcode_atts([
        'order_id' => 0
    ], $atts);

    $order_id = intval($atts['order_id']);
    if (!$order_id) return '';

    $order = wc_get_order($order_id);
    if (!$order) return '';

    // Extraer primer ítem del pedido
    $items = $order->get_items();
    $actividad = reset($items);
    if (!$actividad) return '';

    $product_id = $actividad->get_product_id();

    // Si es variación, pasar al producto padre
    $parent_product_id = wp_get_post_parent_id($product_id);
    if ($parent_product_id) {
        $product_id = $parent_product_id;
    }

    // Metadatos
    $estado_actividad    = get_post_meta($product_id, 'estado_actividad', true);
    $mensaje_actividad   = get_post_meta($product_id, 'mensaje_actividad', true);
    $plazas_totales      = get_post_meta($product_id, 'plazas_totales', true);
    $plazas_minimas      = get_post_meta($product_id, 'plazas_minimas', true);
    $product_obj         = wc_get_product($product_id);
    $plazas_disponibles  = $product_obj ? $product_obj->get_stock_quantity() : '—';

    // Plazas reservadas (unidades vendidas)
    $plazas_reservadas = 0;

    $orders = wc_get_orders([
        'limit'        => -1,
        'status'       => ['wc-processing', 'wc-completed'],
        'return'       => 'ids'
    ]);

    foreach ($orders as $oid) {
        $o = wc_get_order($oid);
        foreach ($o->get_items() as $item) {
            if ($item->get_product_id() == $product_id || $item->get_variation_id() == $product_id) {
                $plazas_reservadas += $item->get_quantity();
            }
        }
    }

    ob_start();
    ?>

    <div class="mc-mr-vr-contenedor">

        <div class="mc-mr-vr-titular">
            <h2>Estado de la actividad</h2>
        </div>

        <div class="mc-mr-vr-contenido">

            <div class="mc-mr-vr-fila-datos">
                <span class="etiqueta">Grupo máximo</span>
                <span class="valor"><?php echo esc_html($plazas_totales ?: '—'); ?></span>
            </div>

            <div class="mc-mr-vr-fila-datos">
                <span class="etiqueta">Grupo mínimo</span>
                <span class="valor"><?php echo esc_html($plazas_minimas ?: '—'); ?></span>
            </div>

            <div class="mc-mr-vr-fila-datos">
                <span class="etiqueta">Plazas disponibles</span>
                <span class="valor"><?php echo esc_html($plazas_disponibles !== '' ? $plazas_disponibles : '—'); ?></span>
            </div>

            <div class="mc-mr-vr-fila-datos">
                <span class="etiqueta">Plazas reservadas</span>
                <span class="valor"><?php echo esc_html($plazas_reservadas); ?></span>
            </div>

            <!-- NUEVO BLOQUE: Estado -->
            <div class="mc-mr-vr-estado-actividad">
                <?php echo esc_html($estado_actividad ?: 'Sin definir'); ?>
            </div>

            <!-- NUEVO BLOQUE: Mensaje -->
            <?php if (!empty($mensaje_actividad)) : ?>
                <div class="mc-mr-vr-fila-datos-texto">
                    <?php echo esc_html($mensaje_actividad); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php
    return ob_get_clean();
});
