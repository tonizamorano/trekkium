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
    $estado = get_post_meta($post->ID, 'estado_actividad', true) ?: 'Sin definir';
    $mensaje = get_post_meta($post->ID, 'mensaje_actividad', true) ?: '';
    echo '<input type="text" value="' . esc_attr($estado) . '" readonly style="width:100%; background:#f8f8f8; border:1px solid #ccc; margin-bottom:5px;" />';
    if ($mensaje) {
        echo '<textarea readonly style="width:100%; background:#f8f8f8; border:1px solid #ccc;">' . esc_textarea($mensaje) . '</textarea>';
    }
}

// --- Función principal: actualizar estado y mensaje ---
function actualizar_estado_actividad($post_id, $post = null) {
    // evitar revisiones y autosaves
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;

    if (!$post) $post = get_post($post_id);
    if (!$post || $post->post_type !== 'product') return;

    static $doing = false;
    if ($doing) return;
    $doing = true;

    $estado_publicacion = get_post_status($post_id);
    $stock_total = (int) get_post_meta($post_id, '_stock', true);
    $low_stock = (int) get_post_meta($post_id, '_low_stock_amount', true);
    $plazas_min = (int) get_post_meta($post_id, 'plazas_minimas', true);
    if (!$low_stock) $low_stock = 0;

    $estado = 'Sin definir';
    $mensaje = '';

    // --- Estados según tu lógica ---
    switch ($estado_publicacion) {
        case 'wc-cancelled':
            $estado = 'Cancelada';
            $mensaje = 'Esta actividad ha sido cancelada.';
            break;

        case 'wc-completed': // WooCommerce no tiene 'Finalizado' nativo, asumo wc-completed
        case 'finalizado': // por si usas custom status
            $estado = 'Finalizada';
            $mensaje = 'Esta actividad ha finalizado.';
            break;

        case 'publish':
            if ($stock_total === 0) {
                $estado = 'Completa';
                $mensaje = 'No quedan plazas disponibles, salida confirmada.';
            } elseif ($stock_total <= $low_stock) {
                $estado = 'Últimas plazas';
                $mensaje = 'Quedan pocas plazas, salida confirmada.';
            } elseif ($stock_total <= ($stock_total - $plazas_min)) {
                $estado = 'Confirmada';
                $mensaje = 'Grupo mínimo suficiente, salida confirmada.';
            } elseif ($stock_total > ($stock_total - $plazas_min)) {
                $estado = 'Sin confirmar';
                $mensaje = 'Grupo mínimo insuficiente, salida sin confirmar.';
            }
            break;

        default:
            $estado = 'Sin definir';
            $mensaje = '';
            break;
    }

    update_post_meta($post_id, 'estado_actividad', $estado);
    update_post_meta($post_id, 'mensaje_actividad', $mensaje);

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

add_action('transition_post_status', function($new_status, $old_status, $post) {
    if ($post->post_type !== 'product') return;
    if ($new_status === $old_status) return;
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

// --- Mostrar en frontend ---
add_action('woocommerce_single_product_summary', 'mostrar_estado_actividad_en_producto', 20);
function mostrar_estado_actividad_en_producto() {
    global $post;
    $estado = get_post_meta($post->ID, 'estado_actividad', true);
    $mensaje = get_post_meta($post->ID, 'mensaje_actividad', true);
    if ($estado) {
        echo '<div class="estado-actividad" style="margin-top:15px;">
                <strong>Estado de la actividad:</strong> ' . esc_html($estado) . '<br/>
                <span>' . esc_html($mensaje) . '</span>
              </div>';
    }
}
