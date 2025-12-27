<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// ---------------------------------------------------
// NOTICES – No los borramos (importante p/evitar sesión caducada)
// ---------------------------------------------------


// Eliminar mensajes de "producto añadido al carrito" si quieres
add_filter( 'woocommerce_add_to_cart_message_html', '__return_empty_string' );
add_filter( 'woocommerce_cart_item_removed_notice', '__return_empty_string' );
add_filter( 'woocommerce_cart_item_added_to_cart', '__return_empty_string' );

// NO USAR wc_clear_notices() → rompe la sesión y el checkout



// Comprobación de registro obligatorio
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
    return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout"
      action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
      enctype="multipart/form-data"
      aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

    <div class="pagina-grid-3366">

        <!-- -------------------------
             COLUMNA IZQUIERDA (33%)
        -------------------------- -->
        <div class="pagina-columna33-sticky">

            <?php echo do_shortcode('[checkout_resumen_pedido]'); ?>

           
            
        </div>


        <!-- -------------------------
             COLUMNA DERECHA (66%)
        -------------------------- -->
        <div class="pagina-columna66">

            <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

            <!-- CAMPOS NATIVOS OCULTOS (WooCommerce los necesita para AJAX y sesión) -->
            <div class="seccion_oculta" style="display:none !important;">
                <?php do_action( 'woocommerce_checkout_billing' ); ?>
                <?php do_action( 'woocommerce_checkout_shipping' ); ?>
            </div>

            <!-- TUS BLOQUES PERSONALIZADOS -->
            
            <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

            <?php echo do_shortcode('[checkout_titular_reserva]'); ?>

            <?php echo do_shortcode('[checkout_datos_acompanantes]'); ?>            

            <?php echo do_shortcode('[checkout_info_reserva]'); ?>
            
            <?php echo do_shortcode('[checkout_info_adicional]'); ?>

            <?php echo do_shortcode('[checkout_metodos_pago]'); ?>

        </div>

        

    </div>


    <!-- ----------------------------------------------------------
         BLOQUE NATIVO OBLIGATORIO DE WOOCOMMERCE (OCULTO)
         * Sin este bloque, fallan:
           - update_order_review
           - validación de sesión
           - Stripe
           - fragmentos
           - totales
        ---------------------------------------------------------- -->
    <div style="display:none !important;">
        <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

        <h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

        <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

        <div id="order_review" class="woocommerce-checkout-review-order">
            <?php do_action( 'woocommerce_checkout_order_review' ); ?>
        </div>

        <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
    </div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<!-- debug scripts removed -->
