<?php
/**
 * Orders
 *
 * Template personalizado para mostrar solo el shortcode [seccion_mis_reservas].
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );

// Mostrar el shortcode
echo do_shortcode('[seccion_mis_reservas]');

do_action( 'woocommerce_after_account_orders', $has_orders );

