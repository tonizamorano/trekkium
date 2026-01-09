<?php
// Shortcode: [bs_articulos_relacionados]
function trekkium_bs_articulos_relacionados( $atts = array() ) {
    if ( ! is_singular('post') ) return ''; // Mostrar solo en single de entradas

    global $post;
    $current_id = (int) $post->ID;
    $author_id = (int) $post->post_author;
    $max_items = 10;

    $collected = array();

    // 1) Posts que compartan categorías con el actual
    $cat_ids = wp_get_post_categories( $current_id );
    if ( ! empty( $cat_ids ) ) {
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => $max_items,
            'post_status'    => 'publish',
            'post__not_in'   => array( $current_id ),
            'category__in'   => $cat_ids,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        $posts_cat = get_posts( $args );
        foreach ( $posts_cat as $p ) {
            $collected[] = $p->ID;
            if ( count( $collected ) >= $max_items ) break;
        }
    }

    // 2) Posts del mismo autor (excluir ya recogidos)
    if ( count( $collected ) < $max_items ) {
        $remaining = $max_items - count( $collected );
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => $remaining,
            'post_status'    => 'publish',
            'post__not_in'   => array_merge( array( $current_id ), $collected ),
            'author'         => $author_id,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        $posts_auth = get_posts( $args );
        foreach ( $posts_auth as $p ) {
            $collected[] = $p->ID;
            if ( count( $collected ) >= $max_items ) break;
        }
    }

    // 3) Otros posts recientes hasta completar
    if ( count( $collected ) < $max_items ) {
        $remaining = $max_items - count( $collected );
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => $remaining,
            'post_status'    => 'publish',
            'post__not_in'   => array_merge( array( $current_id ), $collected ),
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        $posts_more = get_posts( $args );
        foreach ( $posts_more as $p ) {
            $collected[] = $p->ID;
            if ( count( $collected ) >= $max_items ) break;
        }
    }

    if ( empty( $collected ) ) return '';

    ob_start();
    ?>

    <div class="bs-ar-contenedor">
        <div class="bs-ar-titulo">
            <h6>Artículos relacionados</h6>
        </div>

        <div class="bs-ar-contenido">

            <div class="bs-ar-container">

                <div class="bs-ar-wrapper">

                    <div class="bs-ar-slider" id="bs-ar-slider-<?php echo esc_attr( $author_id ); ?>">
                        <?php $index = 0; foreach ( $collected as $pid ) :
                            $thumb = get_the_post_thumbnail( $pid, 'large', array( 'style' => 'max-width:100%; height:auto; display:block;' ) );
                            $permalink = get_permalink( $pid );
                            $title = get_the_title( $pid );
                            $cats = get_the_category( $pid );
                            $cat_names = array();
                            if ( ! empty( $cats ) ) {
                                foreach ( $cats as $c ) {
                                    $cat_names[] = esc_html( $c->name );
                                }
                            }
                        ?>

                            <div class="bs-ar-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo esc_attr( $index ); ?>">

                                <div class="bs-ar-item">

                                    <div class="bs-ar-imagen">
                                        <a href="<?php echo esc_url( $permalink ); ?>">
                                            <?php echo $thumb; ?>
                                        </a>
                                    </div>

                                    <div class="bs-ar-item-contenido">

                                        <h3 class="bs-ar-item-titulo">
                                            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                                        </h3>

                                        <div class="bs-ar-modalidad-contenedor">
                                            <?php if ( ! empty( $cat_names ) ) : foreach ( $cat_names as $cn ) : ?>
                                                <span class="bs-ar-modalidad"><?php echo $cn; ?></span>
                                            <?php endforeach; endif; ?>
                                        </div>

                                        

                                    </div>

                                </div>

                            </div>

                        <?php $index++; endforeach; ?>
                    </div>

                    <?php if ( count( $collected ) > 1 ) : ?>
                        <button class="ps-entradas-rel-slider-btn prev" onclick="trekkium_gs_pa_changeSlide(-1, 'bs-ar-slider-<?php echo esc_attr($author_id); ?>')">
                            <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                        </button>
                        <button class="ps-entradas-rel-slider-btn next" onclick="trekkium_gs_pa_changeSlide(1, 'bs-ar-slider-<?php echo esc_attr($author_id); ?>')">
                            <svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <script>
                function trekkium_gs_pa_changeSlide(direction, sliderId) {
                    const slider = document.getElementById(sliderId);
                    const slides = slider.querySelectorAll('.bs-ar-slide');
                    const activeSlide = slider.querySelector('.bs-ar-slide.active');
                    let currentIndex = parseInt(activeSlide.dataset.index);

                    currentIndex += direction;
                    if (currentIndex < 0) currentIndex = slides.length - 1;
                    else if (currentIndex >= slides.length) currentIndex = 0;

                    slides.forEach(slide => slide.classList.remove('active'));
                    slides[currentIndex].classList.add('active');
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const sliders = document.querySelectorAll('.bs-ar-slider');
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

    <?php
    return ob_get_clean();
}
add_shortcode('bs_articulos_relacionados', 'trekkium_bs_articulos_relacionados');
?>
