<?php
// Shortcode independiente para Detalles de la reserva
add_shortcode('mc_vr_detalles_reserva', 'mc_vr_mostrar_detalles_reserva');

function mc_vr_mostrar_detalles_reserva() {

    if (!is_user_logged_in()) return '';

    $order_id = get_query_var('ver-reservas');
    if (!$order_id) return '';

    $order = wc_get_order($order_id);
    if (!$order) return '';

    $items = $order->get_items();
    $actividad = reset($items);
    if (!$actividad) return '';

    $producto_nombre = $actividad->get_name();
    $product_id = $actividad->get_product_id();
    $precio_unitario_con_iva = ($actividad->get_total() + $actividad->get_total_tax()) / $actividad->get_quantity();

    $parent_product_id = wp_get_post_parent_id($product_id);
    if ($parent_product_id) $product_id = $parent_product_id;

    // Metadatos
    $fecha_raw = get_post_meta($product_id, 'fecha', true);
    $hora_meta = get_post_meta($product_id, 'hora', true);

    $fecha_actividad = $fecha_raw ? date('d/m/Y', strtotime($fecha_raw)) : '—';
    $hora_actividad = $hora_meta ? esc_html($hora_meta) . 'h' : '—';

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

    ob_start(); ?>

    <div class="mc-mr-vr-contenedor">

        <?php if (has_post_thumbnail($product_id)) : ?>
            <div class="imagen-actividad" style="position: relative;">
                <?php echo get_the_post_thumbnail($product_id,'large',[
                    'style'=>'width:100%;aspect-ratio:16/9;border-radius:0;object-fit:cover;'
                ]); ?>
                
                <!-- Estado de la reserva sobre la imagen -->
                <div class="mc-mr-vr-estado-sobre-imagen"><?php echo esc_html($estado_traducido); ?></div>
            </div>
        <?php endif; ?>

        <div class="mc-mr-vr-contenido"> 
            
            <!-- Número de reserva -->
            <div class="mc-mr-vr-numero-reserva">
                Reserva Nº <?php echo esc_html($order_id); ?>
            </div>

            <div class="mc-mr-vr-titulo-reserva"><?php echo esc_html($producto_nombre); ?></div>

            <?php
            // Obtener provincia y región
            $provincia_terms = wp_get_post_terms($product_id, 'provincia');
            $region_terms = wp_get_post_terms($product_id, 'region');

            $provincia = !empty($provincia_terms) ? $provincia_terms[0]->name : '—';
            $region = !empty($region_terms) ? $region_terms[0]->name : '—';
            ?>

            <div class="mc-mr-vr-ubicacion-actividad">
                <?php echo esc_html($provincia . ' (' . $region . ')'); ?>
            </div>

            <!-- Nueva fila: Fecha y Hora del producto -->
            <div class="mc-mr-vr-fila-datos">
                <span class="etiqueta">Fecha</span><span class="valor"><?php echo esc_html($fecha_actividad); ?></span>
            </div>
            <div class="mc-mr-vr-fila-datos">
                <span class="etiqueta">Hora</span><span class="valor"><?php echo esc_html($hora_actividad); ?></span>
            </div>

            <!-- Botón Ver Actividad solo si está publicado -->
            <?php if (get_post_status($product_id) === 'publish') : ?>
                <div class="mc-mr-vr-boton-wrapper">
                    <a href="<?php echo get_permalink($product_id); ?>" class="mc-mr-vr-boton">Ver actividad</a>
                </div>
            <?php endif; ?>

        </div>

    </div>

    <?php
    return ob_get_clean();
}
