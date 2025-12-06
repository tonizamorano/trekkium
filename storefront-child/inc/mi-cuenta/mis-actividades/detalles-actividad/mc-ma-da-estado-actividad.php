<?php

// Shortcode: Estado de la actividad para detalles del guía
add_shortcode('mc_ma_da_estado_actividad', function ($atts) {

    $atts = shortcode_atts([
        'id' => 0
    ], $atts);

    $actividad_id = intval($atts['id']);
    if (!$actividad_id) return '<p>Actividad no válida.</p>';

    $actividad = get_post($actividad_id);
    if (!$actividad || $actividad->post_type !== 'product') return '<p>Actividad no válida.</p>';

    // Metadatos
    $estado_actividad   = get_post_meta($actividad_id, 'estado_actividad', true);
    $mensaje_actividad  = get_post_meta($actividad_id, 'mensaje_actividad', true);
    $plazas_totales     = get_post_meta($actividad_id, 'plazas_totales', true);
    $plazas_minimas     = get_post_meta($actividad_id, 'plazas_minimas', true);

    $product_obj        = wc_get_product($actividad_id);
    $plazas_disponibles = $product_obj ? $product_obj->get_stock_quantity() : '—';

    // Plazas reservadas (unidades vendidas)
    $plazas_reservadas = 0;

    $orders = wc_get_orders([
        'limit'  => -1,
        'status' => ['wc-processing', 'wc-completed'],
        'return' => 'ids'
    ]);

    foreach ($orders as $oid) {
        $o = wc_get_order($oid);
        foreach ($o->get_items() as $item) {
            if ($item->get_product_id() == $actividad_id || $item->get_variation_id() == $actividad_id) {
                $plazas_reservadas += $item->get_quantity();
            }
        }
    }

    ob_start();
    ?>

    <div class="mc-ma-da-contenedor">

        <div class="mc-ma-da-titular">
            <h2>Estado de la actividad</h2>
        </div>

        <div class="mc-ma-da-contenido">

            <div class="mc-ma-da-fila-datos">
                <span class="etiqueta">Grupo máximo</span>
                <span class="valor"><?php echo esc_html($plazas_totales ?: '—'); ?></span>
            </div>

            <div class="mc-ma-da-fila-datos">
                <span class="etiqueta">Grupo mínimo</span>
                <span class="valor"><?php echo esc_html($plazas_minimas ?: '—'); ?></span>
            </div>

            <div class="mc-ma-da-fila-datos">
                <span class="etiqueta">Plazas disponibles</span>
                <span class="valor"><?php echo esc_html($plazas_disponibles !== '' ? $plazas_disponibles : '—'); ?></span>
            </div>

            <div class="mc-ma-da-fila-datos">
                <span class="etiqueta">Plazas reservadas</span>
                <span class="valor"><?php echo esc_html($plazas_reservadas); ?></span>
            </div>

            <div class="mc-ma-da-estado-actividad">
                <?php echo esc_html($estado_actividad ?: 'Sin definir'); ?>
            </div>

            <?php if (!empty($mensaje_actividad)) : ?>
                <div class="mc-ma-da-fila-datos-texto">
                    <?php echo esc_html($mensaje_actividad); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php
    return ob_get_clean();
});
