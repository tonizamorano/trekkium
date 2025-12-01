<?php
// Shortcode: [seccion_proximas_actividades]
function trekkium_seccion_proximas_actividades() {
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
        return '<p>Este guía no tiene próximas actividades publicadas.</p>';
    }

    ob_start(); ?>

    <div class="gs-entradas-rel-contenedor">
        <div class="ps-entradas-rel-titulo">
            <h6>Próximas actividades</h6>
        </div>

        <div class="ps-entradas-rel-contenido">
            <div class="ps-rel-slider-container">
                <div class="ps-rel-slider-wrapper">
                    <div class="ps-rel-slider" id="ps-proximas-slider-<?php echo esc_attr($author_id); ?>">
                        <?php $index = 0;
                        while ($actividades->have_posts()) : $actividades->the_post(); ?>
                            <div class="ps-rel-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo esc_attr($index); ?>">
                                <div class="ps-entradas-rel-item">
                                    
                                    <div class="ps-entradas-rel-imagen">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('woocommerce_thumbnail', ['style' => 'width:100%; height:auto;']); ?>
                                        </a>
                                    </div>

                                    <div class="ps-entradas-rel-item-contenido">
                                        <div class="ps-entradas-rel-modalidad-contenedor">
                                            <?php
                                            $modalidades = get_the_terms(get_the_ID(), 'modalidad');
                                            if (!empty($modalidades) && !is_wp_error($modalidades)) {
                                                echo '<span class="ps-entradas-rel-modalidad">' . esc_html($modalidades[0]->name) . '</span>';
                                            }
                                            ?>
                                        </div>

                                        <h3 class="ps-entradas-rel-item-titulo">
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

                                        <?php if ($ubicacion_primaria || $ubicacion_secundaria) : ?>
                                            <div class="ps-ubicacion">
                                                <?php echo esc_html($ubicacion_primaria); ?>
                                                <?php if ($ubicacion_primaria && $ubicacion_secundaria) : ?>
                                                    (<?php echo esc_html($ubicacion_secundaria); ?>)
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="ps-info-extra">
                                            <?php
                                            $fecha = get_post_meta(get_the_ID(), 'fecha', true);
                                            if ($fecha) {
                                                $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
                                                if ($fecha_obj) $fecha = $fecha_obj->format('d/m/Y');
                                            }
                                            if ($fecha) : ?>
                                                <div class="ps-info-item-fecha">
                                                    <svg class="icon" viewBox="0 0 448 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M436 160H12c-6.627 0-12-5.373-12-12v-36c0-26.51 21.49-48 48-48h48V12c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v52h128V12c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v52h48c26.51 0 48 21.49 48 48v36c0 6.627-5.373 12-12 12zM12 192h424c6.627 0 12 5.373 12 12v260c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V204c0-6.627 5.373-12 12-12zm333.296 95.947l-28.169-28.398c-4.667-4.705-12.265-4.736-16.97-.068L194.12 364.665l-45.98-46.352c-4.667-4.705-12.266-4.736-16.971-.068l-28.397 28.17c-4.705 4.667-4.736 12.265-.068 16.97l82.601 83.269c4.667 4.705 12.265 4.736 16.97.068l142.953-141.805c4.705-4.667 4.736-12.265.068-16.97z"></path></svg>
                                                    <span><?php echo esc_html($fecha); ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php
                                            $dificultades = get_the_terms(get_the_ID(), 'dificultad');
                                            $dificultad_name = (!empty($dificultades) && !is_wp_error($dificultades)) ? esc_html($dificultades[0]->name) : '';
                                            if ($dificultad_name) : ?>
                                                <div class="ps-info-item-dificultad">
                                                    <svg class="icon" viewBox="0 0 504 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M456 128c26.5 0 48-21 48-47 0-20-28.5-60.4-41.6-77.8-3.2-4.3-9.6-4.3-12.8 0C436.5 20.6 408 61 408 81c0 26 21.5 47 48 47zm0 32c-44.1 0-80-35.4-80-79 0-4.4.3-14.2 8.1-32.2C345 23.1 298.3 8 248 8 111 8 0 119 0 256s111 248 248 248 248-111 248-248c0-35.1-7.4-68.4-20.5-98.6-6.3 1.5-12.7 2.6-19.5 2.6zm-128-8c23.8 0 52.7 29.3 56 71.4.7 8.6-10.8 12-14.9 4.5l-9.5-17c-7.7-13.7-19.2-21.6-31.5-21.6s-23.8 7.9-31.5 21.6l-9.5 17c-4.1 7.4-15.6 4-14.9-4.5 3.1-42.1 32-71.4 55.8-71.4zm-160 0c23.8 0 52.7 29.3 56 71.4.7 8.6-10.8 12-14.9 4.5l-9.5-17c-7.7-13.7-19.2-21.6-31.5-21.6s-23.8 7.9-31.5 21.6l-9.5 17c-4.2 7.4-15.6 4-14.9-4.5 3.1-42.1 32-71.4 55.8-71.4zm80 280c-60.6 0-134.5-38.3-143.8-93.3-2-11.8 9.3-21.6 20.7-17.9C155.1 330.5 200 336 248 336s92.9-5.5 123.1-15.2c11.5-3.7 22.6 6.2 20.7 17.9-9.3 55-83.2 93.3-143.8 93.3z"></path></svg>
                                                    <span><?php echo esc_html($dificultad_name); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php $index++;
                        endwhile; ?>
                    </div>

                    <?php if ($actividades->post_count > 1) : ?>
                        <button class="ps-entradas-rel-slider-btn prev" onclick="changeSlide(-1, 'ps-proximas-slider-<?php echo esc_attr($author_id); ?>')">
                            <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                        </button>
                        <button class="ps-entradas-rel-slider-btn next" onclick="changeSlide(1, 'ps-proximas-slider-<?php echo esc_attr($author_id); ?>')">
                            <svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <script>
                function changeSlide(direction, sliderId) {
                    const slider = document.getElementById(sliderId);
                    const slides = slider.querySelectorAll('.ps-rel-slide');
                    const activeSlide = slider.querySelector('.ps-rel-slide.active');
                    let currentIndex = parseInt(activeSlide.dataset.index);

                    currentIndex += direction;
                    if (currentIndex < 0) currentIndex = slides.length - 1;
                    else if (currentIndex >= slides.length) currentIndex = 0;

                    slides.forEach(slide => slide.classList.remove('active'));
                    slides[currentIndex].classList.add('active');
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const sliders = document.querySelectorAll('.ps-rel-slider');
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
                            if (diff > 0) changeSlide(1, sliderId);
                            else changeSlide(-1, sliderId);
                        }
                    }
                });
            </script>
        </div>
    </div>

    <?php wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('seccion_proximas_actividades', 'trekkium_seccion_proximas_actividades');
?>
