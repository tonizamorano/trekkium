<?php

// Shortcode [seccion_entradas_relacionadas]
add_shortcode('seccion_entradas_relacionadas', 'trekkium_seccion_entradas_relacionadas');
function trekkium_seccion_entradas_relacionadas() {

    if (!is_singular('product')) return ''; // Solo mostrar en single de producto

    global $post, $product;

    ob_start();

    $current_id = get_the_ID();

    // Datos del producto actual
    $modalidades = get_the_terms($current_id, 'modalidad');
    $regiones    = get_the_terms($current_id, 'region');
    $autor_id    = get_the_author_meta('ID');

    $modalidad_ids = !empty($modalidades) ? wp_list_pluck($modalidades, 'term_id') : [];
    $region_ids    = !empty($regiones) ? wp_list_pluck($regiones, 'term_id') : [];

    // Array para mantener los IDs ya mostrados
    $mostrar_ids = [];

    // Función para ejecutar queries con distintos criterios
    function trekkium_query_actividades($tax_query_args = [], $author = null, $exclude_ids = [], $limit = 10) {
        $meta_query = [
            'relation' => 'OR',
            [
                'key'     => 'fecha',
                'compare' => 'EXISTS',
            ]
        ];

        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'post__not_in'   => $exclude_ids,
            'orderby'        => 'meta_value',
            'meta_key'       => 'fecha',
            'order'          => 'ASC',
            'meta_query'     => $meta_query,
        ];

        if (!empty($tax_query_args)) {
            $args['tax_query'] = $tax_query_args;
        }

        if ($author) {
            $args['author'] = $author;
        }

        return new WP_Query($args);
    }

    // 1) Misma región y modalidad
    if (!empty($region_ids) && !empty($modalidad_ids)) {
        $tax_query = [
            'relation' => 'AND',
            [
                'taxonomy' => 'region',
                'field'    => 'term_id',
                'terms'    => $region_ids,
            ],
            [
                'taxonomy' => 'modalidad',
                'field'    => 'term_id',
                'terms'    => $modalidad_ids,
            ]
        ];
        $query1 = trekkium_query_actividades($tax_query, null, $mostrar_ids, 10);
    } else {
        $query1 = null;
    }

    // 2) Misma región
    if (!empty($region_ids)) {
        $tax_query = [
            [
                'taxonomy' => 'region',
                'field'    => 'term_id',
                'terms'    => $region_ids,
            ]
        ];
        $query2 = trekkium_query_actividades($tax_query, null, $mostrar_ids, 10);
    } else {
        $query2 = null;
    }

    // 3) Misma modalidad
    if (!empty($modalidad_ids)) {
        $tax_query = [
            [
                'taxonomy' => 'modalidad',
                'field'    => 'term_id',
                'terms'    => $modalidad_ids,
            ]
        ];
        $query3 = trekkium_query_actividades($tax_query, null, $mostrar_ids, 10);
    } else {
        $query3 = null;
    }

    // 4) Mismo autor
    $query4 = trekkium_query_actividades([], $autor_id, $mostrar_ids, 10);

    // 5) Cualquier otra actividad
    $query5 = trekkium_query_actividades([], null, $mostrar_ids, 10);

    // Combinar resultados, respetando máximo de 10
    $actividades = [];
    foreach ([$query1, $query2, $query3, $query4, $query5] as $q) {
        if ($q && $q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();
                $id = get_the_ID();
                if (!in_array($id, $actividades) && $id != $current_id) {
                    $actividades[] = $id;
                    if (count($actividades) >= 10) break 2;
                }
            }
        }
    }
    wp_reset_postdata();

    if (empty($actividades)) return ''; // No hay actividades relacionadas

    ?>

    <div class="in-wrapper" style="margin-top:15px;padding-top:15px;">

        <div class="in-header">

            <button class="in-arrow in-arrow-left">
                <svg viewBox="0 0 24 24">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                </svg>
            </button>

            <h2 class="in-sectiontitle">Actividades Relacionadas</h2>

            <button class="in-arrow in-arrow-right">
                <svg viewBox="0 0 24 24">
                    <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                </svg>
            </button>

        </div>

        <div class="in-carousel">
            <?php foreach ($actividades as $id) : ?>
                <?php
                $product = wc_get_product($id);

                $fecha = get_post_meta($id, 'fecha', true);
                if ($fecha) {
                    $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
                    if ($fecha_obj) $fecha = $fecha_obj->format('d/m/Y');
                }

                $precio_raw = get_post_meta($id, 'precio', true);
                $precio = '';
                if ($precio_raw !== '' && $precio_raw !== null) {
                    $normalized = str_replace(',', '.', trim($precio_raw));
                    $precio = is_numeric($normalized)
                        ? number_format((float)$normalized, 2, ',', '')
                        : str_replace('.', ',', $precio_raw);
                }

                $modalidades = get_the_terms($id, 'modalidad');
                $dificultades = get_the_terms($id, 'dificultad');
                $provincias   = get_the_terms($id, 'provincia');
                $regiones     = get_the_terms($id, 'region');
                $paises       = get_the_terms($id, 'pais');

                $modalidad_name  = (!empty($modalidades)) ? $modalidades[0]->name : '';
                $dificultad_name = (!empty($dificultades)) ? $dificultades[0]->name : '';
                $provincia_name  = (!empty($provincias)) ? $provincias[0]->name : '';
                $region_name     = (!empty($regiones)) ? $regiones[0]->name : '';
                $pais_name       = (!empty($paises)) ? $paises[0]->name : '';

                $mostrar_como_espana = ($pais_name === 'España' || empty($pais_name));
                $ubicacion_primaria   = $mostrar_como_espana ? $provincia_name : $region_name;
                $ubicacion_secundaria = $mostrar_como_espana ? $region_name : $pais_name;

                $autor_id = get_post_field('post_author', $id);
                $avatar   = get_avatar_url($autor_id, ['size' => 80]);
                ?>

                <div class="in-item">
                    <a href="<?php echo esc_url(get_permalink($id)); ?>">

                        <div class="in-imgcontenedor">
                            <?php if (has_post_thumbnail($id)) : ?>
                                <?php echo get_the_post_thumbnail($id, 'large', ['class' => 'in-img']); ?>
                            <?php endif; ?>

                            <?php if ($avatar) : ?>
                                <div class="in-avatarcontenedor">
                                    <img src="<?php echo esc_url($avatar); ?>" class="in-avatar" />
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="in-contenido" style="margin-top: 0px;">

                            <?php if ($modalidad_name) : ?>
                                <div class="in-modalidad">
                                    <?php echo strtoupper(esc_html($modalidad_name)); ?>
                                </div>
                            <?php endif; ?>

                            <div class="in-titulo"><?php echo get_the_title($id); ?></div>

                            <!-- Ubicación -->
                            <?php 
                            $espacio_natural = get_post_meta($id, 'espacio_natural', true);
                            $provincia_name  = (!empty($provincias) && !is_wp_error($provincias)) ? esc_html($provincias[0]->name) : '';
                            $region_name     = (!empty($regiones) && !is_wp_error($regiones)) ? esc_html($regiones[0]->name) : '';

                            if ($espacio_natural || $provincia_name || $region_name) : ?>
                                <div class="in-ubicacion">
                                    <?php if ($espacio_natural) : ?>
                                        <?php echo esc_html($espacio_natural); ?>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    $ubicacion = '';
                                    if ($provincia_name && $region_name) {
                                        $ubicacion = $provincia_name . ', ' . $region_name;
                                    } elseif ($provincia_name) {
                                        $ubicacion = $provincia_name;
                                    } elseif ($region_name) {
                                        $ubicacion = $region_name;
                                    }

                                    if ($ubicacion) {
                                        echo ' (' . $ubicacion . ')';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>

                            <div class="in-infoextra">
                                <?php if ($fecha) : ?>
                                    <div class="in-fecha">
                                        <?php 
                                        $svg_path = get_stylesheet_directory() . '/svg/fecha1.svg'; 
                                        if (file_exists($svg_path)) {
                                            include $svg_path;
                                        }            
                                        ?>
                                        <span><?php echo esc_html($fecha); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($dificultad_name) : ?>
                                    <div class="in-dificultad">
                                        <?php 
                                        $svg_path = get_stylesheet_directory() . '/svg/dificultad1.svg'; 
                                        if (file_exists($svg_path)) {
                                            include $svg_path;
                                        }            
                                        ?>
                                        <span><?php echo esc_html($dificultad_name); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="in-ultima">

                                <div class="in-plazas">
                                    <?php
                                    if ($product && $product->managing_stock()) {
                                        $stock = (int)$product->get_stock_quantity();

                                        if ($stock > 1) {
                                            echo '<span>' . esc_html($stock) . ' plazas disponibles</span>';
                                        } elseif ($stock === 1) {
                                            echo '<span>1 plaza disponible</span>';
                                        } else {
                                            echo '<span class="in-completa">Actividad completa</span>';
                                        }
                                    } else {
                                        echo '<span>Plazas no definidas</span>';
                                    }
                                    ?>
                                </div>

                                <div class="in-precio">
                                    <?php if ($precio) : ?>
                                        <h3>
                                            <span class="importe"><?php echo esc_html($precio); ?></span>
                                            <span class="euros">€</span>
                                        </h3>
                                    <?php endif; ?>
                                </div>

                            </div>

                        </div>

                    </a>
                </div>

            <?php endforeach; ?>
        </div>

    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const carousel = document.querySelector(".in-carousel");
        const left = document.querySelector(".in-arrow-left");
        const right = document.querySelector(".in-arrow-right");

        right.addEventListener("click", () => {
            carousel.scrollBy({ left: 320, behavior: "smooth" });
        });

        left.addEventListener("click", () => {
            carousel.scrollBy({ left: -320, behavior: "smooth" });
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
