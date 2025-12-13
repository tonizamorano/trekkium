<?php
defined( 'ABSPATH' ) || exit;
?>

<?php if ( $order ) : ?>
    <?php do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>
<?php endif; ?>

<style>

    .thankyou-wrapper {
        max-width: 400px;
        margin: 15px auto;
        background: #ffffff;
        padding: 0;
        border-radius: 10px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.08);
        text-align: center;
    }

    .thankyou-image-wrapper {
        position: relative;
        width: 100%;
        aspect-ratio: 16/9;
        overflow: hidden;
        box-shadow: 0 6px 12px rgba(0,0,0,0.12);
    }

    .thankyou-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 10px 10px 0 0 !important;
    }

    .thankyou-wrapper h1 {
        margin-bottom: 10px !important;
        font-size: 24px;
        font-weight: 600;
    }

    .thankyou-contenido {
        padding: 15px;
    }

    .thankyou-contenido p {
        font-size: 18px;
        color: var(--azul1-100); /* azul tema */
       
    }

    .thankyou-button {
        display: inline-block;
        background: #E67E22; /* naranja tema */
        color: #fff;
        padding: 7px 15px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 500;
        transition: opacity 0.3s;
        margin-top: 10px;
        line-height: 1;
        font-family: inherit;
    }

    .thankyou-button:hover {
        opacity: 0.8;
    }

    @media (max-width: 768px) {

        .thankyou-wrapper {
            width: 90%;
            
        }

        .thankyou-wrapper {
            max-width: 400px;
            margin: 15px auto;
            background: #fff;
            padding: 0;
            border-radius: 10px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.08);
            text-align: center;
        }





    }

</style>

<div class="thankyou-wrapper">

    <div class="thankyou-image-wrapper">
        <img src="https://staging2.trekkium.com/wp-content/uploads/2025/12/unnamed.jpg" alt="">
    </div>

    <div class="thankyou-contenido">

        <?php
        // Nombre del titular de la reserva
        $billing_first_name = $order->get_billing_first_name();
        $order_number = $order->get_order_number();
        ?>

        <h1><?php echo esc_html( $billing_first_name ); ?>, gracias por realizar tu reserva.</h1>

        <p>
            Tu reserva Nº <?php echo esc_html( $order_number ); ?> se ha realizado correctamente. Te hemos enviado un correo electrónico con todos los detalles.</p>

        <p>También puedes consultar el estado y los detalles de tu reserva pulsando aquí:</p>

        <a href="<?php echo esc_url( wc_get_endpoint_url( 'view-order', $order->get_id(), wc_get_page_permalink( 'myaccount' ) ) ); ?>" class="thankyou-button">
            Ver mi reserva
        </a>

    </div>

</div>

<?php
if ( $order ) :
    do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
    do_action( 'woocommerce_thankyou', $order->get_id() );
endif;
?>
