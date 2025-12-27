<?php
/**
 * Snippet: Checkout - Sección Información Adicional
 * Shortcode: [checkout_info_adicional]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function trekkium_checkout_info_adicional() {
    if ( ! function_exists( 'WC' ) ) {
        return '<p>No se pudo cargar WooCommerce.</p>';
    }

    $checkout = WC()->checkout();
    if ( ! $checkout ) {
        return '<p>No se pudo cargar el checkout.</p>';
    }

    // Obtener el valor del campo
    $order_comments_value = $checkout->get_value('order_comments');
    
    ob_start();
    ?>

<div class="fc-contenedor">
    <div class="fc-titulo">          
        <span class="fc-titulo-texto">
            Información adicional
        </span>
    </div>

    <div class="fc-contenido">
        <textarea 
            name="order_comments" 
            class="fc-ia-campo"
            placeholder="Indica si tienes alguna petición especial o algo importante que el guía deba saber (medicación, enfermedad, etc...)"
        ><?php echo esc_textarea($order_comments_value); ?></textarea>
    </div>
</div>

    <?php
    return ob_get_clean();
}
add_shortcode( 'checkout_info_adicional', 'trekkium_checkout_info_adicional' );