<?php

// Shortcode: [seccion_entradas_relacionadas_blog]
function trekkium_seccion_entradas_relacionadas_blog() {
    // Verificar si estamos en una entrada individual del blog
    if (!is_single()) return '';

    // Obtener el ID del post actual
    $post_id = get_the_ID();
    $current_post_id = $post_id;
    
    $categories = wp_get_post_categories($current_post_id);
    
    if (empty($categories)) {
        return '<p>No hay categorías relacionadas.</p>';
    }
    
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 10,
        'post__not_in' => array($current_post_id),
        'category__in' => $categories,
        'orderby' => 'rand'
    );
    
    $related_posts = new WP_Query($args);

    ob_start();
    ?>

    <div class="bs-entradas-rel-contenedor">

        <div class="bs-entradas-rel-titulo">
            <h6>Entradas relacionadas</h6>
        </div>

        <div class="bs-entradas-rel-contenido">
            <?php if ($related_posts->have_posts()) : ?>

                <div class="bs-entradas-rel-slider">

                    <div class="swiper-wrapper">

                        <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>

                            <div class="swiper-slide">

                                <div class="bs-entrada-container">

                                    <?php if (has_post_thumbnail()) : ?>

                                        <div class="bs-entrada-imagen">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('full', array('style' => 'width:100%; height:auto;')); ?>
                                            </a>
                                        </div>
                                        
                                    <?php endif; ?>

                                    <div class="bs-entrada-contenido">

                                        <h3 class="bs-entrada-titulo">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>

                                        <div class="bs-categorias">
                                        <?php
                                        $categories = get_the_category();
                                        if ( $categories ) {
                                            foreach ( $categories as $category ) {
                                            echo '<span class="bs-categoria">' . esc_html( $category->name ) . '</span>';
                                            }
                                        }
                                        ?>
                                        </div>

                                    
                                    </div>
                                    
                                </div>

                            </div>

                        <?php endwhile; ?>

                    </div>
                    
                    <!-- Navegación -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    
                    <!-- Paginación -->
                    <div class="swiper-pagination"></div>
                </div>
                
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof Swiper === 'undefined') {
                        var swiperCSS = document.createElement('link');
                        swiperCSS.rel = 'stylesheet';
                        swiperCSS.href = 'https://unpkg.com/swiper/swiper-bundle.min.css';
                        document.head.appendChild(swiperCSS);
                        
                        var swiperJS = document.createElement('script');
                        swiperJS.src = 'https://unpkg.com/swiper/swiper-bundle.min.js';
                        swiperJS.onload = initializeSwiper;
                        document.head.appendChild(swiperJS);
                    } else {
                        initializeSwiper();
                    }
                    
                    function initializeSwiper() {
                        const swiper = new Swiper('.bs-entradas-rel-slider', {
                            slidesPerView: 1,
                            spaceBetween: 20,
                            loop: true,
                            centeredSlides: true,
                            navigation: {
                                nextEl: '.swiper-button-next',
                                prevEl: '.swiper-button-prev',
                            },
                        });

                        // Reemplazar botones por SVG
                        const prevButton = document.querySelector('.swiper-button-prev');
                        const nextButton = document.querySelector('.swiper-button-next');

                        if (prevButton) {
                            prevButton.innerHTML = `<svg viewBox="0 0 320 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z"></path></svg>`;
                        }

                        if (nextButton) {
                            nextButton.innerHTML = `<svg viewBox="0 0 320 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>`;
                        }
                    }
                });
                </script>
            <?php else : ?>
                <p>No se encontraron entradas relacionadas.</p>
            <?php endif; ?>
            
            <?php wp_reset_postdata(); ?>
        </div>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('seccion_entradas_relacionadas_blog', 'trekkium_seccion_entradas_relacionadas_blog');