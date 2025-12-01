<?php
/* Shortcode unificado para el dashboard de productos (listado + detalles)
   Sustituye admin-dashboard-productos.php por este archivo.
*/

/* -----------------------
   Shortcode
   ----------------------- */
add_shortcode('admin_dashboard_productos', function() {
    if ( isset($_GET['view']) && $_GET['view'] === 'detalles' && isset($_GET['product_id']) ) {
        return mostrar_detalles_producto();
    } else {
        return mostrar_listado_productos();
    }
});

/* -----------------------
   Helpers
   ----------------------- */

// Intenta parsear una fecha con varios formatos y devuelve DateTime|false
function adm_parse_date_to_datetime($date_str) {
    if (empty($date_str)) return false;
    $formats = ['Y-m-d','d/m/Y','d-m-Y','Y/m/d','d.m.Y'];
    foreach ($formats as $fmt) {
        $dt = DateTime::createFromFormat($fmt, $date_str);
        if ($dt && $dt->format($fmt) === $date_str) {
            return $dt;
        }
    }
    try {
        return new DateTime($date_str);
    } catch (Exception $e) {
        return false;
    }
}

/* -----------------------
   Listado de productos
   ----------------------- */
function mostrar_listado_productos() {
    ob_start();

    $current_url = get_permalink( get_queried_object_id() );

    $args = [
        'post_type' => 'product',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ];
    $productos = new WP_Query($args);
    ?>

    <div class="admin-dashboard-contenido">
        <table id="tabla-productos" class="tabla-horizontal">
            <thead>
                <tr>
                    <th data-sort="title" data-sort-type="text">Título <span class="flechas">▲▼</span></th>
                    <th data-sort="author" data-sort-type="text">Autor <span class="flechas">▲▼</span></th>
                    <th data-sort="modalidad" data-sort-type="text">Modalidad <span class="flechas">▲▼</span></th>
                    <th data-sort="fecha" data-sort-type="date">Fecha <span class="flechas">▲▼</span></th>
                    <th data-sort="estado" data-sort-type="text">Estado <span class="flechas">▲▼</span></th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($productos->have_posts()): ?>
                    <?php while ($productos->have_posts()): $productos->the_post(); ?>
                        <?php
                        $post_id = get_the_ID();
                        $fecha = get_post_meta($post_id, 'fecha', true);
                        $dobj = adm_parse_date_to_datetime($fecha);
                        $fecha_orden = $dobj ? $dobj->format('Y-m-d') : '0000-00-00';

                        $terms_modalidad = get_the_terms($post_id, 'modalidad');
                        $modalidades_arr = $terms_modalidad ? wp_list_pluck($terms_modalidad, 'name') : [];
                        $modalidades_str = $modalidades_arr ? implode(', ', $modalidades_arr) : '';
                        $author_display = get_the_author_meta('display_name');
                        $status = get_post_status($post_id);
                        ?>
                        <tr data-title="<?php echo esc_attr(get_the_title()); ?>"
                            data-author="<?php echo esc_attr($author_display); ?>"
                            data-modalidad="<?php echo esc_attr($modalidades_str); ?>"
                            data-fecha="<?php echo esc_attr($fecha_orden); ?>"
                            data-estado="<?php echo esc_attr($status); ?>">
                            <td><?php echo esc_html(get_the_title()); ?></td>
                            <td><?php echo esc_html($author_display); ?></td>
                            <td><?php echo esc_html($modalidades_str ?: '—'); ?></td>
                            <td>
                                <?php
                                    if ($fecha) {
                                        echo $dobj ? esc_html($dobj->format('d/m/Y')) : esc_html($fecha);
                                    } else {
                                        echo '—';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    switch ($status) {
                                        case 'publish': echo 'Publicado'; break;
                                        case 'draft': echo 'Borrador'; break;
                                        case 'pending': echo 'Pendiente'; break;
                                        default: echo esc_html(ucfirst($status)); break;
                                    }
                                ?>
                            </td>
                            <td>
                                <a href="<?php echo esc_url(get_edit_post_link($post_id)); ?>" class="btn-editar" target="_blank" rel="noopener noreferrer">Editar</a>
                                <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="btn-ver" target="_blank" rel="noopener noreferrer">Ver</a>
                                <a href="<?php echo esc_url(add_query_arg(['product_id' => $post_id, 'view' => 'detalles'], $current_url)); ?>" class="btn-detalles">Detalles</a>
                            </td>
                        </tr>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else: ?>
                    <tr><td colspan="6">No hay productos.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('tabla-productos');
        if (!table) return;
        const headers = table.querySelectorAll('th[data-sort]');
        let currentSort = { column: null, direction: 'asc' };

        headers.forEach(header => {
            header.addEventListener('click', function() {
                const column = this.getAttribute('data-sort');
                const type = this.getAttribute('data-sort-type');
                headers.forEach(h => h.classList.remove('sorted-asc', 'sorted-desc'));
                if (currentSort.column === column) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.column = column;
                    currentSort.direction = 'asc';
                }
                this.classList.add(currentSort.direction === 'asc' ? 'sorted-asc' : 'sorted-desc');
                sortTable(column, currentSort.direction, type);
            });
        });

        function sortTable(column, direction, type) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.sort((a, b) => {
                let aValue = a.getAttribute('data-' + column) || '';
                let bValue = b.getAttribute('data-' + column) || '';
                if (type === 'date') {
                    aValue = aValue || '0000-00-00';
                    bValue = bValue || '0000-00-00';
                } else if (type === 'text') {
                    aValue = aValue.toLowerCase();
                    bValue = bValue.toLowerCase();
                }
                if (direction === 'asc') {
                    return aValue < bValue ? -1 : aValue > bValue ? 1 : 0;
                } else {
                    return aValue > bValue ? -1 : aValue < bValue ? 1 : 0;
                }
            });
            // Vaciar y reinsertar filas
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
        }
    });
    </script>

    <?php
    return ob_get_clean();
}

/* -----------------------
   Detalles de producto
   ----------------------- */
function mostrar_detalles_producto() {
    ob_start();

    $product_id = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;
    if (!$product_id) {
        echo '<p>No se ha especificado un producto.</p>';
        return ob_get_clean();
    }

    if (! current_user_can('edit_post', $product_id) ) {
        echo '<p>No tienes permiso para ver este producto.</p>';
        return ob_get_clean();
    }

    $producto = get_post($product_id);
    if (!$producto) {
        echo '<p>Producto no encontrado.</p>';
        return ob_get_clean();
    }

    $autor = get_the_author_meta('display_name', $producto->post_author);
    $modalidad = wp_get_post_terms($product_id, 'modalidad', ['fields' => 'names']);
    $pais = wp_get_post_terms($product_id, 'pais', ['fields' => 'names']);
    $region = wp_get_post_terms($product_id, 'region', ['fields' => 'names']);
    $provincia = wp_get_post_terms($product_id, 'provincia', ['fields' => 'names']);
    $fecha_meta = get_post_meta($product_id, 'fecha', true);
    $estado_actividad = get_post_meta($product_id, 'estado_actividad', true);
    $plazas_totales = get_post_meta($product_id, 'plazas_totales', true);
    $plazas_minimas = get_post_meta($product_id, 'plazas_minimas', true);

    $fecha_obj = adm_parse_date_to_datetime($fecha_meta);
    $fecha_formateada = $fecha_obj ? $fecha_obj->format('d/m/Y') : ($fecha_meta ?: 'No definida');

    $stock = '—';
    if ( function_exists('wc_get_product') ) {
        $wc_prod = wc_get_product($product_id);
        if ($wc_prod) {
            $stock_qty = $wc_prod->get_stock_quantity();
            $stock = is_null($stock_qty) ? '—' : $stock_qty;
        }
    }

    $show_debug = current_user_can('manage_options') || (defined('WP_DEBUG') && WP_DEBUG);

    global $wpdb;
    $order_items_table = $wpdb->prefix . 'woocommerce_order_items';
    $order_itemmeta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';

    $sql = $wpdb->prepare("
        SELECT DISTINCT oi.order_id
        FROM {$order_items_table} oi
        INNER JOIN {$order_itemmeta_table} oim ON oi.order_item_id = oim.order_item_id
        WHERE oim.meta_key IN ('_product_id','product_id') AND oim.meta_value = %d
    ", $product_id);

    $order_ids = $wpdb->get_col($sql);

    $clientes_producto = [];
    if (!empty($order_ids) && function_exists('wc_get_order')) {
        $dni_keys = ['dni', '_billing_dni', 'billing_dni', 'user_dni', 'customer_dni', 'dni_number'];
        $birth_keys = ['fecha_nacimiento', '_billing_fecha_nacimiento', 'birth_date', 'fecha_de_nacimiento', 'birthdate', 'dob'];

        foreach ($order_ids as $oid) {
            $order = wc_get_order($oid);
            if (! $order ) continue;
            $found = false;

            foreach ($order->get_items() as $item) {
                // item puede ser WC_Order_Item_Product u otro tipo
                if ( method_exists($item, 'get_product_id') ) {
                    $item_prod_id = $item->get_product_id();
                } else {
                    $item_prod_id = isset($item['product_id']) ? $item['product_id'] : 0;
                }

                if ( $item_prod_id != $product_id ) continue;

                $user_id = $order->get_user_id();

                // DNI: busca en user_meta y en order meta usando varias claves
                $dni = '';
                foreach ($dni_keys as $key) {
                    if ($user_id) {
                        $dni_test = get_user_meta($user_id, $key, true);
                        if (!empty($dni_test)) { $dni = $dni_test; break; }
                    }
                    $dni_test = $order->get_meta($key, true);
                    if (!empty($dni_test)) { $dni = $dni_test; break; }
                }

                // Fecha de nacimiento: busca en user_meta y order meta
                $fecha_nacimiento = '';
                foreach ($birth_keys as $key) {
                    if ($user_id) {
                        $birth_test = get_user_meta($user_id, $key, true);
                        if (!empty($birth_test)) { $fecha_nacimiento = $birth_test; break; }
                    }
                    $birth_test = $order->get_meta($key, true);
                    if (!empty($birth_test)) { $fecha_nacimiento = $birth_test; break; }
                }

                $telefono = $order->get_billing_phone() ?: 
                            $order->get_meta('_billing_phone', true) ?: 
                            $order->get_meta('billing_phone', true) ?: 
                            ($user_id ? get_user_meta($user_id, 'billing_phone', true) : '');

                if (empty($telefono)) $telefono = '—';

                $clientes_producto[] = [
                    'order_id' => $order->get_id(),
                    'user_id' => $user_id,
                    'nombre' => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
                    'telefono' => $telefono,
                    'dni' => $dni,
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'estado' => $order->get_status(),
                    'debug_user_meta' => $show_debug && $user_id ? get_user_meta($user_id) : [],
                    'debug_order_meta' => $show_debug ? $order->get_meta_data() : []
                ];

                $found = true;
                break; // si el pedido contiene el producto ya lo añadimos y pasamos al siguiente pedido
            }

            if (!$found) continue;
        }
    }

    $current_url = get_permalink( get_queried_object_id() );
    ?>

    <div class="admin-dashboard-contenido">
        <div class="detalles-contenedor-titular">
            <div class="detalles-titular"><?php echo esc_html( $producto->post_title ); ?></div>
        </div>

        <div class="detalles-grid">
            <div>
                <table class="tabla-vertical">
                    <tr><th>Autor:</th><td><?php echo esc_html($autor ?: '—'); ?></td></tr>
                    <tr><th>Modalidad:</th><td><?php echo esc_html(!empty($modalidad) ? implode(', ', $modalidad) : '—'); ?></td></tr>
                    <tr><th>Fecha:</th><td><?php echo esc_html($fecha_formateada); ?></td></tr>
                </table>
            </div>

            <div>
                <table class="tabla-vertical">
                    <tr><th>País:</th><td><?php echo esc_html(!empty($pais) ? implode(', ', $pais) : '—'); ?></td></tr>
                    <tr><th>Región:</th><td><?php echo esc_html(!empty($region) ? implode(', ', $region) : '—'); ?></td></tr>
                    <tr><th>Provincia:</th><td><?php echo esc_html(!empty($provincia) ? implode(', ', $provincia) : '—'); ?></td></tr>
                </table>
            </div>

            <div>
                <table class="tabla-vertical">
                    <tr><th>Estado:</th><td><?php echo esc_html($estado_actividad ?: '—'); ?></td></tr>
                    <tr><th>Grupo máx:</th><td><?php echo esc_html($plazas_totales ?: '—'); ?></td></tr>
                    <tr><th>Grupo mín:</th><td><?php echo esc_html($plazas_minimas ?: '—'); ?></td></tr>
                    <tr><th>Stock:</th><td><?php echo esc_html($stock !== null ? $stock : '—'); ?></td></tr>
                </table>
            </div>
        </div>

        <table class="tabla-horizontal">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>DNI</th>
                    <th>Edad</th>
                    <th>Nº Pedido</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($clientes_producto)): ?>
                    <?php foreach ($clientes_producto as $cliente): ?>
                        <tr>
                            <td><?php echo esc_html($cliente['nombre'] ?: '—'); ?></td>
                            <td><?php echo esc_html($cliente['telefono'] ?: '—'); ?></td>
                            <td><?php echo esc_html($cliente['dni'] ?: '—'); ?></td>
                            <td>
                                <?php
                                    if (!empty($cliente['fecha_nacimiento'])) {
                                        $fn_obj = adm_parse_date_to_datetime($cliente['fecha_nacimiento']);
                                        if ($fn_obj) {
                                            echo esc_html((new DateTime())->diff($fn_obj)->y . ' años');
                                        } else {
                                            echo 'Fecha inválida';
                                        }
                                    } else {
                                        echo 'No disponible';
                                    }
                                ?>
                            </td>
                            <td><?php echo esc_html($cliente['order_id']); ?></td>
                            <td>
                                <?php
                                    switch($cliente['estado']) {
                                        case 'completed': echo 'Completado'; break;
                                        case 'processing': echo 'Procesando'; break;
                                        case 'pending': echo 'Pendiente de pago'; break;
                                        case 'on-hold': echo 'En espera'; break;
                                        case 'cancelled': echo 'Cancelado'; break;
                                        case 'refunded': echo 'Reembolsado'; break;
                                        case 'failed': echo 'Fallido'; break;
                                        default: echo esc_html(ucfirst($cliente['estado'])); break;
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No hay clientes registrados para este producto.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="boton-volver">
            <a href="<?php echo esc_url(remove_query_arg(['product_id','view'], $current_url)); ?>" class="btn-volver">← Volver al listado</a>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
