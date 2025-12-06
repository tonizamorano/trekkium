<?php

function contenido_mis_actividades_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>Debes iniciar sesión para ver tus actividades.</p>';
    }

    $current_user_id = get_current_user_id();
    $args = array(
        'post_type'      => 'product',
        'author'         => $current_user_id,
        'posts_per_page' => -1,
        'post_status'    => array('publish', 'pending', 'draft', 'private', 'wc-finalizado', 'wc-cancelado')
    );

    $query = new WP_Query($args);
    
    ob_start();
    ?>

    <div class="mc-mis-actividades-contenedor">

        <div class="mc-mis-actividades-titulo">
            <h2>Mis actividades</h2>	
        </div>

        <div class="mc-mis-actividades-contenido">

            <?php if ($query->have_posts()) : ?>

                <?php while ($query->have_posts()) : 
                    $query->the_post(); 
                    $post_id = get_the_ID();
                    $fecha = get_post_meta($post_id, 'fecha', true);
                    $fecha_formateada = $fecha ? date_i18n('d/m/y', strtotime($fecha)) : 'N/A';
                    $total_ventas = get_post_meta($post_id, 'total_sales', true) ?: 0;
                    $estado_traducido = traducir_estado(get_post_status($post_id));

                    // Provincia y región
                    $provincias = wp_get_post_terms($post_id, 'provincia');
                    $regiones = wp_get_post_terms($post_id, 'region');
                    $provincia_texto = !empty($provincias) ? esc_html($provincias[0]->name) : 'Sin provincia';
                    $region_texto = !empty($regiones) ? esc_html($regiones[0]->name) : 'Sin región';
                    $ubicacion_texto = $provincia_texto . ' (' . $region_texto . ')';
                ?>

                    <!-- TODO EL ITEM ES AHORA UN ENLACE A VER DETALLES -->
                    <a class="mc-mis-actividades-contenedor-item" 
                    href="<?php echo esc_url(home_url('/detalles-actividad/?id=' . $post_id)); ?>">

                        <div class="mc-mis-actividades-seccion-item">

                            <div class="mc-mis-actividades-imagen-contenedor">
                                <?php if (has_post_thumbnail()) : ?>
                                    <img src="<?php echo get_the_post_thumbnail_url($post_id, 'large'); ?>" 
                                        alt="<?php the_title(); ?>" 
                                        class="mc-mis-actividades-imagen">
                                <?php else : ?>
                                    <div class="mc-mis-actividades-imagen no-image">
                                        <span>Sin imagen</span>
                                    </div>
                                <?php endif; ?>

                                <div class="mc-mis-actividades-estado-item">
                                    <?php echo esc_html($estado_traducido); ?>
                                </div>
                            </div>

                        </div>

                        <div class="mc-mis-actividades-item-contenido">

                            <div class="mc-mis-actividades-contenido-titulo">
                                <?php the_title(); ?>
                            </div>

                            <div class="mc-mis-actividades-contenido-ubicacion">
                                <?php echo esc_html($ubicacion_texto); ?>
                            </div>

                            <div class="mc-mis-actividades-fecha-item">
                                <?php echo do_shortcode('[icon_fecha1]'); ?>
                                <?php echo esc_html($fecha_formateada); ?>
                            </div>

                            <div class="mc-mis-actividades-plazas-item">
                                <?php echo do_shortcode('[icon_user_avatar]'); ?>
                                <?php echo esc_html($total_ventas); ?>
                            </div>

                        </div>

                    </a>

                <?php endwhile; ?>

                <?php wp_reset_postdata(); ?>

            <?php else : ?>

                <div class="mc-mis-actividades-contenedor-sin-actividades">
                    <p>En este momento no tienes actividades creadas.</p>
                </div>

            <?php endif; ?>

        </div>
    
    </div>

    <div class="mc-mis-actividades-nueva-actividad">
        <a href="<?php echo esc_url(home_url('/nueva-actividad/')); ?>" class="mc-mis-actividades-boton-nueva-actividad">
            <?php echo do_shortcode('[icon_mas]'); ?>
            <span>Nueva actividad</span>
        </a>
    </div>

    <?php
    return ob_get_clean();
}

function traducir_estado($post_status) {
    switch ($post_status) {
        case 'publish': return 'Publicado';
        case 'draft': return 'Borrador';
        case 'pending': return 'Pendiente';
        case 'private': return 'Privado';
        case 'wc-finalizado': return 'Finalizado';
        case 'wc-cancelado': return 'Cancelado';
        default: return ucfirst($post_status);
    }
}

add_shortcode('contenido_mis_actividades', 'contenido_mis_actividades_shortcode');
