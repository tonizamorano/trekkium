<?php
// --- Mostrar campo "Estado de la actividad" (solo lectura) ---
add_action('add_meta_boxes', function () {
    add_meta_box(
        'estado_actividad_box',
        'Estado de la actividad',
        'mostrar_estado_actividad_meta_box',
        'product',
        'side',
        'high'
    );
});
function mostrar_estado_actividad_meta_box($post) {
    $estado = get_post_meta($post->ID, 'estado_actividad', true);
    if (!$estado) $estado = 'Sin definir';
    echo '<input type="text" value="' . esc_attr($estado) . '" readonly style="width:100%; background:#f8f8f8; border:1px solid #ccc;" />';
}


// --- Función principal: actualizar estado meta ---
function actualizar_estado_actividad($post_id, $post = null) {
    // evitar revisiones
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;

    if (!$post) $post = get_post($post_id);
    if (!$post || $post->post_type !== 'product') return;

    // bandera para evitar recursión
    static $doing = false;
    if ($doing) return;
    $doing = true;

    // Obtener el estado del producto
    $estado_publicacion = get_post_status($post_id);

    // ✅ Solo si está en wc-cancelled (Cancelado) => CANCELADA
    if ($estado_publicacion === 'wc-cancelled') {
        update_post_meta($post_id, 'estado_actividad', 'CANCELADA');
        $doing = false;
        return;
    }

    // --- Si no está cancelado, ejecuta el cálculo normal ---
    $stock        = (int) get_post_meta($post_id, '_stock', true);
    $low_stock    = (int) get_post_meta($post_id, '_low_stock_amount', true);
    $plazas_min   = (int) get_post_meta($post_id, 'plazas_minimas', true);
    if (!$low_stock) $low_stock = 0;

    // Calcular unidades vendidas (orders processing, on-hold, completed)
    $orders = wc_get_orders([
        'limit'  => -1,
        'status' => ['processing', 'on-hold', 'completed'],
        'type'   => 'shop_order',
        'return' => 'ids',
    ]);
    $vendidas = 0;
    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);
        if (! $order ) continue;
        foreach ($order->get_items() as $item) {
            if ($item->get_product_id() == $post_id) {
                $vendidas += $item->get_quantity();
            }
        }
    }

    // Evaluar condiciones
    if ($stock <= 0) {
        $estado = 'COMPLETA';
    } elseif ($stock <= $low_stock) {
        $estado = 'ÚLTIMAS PLAZAS';
    } elseif ($plazas_min > 0 && $vendidas >= $plazas_min && $stock > $low_stock) {
        $estado = 'PLAZAS DISPONIBLES';
    } elseif ($plazas_min > 0 && $vendidas < $plazas_min) {
        $estado = 'SIN CONFIRMAR';
    } else {
        $estado = 'Sin definir';
    }

    update_post_meta($post_id, 'estado_actividad', $estado);

    $doing = false;
}


// --- Hooks principales ---
add_action('save_post_product', 'actualizar_estado_actividad', 20, 2);
add_action('woocommerce_order_status_changed', function($order_id, $old_status, $new_status) {
    $order = wc_get_order($order_id);
    if (!$order) return;
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $post = get_post($product_id);
        if ($post && $post->post_type === 'product') {
            actualizar_estado_actividad($product_id, $post);
        }
    }
}, 20, 3);

// Detectar cambio de estado del producto
add_action('transition_post_status', function($new_status, $old_status, $post) {
    if ($post->post_type !== 'product') return;
    if ($new_status === $old_status) return;
    // Si entra o sale de wc-cancelled, recalcular
    if ($new_status === 'wc-cancelled' || $old_status === 'wc-cancelled') {
        actualizar_estado_actividad($post->ID, $post);
    }
}, 10, 3);

// Recalcular por cambios de stock
add_action('woocommerce_product_set_stock', 'recalcular_estado_actividad_por_stock');
add_action('woocommerce_reduce_order_stock', 'recalcular_estado_actividad_por_stock');
add_action('woocommerce_restore_order_stock', 'recalcular_estado_actividad_por_stock');
add_action('woocommerce_product_set_stock_status', 'recalcular_estado_actividad_por_stock');

function recalcular_estado_actividad_por_stock($product_or_id) {
    $product_id = is_object($product_or_id) ? $product_or_id->get_id() : $product_or_id;
    $post = get_post($product_id);
    if (!$post || $post->post_type !== 'product') return;
    actualizar_estado_actividad($product_id, $post);
}

// Mostrar en frontend
add_action('woocommerce_single_product_summary', 'mostrar_estado_actividad_en_producto', 20);
function mostrar_estado_actividad_en_producto() {
    global $post;
    $estado = get_post_meta($post->ID, 'estado_actividad', true);
    if ($estado) {
        echo '<div class="estado-actividad" style="margin-top:15px;">
                <strong>Estado de la actividad:</strong> ' . esc_html($estado) . '
              </div>';
    }
}
