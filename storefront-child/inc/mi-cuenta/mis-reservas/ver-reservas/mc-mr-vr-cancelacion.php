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

    <div class="mc-mr-vr-contenedor">

        <div class="mc-mr-vr-titular">
            <h2>Cancelación</h2>
        </div>

        <div class="mc-mr-vr-contenido">

            <?php if ($pedido_cancelado): ?>

                <div class="mc-mr-vr-texto-cancelacion cancelada">
                    Esta reserva ha sido cancelada.
                </div>

            <?php elseif ($cancelacion_vencida): ?>

                <div class="mc-mr-vr-texto-cancelacion vencida">
                    La fecha límite de cancelación ha vencido, ya no se puede cancelar la reserva.
                </div>

            <?php else: ?>

                <div class="mc-mr-vr-texto-cancelacion">
                    Puedes cancelar tu reserva hasta el <?php echo $limite_cancelacion_texto; ?>
                </div>

            <?php endif; ?>

            <?php if ( ! $pedido_cancelado && ! $cancelacion_vencida ) : ?>
    <div class="mc-mr-vr-boton-wrapper">
        <a href="<?php echo esc_url(add_query_arg(['cancelar-reserva' => $order_id], wc_get_account_endpoint_url('ver-reservas'))); ?>" 
        class="mc-mr-vr-boton" 
        onclick="return confirm('¿Estás seguro de que quieres cancelar tu reserva? Esta acción no se puede deshacer.');">
            Cancelar reserva
        </a>
    </div>
<?php endif; ?>

        </div>
    </div>

    <?php
    return ob_get_clean();
}