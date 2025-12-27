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

    <div class="fc-contenedor">

        <div class="fc-titulo">          
            <span class="fc-titulo-texto">
                Métodos de pago
            </span>
        </div>

        <div class="fc-contenido" style="margin-top:15px;">
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
