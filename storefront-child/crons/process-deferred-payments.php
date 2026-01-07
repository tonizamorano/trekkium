<?php
/**
 * Cron CLI: procesar pagos diferidos con Stripe (mejorado)
 *
 * Ejecutar solo desde CLI.
 */

if ( php_sapi_name() !== 'cli' ) {
    fwrite(STDERR, "Este script solo puede ejecutarse desde CLI\n");
    exit(1);
}

// ------------------------------------------------------------
// Cargar WordPress
// ------------------------------------------------------------
$search_dir = __DIR__;
$wp_loaded = false;
for ($i = 0; $i < 10; $i++) {
    if (file_exists($search_dir . '/wp-load.php')) {
        require_once $search_dir . '/wp-load.php';
        $wp_loaded = true;
        break;
    }
    $parent = dirname($search_dir);
    if ($parent === $search_dir) break;
    $search_dir = $parent;
}
if (!$wp_loaded) {
    fwrite(STDERR, "No se pudo cargar WordPress\n");
    exit(1);
}

// ------------------------------------------------------------
// Logging
// ------------------------------------------------------------
$log_dir  = __DIR__ . '/logs';
$log_info = $log_dir . '/cron-deferred-payments-info-' . date('Y-m-d') . '.log';
$log_err  = $log_dir . '/cron-deferred-payments-error-' . date('Y-m-d') . '.log';
if (!is_dir($log_dir)) mkdir($log_dir, 0755, true);

function cron_log($message, $level = 'INFO') {
    global $log_info, $log_err;
    $line = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), $level, $message);
    if ($level === 'ERROR') {
        error_log($line, 3, $log_err);
    } else {
        error_log($line, 3, $log_info);
    }
}

// ------------------------------------------------------------
// Ejecutar cron con try/catch global
// ------------------------------------------------------------
try {

    $start_time = microtime(true);
    $processed = 0;
    $tz  = wp_timezone();
    $now = (new DateTime('now', $tz))->getTimestamp();

    cron_log("Start exec cron process-deferred-payments");

    // Obtener pedidos pending con pago diferido y actividad entre ahora y 24h
    $max_ts = $now + 24*3600;

    $args = array(
        'status' => 'pending',
        'limit' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_deferred_payment',
                'value' => 'yes',
                'compare' => '=',
            ),
            array(
                'key' => '_activity_start',
                'value' => array($now, $max_ts),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            )
        )
    );

    $orders = wc_get_orders($args);
    cron_log('Pedidos pending (deferred) encontrados en rango 0-24h: ' . count($orders));

    foreach ($orders as $order) {
        $order_id = $order->get_id();
        $user_id = $order->get_user_id();
        $user_email = $order->get_billing_email();

        $activity_name = '';
        $items = $order->get_items();
        if (!empty($items)) {
            $item = reset($items);
            $product_id = $item->get_product_id();
            $activity_name = get_the_title($product_id);
        }

        $activity_ts = (int) $order->get_meta('_activity_start');
        if (!$activity_ts) {
            cron_log("Pedido $order_id sin _activity_start, se omite", 'ERROR');
            continue;
        }

        $diff = $activity_ts - $now;
        cron_log("Procesando pedido $order_id ($activity_name), inicio en $diff s");

        // Obtener método/token Stripe
        $pm = $order->get_meta('_stripe_payment_method');
        if (!$pm) $pm = $order->get_meta('_stripe_token');
        if (!$pm) {
            $order->add_order_note('No se encontró método de pago almacenado para intento automático.');
            cron_log("Pedido $order_id: no hay método de pago", 'ERROR');
            continue;
        }

        // Clave Stripe
        $stripe_settings = get_option('woocommerce_stripe_settings', []);
        $secret_key = $stripe_settings['secret_key'] ?? $stripe_settings['test_secret_key'] ?? getenv('STRIPE_SECRET_KEY') ?? '';
        if (!$secret_key) {
            cron_log("Pedido $order_id: no hay clave Stripe configurada, se omite", 'ERROR');
            continue;
        }

        // Intento de cobro con retry simple
        $attempts = 2; // primer intento + retry
        for ($i=1; $i<=$attempts; $i++) {
            try {
                if (!class_exists('\\Stripe\\Stripe')) throw new Exception('Stripe PHP SDK no disponible');

                \\Stripe\\Stripe::setApiKey($secret_key);
                $amount = (int) round($order->get_total()*100);
                $currency = strtolower($order->get_currency());

                $intent = \\Stripe\\PaymentIntent::create([
                    'amount' => $amount,
                    'currency' => $currency,
                    'payment_method' => $pm,
                    'confirm' => true,
                    'off_session' => true,
                    'metadata' => ['order_id' => $order_id],
                ]);

                if (isset($intent->status) && $intent->status === 'succeeded') {
                    $order->payment_complete($intent->id);
                    $order->add_order_note("Pago diferido procesado automáticamente. PaymentIntent {$intent->id}");
                    $order->update_status('completed', 'Pago diferido completado mediante cron.');
                    cron_log("Pedido $order_id ($activity_name), usuario $user_id ($user_email), cobro realizado: PaymentIntent {$intent->id}, total {$order->get_total()}, fecha ".current_time('mysql'));
                    break; // éxito, salir del retry
                } else {
                    $msg = 'Estado Stripe: ' . ($intent->status ?? 'unknown');
                    if ($i==$attempts) {
                        $order->add_order_note("Intento de cobro diferido fallido. $msg");
                        $order->update_status('failed', "Pago diferido no completado: $msg");
                        cron_log("Pedido $order_id: cobro no completado. $msg", 'ERROR');
                    } else {
                        cron_log("Pedido $order_id: intento $i fallido ($msg), retry...", 'INFO');
                        sleep(3); // espera antes del retry
                    }
                }

            } catch (\Stripe\Exception\CardException $e) {
                // Tarjeta rechazada / fondos insuficientes
                $msg = $e->getMessage();
                $order->add_order_note("Error de tarjeta: $msg");
                $order->update_status('failed', "Error en pago diferido: $msg");
                cron_log("Pedido $order_id ($activity_name): tarjeta/fondos insuficientes: $msg", 'ERROR');
                break; // no retry, es error usuario
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // Error de Stripe API → retry si queda intento
                $msg = $e->getMessage();
                if ($i==$attempts) {
                    $order->add_order_note("Error Stripe API persistente: $msg");
                    $order->update_status('failed', "Error Stripe API: $msg");
                    cron_log("Pedido $order_id ($activity_name): error Stripe API final: $msg", 'ERROR');
                } else {
                    cron_log("Pedido $order_id ($activity_name): error Stripe API, intento $i, retry...: $msg", 'INFO');
                    sleep(3);
                }
            } catch (\Exception $e) {
                // Otros errores → fallback
                $msg = $e->getMessage();
                $order->add_order_note("Error general procesando pago diferido: $msg");
                $order->update_status('failed', "Error general: $msg");
                cron_log("Pedido $order_id ($activity_name): excepción: $msg", 'ERROR');
                break;
            }
        }

        $processed++;
    }

    $duration = round(microtime(true) - $start_time, 2);
    cron_log("Fin ejecución. Pedidos procesados: $processed. Duración: {$duration}s");

} catch (\Exception $e) {
    cron_log('Excepción global en cron: '.$e->getMessage(),'ERROR');
    exit(1);
}

exit(0);
