<?php

// Desactivar totalmente las opciones y cálculos de envío en WooCommerce
add_filter( 'woocommerce_cart_needs_shipping', '__return_false' );
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );
add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    unset( $fields['shipping'] );
    return $fields;
});
add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_false' );
add_filter( 'woocommerce_shipping_calculator_enable_postcode', '__return_false' );
add_filter( 'woocommerce_shipping_calculator_enable_state', '__return_false' );

// Eliminar pestañas de envío en el panel de ajustes
add_filter( 'woocommerce_get_settings_pages', function( $settings ) {
    foreach ( $settings as $index => $page ) {
        if ( isset( $page->id ) && $page->id === 'shipping' ) {
            unset( $settings[$index] );
        }
    }
    return $settings;
});
