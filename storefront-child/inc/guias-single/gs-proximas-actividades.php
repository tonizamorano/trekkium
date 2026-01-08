<?php
// Shortcode: [seccion_proximas_actividades]
function trekkium_gs_proximas_actividades() {
    if (!is_author()) return ''; // Solo en páginas de autor

    $author_id = get_queried_object_id(); // ID del autor en author.php
    $today = date('Y-m-d'); // Fecha de hoy

    $args = array(
        'post_type'      => 'product', // Tus actividades
        'posts_per_page' => 10,
        'post_status'    => 'publish',
        'author'         => $author_id,
        'meta_key'       => 'fecha',
        'meta_value'     => $today,
        'meta_compare'   => '>=',       // Solo próximas actividades
        'orderby'        => 'meta_value',
        'order'          => 'ASC',      // De la más próxima a la más lejana
    );

    $actividades = new WP_Query($args);

    if (!$actividades->have_posts()) {
        return ''; // No mostrar nada si no hay próximas actividades
    }


    ob_start(); ?>

    <div class="gs-pa-contenedor">
        <div class="gs-pa-titulo">
            <h6>Próximas actividades</h6>
        </div>

        <div class="gs-pa-contenido">

            <div class="ps-pa-container">

                <div class="gs-pa-wrapper">

                    <div class="gs-pa-slider" id="gs-pa-slider-<?php echo esc_attr($author_id); ?>">
                        <?php $index = 0;
                        while ($actividades->have_posts()) : $actividades->the_post(); ?>

                            <div class="gs-pa-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo esc_attr($index); ?>">
                                
                                <div class="gs-pa-item">
                                    
                                    <div class="gs-pa-imagen">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php
                                            // Mostrar la imagen principal del producto en lugar del thumbnail
                                            if ( function_exists('the_post_thumbnail') ) {
                                                the_post_thumbnail('woocommerce_single', ['style' => 'max-width:100%; height:auto; display:block;']);
                                            }
                                            ?>
                                        </a>
                                    </div>

                                    <div class="gs-pa-item-contenido">

                                        <div class="gs-pa-modalidad-contenedor">
                                            <?php
                                            $modalidades = get_the_terms(get_the_ID(), 'modalidad');
                                            if (!empty($modalidades) && !is_wp_error($modalidades)) {
                                                echo '<span class="gs-pa-modalidad">' . esc_html($modalidades[0]->name) . '</span>';
                                            }
                                            ?>
                                        </div>

                                        <h3 class="gs-pa-item-titulo">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>

                                        <?php
                                        $provincias = get_the_terms(get_the_ID(), 'provincia');
                                        $regiones   = get_the_terms(get_the_ID(), 'region');
                                        $paises     = get_the_terms(get_the_ID(), 'pais');

                                        $provincia_name = (!empty($provincias) && !is_wp_error($provincias)) ? esc_html($provincias[0]->name) : '';
                                        $region_name    = (!empty($regiones) && !is_wp_error($regiones)) ? esc_html($regiones[0]->name) : '';
                                        $pais_name      = (!empty($paises) && !is_wp_error($paises)) ? esc_html($paises[0]->name) : '';

                                        $mostrar_como_espana = ($pais_name === 'España' || empty($pais_name));
                                        $ubicacion_primaria   = $mostrar_como_espana ? $provincia_name : $region_name;
                                        $ubicacion_secundaria = $mostrar_como_espana ? $region_name : $pais_name;
                                        ?>

                                        <?php
                                        // Mostrar Espacio natural (Provincia, Region)
                                        $espacio_natural = get_post_meta(get_the_ID(), 'espacio_natural', true);
                                        $prov = $provincia_name;
                                        $reg = $region_name;

                                        if ($espacio_natural || $prov || $reg) : ?>
                                            <div class="gs-pa-ubicacion">
                                                <?php
                                                if ($espacio_natural) {
                                                    echo esc_html($espacio_natural);

                                                    $parts = array();
                                                    if (!empty($prov)) $parts[] = $prov;
                                                    if (!empty($reg)) $parts[] = $reg;
                                                    if (!empty($parts)) {
                                                        echo ' (' . esc_html(implode(', ', $parts)) . ')';
                                                    }

                                                } else {
                                                    // Fallback: mostrar Provincia (Region) como antes
                                                    if (!empty($prov)) {
                                                        echo esc_html($prov);
                                                        if (!empty($reg)) echo ' (' . esc_html($reg) . ')';
                                                    } else {
                                                        echo esc_html($reg);
                                                    }
                                                }
                                                ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="gs-pa-info-extra">
                                            <?php
                                            $fecha = get_post_meta(get_the_ID(), 'fecha', true);
                                            if ($fecha) {
                                                $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
                                                if ($fecha_obj) $fecha = $fecha_obj->format('d/m/Y');
                                            }
                                            if ($fecha) : ?>
                                                <div class="gs-pa-info-item-fecha">
                                                    <?php echo do_shortcode('[icon_fecha1]'); ?>
                                                    <span><?php echo esc_html($fecha); ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php
                                            $dificultades = get_the_terms(get_the_ID(), 'dificultad');
                                            $dificultad_name = (!empty($dificultades) && !is_wp_error($dificultades)) ? esc_html($dificultades[0]->name) : '';
                                            if ($dificultad_name) : ?>
                                                <div class="gs-pa-info-item-dificultad">
                                                    <?php echo do_shortcode('[icon_dificultad1]'); ?>
                                                    <span><?php echo esc_html($dificultad_name); ?></span>
                                                </div>
                                                
                                            <?php endif; ?>

                                        </div>

                                        <?php
                                        // Copiado de la sección pa-seccion-ultima (adaptado)
                                        $estado_actividad = get_post_meta(get_the_ID(), 'estado_actividad', true);

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

                                        $autor_id = get_post_field('post_author', get_the_ID());
                                        $avatar = get_avatar_url($autor_id, ['size' => 80]);
                                        ?>

                                        <div class="pa-seccion-ultima">
                                            
                                            <div style="display:flex;flex-direction:column;">
                                                
                                                <!-- Mostrar el estado de la actividad -->
                                                <?php if (!empty($estado_actividad) && $estado_actividad !== 'Sin definir'): ?>

                                                <div class="pa-estado-actividad">
                                                    <?php echo esc_html($estado_actividad); ?>
                                                </div>
                                                
                                                <?php endif; ?>

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

                                </div>

                            </div>
                        <?php $index++;
                        endwhile; ?>
                    </div>

                    <?php if ($actividades->post_count > 1) : ?>
                        <button class="ps-entradas-rel-slider-btn prev" onclick="trekkium_gs_pa_changeSlide(-1, 'gs-pa-slider-<?php echo esc_attr($author_id); ?>')">
                            <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                        </button>
                        <button class="ps-entradas-rel-slider-btn next" onclick="trekkium_gs_pa_changeSlide(1, 'gs-pa-slider-<?php echo esc_attr($author_id); ?>')">
                            <svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <script>
                function trekkium_gs_pa_changeSlide(direction, sliderId) {
                    const slider = document.getElementById(sliderId);
                    const slides = slider.querySelectorAll('.gs-pa-slide');
                    const activeSlide = slider.querySelector('.gs-pa-slide.active');
                    let currentIndex = parseInt(activeSlide.dataset.index);

                    currentIndex += direction;
                    if (currentIndex < 0) currentIndex = slides.length - 1;
                    else if (currentIndex >= slides.length) currentIndex = 0;

                    slides.forEach(slide => slide.classList.remove('active'));
                    slides[currentIndex].classList.add('active');
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const sliders = document.querySelectorAll('.gs-pa-slider');
                    sliders.forEach(slider => {
                        let startX = 0, endX = 0;
                        slider.addEventListener('touchstart', e => startX = e.touches[0].clientX);
                        slider.addEventListener('touchend', e => {
                            endX = e.changedTouches[0].clientX;
                            handleSwipe(startX, endX, slider.id);
                        });
                        slider.addEventListener('mousedown', e => {
                            startX = e.clientX;
                            document.addEventListener('mouseup', onMouseUp);
                        });
                        function onMouseUp(e) {
                            endX = e.clientX;
                            handleSwipe(startX, endX, slider.id);
                            document.removeEventListener('mouseup', onMouseUp);
                        }
                    });

                    function handleSwipe(startX, endX, sliderId) {
                        const diff = startX - endX;
                        const minSwipeDistance = 50;
                        if (Math.abs(diff) > minSwipeDistance) {
                            if (diff > 0) trekkium_gs_pa_changeSlide(1, sliderId);
                            else trekkium_gs_pa_changeSlide(-1, sliderId);
                        }
                    }
                });
            </script>
        </div>
    </div>

    <?php wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('gs_proximas_actividades', 'trekkium_gs_proximas_actividades');
?>
