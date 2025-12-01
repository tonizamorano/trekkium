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
        'completed'  => 'Completado',
        'cancelled'  => 'Cancelado',
        'refunded'   => 'Reembolsado',
        'failed'     => 'Fallido'
    ];
    $estado_traducido = $estados_traducidos[$estado_wc] ?? ucfirst($estado_wc);
    $total  = $order->get_formatted_order_total();
    $plazas = intval($actividad->get_quantity());

    // Calcular cantidad comprada para el bloque info-extra
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
        <div class="contenedor-dos-columnas">

            <!-- COLUMNA IZQUIERDA -->
            <div class="columna-izquierda">
                <!-- DETALLES DE LA RESERVA -->
                <div class="contenedor-detalles">
                    <h1>DETALLES DE LA RESERVA</h1>
                    <div class="contenido-detalles">

                        <div class="titulo-reserva"><?php echo esc_html($producto_nombre); ?></div>

                        <?php if (has_post_thumbnail($product_id)) {
                            echo '<div class="imagen-actividad">'.get_the_post_thumbnail($product_id,'large',['style'=>'width:100%;aspect-ratio:16/9;object-fit:cover;']).'</div>';
                        } ?>

                        <!-- BLOQUE INFO EXTRA -->
                        <div class="reserva-info-extra">

                            <div class="reserva-info-item-fecha">
                                <svg class="icon" viewBox="0 0 448 512"><path d="M436 160H12c-6.627 0-12-5.373-12-12v-36c0-26.51 21.49-48 48-48h48V12c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v52h128V12c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v52h48c26.51 0 48 21.49 48 48v36c0 6.627-5.373 12-12 12zM12 192h424c6.627 0 12 5.373 12 12v260c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V204c0-6.627 5.373-12 12-12zm333.296 95.947l-28.169-28.398c-4.667-4.705-12.265-4.736-16.97-.068L194.12 364.665l-45.98-46.352c-4.667-4.705-12.266-4.736-16.971-.068l-28.397 28.17c-4.705 4.667-4.736 12.265-.068 16.97l82.601 83.269c4.667 4.705 12.265 4.736 16.97.068l142.953-141.805c4.705-4.667 4.736-12.265.068-16.97z"/></svg>
                                <?php echo esc_html($fecha_actividad); ?>
                            </div>

                            <div class="reserva-info-item-plazas">
                                <svg class="icon" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                                <?php echo esc_html($cantidad_comprada); ?>
                            </div>

                            <?php $clase_estado = 'estado-' . sanitize_html_class($estado_wc); ?>
                            <div class="reserva-info-item-estado <?php echo esc_attr($clase_estado); ?>">
                                <?php echo esc_html($estado_traducido); ?>
                            </div>
                            
                        </div>

                        <div class="campo-detalle"><span class="etiqueta">Fecha reserva</span><span class="valor"><?php echo esc_html($fecha_reserva); ?></span></div>
                        <div class="campo-detalle"><span class="etiqueta">Código</span><span class="valor">#<?php echo esc_html($order_id); ?></span></div>
                        <div class="campo-detalle"><span class="etiqueta">Estado</span><span class="valor"><?php echo esc_html($estado_traducido); ?></span></div>
                    </div>
                </div>

                <!-- DETALLES DE LA ACTIVIDAD -->
                <div class="contenedor-detalles">
                    <h1>Detalles de la actividad</h1>
                    <div class="contenido-detalles">
                        <div class="campo-detalle"><span class="etiqueta">Fecha</span><span class="valor"><?php echo esc_html($fecha_actividad); ?></span></div>
                        <div class="campo-detalle"><span class="etiqueta">Hora</span><span class="valor"><?php echo esc_html($hora_raw ?: '—'); ?></span></div>
                        <div class="campo-detalle"><span class="etiqueta">Grupo máximo</span><span class="valor"><?php echo esc_html($plazas_totales ?: '—'); ?></span></div>
                        <div class="campo-detalle"><span class="etiqueta">Grupo mínimo</span><span class="valor"><?php echo esc_html($plazas_minimas ?: '—'); ?></span></div>
                        <div class="campo-detalle"><span class="etiqueta">Plazas disponibles</span><span class="valor"><?php echo esc_html($plazas_disponibles !== '' ? $plazas_disponibles : '—'); ?></span></div>
                        <div class="campo-detalle"><span class="etiqueta">Estado</span><span class="valor"><?php echo esc_html($estado_actividad ?: '—'); ?></span></div>
                        <div class="campo-detalle-texto">Esta actividad supera el número mínimo de participantes, la salida está garantizada.</div>
                    </div>
                </div>

                <!-- Acompañantes -->
                <?php
                $acompanantes_output = '';
                foreach ($order->get_items() as $item) {
                    $acompanantes = $item->get_meta('Acompañantes', true);
                    if (!empty($acompanantes)) {
                        $lineas = array_filter(array_map('trim', explode("\n", $acompanantes)));
                        $total_acompanantes = count($lineas);
                        $acompanantes_output .= '<div class="contenedor-detalles"><h1>Datos de acompañantes</h1>';
                        foreach ($lineas as $index => $linea) {
                            $partes = explode(' / ', $linea);
                            $nombre = $partes[0] ?? '—';
                            $dni = $partes[1] ?? '—';
                            $telefono = $partes[2] ?? '—';
                            $edad = $partes[3] ?? '—';
                            if ($total_acompanantes > 1) $acompanantes_output .= '<div class="campo-detalle"><strong>Acompañante '.($index+1).':</strong></div>';
                            $acompanantes_output .= '<div class="campo-detalle"><span class="etiqueta">Nombre:</span><span class="valor">'.esc_html($nombre).'</span></div>';
                            $acompanantes_output .= '<div class="campo-detalle"><span class="etiqueta">DNI/NIE:</span><span class="valor">'.esc_html($dni).'</span></div>';
                            $acompanantes_output .= '<div class="campo-detalle"><span class="etiqueta">Teléfono:</span><span class="valor">'.esc_html($telefono).'</span></div>';
                            $acompanantes_output .= '<div class="campo-detalle-end"><span class="etiqueta">Edad:</span><span class="valor">'.esc_html($edad).'</span></div>';
                        }
                        $acompanantes_output .= '</div>';
                    }
                }
                echo $acompanantes_output;
                ?>

            </div>

            <!-- COLUMNA DERECHA -->
            <div class="columna-derecha">

                <!-- Resumen del pago -->
                <div class="contenedor-detalles">
                    <h1>Resumen del pago</h1>
                    <div class="contenido-detalles">
                        <div class="campo-detalle"><span class="etiqueta">Precio actividad</span><span class="valor"><?php echo wc_price($precio_total_actividad); ?></span></div>
                        <div class="campo-detalle"><span class="etiqueta">Importe reserva</span><span class="valor"><?php echo wc_price($precio_unitario_con_iva); ?></span></div>
                        <div class="campo-detalle"><span class="etiqueta">Plazas reservadas</span><span class="valor">x <?php echo esc_html($plazas); ?></span></div>
                        <div class="linea-divisoria"></div>

                        <!-- Bloque Total Actividad -->
                        <div class="campo-detalle">
                            <span class="etiqueta">Total actividad</span>
                            <span class="valor"><?php echo wc_price($precio_total_actividad * $plazas); ?></span>
                        </div>

                        <div class="campo-detalle"><span class="etiqueta">Total reserva</span><span class="valor"><?php echo $total; ?></span></div>
                        <p class="campo-detalle"><span class="etiqueta">Importe pendiente</span><span class="valor"><?php echo wc_price($falta_por_pagar); ?></span></p>
                        <div class="campo-detalle-texto">IVA incluído en todos los precios.<br>El día de la actividad deberás pagar al guía al contado el importe pendiente.</div>
                        <div class="campo-detalle-texto">Tu pago se hará efectivo en tu tarjeta cuando finalice el plazo límite de cancelación gratuita.</div>
                    </div>
                </div>

                <!-- Cancelación -->
                <div class="contenedor-detalles">
                    <h1>Cancelación</h1>
                    <div class="contenido-detalles" style="padding: 10px 0;">
                        <?php
                        $pedido_cancelado = ($order->get_status() === 'cancelled');
                        $cancelacion_vencida = false;
                        if ($limite_cancelacion_raw) {
                            $timestamp_limite = strtotime(str_replace('/', '-', $limite_cancelacion_raw));
                            $ahora = current_time('timestamp');
                            if ($ahora > $timestamp_limite) $cancelacion_vencida = true;
                        }

                        if ($pedido_cancelado) {
                            echo '<div class="texto-cancelacion cancelada">Esta reserva ha sido cancelada.</div>';
                        } elseif ($cancelacion_vencida) {
                            echo '<div class="texto-cancelacion vencida">La fecha límite de cancelación ha vencido, ya no se puede cancelar la reserva.</div>';
                        } else {
                            echo '<div class="texto-cancelacion">Puedes cancelar tu reserva hasta el '.$limite_cancelacion_texto.'</div>';
                        }

                        if (!$pedido_cancelado && !$cancelacion_vencida) {
                            echo '<div class="boton-cancelar-wrapper"><button class="boton-cancelar-reserva" id="boton-cancelar-reserva">Cancelar reserva</button></div>';
                        }
                        ?>
                    </div>
                </div>

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