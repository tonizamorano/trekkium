<?php
// Shortcode: Cancelación
add_shortcode('mc_vr_cancelacion', 'mc_vr_cancelacion_callback');

function mc_vr_cancelacion_callback($atts) {

    $atts = shortcode_atts([
        'order_id' => ''
    ], $atts);

    $order_id = intval($atts['order_id']);
    if (!$order_id) return '';

    $order = wc_get_order($order_id);
    if (!$order) return '';

    // Actividad del pedido
    $items = $order->get_items();
    $actividad = reset($items);
    if (!$actividad) return '';

    $product_id = $actividad->get_product_id();
    $parent_product_id = wp_get_post_parent_id($product_id);
    if ($parent_product_id) $product_id = $parent_product_id;

    // Límite de cancelación
    $limite_cancelacion_raw = get_post_meta($product_id, 'limite_cancelacion', true);

    $pedido_cancelado = ($order->get_status() === 'cancelled');
    $cancelacion_vencida = false;

    if ($limite_cancelacion_raw) {
        $timestamp_limite = strtotime(str_replace('/', '-', $limite_cancelacion_raw));
        $timestamp_actual = current_time('timestamp');
        $cancelacion_vencida = ($timestamp_actual > $timestamp_limite);
    }

    // Formatear fecha/hora
    $limite_cancelacion_texto = '—';
    if ($limite_cancelacion_raw) {
        $timestamp = strtotime(str_replace('/', '-', $limite_cancelacion_raw));
        $fecha = date_i18n('d/m/Y', $timestamp);
        $hora  = date_i18n('H:i', $timestamp);
        $limite_cancelacion_texto = "<strong>$fecha</strong> a las <strong>$hora h.</strong>";
    }

    ob_start();
    ?>

    <div class="contenedor-detalles">
        <h1>Cancelación</h1>

        <div class="contenido-detalles">

            <?php if ($pedido_cancelado): ?>

                <div class="texto-cancelacion cancelada">
                    Esta reserva ha sido cancelada.
                </div>

            <?php elseif ($cancelacion_vencida): ?>

                <div class="texto-cancelacion vencida">
                    La fecha límite de cancelación ha vencido, ya no se puede cancelar la reserva.
                </div>

            <?php else: ?>

                <div class="texto-cancelacion">
                    Puedes cancelar tu reserva hasta el <?php echo $limite_cancelacion_texto; ?>
                </div>

            <?php endif; ?>

            <?php if ( ! $pedido_cancelado && ! $cancelacion_vencida ) : ?>
                <div class="mc-vr-boton-wrapper">
                    <a href="#" 
                    class="mc-vr-boton" 
                    id="boton-cancelar-reserva"
                    data-order="<?php echo esc_attr( $order_id ); ?>">
                        Cancelar reserva
                    </a>
                </div>
            <?php endif; ?>


        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const boton = document.getElementById("boton-cancelar-reserva");
        if (!boton) return;

        boton.addEventListener("click", function () {
            const id = this.getAttribute("data-order");
            if (confirm("¿Estás seguro de que quieres cancelar tu reserva? Esta acción no se puede deshacer.")) {
                window.location.href = "<?php echo esc_url(
                    add_query_arg(['cancelar-reserva' => 'ORDER_ID'], wc_get_account_endpoint_url('ver-reservas'))
                ); ?>".replace("ORDER_ID", id);
            }
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
