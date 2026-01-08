<?php
// Shortcode: Detalles de la actividad para página de producto
add_shortcode('ps_estado_actividad', function ($atts) {

    global $post;

    // Si estamos en una página de producto, usar el ID del producto actual
    if (is_product() && isset($post->ID)) {
        $product_id = $post->ID;
    } else {
        // Si no estamos en una página de producto, verificar si se pasó un ID como atributo
        $atts = shortcode_atts([
            'product_id' => 0
        ], $atts);
        
        $product_id = intval($atts['product_id']);
        if (!$product_id) return '';
    }

    // Si es variación, pasar al producto padre
    $parent_product_id = wp_get_post_parent_id($product_id);
    if ($parent_product_id) {
        $product_id = $parent_product_id;
    }

    // Metadatos del producto
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

    <div class="ps-ea-contenedor">

        <div class="ps-ea-titular">
            <h2>Estado de la actividad</h2>
        </div>

        <div class="ps-ea-contenido">

            <div class="ps-ea-fila-datos">
                <span class="etiqueta">Grupo máximo</span>
                <span class="valor"><?php echo esc_html($plazas_totales ?: '—'); ?></span>
            </div>

            <div class="ps-ea-fila-datos">
                <span class="etiqueta">Grupo mínimo</span>
                <span class="valor"><?php echo esc_html($plazas_minimas ?: '—'); ?></span>
            </div>

            <div class="ps-ea-fila-datos">
                <span class="etiqueta">Plazas reservadas</span>
                <span class="valor"><?php echo esc_html($plazas_reservadas !== '' ? $plazas_reservadas : '—'); ?></span>
            </div>

            <!-- Estado -->
            <div class="ps-ea-estado-actividad">
                <?php echo esc_html($estado_actividad ?: 'Sin definir'); ?>
            </div>

            <!-- Mensaje -->
            <?php if (!empty($mensaje_actividad)) : ?>
                <div class="ps-ea-fila-datos-texto">
                    <?php echo esc_html($mensaje_actividad); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php
    return ob_get_clean();
});