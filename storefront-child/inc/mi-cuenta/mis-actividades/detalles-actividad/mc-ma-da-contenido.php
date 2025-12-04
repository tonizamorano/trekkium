<?php
function contenido_detalles_actividad_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>Debes iniciar sesión para ver los detalles de la actividad.</p>';
    }

    // Obtener ID desde la URL
    $actividad_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if (!$actividad_id) {
        return '<p>No se ha especificado ninguna actividad.</p>';
    }

    // Verificar que el post exista y sea del tipo correcto
    $actividad = get_post($actividad_id);
    if (!$actividad || $actividad->post_type !== 'product') {
        return '<p>La actividad no existe o no es válida.</p>';
    }

    // Obtener campos meta
    $fecha = get_post_meta($actividad_id, 'fecha', true);
    $hora = get_post_meta($actividad_id, 'hora', true);
    $estado_actividad = get_post_meta($actividad_id, 'estado_actividad', true);

    // Traducir el estado de publicación
    $estado_publicacion = traducir_estado(get_post_status($actividad_id));

    ob_start();
    ?>

    <!-- Sección detalles de la actividad -->

    <div>
        <div class="detalles-actividad-seccion-titular">
            <h2 class="detalles-actividad-titular">Detalles de la actividad</h2>    
        </div>

        <div class="detalles-actividad-contenido">
            <h2 class="detalles-actividad-titulo"><?php echo esc_html(get_the_title($actividad_id)); ?></h2>

            <div class="detalles-actividad-item">
                <strong>Fecha:</strong> <?php echo $fecha ? esc_html(date_i18n('d/m/Y', strtotime($fecha))) : 'No especificada'; ?>
            </div>

            <div class="detalles-actividad-item">
                <strong>Hora:</strong> <?php echo $hora ? esc_html($hora) : 'No especificada'; ?>
            </div>

            <div class="detalles-actividad-item">
                <strong>Estado de la publicación:</strong> <?php echo esc_html($estado_publicacion); ?>
            </div>

            <div class="detalles-actividad-item">
                <strong>Estado de la actividad:</strong> <?php echo $estado_actividad ? esc_html($estado_actividad) : 'No especificado'; ?>
            </div>
        </div>
    </div>

    <!-- Sección Lista de participantes -->

    <div style="margin-top:15px;">
        <div class="detalles-actividad-seccion-titular">
            <h2 class="detalles-actividad-titular">Lista de participantes</h2>    
        </div>

        <div class="detalles-actividad-contenido">
            <div class="participantes-grid">
                <?php
                global $wpdb;

                $query = "
                    SELECT DISTINCT o.ID 
                    FROM {$wpdb->posts} o
                    INNER JOIN {$wpdb->prefix}woocommerce_order_items oi ON o.ID = oi.order_id
                    INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id
                    WHERE o.post_type = 'shop_order'
                    AND o.post_status IN ('wc-processing', 'wc-on-hold', 'wc-completed')
                    AND oim.meta_key IN ('_product_id', '_variation_id')
                    AND oim.meta_value = %d
                    ORDER BY o.ID DESC
                ";

                $order_ids = $wpdb->get_col($wpdb->prepare($query, $actividad_id));

                if ($order_ids) {
                    foreach ($order_ids as $order_id) {
                        $order = wc_get_order($order_id);
                        if (!$order) continue;

                        $user_id = $order->get_user_id();
                        if ($user_id) {
                            $user_info = get_userdata($user_id);
                            $telefono = get_user_meta($user_id, 'billing_phone', true);
                            $email = $user_info->user_email;
                            $nombre = get_user_meta($user_id, 'first_name', true);
                            $apellido = get_user_meta($user_id, 'last_name', true);
                            $nombre_completo = trim($nombre . ' ' . $apellido);
                            if (empty($nombre_completo)) {
                                $nombre_completo = $user_info->display_name ?: 'Usuario #' . $user_id;
                            }

                            $avatar_id = get_user_meta($user_id, 'avatar_del_usuario', true);
                            $avatar_url = !empty($avatar_id) && is_numeric($avatar_id) ? wp_get_attachment_image_url($avatar_id, 'thumbnail') : '';

                            $telefono_formateado = 'No especificado';
                            if (!empty($telefono)) {
                                $telefono = preg_replace('/\s+/', '', $telefono);
                                $telefono_formateado = trim(chunk_split($telefono, 3, ' '));
                            }

                            $numero_reserva = $order_id;
                            $estado_reserva = wc_get_order_status_name($order->get_status());
                            $order_status = $order->get_status();
                            $clase_estado = in_array($order_status, ['processing', 'on-hold']) ? 'participante-item-estado-pendiente' : 'participante-item-estado-completado';
                            ?>
                            <div class="participante-item <?php echo esc_attr($clase_estado); ?>">
                                <div class="participante-avatar-col">
                                    <?php if (!empty($avatar_url)): ?>
                                        <img src="<?php echo esc_url($avatar_url); ?>" alt="Avatar de <?php echo esc_attr($nombre_completo); ?>" class="participante-avatar">
                                    <?php else: ?>
                                        <div class="participante-avatar-placeholder"><?php echo get_avatar($user_id, 50); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="participante-info-col">
                                    <div class="participante-nombre"><?php echo esc_html($nombre_completo); ?></div>
                                    <div class="participante-telefono"><?php echo esc_html($telefono_formateado); ?></div>
                                    <div class="participante-numero-reserva">Reserva Nº <?php echo esc_html($numero_reserva); ?></div>
                                    <div class="participante-estado-reserva"><?php echo esc_html($estado_reserva); ?></div>
                                </div>
                            </div>
                            <?php
                        }

                        // ---- Mostrar Acompañantes ----
                        $acompanantes = get_post_meta($order_id, '_trekkium_acompanantes', true);
                        if (!empty($acompanantes) && is_array($acompanantes)) {
                            foreach ($acompanantes as $index => $acompanante) {
                                $nombre_completo = trim(($acompanante['nombre'] ?? '') . ' ' . ($acompanante['apellido'] ?? ''));
                                if (empty($nombre_completo)) $nombre_completo = 'Acompañante #' . ($index + 1);

                                $telefono = $acompanante['telefono'] ?? 'No especificado';
                                $telefono = preg_replace('/\s+/', '', $telefono);
                                $telefono_formateado = $telefono !== 'No especificado' ? chunk_split($telefono, 3, ' ') : $telefono;

                                $email = $acompanante['email'] ?? 'No especificado';
                                $avatar_url = ''; // Avatar genérico para acompañante
                                ?>
                                <div class="participante-item participante-item-estado-completado">
                                    <div class="participante-avatar-col">
                                        <div class="participante-avatar-placeholder"><span style="font-size:24px; color:#888;">👤</span></div>
                                    </div>
                                    <div class="participante-info-col">
                                        <div class="participante-nombre"><?php echo esc_html($nombre_completo); ?></div>
                                        <div class="participante-telefono"><?php echo esc_html($telefono_formateado); ?></div>
                                        <div class="participante-numero-reserva">Reserva Nº <?php echo esc_html($order_id); ?></div>
                                        <div class="participante-estado-reserva">Acompañante</div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    }
                } else {
                    echo '<p>Todavía no se ha realizado ninguna reserva para esta actividad.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <?php
    return ob_get_clean();
}

add_shortcode('contenido_detalles_actividad', 'contenido_detalles_actividad_shortcode');
