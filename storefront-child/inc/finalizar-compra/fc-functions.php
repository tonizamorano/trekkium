<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 1. Eliminar todos los campos de dirección de facturación del checkout
 */
add_filter( 'woocommerce_checkout_fields', function ( $fields ) {

    $address_fields = [
        'billing_country',
        'billing_address_1',
        'billing_address_2',
        'billing_city',
        'billing_state',
        'billing_postcode',
    ];

    foreach ( $address_fields as $field ) {
        if ( isset( $fields['billing'][ $field ] ) ) {
            unset( $fields['billing'][ $field ] );
        }
    }

    return $fields;
}, 20 );

/**
 * 2. Forzar que ningún campo de billing sea obligatorio
 */
add_filter( 'woocommerce_billing_fields', function ( $fields ) {

    foreach ( $fields as $key => $field ) {
        $fields[ $key ]['required'] = false;
    }

    return $fields;
}, 20 );

/**
 * 3. Eliminar errores residuales de validación relacionados con Billing Address
 */
add_action( 'woocommerce_checkout_process', function () {

    $errors = wc_get_notices( 'error' );

    if ( empty( $errors ) ) {
        return;
    }

    foreach ( $errors as $notice ) {
        if ( isset( $notice['notice'] ) && strpos( $notice['notice'], 'Billing Address' ) !== false ) {
            wc_clear_notices();
            break;
        }
    }
});
