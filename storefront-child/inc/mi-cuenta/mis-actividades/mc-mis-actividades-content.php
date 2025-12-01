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

    <!-- Titular de sección -->

    <div class="mis-actividades-seccion-titular">
        <h2 class="mis-actividades-titular">
            <span>Mis actividades</span>
        </h2>	
    </div>

     <!-- Query de Mis actividades -->

    <div class="mis-actividades-container">

        <?php if ($query->have_posts()) : ?>

            <?php while ($query->have_posts()) : 
                $query->the_post(); 
                $post_id = get_the_ID();
                $fecha = get_post_meta($post_id, 'fecha', true);
                $fecha_formateada = $fecha ? date_i18n('d/m/y', strtotime($fecha)) : 'N/A';
                $total_ventas = get_post_meta($post_id, 'total_sales', true) ?: 0;
                $estado_traducido = traducir_estado(get_post_status($post_id));
                $clase_estado = obtener_clase_estado(get_post_status($post_id));
                // Obtener los términos de la taxonomía "modalidad"
                $modalidades = wp_get_post_terms($post_id, 'modalidad');
                $modalidad_texto = !empty($modalidades) ? esc_html($modalidades[0]->name) : 'Sin modalidad';
                
                // Obtener provincia y región
                $provincias = wp_get_post_terms($post_id, 'provincia');
                $regiones = wp_get_post_terms($post_id, 'region');
                $provincia_texto = !empty($provincias) ? esc_html($provincias[0]->name) : 'Sin provincia';
                $region_texto = !empty($regiones) ? esc_html($regiones[0]->name) : 'Sin región';
                $ubicacion_texto = $provincia_texto . ' (' . $region_texto . ')';
            ?>

                <div class="mis-actividades-contenedor-item">

                    <div class="mis-actividades-seccion-item">

                        <!-- Modalidad -->

                        <div class="mis-actividades-modalidad-item">
                            <?php echo $modalidad_texto; ?>
                        </div>

                        <!-- Título -->
                        
                        <div class="mis-actividades-titulo">
                            <?php the_title(); ?>
                        </div>

                        <!-- Ubicación (Provincia y Región) -->
                        
                        <div class="mis-actividades-ubicacion">
                            <?php echo esc_html($ubicacion_texto); ?>
                        </div>

                    </div>

                    <!-- Imagen destacada -->
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <img src="<?php echo get_the_post_thumbnail_url($post_id, 'large'); ?>" 
                             alt="<?php the_title(); ?>" 
                             class="mis-actividades-imagen">
                    <?php else : ?>

                        <div class="mis-actividades-imagen no-image">
                            <span>Sin imagen</span>
                        </div>
                    <?php endif; ?>

                    <!-- Sección Info -->
                    
                    <div class="mis-actividades-info-container">

                        <!-- Fecha de la actividad -->

                        <div class="mis-actividades-fecha-item">
                            <svg class="mis-actividades-fecha-icon" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M436 160H12c-6.627 0-12-5.373-12-12v-36c0-26.51 21.49-48 48-48h48V12c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v52h128V12c0-6.627 5.373-12 12-12h40c6.627 0 12 5.373 12 12v52h48c26.51 0 48 21.49 48 48v36c0 6.627-5.373 12-12 12zM12 192h424c6.627 0 12 5.373 12 12v260c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V204c0-6.627 5.373-12 12-12zm333.296 95.947l-28.169-28.398c-4.667-4.705-12.265-4.736-16.97-.068L194.12 364.665l-45.98-46.352c-4.667-4.705-12.266-4.736-16.971-.068l-28.397 28.17c-4.705 4.667-4.736 12.265-.068 16.97l82.601 83.269c4.667 4.705 12.265 4.736 16.97.068l142.953-141.805c4.705-4.667 4.736-12.265.068-16.97z"></path></svg>
                            <?php echo esc_html($fecha_formateada); ?>
                        </div>

                        <!-- Nº de plazas reservadas -->
                        
                        <div class="mis-actividades-plazas-item">
                            <svg class="mis-actividades-plazas-icon" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            <?php echo esc_html($total_ventas); ?>
                        </div>

                        <!-- Estado de la actividad -->
                        
                        <div class="mis-actividades-estado-item <?php echo esc_attr($clase_estado); ?>">
                            <?php echo esc_html($estado_traducido); ?>
                        </div>

                    </div>

                     <!-- Botones Editar Actividad y Detalles de la Actividad -->

                    <div class="mis-actividades-seccion-botones">

                        <div class="mis-actividades-boton-detalles">
                            <a href="<?php echo esc_url(home_url('/editar-actividad/?post_id=' . $post_id)); ?>">Editar</a>
                        </div>

                        <div class="mis-actividades-boton-detalles">
                            <a href="<?php echo esc_url(home_url('/detalles-actividad/?id=' . $post_id)); ?>">Ver detalles</a>
                        </div>


                    </div>     

                </div>

            <?php endwhile; ?>

            <?php wp_reset_postdata(); ?>

        <?php else : ?>

            <div class="contenedor-no-actividades">
                <p>En este momento no tienes actividades creadas.</p>
            </div>

        <?php endif; ?>

    </div>

    <div class="mis-actividades-nueva-actividad">
        <a href="<?php echo esc_url(home_url('/nueva-actividad/')); ?>" class="boton-nueva-actividad">
            <svg class="icono-nueva-actividad" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-hidden="true">
                <path fill="currentColor" d="M256 8C119.034 8 8 119.034 8 256s111.034 248 248 248 248-111.034 248-248S392.966 8 256 8zm104 264h-80v80c0 8.837-7.163 16-16 16h-16c-8.837 
                0-16-7.163-16-16v-80h-80c-8.837 0-16-7.163-16-16v-16c0-8.837 
                7.163-16 16-16h80v-80c0-8.837 7.163-16 
                16-16h16c8.837 0 16 7.163 16 16v80h80c8.837 
                0 16 7.163 16 16v16c0 8.837-7.163 
                16-16 16z"/>
            </svg>
            <span>Nueva actividad</span>
        </a>
    </div>

    <style>
    .mis-actividades-nueva-actividad {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 30px;
    }

    .boton-nueva-actividad {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: #E67E22; /* color naranja tema */
        color: #fff;
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 10px;
        font-weight: 500;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .boton-nueva-actividad:hover {
        background-color: #cf6d18; /* tono más oscuro al pasar el ratón */
        transform: scale(1.05);
    }

    .icono-nueva-actividad {
        width: 18px;
        height: 18px;
    }
    </style>

    <?php
    return ob_get_clean();
}

// Función auxiliar para traducir estados
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

// Función auxiliar para obtener la clase CSS según el estado
function obtener_clase_estado($post_status) {
    switch ($post_status) {
        case 'draft':
        case 'pending':
        case 'private':
            return 'estado-borrador-pendiente-privado';
        case 'publish':
            return 'estado-publicado';
        case 'wc-finalizado':
            return 'estado-finalizado';
        case 'wc-cancelado':
            return 'estado-cancelado';
        default:
            return 'estado-default';
    }
}

add_shortcode('contenido_mis_actividades', 'contenido_mis_actividades_shortcode');