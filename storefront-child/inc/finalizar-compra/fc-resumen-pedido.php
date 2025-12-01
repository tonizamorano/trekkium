<?php
/**
 * Snippet: Checkout - Sección Resumen Pedido
 * Shortcode: [checkout_resumen_pedido]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function trekkium_checkout_resumen_pedido() {
    // Asegurarse de que hay productos en el carrito
    if ( ! WC()->cart || WC()->cart->is_empty() ) {
        return '<p>No hay productos en tu carrito.</p>';
    }

    ob_start();

    // Solo mostrar el primer producto del carrito
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $product   = $cart_item['data'];
        $product_id = $product->get_id();
        $quantity  = $cart_item['quantity'];
        $price     = $product->get_price(); // precio unitario
        $total     = $price * $quantity;    // total calculado
        $image     = get_the_post_thumbnail( $product_id, 'medium' );
        $title     = $product->get_name();
        break; // solo primer producto
    }
    ?>

    <div class="checkout-contenedor-seccion">

        <div class="checkout-seccion-titulo">
            <!-- Icono usuario SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ffffff" viewBox="0 0 24 24">
                <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
            </svg>
            <span style="font-size:16px; font-weight:700; color:#fff; text-align:center;">
                RESUMEN DE TU RESERVA
            </span>
        </div>

        <div class="checkout-seccion-contenido">

            <!-- Imagen y título -->
            <div>
                <?php echo $image; ?>
                <div class="resumen-pedido-titulo" style="font-size:20px; font-weight:700; color:#0b568b;">
                    <?php echo esc_html( $title ); ?>
                </div>
            </div>

            <!-- Precio de la reserva -->
            <div style="display:flex; justify-content:space-between; margin-top:10px;">
                <div style="font-size:18px; font-weight:600;">Precio de la Reserva</div>
                <div style="font-size:18px; font-weight:500;"><?php echo wc_price( $price ); ?></div>
            </div>

            <!-- Plazas reservadas -->
            <div style="display:flex; justify-content:space-between; margin-top:10px;">
                <div style="font-size:18px; font-weight:600;">Plazas reservadas</div>
                <div style="font-size:18px; font-weight:500;"><?php echo esc_html( $quantity ); ?></div>
            </div>

            <!-- Total -->
            <div style="display:flex; justify-content:space-between; margin-top:10px;">
                <div style="font-size:18px; font-weight:600;">Total</div>
                <div style="font-size:18px; font-weight:500;"><?php echo wc_price( $total ); ?></div>
            </div>

        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode( 'checkout_resumen_pedido', 'trekkium_checkout_resumen_pedido' );
