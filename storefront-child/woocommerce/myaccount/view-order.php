<?php
/**
 * View Order
 *
 * Template personalizado para mostrar solo el shortcode [seccion_detalles_reserva].
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hook: woocommerce_before_view_order.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_before_view_order', $order_id );

// Mostrar el shortcode personalizado
echo do_shortcode('[seccion_detalles_reserva]');

/**
 * Hook: woocommerce_after_view_order.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_after_view_order', $order_id );
