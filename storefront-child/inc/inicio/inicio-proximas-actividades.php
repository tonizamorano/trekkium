<?php

// Shortcode [in_proximas_actividades]
add_shortcode('in_proximas_actividades', 'trekkium_in_proximas_actividades');
function trekkium_in_proximas_actividades() {

    ob_start();

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1, // TODAS LAS ACTIVIDADES
        'meta_key'       => 'fecha',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
    );

    $query = new WP_Query($args);
    ?>

    <div class="in-wrapper">

        <div class="in-header">

            <button class="in-arrow in-arrow-left">
                <?php echo do_shortcode('[icon_flecha_izq]'); ?>
            </button>

            <h2 class="in-sectiontitle">Próximas Actividades</h2>

            <button class="in-arrow in-arrow-right">
                <?php echo do_shortcode('[icon_flecha_der]'); ?>
            </button>

        </div>

        <?php if ($query->have_posts()) : ?>
            <div class="in-carousel">

                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    global $product;

                    $estado_actividad = get_post_meta(get_the_ID(), 'estado_actividad', true);

                    $fecha = get_post_meta(get_the_ID(), 'fecha', true);
                    if ($fecha) {
                        $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
                        if ($fecha_obj) $fecha = $fecha_obj->format('d/m/Y');
                    }

                    $precio_raw = get_post_meta(get_the_ID(), 'precio', true);
                    $precio = '';
                    if ($precio_raw !== '' && $precio_raw !== null) {
                        $normalized = str_replace(',', '.', trim($precio_raw));
                        $precio = is_numeric($normalized)
                            ? number_format((float)$normalized, 2, ',', '')
                            : str_replace('.', ',', $precio_raw);
                    }

                    $modalidades = get_the_terms(get_the_ID(), 'modalidad');
                    $dificultades = get_the_terms(get_the_ID(), 'dificultad');
                    $provincias   = get_the_terms(get_the_ID(), 'provincia');
                    $regiones     = get_the_terms(get_the_ID(), 'region');
                    $paises       = get_the_terms(get_the_ID(), 'pais');

                    $modalidad_name  = (!empty($modalidades)) ? $modalidades[0]->name : '';
                    $dificultad_name = (!empty($dificultades)) ? $dificultades[0]->name : '';
                    $provincia_name  = (!empty($provincias)) ? $provincias[0]->name : '';
                    $region_name     = (!empty($regiones)) ? $regiones[0]->name : '';
                    $pais_name       = (!empty($paises)) ? $paises[0]->name : '';

                    $mostrar_como_espana = ($pais_name === 'España' || empty($pais_name));
                    $ubicacion_primaria   = $mostrar_como_espana ? $provincia_name : $region_name;
                    $ubicacion_secundaria = $mostrar_como_espana ? $region_name : $pais_name;

                    $autor_id = get_the_author_meta('ID');
                    $avatar   = get_avatar_url($autor_id, ['size' => 80]);
                    ?>

                    <div class="in-item">
                        <a href="<?php echo esc_url(get_permalink()); ?>">

                            <div class="in-contenedor-imagen">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php echo get_the_post_thumbnail(get_the_ID(), 'large', ['class' => 'in-imagen']); ?>
                                <?php endif; ?>
                                
                                <!-- Modalidad - MOVIDA AQUÍ -->
                                <?php if (!empty($modalidades) && !is_wp_error($modalidades)) : ?>
                                    <div class="in-modalidad">
                                        <?php echo esc_html($modalidades[0]->name); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($avatar) : ?>
                                    <div class="in-avatar-contenedor">
                                        <img src="<?php echo esc_url($avatar); ?>" class="in-avatar-autor" />
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="in-contenido">

                                <div class="in-titulo"><?php the_title(); ?></div>

                                <!-- Ubicación -->
                                <?php 
                                $espacio_natural = get_post_meta(get_the_ID(), 'espacio_natural', true);
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

                                <div class="in-infoextra">
                                    <?php if ($fecha) : ?>
                                        <div class="in-fecha">

                                            <?php echo do_shortcode('[icon_fecha1]'); ?>

                                            <span><?php echo esc_html($fecha); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($dificultad_name) : ?>
                                        <div class="in-dificultad">

                                            <?php echo do_shortcode('[icon_dificultad1]'); ?>

                                            <span><?php echo esc_html($dificultad_name); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>


                                <!-- Sección del precio -->
                                    <div class="in-ultima">

                                    <!-- Mostrar el estado de la actividad -->
                                    <?php if (!empty($estado_actividad) && $estado_actividad !== 'Sin definir'): ?>

                                    <div class="in-estado-actividad">
                                        <?php echo esc_html($estado_actividad); ?>
                                    </div>
                                    
                                    <?php endif; ?>

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

                <?php endwhile; ?>

            </div>

            <div class="in-dots">
                <?php for ($i = 0; $i < $query->post_count; $i++) : ?>
                    <span class="in-dot <?php echo $i === 0 ? 'active' : ''; ?>"></span>
                <?php endfor; ?>
            </div>


            
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const carousel = document.querySelector(".in-carousel");
        const left = document.querySelector(".in-arrow-left");
        const right = document.querySelector(".in-arrow-right");
        const dots = document.querySelectorAll(".in-dot");
        const items = document.querySelectorAll(".in-item");

        let index = 0;

        function updateDots() {
            dots.forEach((dot, i) => {
                dot.classList.toggle("active", i === index);
            });
        }

        function scrollToIndex(i) {
            const itemWidth = items[0].offsetWidth + 15;
            carousel.scrollTo({
                left: itemWidth * i,
                behavior: "smooth"
            });
            index = i;
            updateDots();
        }

        right.addEventListener("click", () => {
            if (index < items.length - 1) {
                scrollToIndex(index + 1);
            }
        });

        left.addEventListener("click", () => {
            if (index > 0) {
                scrollToIndex(index - 1);
            }
        });

        dots.forEach((dot, i) => {
            dot.addEventListener("click", () => scrollToIndex(i));
        });

        carousel.addEventListener("scroll", () => {
            const itemWidth = items[0].offsetWidth + 15;
            index = Math.round(carousel.scrollLeft / itemWidth);
            updateDots();
        });
    });
    </script>


    <?php
    return ob_get_clean();
}
