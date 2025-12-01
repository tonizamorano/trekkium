<?php
// Shortcode [query_productos] - VERSIÓN CORREGIDA
add_shortcode('query_productos', 'trekkium_query_productos');
function trekkium_query_productos() {
    ob_start();

    // Obtener parámetros de la URL
    $region_filtro = isset($_GET['region']) ? sanitize_text_field($_GET['region']) : '';
    $modalidad_filtro = isset($_GET['modalidad']) ? sanitize_text_field($_GET['modalidad']) : '';
    $dificultad_filtro = isset($_GET['dificultad']) ? sanitize_text_field($_GET['dificultad']) : '';

    $args = array(
        'post_type'      => 'product',
        'post_status'    => array('publish', 'wc-cancelado'),
        'posts_per_page' => -1,
        'meta_key'       => 'fecha',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
    );

    // Añadir filtros de taxonomías si existen
    $tax_query = array('relation' => 'AND');

    if (!empty($region_filtro)) {
        $tax_query[] = array(
            'taxonomy' => 'region',
            'field'    => 'slug',
            'terms'    => $region_filtro,
        );
    }

    if (!empty($modalidad_filtro)) {
        $tax_query[] = array(
            'taxonomy' => 'modalidad',
            'field'    => 'slug',
            'terms'    => $modalidad_filtro,
        );
    }

    if (!empty($dificultad_filtro)) {
        $tax_query[] = array(
            'taxonomy' => 'dificultad',
            'field'    => 'slug',
            'terms'    => $dificultad_filtro,
        );
    }

    // Solo añadir tax_query si hay al menos un filtro
    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query($args);

    // Determinar si hay filtros activos desde la URL
    $filtros_activos = !empty($region_filtro) || !empty($modalidad_filtro) || !empty($dificultad_filtro);
    ?>

    <div class="pa-query-wrapper" 
         data-region-filtro="<?php echo esc_attr($region_filtro); ?>"
         data-modalidad-filtro="<?php echo esc_attr($modalidad_filtro); ?>"
         data-dificultad-filtro="<?php echo esc_attr($dificultad_filtro); ?>">

        <!-- Mostrar filtros activos SOLO si hay filtros -->
        <?php if ($filtros_activos): ?>
        <div class="filtros-activos">
            <div class="filtros-lista">
                
                <?php if (!empty($region_filtro)): ?>
                    <?php $region_term = get_term_by('slug', $region_filtro, 'region'); ?>
                    <span class="filtros-activos-item">
                        <?php echo esc_html($region_term->name); ?> 
                        <a href="<?php echo esc_url(remover_parametro_url('region')); ?>" class="quitar-filtro">×</a>
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($modalidad_filtro)): ?>
                    <?php $modalidad_term = get_term_by('slug', $modalidad_filtro, 'modalidad'); ?>
                    <span class="filtros-activos-item">
                        <?php echo esc_html($modalidad_term->name); ?> 
                        <a href="<?php echo esc_url(remover_parametro_url('modalidad')); ?>" class="quitar-filtro">×</a>
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($dificultad_filtro)): ?>
                    <?php $dificultad_term = get_term_by('slug', $dificultad_filtro, 'dificultad'); ?>
                    <span class="filtros-activos-item">
                        <?php echo esc_html($dificultad_term->name); ?> 
                        <a href="<?php echo esc_url(remover_parametro_url('dificultad')); ?>" class="quitar-filtro">×</a>
                    </span>
                <?php endif; ?>
                
            </div>
        </div>
        <?php endif; ?>

        <?php if ($query->have_posts()) : ?>
            <div class="pa-query-grid">
                
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    global $post, $product;

                    // 🔹 Obtener meta y taxonomías
                    $estado_actividad = get_post_meta(get_the_ID(), 'estado_actividad', true);

                    $modalidades = get_the_terms(get_the_ID(), 'modalidad');
                    $modalidad_slugs = (!empty($modalidades) && !is_wp_error($modalidades)) ? implode(',', wp_list_pluck($modalidades, 'slug')) : '';
                    $modalidad_names = (!empty($modalidades) && !is_wp_error($modalidades)) ? implode(',', wp_list_pluck($modalidades, 'name')) : '';

                    $dificultades = get_the_terms(get_the_ID(), 'dificultad');
                    $dificultad_slugs = (!empty($dificultades) && !is_wp_error($dificultades)) ? implode(',', wp_list_pluck($dificultades, 'slug')) : '';
                    $dificultad_name = (!empty($dificultades) && !is_wp_error($dificultades)) ? esc_html($dificultades[0]->name) : '';

                    $provincias = get_the_terms(get_the_ID(), 'provincia');
                    $regiones   = get_the_terms(get_the_ID(), 'region');
                    $paises     = get_the_terms(get_the_ID(), 'pais');

                    $provincia_name = (!empty($provincias) && !is_wp_error($provincias)) ? esc_html($provincias[0]->name) : '';
                    $region_name    = (!empty($regiones) && !is_wp_error($regiones)) ? esc_html($regiones[0]->name) : '';
                    $pais_name      = (!empty($paises) && !is_wp_error($paises)) ? esc_html($paises[0]->name) : '';

                    // 🔹 Lógica de ubicación
                    $mostrar_como_espana = ($pais_name === 'España' || empty($pais_name));
                    $ubicacion_primaria   = $mostrar_como_espana ? $provincia_name : $region_name;
                    $ubicacion_secundaria = $mostrar_como_espana ? $region_name : $pais_name;

                    // 🔹 Fecha
                    $fecha = get_post_meta(get_the_ID(), 'fecha', true);
                    if ($fecha) {
                        $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
                        if ($fecha_obj) $fecha = $fecha_obj->format('d/m/Y');
                    }

                    // 🔹 Precio
                    $precio_raw = get_post_meta(get_the_ID(), 'precio', true);
                    $precio = '';
                    if ($precio_raw !== '' && $precio_raw !== null) {
                        $normalized = str_replace(',', '.', trim($precio_raw));
                        if (is_numeric($normalized)) {
                            $precio = number_format((float) $normalized, 2, ',', '');
                        } else {
                            $precio = str_replace('.', ',', $precio_raw);
                        }
                    }

                    // 🔹 Autor y avatar
                    $autor_id = get_the_author_meta('ID');
                    $avatar   = get_avatar_url($autor_id, ['size' => 80]);

                    $permalink = get_permalink(get_the_ID());
                    ?>
                    
                    <div class="pa-query-item" 
                        data-modalidad="<?php echo esc_attr($modalidad_slugs); ?>" 
                        data-region="<?php echo esc_attr($region_filtro ? $region_filtro : sanitize_title($region_name)); ?>" 
                        data-dificultad="<?php echo esc_attr($dificultad_slugs); ?>"
                        data-pais="<?php echo esc_attr(sanitize_title($pais_name)); ?>">
                        
                        <a href="<?php echo esc_url($permalink); ?>">

                        <!-- Imagen con avatar -->
                        <div class="pa-contenedor-imagen">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php echo get_the_post_thumbnail(get_the_ID(), 'large', ['class' => 'pa-imagen']); ?>
                            <?php endif; ?>
                            
                            <?php if ($avatar) : ?>
                                <div class="pa-avatar-contenedor">
                                    <img src="<?php echo esc_url($avatar); ?>" class="pa-avatar-autor" />
                                </div>
                            <?php endif; ?>
                        </div>

                            <div class="pa-query-contenido">                                

                                <!-- Modalidad -->
                                <?php if (!empty($modalidades) && !is_wp_error($modalidades)) : ?>
                                    <div class="pa-modalidad">
                                        <?php echo strtoupper(esc_html($modalidades[0]->name)); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Título -->
                                <div class="pa-titulo"><?php the_title(); ?></div>

                                <!-- Ubicación -->
                                <?php 
                                $espacio_natural = get_post_meta(get_the_ID(), 'espacio_natural', true);
                                $provincia_name  = (!empty($provincias) && !is_wp_error($provincias)) ? esc_html($provincias[0]->name) : '';
                                $region_name     = (!empty($regiones) && !is_wp_error($regiones)) ? esc_html($regiones[0]->name) : '';

                                if ($espacio_natural || $provincia_name || $region_name) : ?>
                                    <div class="pa-ubicacion">
                                        <?php if ($espacio_natural) : ?>
                                            <?php echo esc_html($espacio_natural); ?>
                                        <?php endif; ?>
                                        
                                        <?php 
                                        $ubicacion = '';
                                        if ($provincia_name && $region_name) {
                                            $ubicacion = $provincia_name . ', ' . $region_name; // 👈 coma + espacio
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




                                <!-- Información extra -->
                                <div class="info-extra">
                                    
                                    <!-- Fecha -->
                                    <?php if ($fecha) : ?>
                                        <div class="info-item-fecha">
                                            <?php 
                                            $svg_path = get_stylesheet_directory() . '/svg/fecha1.svg'; 
                                            if (file_exists($svg_path)) {
                                                include $svg_path;
                                            }            
                                            ?>
                                            <span><?php echo esc_html($fecha); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Dificultad -->
                                    <?php if ($dificultad_name) : ?>
                                        <div class="info-item-dificultad">
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

                                <!-- Sección del precio -->
                                <div class="pa-seccion-ultima">
                                    
                                    <div class="pa-plazas-disponibles">
                                        <?php
                                        if (function_exists('wc_get_product')) {
                                            $product = wc_get_product(get_the_ID());
                                            if ($product && $product->managing_stock()) {
                                                $stock = (int) $product->get_stock_quantity();

                                                if ($stock > 1) {
                                                    echo '<span>' . esc_html($stock) . ' plazas disponibles</span>';
                                                } elseif ($stock === 1) {
                                                    echo '<span>1 plaza disponible</span>';
                                                } else {
                                                    echo '<span class="actividad-completa">Actividad completa</span>';
                                                }
                                            } else {
                                                echo '<span>Plazas no definidas</span>';
                                            }
                                        }
                                        ?>
                                    </div>

                                    <div class="pa-precio">
                                        <?php if ($precio) : ?>
                                            <h3>
                                                <span class="importe">
                                                    <?php echo esc_html($precio); ?>
                                                </span>
                                                <span class="euros">€</span>
                                            </h3>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                        </a>
                    </div>

                <?php endwhile; ?>

            </div>
        <?php
        else:
            echo '<div class="no-resultados">';
            echo '<h3>No se encontraron actividades con los filtros seleccionados</h3>';
            echo '<p><a href="' . esc_url(home_url('/actividades/')) . '">Ver todas las actividades</a></p>';
            echo '</div>';
        endif;

        wp_reset_postdata();
        ?>
    </div>
    <?php
    return ob_get_clean();
}

// Función auxiliar para remover parámetros de la URL
function remover_parametro_url($parametro) {
    $url = home_url($_SERVER['REQUEST_URI']);
    $parsed_url = parse_url($url);
    
    if (isset($parsed_url['query'])) {
        parse_str($parsed_url['query'], $params);
        unset($params[$parametro]);
        
        if (!empty($params)) {
            return $parsed_url['path'] . '?' . http_build_query($params);
        } else {
            return $parsed_url['path'];
        }
    }
    
    return $url;
}