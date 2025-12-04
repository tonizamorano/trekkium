<?php
add_shortcode('seccion_mis_reservas', 'mostrar_reservas_directamente');

function mostrar_reservas_directamente() {
    if (!is_user_logged_in()) {
        return '<p>Debes iniciar sesión para ver tus reservas.</p>';
    }

    // 1️⃣ Obtener todas las reservas (pedidos) del usuario actual
    $orders = obtener_ordenes_usuario_actual();

    // 2️⃣ Construir array con todas las actividades y sus fechas
    $reservas = [];

    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if (!$product) continue;

            $product_id = $product->get_id();
            $fecha = get_post_meta($product_id, 'fecha', true);
            $timestamp = $fecha ? strtotime($fecha) : 0;

            $reservas[] = [
                'order'   => $order,
                'product' => $product,
                'fecha'   => $timestamp,
            ];
        }
    }

    // 3️⃣ Ordenar por la fecha más próxima
    usort($reservas, function($a, $b) {
        if ($a['fecha'] == $b['fecha']) return 0;
        if ($a['fecha'] == 0) return 1; // los sin fecha al final
        if ($b['fecha'] == 0) return -1;
        return $a['fecha'] - $b['fecha'];
    });

    ob_start();
    ?>
    <div class="reservas-container">

        <div class="reservas-container-titulo">
            <h2>Mis reservas</h2>
        </div>

        <?php if (empty($reservas)): ?>

                <div class="contenedor-sin-reservas">

                <span>En este momento no tienes ninguna actividad reservada.</span>


                </div>
        
        <?php else: ?>

        <div class="grid-reservas">

                <?php 
                foreach ($reservas as $r) {
                    echo generar_tarjeta_individual($r['product'], $r['order']);
                }
                ?>
        <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function obtener_ordenes_usuario_actual() {
    $user_id = get_current_user_id();

    return wc_get_orders([
        'customer_id' => $user_id,
        'limit'       => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'status'      => ['pending', 'processing', 'completed'], 
    ]);
}

function generar_tarjeta_individual($product, $order) {
    $nombre = esc_html($product->get_name());
    $product_id = $product->get_id();
    $order_id = $order->get_id();
    
    // Obtener la URL de la página de detalles de la reserva
    $url_detalles = wc_get_account_endpoint_url('ver-reservas') . $order_id . '/';

    $imagen_html = obtener_imagen_producto($product_id, $nombre);
    $fecha_actividad = get_post_meta($product_id, 'fecha', true);
    $fecha_actividad = $fecha_actividad ? date_i18n('d/m/y', strtotime($fecha_actividad)) : 'Sin fecha';

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
    
    // Añade la clase CSS basada en el estado para estilos
    $clase_estado = 'estado-' . sanitize_html_class($estado_wc);

    $cantidad_comprada = 0;
    foreach ($order->get_items() as $item) {
        if ($item->get_product_id() == $product_id) {
            $cantidad_comprada = $item->get_quantity();
            break;
        }
    }

    ob_start();
    ?>
    <a href="<?php echo esc_url($url_detalles); ?>" class="card-reserva-link">
        <div class="card-reserva">
            <div class="card-reserva-imagen">
                <?php echo $imagen_html; ?>
                <div class="reserva-info-item-estado <?php echo esc_attr($clase_estado); ?>">
                    <?php echo esc_html($estado_traducido); ?>
                </div>
            </div>

            <div class="card-reserva-info">

                <div class="numero-reserva">
                    <h4>Reserva Nº <?php echo $order_id; ?></h4>
                </div>

                <div class="reserva-info-titulo">
                    <h3><?php echo $nombre; ?></h3>
                </div>                

                <div class="reserva-info-extra">

                    <div class="reserva-info-item-fecha">
                        <svg class="icon" viewBox="0 0 448 512"><path d="M436 160H12c-6.627 0-12-5.373-12-12v-36c0-26.51 21.49-48 48-48h48V12c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v52h128V12c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v52h48c26.51 0 48 21.49 48 48v36c0 6.627-5.373 12-12 12zM12 192h424c6.627 0 12 5.373 12 12v260c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V204c0-6.627 5.373-12 12-12zm333.296 95.947l-28.169-28.398c-4.667-4.705-12.265-4.736-16.97-.068L194.12 364.665l-45.98-46.352c-4.667-4.705-12.266-4.736-16.971-.068l-28.397 28.17c-4.705 4.667-4.736 12.265-.068 16.97l82.601 83.269c4.667 4.705 12.265 4.736 16.97.068l142.953-141.805c4.705-4.667 4.736-12.265.068-16.97z"/></svg>
                        <?php echo esc_html($fecha_actividad); ?>
                    </div>

                    <div class="reserva-info-item-plazas">
                        <svg class="icon" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                        <?php echo esc_html($cantidad_comprada); ?>
                    </div>
                    
                </div>

            </div>
        </div>
    </a>
    <?php
    return ob_get_clean();
}

function obtener_imagen_producto($product_id, $nombre) {
    $imagen_url = get_the_post_thumbnail_url($product_id, 'medium_large');
    if ($imagen_url) {
        return '<img src="' . esc_url($imagen_url) . '" class="imagen-actividad" alt="' . esc_attr($nombre) . '" />';
    }
    return '<div class="sin-imagen">Sin imagen</div>';
}
