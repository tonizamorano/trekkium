<?php
// Shortcode para mostrar los detalles de la reserva
add_shortcode('seccion_detalles_reserva', 'mostrar_detalles_del_pedido');

function mostrar_detalles_del_pedido() {
    if (!is_user_logged_in()) return '';

    $order_id = get_query_var('ver-reservas');
    if (!$order_id) return '';

    $order = wc_get_order($order_id);
    if (!$order) return '';

    ob_start();

    $items = $order->get_items();
    $actividad = reset($items);
    if (!$actividad) return '';

    $producto_nombre = $actividad->get_name();
    $product_id = $actividad->get_product_id();
    $precio_unitario_con_iva = ($actividad->get_total() + $actividad->get_total_tax()) / $actividad->get_quantity();

    $parent_product_id = wp_get_post_parent_id($product_id);
    if ($parent_product_id) $product_id = $parent_product_id;

    // Metadatos de la actividad
    $fecha_raw = get_post_meta($product_id, 'fecha', true);
    $hora_raw  = get_post_meta($product_id, 'hora', true);
    $estado_actividad = get_post_meta($product_id, 'estado_actividad', true);
    $limite_cancelacion_raw = get_post_meta($product_id, 'limite_cancelacion', true);
    $precio_total_actividad = floatval(get_post_meta($product_id, 'precio', true));
    $plazas_totales = get_post_meta($product_id, 'plazas_totales', true);
    $plazas_minimas = get_post_meta($product_id, 'plazas_minimas', true);

    $product = wc_get_product($product_id);
    $plazas_disponibles = $product ? $product->get_stock_quantity() : '—';

    $fecha_actividad = $fecha_raw ? date('d/m/Y', strtotime($fecha_raw)) : '—';
    $fecha_reserva = $order->get_date_created() ? date_i18n('d/m/Y', $order->get_date_created()->getTimestamp()) : '—';
    $estado_wc = $order->get_status();
    $estados_traducidos = [
        'pending'    => 'Pendiente',
        'processing' => 'Procesando',
        'on-hold'    => 'En espera',
        'completed'  => 'Completada',
        'cancelled'  => 'Cancelada',
        'refunded'   => 'Reembolsada',
        'failed'     => 'Fallida'
    ];
    $estado_traducido = $estados_traducidos[$estado_wc] ?? ucfirst($estado_wc);
    $total  = $order->get_formatted_order_total();
    $plazas = intval($actividad->get_quantity());

    // Calcular cantidad comprada para info-extra
    $cantidad_comprada = 0;
    foreach ($order->get_items() as $item) {
        if ($item->get_product_id() == $product_id) {
            $cantidad_comprada = $item->get_quantity();
            break;
        }
    }

    $falta_por_pagar = ($precio_total_actividad - $precio_unitario_con_iva) * $plazas;

    $limite_cancelacion_texto = '—';
    if ($limite_cancelacion_raw) {
        $timestamp = strtotime(str_replace('/', '-', $limite_cancelacion_raw));
        $fecha_cancelacion = date_i18n('d/m/Y', $timestamp);
        $hora_cancelacion = date_i18n('H:i', $timestamp);
        $limite_cancelacion_texto = "<strong>$fecha_cancelacion</strong> a las <strong>$hora_cancelacion h.</strong>";
    }

    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const botonCancelar = document.getElementById("boton-cancelar-reserva");
        if (botonCancelar) {
            botonCancelar.addEventListener("click", function () {
                if (confirm("¿Estás seguro de que quieres cancelar tu reserva? Esta acción no se puede deshacer.")) {
                    window.location.href = "<?php echo esc_url(add_query_arg(['cancelar-reserva' => $order_id], wc_get_account_endpoint_url('ver-reservas'))); ?>";
                }
            });
        }
    });
    </script>

    <div class="detalles-reservas-container">  
        
        <div class="reservas-container-titulo">
            <h2>Detalles de la reserva</h2>
        </div>

        <div class="contenedor-dos-columnas">

            <!-- COLUMNA IZQUIERDA -->
            <div class="columna-izquierda">

                <!-- Detalles de la reserva -->
                <?php echo do_shortcode('[mc_vr_detalles_reserva]'); ?>
                
                <!-- Detalles de la reserva -->
                <?php echo do_shortcode('[mc_vr_titular]'); ?>

                <!-- Acompañantes -->
                <?php echo do_shortcode('[mc_vr_acompanantes]'); ?>

            </div>

            <!-- COLUMNA DERECHA -->
            <div class="columna-derecha">

                <!-- Detalles de la actividad-->
                <?php echo do_shortcode('[mc_vr_estado_actividad order_id="'.$order_id.'"]'); ?>

                <!-- Resumen del pago -->
                <?php echo do_shortcode('[mc_vr_resumen_pago order_id="'.$order_id.'"]'); ?>

                <!-- Cancelación -->
                <?php echo do_shortcode('[mc_vr_cancelacion order_id="'.$order_id.'"]'); ?>

            </div>

        </div>
    </div>
    <?php

    return ob_get_clean();
}

// Manejo de cancelación
add_action('template_redirect', function () {
    if (is_user_logged_in() && isset($_GET['cancelar-reserva'])) {
        $order_id = intval($_GET['cancelar-reserva']);
        $order = wc_get_order($order_id);

        if ($order && $order->get_user_id() === get_current_user_id()) {
            $order->update_status('cancelled', 'Cancelado por el usuario desde su cuenta.');
            wp_redirect(wc_get_account_endpoint_url('reservas'));
            exit;
        }
    }
});
