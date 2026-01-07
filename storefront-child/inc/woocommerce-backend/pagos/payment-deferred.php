<?php
if (!defined('ABSPATH')) exit;

// ------------------------
// Logging helper
// ------------------------
if (!function_exists('payment_deferred_file_log')) {
    function payment_deferred_file_log($message, $level='INFO'){
        $file = defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR . '/payment-deferred.log' : __DIR__ . '/payment-deferred.log';
        $line = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), $level, $message);
        @error_log($line, 3, $file);
    }
}

// ------------------------
// Registrar pedidos diferidos
// ------------------------
add_action('woocommerce_checkout_create_order', function ($order, $data) {

    if (is_numeric($order)) {
        $order = wc_get_order($order);
    }
    if (!$order || !WC()->cart) return;

    $cart = WC()->cart->get_cart();
    if (empty($cart)) return;

    $activity_ts = null;
    $tz = wp_timezone();

    foreach ($cart as $item) {
        $product_id = $item['product_id'] ?? 0;
        if (!$product_id) continue;

        $fecha = get_post_meta($product_id, 'fecha', true);
        $hora  = get_post_meta($product_id, 'hora', true);
        if (!$fecha || !$hora) continue;

        $dt = DateTime::createFromFormat('Y-m-d H:i', "$fecha $hora", $tz);
        if (!$dt) continue;

        $ts = $dt->getTimestamp();
        if ($activity_ts === null || $ts < $activity_ts) {
            $activity_ts = $ts;
        }
    }

    $now = current_time('timestamp');

    payment_deferred_file_log(
        "checkout_create_order: order_id={$order->get_id()} activity_ts={$activity_ts} now={$now}",
        'INFO'
    );

    if (function_exists('wc_get_logger')) {
        wc_get_logger()->info(
            "checkout_create_order: order_id={$order->get_id()} activity_ts={$activity_ts} now={$now}",
            ['source' => 'payment-deferred']
        );
    }

    if ($activity_ts && ($activity_ts - $now) > DAY_IN_SECONDS) {
        $order->update_meta_data('_deferred_payment', 'yes');
        $order->update_meta_data('_activity_start', $activity_ts);
        $order->set_status('pending');

        payment_deferred_file_log(
            "Order {$order->get_id()} marcado como DEFERRED (pending)",
            'INFO'
        );
    } else {
        $order->update_meta_data('_deferred_payment', 'no');
        if ($activity_ts) {
            $order->update_meta_data('_activity_start', $activity_ts);
        }

        payment_deferred_file_log(
            "Order {$order->get_id()} marcado como IMMEDIATE",
            'INFO'
        );
    }

    $order->save();

}, 10, 2);

// ------------------------
// Guardar método de pago Stripe
// ------------------------
add_action('woocommerce_checkout_update_order_meta', function($order_id, $data){
    if(!empty($_POST['stripe_payment_method'])){
        update_post_meta($order_id, '_stripe_payment_method', sanitize_text_field(wp_unslash($_POST['stripe_payment_method'])));
        payment_deferred_file_log("Saved stripe_payment_method for order {$order_id}", 'INFO');
    }
    if(!empty($_POST['stripe_token'])){
        update_post_meta($order_id, '_stripe_token', sanitize_text_field(wp_unslash($_POST['stripe_token'])));
        payment_deferred_file_log("Saved stripe_token for order {$order_id}", 'INFO');
    }
}, 10, 2);

// ------------------------
// Evitar cobro automático Stripe
// ------------------------
add_filter('wc_stripe_payment_intent_args', function ($args, $order) {

    if (!$order instanceof WC_Order) {
        return $args;
    }

    $deferred = $order->get_meta('_deferred_payment');

    if ($deferred) {
        $args['capture_method'] = 'manual';

        error_log(sprintf(
            '[DEFERRED][Stripe] PaymentIntent en modo MANUAL. order_id=%d',
            $order->get_id()
        ));
    }

    return $args;

}, 10, 2);

add_action('woocommerce_checkout_order_processed', function($order_id, $posted_data = [], $order = null) {

    if (!$order instanceof WC_Order) {
        $order = wc_get_order($order_id);
    }
    if (!$order) return;

    payment_deferred_file_log("checkout_order_processed START: order_id={$order_id} deferred=" . $order->get_meta('_deferred_payment'), 'INFO');
}, 10, 3);

add_filter('wc_stripe_process_payment', function($process, $order, $stripe_gateway){
    if (is_numeric($order)) $order = wc_get_order($order);
    if (!$order) return $process;

    $order_id = $order->get_id();
    $deferred = $order->get_meta('_deferred_payment');
    payment_deferred_file_log("FILTER wc_stripe_process_payment: order={$order_id} deferred={$deferred} incoming_process=" . ($process ? '1' : '0'), 'INFO');

    if(strtolower($deferred) === 'yes'){
        payment_deferred_file_log("FILTER wc_stripe_process_payment: blocking Stripe charge for order {$order_id}", 'INFO');
        return false;
    }
    return $process;
}, 10, 3);

// ------------------------
// Forzar estado pending para diferidos
// ------------------------
add_filter('woocommerce_payment_complete_order_status', function($status, $order){
    if (is_numeric($order)) $order = wc_get_order($order);
    if (!$order) return;

    if(strtolower($order->get_meta('_deferred_payment'))==='yes'){
        payment_deferred_file_log("woocommerce_payment_complete_order_status: forcing pending for order {$order->get_id()}", 'INFO');
        return 'pending';
    }
    return $status;
}, 10, 2);
