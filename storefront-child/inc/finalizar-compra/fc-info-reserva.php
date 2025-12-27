<?php
/**
 * Snippet: checkout-info-reserva
 * Shortcode: [checkout_info_reserva]
 */

function shortcode_checkout_info_reserva() {

    if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
        return '';
    }

    // Obtener el producto del carrito (primer item)
    $plazo_cancelacion = '';

    foreach ( WC()->cart->get_cart() as $cart_item ) {
        if ( isset( $cart_item['product_id'] ) ) {
            $product_id = $cart_item['product_id'];
            $plazo_cancelacion = get_post_meta( $product_id, 'limite_cancelacion', true );
            break;
        }
    }

    // Formatear fecha y hora
    $fecha_formateada = '';
    $hora_formateada  = '';

    if ( $plazo_cancelacion ) {

        $date = DateTime::createFromFormat(
            'd/m/Y H:i',
            $plazo_cancelacion,
            wp_timezone()
        );

        if ( $date ) {
            $fecha_formateada = $date->format( 'd/m/Y' );
            $hora_formateada  = $date->format( 'H:i' );
        }
    }


    ob_start();
    ?>

    <div class="fc-contenedor">

        <div class="fc-titulo">
            <span class="fc-titulo-texto">Información sobre tu reserva</span>
        </div>

        <div class="fc-contenido">

            <div class="fc-grid-label">
                ✔︎ IVA incluido en todos los precios.
            </div>

            <div class="fc-grid-label">
                ✔︎ Cancelación gratis hasta el 
                <strong><?php echo esc_html( $fecha_formateada ); ?></strong>
                a las 
                <strong><?php echo esc_html( $hora_formateada ); ?></strong> h.
            </div>

            <div class="fc-grid-label">
                ✔︎ El importe total de tu reserva será cargado en tu tarjeta cuando termine el plazo de cancelación gratuita.
            </div>

            <div class="fc-grid-label">
                ✔︎ El día de la actividad deberás pagar al guía al contado el importe pendiente.
            </div>

        </div>
    </div>

    <?php
    return ob_get_clean();
}

add_shortcode( 'checkout_info_reserva', 'shortcode_checkout_info_reserva' );
