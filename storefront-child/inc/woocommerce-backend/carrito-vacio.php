<?php
// Redirige siempre al checkout
add_filter('woocommerce_add_to_cart_redirect', function() {
    return wc_get_checkout_url();
});

// Se asegura de que hay una sola actividad en el carrito - no mezclas
add_filter('woocommerce_add_to_cart_validation', function($passed, $product_id, $quantity) {
	
    // Vaciar acompañantes siempre
    WC()->session->__unset('acompanantes');

    $cart = WC()->cart->get_cart();
    foreach ($cart as $item) {
        if ($item['product_id'] !== $product_id) {
            WC()->cart->empty_cart();
            break;
        }
    }

    return $passed;
}, 10, 3);


// Vacía el carrito automáticamente si se navega fuera del checkout,
// PERO no durante una acción dentro del flujo (checkout o formulario-datos-acompanante)
add_action('template_redirect', function() {
    $is_adding_product = isset($_GET['add-to-cart']);
    $current_url = $_SERVER['REQUEST_URI'];

    $es_pagina_valida = (
        is_checkout() ||
        is_wc_endpoint_url('order-received') ||
        strpos($current_url, '/formulario-datos-acompanante') !== false
    );

    if (
        !$is_adding_product &&
        !$es_pagina_valida &&
        !is_admin() &&
        !defined('DOING_AJAX')
    ) {
        WC()->cart->empty_cart();
    }
});


// Redirige /cart a home
add_action('template_redirect', function() {
    if (is_cart()) {
        wp_redirect(home_url());
        exit;
    }
});