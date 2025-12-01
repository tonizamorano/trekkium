<?php
/* Shortcode: Admin Dashboard - Guías */
add_shortcode('admin_dashboard_guias', function() {
    ob_start();

    // Obtener usuarios con rol "guia"
    $args = [
        'role'    => 'guia',
        'orderby' => 'display_name',
        'order'   => 'ASC'
    ];
    $guias = get_users($args);
    ?>

    <div class="admin-dashboard-contenido">
        <table class="tabla-horizontal">
            <thead>
                <tr>
                    <th data-sort="nombre">Nombre <span class="flechas">▲▼</span></th>
                    <th data-sort="titulacion">Titulación <span class="flechas">▲▼</span></th>
                    <th data-sort="provincia">Provincia <span class="flechas">▲▼</span></th>
                    <th data-sort="fecha">Fecha alta <span class="flechas">▲▼</span></th>
                    <th data-sort="actividades">Actividades <span class="flechas">▲▼</span></th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($guias)): ?>
                    <?php foreach ($guias as $guia): ?>
                        <tr>
                            <!-- Nombre -->
                            <td><?php echo esc_html($guia->display_name); ?></td>

                            <!-- Titulación -->
                            <td>
                                <?php
                                    $terms_titulacion = wp_get_object_terms($guia->ID, 'titulacion', ['fields' => 'names']);
                                    if (!is_wp_error($terms_titulacion) && !empty($terms_titulacion)) {
                                        echo implode(', ', $terms_titulacion);
                                    }
                                ?>
                            </td>

                            <!-- Provincia -->
                            <td>
                                <?php 
                                    $provincia = get_user_meta($guia->ID, 'billing_state', true);
                                    echo $provincia ? esc_html($provincia) : '-';
                                ?>
                            </td>

                            <!-- Fecha de alta -->
                            <td>
                                <?php
                                    $fecha = $guia->user_registered;
                                    if ($fecha) {
                                        $date_obj = date_create($fecha);
                                        echo date_format($date_obj, 'd/m/y'); // solo dos cifras de año
                                    }
                                ?>
                            </td>

                            <!-- Actividades publicadas -->
                            <td>
                                <?php 
                                    $count = count_user_posts($guia->ID, 'product', true);
                                    echo intval($count);
                                ?>
                            </td>

                            <!-- Acciones -->
                            <td>
                                <a href="<?php echo admin_url('user-edit.php?user_id=' . $guia->ID); ?>" class="btn-editar" target="_blank">Editar</a>
                                <a href="<?php echo get_author_posts_url($guia->ID); ?>" class="btn-ver" target="_blank">Ver</a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No hay guías registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Ordenamiento simple por columnas
        document.addEventListener('DOMContentLoaded', function() {
            const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
            const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
                !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
            )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

            document.querySelectorAll('.admin-dashboard-guias th[data-sort]').forEach(th => {
                th.addEventListener('click', () => {
                    const table = th.closest('table');
                    Array.from(table.querySelectorAll('tbody tr'))
                        .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                        .forEach(tr => table.querySelector('tbody').appendChild(tr));
                });
            });
        });
    </script>

    <?php
    return ob_get_clean();
});
