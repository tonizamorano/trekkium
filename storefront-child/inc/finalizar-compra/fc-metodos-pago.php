<?php
/**
 * Snippet: Checkout - Sección Métodos de Pago
 * Shortcode: [checkout_metodos_pago]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function trekkium_checkout_metodos_pago() {
    if ( ! function_exists( 'WC' ) ) {
        return '<p>No se pudo cargar WooCommerce.</p>';
    }

    $checkout = WC()->checkout();
    if ( ! $checkout ) {
        return '<p>No se pudo cargar el checkout.</p>';
    }

    ob_start();
    ?>

    <div class="checkout-contenedor-seccion">

        <div class="checkout-seccion-titulo">
            <!-- Icono tarjeta SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#ffffff" viewBox="0 0 24 24">
                <path d="M2 6c0-1.1.9-2 2-2h16c1.1 0 2 .9 2 2v2H2V6zm0 4h20v8c0 1.1-.9 2-2 
                2H4c-1.1 0-2-.9-2-2v-8zm4 4v2h6v-2H6z"/>
            </svg>
            <span style="font-size:16px; font-weight:700; color:#fff; text-align:center;">
                MÉTODOS DE PAGO
            </span>
        </div>

        <div class="checkout-seccion-contenido" style="margin-top:15px;">
            <?php
            // Esto carga directamente el bloque de métodos de pago
            wc_get_template( 'checkout/payment.php', array( 'checkout' => $checkout ) );
            ?>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode( 'checkout_metodos_pago', 'trekkium_checkout_metodos_pago' );
