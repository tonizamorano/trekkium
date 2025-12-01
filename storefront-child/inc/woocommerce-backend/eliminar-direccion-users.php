<?php
/**
 * Eliminar campos de dirección de billing y shipping en WooCommerce
 */
add_filter('woocommerce_billing_fields', function($fields) {
    // Eliminamos los campos de dirección
    unset($fields['billing_address_1']);
    unset($fields['billing_address_2']);
    unset($fields['billing_city']);
    unset($fields['billing_postcode']);
    unset($fields['billing_state']);
    return $fields;
});

add_filter('woocommerce_shipping_fields', function($fields) {
    // Eliminamos los campos de dirección de envío
    unset($fields['shipping_address_1']);
    unset($fields['shipping_address_2']);
    unset($fields['shipping_city']);
    unset($fields['shipping_postcode']);
    unset($fields['shipping_state']);
    return $fields;
});

// Para evitar errores en el procesamiento del checkout
add_action('woocommerce_checkout_process', function() {
    unset($_POST['billing_address_1'], $_POST['billing_address_2'], $_POST['billing_city'], $_POST['billing_postcode'], $_POST['billing_state']);
    unset($_POST['shipping_address_1'], $_POST['shipping_address_2'], $_POST['shipping_city'], $_POST['shipping_postcode'], $_POST['shipping_state']);
});
