<?php
/**
 * Gestiona flujo de reserva con pago inmediato o diferido según proximidad a la actividad.
 */

// Hook para definir estado inicial y meta del pedido
add_action('woocommerce_checkout_create_order', function($order, $data) {
    $cart = WC()->cart->get_cart();
    if (empty($cart)) return;

    $item = reset($cart);
    $product_id = $item['product_id'];

    $actividad_ts = get_activity_timestamp($product_id);
    $ahora = current_time('timestamp');

    if ($actividad_ts && ($actividad_ts - $ahora > 24 * 3600)) {
        $order->set_status('on-hold');
        $order->update_meta_data('_requires_setup_intent', true);

        // Agrega un log
        wc_get_logger()->info("Estado del pedido establecido a: " . $order->get_status(), ['source' => 'stripe']);

    } else {
        $order->update_meta_data('_requires_immediate_payment', true);
    }
}, 10, 2);

// Mostrar mensaje en checkout (antes de la sección de pago)
add_action('woocommerce_checkout_before_payment', function() {

    console_log("before payment hook");

    $cart = WC()->cart->get_cart();
    if (empty($cart)) return;

    $item = reset($cart);
    $product_id = $item['product_id'];

    $actividad_ts = get_activity_timestamp($product_id);
    if (!$actividad_ts) return;

    $ahora = current_time('timestamp');

    if ($actividad_ts - $ahora > 24 * 3600) {
        $message = 'Se guardará un identificador de pago seguro en el pedido para poder procesar el cobro automático antes del inicio de la actividad. No almacenamos datos de tarjeta en nuestro sistema. Si el cobro falla se le notificará por email para que pueda completar el pago manualmente antes de la actividad.';
    } else {
        $message = 'Como queda poco para el inicio de la actividad, el pago se efectuará en este momento para confirmar su reserva.';
    }

    echo '<div class="woocommerce-info" style="margin-bottom: 1em;">' . esc_html($message) . '</div>';
});


// Guardar SetupIntent en pedidos "on-hold" (>24h)
add_action('woocommerce_checkout_order_processed', function($order_id, $posted_data, $order) {
    
    wc_get_logger()->info("Procesando order_processed para pedido $order_id", ['source' => 'stripe']);

    if ($order->get_meta('_requires_setup_intent')) {
        wc_get_logger()->info("Pedido $order_id requiere SetupIntent", ['source' => 'stripe']);

        $payment_intent = $order->get_meta('_stripe_setup_intent');

        if ($payment_intent) {
            wc_get_logger()->info("SetupIntent recibido: $payment_intent", ['source' => 'stripe']);
            $order->update_meta_data('setup_intent_id', $payment_intent);
            $order->save();
        } else {
            wc_get_logger()->warning("No se encontró SetupIntent para pedido $order_id", ['source' => 'stripe']);
        }
    }
}, 10, 3);

// Utilidad para mostrar logs en consola (solo para desarrollo)
function console_log($data, $label = '') {
    if (is_array($data) || is_object($data)) {
        $output = json_encode($data);
    } else {
        $output = json_encode((string) $data);
    }
    echo "<script>console.log('{$label}:', {$output});</script>";
}

// Depuración en consola (solo para checkout)
add_action('wp_footer', function () {
    if (!is_checkout()) return;

    $cart = WC()->cart->get_cart();
    if (empty($cart)) return;

    $item = reset($cart);
    $product_id = $item['product_id'];

    console_log(get_activity_timestamp($product_id), 'Timestamp actividad');
});

// Función para obtener timestamp de actividad desde un product_id
function get_activity_timestamp($product_id) {
    $fecha_raw = get_post_meta($product_id, 'fecha', true); // formato: yyyymmdd
    $hora_raw  = get_post_meta($product_id, 'hora', true);  // formato: HH:MM:SS

    if (!$fecha_raw || !$hora_raw) return false;

    $fecha_obj = DateTime::createFromFormat('Ymd', $fecha_raw);
    if (!$fecha_obj) return false;

    $fecha_str = $fecha_obj->format('Y-m-d');
    $datetime_str = $fecha_str . ' ' . $hora_raw;

    $timestamp = strtotime($datetime_str);
    return $timestamp ?: false;
}
