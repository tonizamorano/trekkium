<?php
/**
 * Snippet: Checkout - Sección Resumen Pedido
 * Shortcode: [checkout_resumen_pedido]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function trekkium_checkout_resumen_pedido() {

    if ( ! WC()->cart || WC()->cart->is_empty() ) {
        return '<p>No hay productos en tu carrito.</p>';
    }

    ob_start();

    // Tomamos solo el primer producto del carrito
    foreach ( WC()->cart->get_cart() as $cart_item ) {

        $product      = $cart_item['data'];
        $product_id   = $product->get_id();
        $plazas       = (int) $cart_item['quantity'];

        // Fecha de la actividad
        $fecha_raw = $product->get_meta( 'fecha' );

        $fecha_formateada = '';
        if ( $fecha_raw ) {
            $timestamp = strtotime( $fecha_raw );

            // Forzar idioma español
            setlocale( LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain' );

            $fecha_formateada = strftime( '%A, %e de %B de %Y', $timestamp );


            // Capitalizar mes (enero → Enero)
            $fecha_formateada = ucfirst( $fecha_formateada );
        }


        // Provincia y Región
        $provincia = wp_get_post_terms( $product_id, 'provincia' );
        $region    = wp_get_post_terms( $product_id, 'region' );

        $provincia_nombre = ! empty( $provincia ) && ! is_wp_error( $provincia ) ? $provincia[0]->name : '';
        $region_nombre    = ! empty( $region ) && ! is_wp_error( $region ) ? $region[0]->name : '';


        // Precios
        $precio_actividad_unitario = (float) $product->get_meta( 'precio' ); // PRECIO REAL ACTIVIDAD
        $precio_reserva_unitario   = (float) $product->get_price();          // PRECIO WOOCOMMERCE (RESERVA)


        $total_actividad = $precio_actividad_unitario * $plazas;

        // Total real cobrado ahora (reserva)
        $total_reserva = WC()->cart->get_total( 'edit' );
        $total_reserva_float = WC()->cart->total;

        $falta_por_pagar = max( 0, $total_actividad - $total_reserva_float );

        $image = get_the_post_thumbnail( $product_id, 'medium' );
        $title = $product->get_name();

        break;
    }
    ?>

    <div class="fc-contenedor">

        <!-- Imagen -->
        <div class="fc-rp-imagen">
            <?php echo $image; ?>
        </div>

        <div class="fc-contenido">

            <!-- Título -->
            <div class="fc-rp-titulo">
                <?php echo esc_html( $title ); ?>
            </div>

            <?php if ( $provincia_nombre ) : ?>

                <div class="fc-rp-localizacion">
                    <?php echo esc_html( $provincia_nombre ); ?>
                    <?php if ( $region_nombre ) : ?>
                        <span class="fc-rp-region">(<?php echo esc_html( $region_nombre ); ?>)</span>
                    <?php endif; ?>
                </div>
                
            <?php endif; ?>

            <?php if ( $fecha_formateada ) : ?>
                <div class="fc-rp-fecha">
                    <?php echo esc_html( $fecha_formateada ); ?>
                </div>
            <?php endif; ?>





            <!-- Desglose de costes -->

            <div class="fc-rp-fila-datos">
                <span class="etiqueta">Precio actividad</span>
                <span class="valor"><?php echo wc_price( $precio_actividad_unitario ); ?></span>
            </div>

            <div class="fc-rp-fila-datos">
                <span class="etiqueta">Importe reserva</span>
                <span class="valor"><?php echo wc_price( $precio_reserva_unitario ); ?></span>
            </div>

            <div class="fc-rp-fila-datos">
                <span class="etiqueta">Plazas reservadas</span>
                <span class="valor">x <?php echo esc_html( $plazas ); ?></span>
            </div>

            <div style="height:2px;background-color:var(--azul1-100);margin:7px 0;border-radius:2px;"></div>

            <div class="fc-rp-fila-datos">
                <span class="etiqueta">Total actividad</span>
                <span class="valor"><?php echo wc_price( $total_actividad ); ?></span>
            </div>

            <div class="fc-rp-fila-datos">
                <span class="etiqueta">Total reserva</span>
                <span class="valor"><?php echo wc_price( $total_reserva_float ); ?></span>
            </div>

            <div class="fc-rp-fila-datos">
                <span class="etiqueta">Importe pendiente</span>
                <span class="valor"><?php echo wc_price( $falta_por_pagar ); ?></span>
            </div>

        </div>
    </div>

    <?php
    return ob_get_clean();
}

add_shortcode( 'checkout_resumen_pedido', 'trekkium_checkout_resumen_pedido' );
