<?php

function mc_ma_da_lista_participantes_shortcode($atts) {
    global $wpdb;

    $atts = shortcode_atts([
        'id' => 0
    ], $atts);

    $actividad_id = intval($atts['id']);

    if (!$actividad_id) {
        return '<p>No se ha especificado ninguna actividad.</p>';
    }

    ob_start();
    ?>

    <div clas="mc-ma-da-contenedor">

        <div class="mc-ma-da-titular">
            <h2 style="text-align:center;">Lista de participantes</h2>
        </div>

        <div class="mc-ma-da-contenido">

            <div class="participantes-grid">

                <?php
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
                            $avatar_url = (!empty($avatar_id) && is_numeric($avatar_id)) ? wp_get_attachment_image_url($avatar_id, 'thumbnail') : '';

                            $telefono_formateado = 'No especificado';
                            if (!empty($telefono)) {
                                $telefono = preg_replace('/\s+/', '', $telefono);
                                $telefono_formateado = trim(chunk_split($telefono, 3, ' '));
                            }

                            $numero_reserva = $order_id;
                            $estado_reserva = wc_get_order_status_name($order->get_status());
                            $order_status = $order->get_status();
                            $clase_estado = in_array($order_status, ['processing', 'on-hold']) ?
                                'participante-item-estado-pendiente' :
                                'participante-item-estado-completado';
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

                        // ---- Acompañantes ----
                        $acompanantes = get_post_meta($order_id, '_trekkium_acompanantes', true);

                        if (!empty($acompanantes) && is_array($acompanantes)) {
                            foreach ($acompanantes as $index => $ac) {

                                $nombre_completo = trim(
                                    ($ac['nombre'] ?? '') . ' ' . ($ac['apellido'] ?? '')
                                );

                                if (empty($nombre_completo)) {
                                    $nombre_completo = 'Acompañante #' . ($index + 1);
                                }

                                $telefono = $ac['telefono'] ?? 'No especificado';
                                $telefono = preg_replace('/\s+/', '', $telefono);
                                $telefono_formateado = $telefono !== 'No especificado'
                                    ? chunk_split($telefono, 3, ' ')
                                    : $telefono;
                                ?>

                                <div class="participante-item participante-item-estado-completado">
                                    <div class="participante-avatar-col">
                                        <div class="participante-avatar-placeholder">
                                            <span style="font-size:24px; color:#888;">👤</span>
                                        </div>
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

add_shortcode('mc_ma_da_lista_participantes', 'mc_ma_da_lista_participantes_shortcode');
