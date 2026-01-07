<?php
if (!defined('ABSPATH')) exit;

add_action('plugins_loaded', function () {

    // Asegurarnos de que Stripe está cargado
    if (!class_exists('WC_Gateway_Stripe')) {
        return;
    }

    class WC_Gateway_Stripe_Deferred extends WC_Gateway_Stripe {

        public function __construct() {
            parent::__construct();

            $this->id = 'stripe_deferred';
            $this->method_title = __('Stripe (Pago diferido)', 'trekkium');
            $this->method_description = __('Pago con Stripe diferido. El cargo se ejecuta posteriormente.', 'trekkium');

            // Texto que verá el usuario
            $this->title = __('Tarjeta (pago diferido)', 'trekkium');

            // Opcional: icono
            // $this->icon = '';
        }

        /**
         * Punto CRÍTICO
         */
        public function process_payment($order_id) {

            $order = wc_get_order($order_id);
            if (!$order) {
                wc_add_notice(__('Error al procesar el pedido.', 'trekkium'), 'error');
                return;
            }

            $deferred = strtolower((string) $order->get_meta('_deferred_payment'));

            if ($deferred === 'yes') {

                // Blindaje total: Stripe NO se ejecuta
                $order->update_status('pending', __('Pago diferido: Stripe no ejecutado en checkout.', 'trekkium'));

                // Vaciar carrito
                WC()->cart->empty_cart();

                if (function_exists('payment_deferred_file_log')) {
                    payment_deferred_file_log(
                        "process_payment OVERRIDE: pedido {$order_id} marcado como deferred, Stripe omitido",
                        'INFO'
                    );
                }

                return [
                    'result'   => 'success',
                    'redirect' => $this->get_return_url($order),
                ];
            }

            // Si NO es diferido → Stripe normal
            return parent::process_payment($order_id);
        }
    }
});

add_filter('woocommerce_payment_gateways', function ($gateways) {
    $gateways[] = 'WC_Gateway_Stripe_Deferred';
    return $gateways;
});
