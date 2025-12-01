<?php
// Shortcode [ba_query] para el blog
add_shortcode('ba_query', 'trekkium_query_blog');
function trekkium_query_blog() {
    ob_start();

    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) :
    ?>

        <div class="ba-query-wrapper">
            <!-- Mostrar filtros activos (rellenado por JS) -->
            <div class="filtros-activos">
                <div class="filtros-lista"></div>
            </div>

            <div class="ba-query-grid">

                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $autor_id   = get_the_author_meta('ID');
                    $avatar     = get_avatar_url($autor_id, ['size' => 80]);
                    $permalink  = get_permalink();
                    $categorias = get_the_category();
                        $categoria_slugs = (!empty($categorias) && !is_wp_error($categorias)) ? implode(',', wp_list_pluck($categorias, 'slug')) : '';
                        $author_nicename = get_the_author_meta('user_nicename');
                    $fecha      = get_the_date('d M Y');
                    $views      = (int) get_post_meta(get_the_ID(), 'post_views_count', true);
                    ?>
                    
                        <div class="ba-query-item" data-categorias="<?php echo esc_attr($categoria_slugs); ?>" data-autor="<?php echo esc_attr($author_nicename); ?>">
                        <!-- Imagen con avatar ENLAZADA -->
                        <a href="<?php echo esc_url($permalink); ?>" class="ba-image-link">
                            <div class="ba-contenedor-imagen">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php echo get_the_post_thumbnail(get_the_ID(), 'large', ['class' => 'ba-imagen']); ?>
                                <?php else: ?>
                                    <div class="ba-imagen-placeholder"></div>
                                <?php endif; ?>
                                
                                <?php if ($avatar) : ?>
                                    <div class="ba-avatar-contenedor">
                                        <img src="<?php echo esc_url($avatar); ?>" class="ba-avatar-autor" alt="<?php echo esc_attr(get_the_author()); ?>" />
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>

                        <div class="ba-query-contenido">
                            <!-- Título ENLAZADO -->
                            <a href="<?php echo esc_url($permalink); ?>" class="ba-query-contenido-link">
                                <h3 class="ba-titulo"><?php the_title(); ?></h3>
                            </a>

                            <!-- Categorías -->
                            <?php if (!empty($categorias)) : ?>
                                <div class="ba-query-categorias">
                                    <?php 
                                    foreach ($categorias as $categoria) :
                                        if (!empty($categoria->name)) : ?>
                                            <a href="<?php echo esc_url(get_category_link($categoria->term_id)); ?>" class="ba-categoria-link">
                                                <?php echo esc_html($categoria->name); ?>
                                            </a>
                                        <?php 
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            <?php endif; ?>

                            <!-- 📅 Fecha y 👁️ Vistas -->
                            <div class="ba-info-extra">
                                <div class="ba-info-item">
                                    <!-- Calendario SVG -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
                                        <path fill="currentColor" d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-1.99.9-1.99 2L3 20
                                        c0 1.1.9 2 2 2h14c1.1 0 2-.9
                                        2-2V6c0-1.1-.9-2-2-2zm0
                                        16H5V9h14v11zM7 11h5v5H7z"/>
                                    </svg>
                                    <span><?php echo esc_html($fecha); ?></span>
                                </div>

                                <div class="ba-info-item">
                                    <!-- Ojo SVG -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
                                        <path fill="currentColor" d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11
                                        7.5s9.27-3.11 11-7.5C21.27
                                        7.61 17 4.5 12 4.5zm0
                                        12c-2.48 0-4.5-2.02-4.5-4.5S9.52
                                        7.5 12 7.5s4.5 2.02 4.5
                                        4.5-2.02 4.5-4.5
                                        4.5zm0-7c-1.38 0-2.5
                                        1.12-2.5 2.5s1.12 2.5
                                        2.5 2.5 2.5-1.12
                                        2.5-2.5S13.38 9.5 12 9.5z"/>
                                    </svg>
                                    <span><?php echo esc_html($views); ?></span>
                                </div>
                            </div>

                        </div>
                    </div>

                <?php endwhile; ?>

            </div>
        </div>
    <?php
    endif;

    wp_reset_postdata();
    return ob_get_clean();
}
?>