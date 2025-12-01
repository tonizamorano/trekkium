<?php
// Shortcode: [seccion_fecha_hora_encuentro]
add_shortcode('seccion_fecha_hora_encuentro', function() {

    global $post;

    // Obtener campos personalizados
    $fecha = get_post_meta($post->ID, 'fecha', true);
    $hora = get_post_meta($post->ID, 'hora', true);
    $lugar_encuentro = get_post_meta($post->ID, 'encuentro', true);
    $google_maps = get_post_meta($post->ID, 'google_maps', true);

    // Formatear fecha
    $fecha_formateada = $fecha ? date_i18n('d/m/Y', strtotime($fecha)) : '';
    $hora_formateada = $hora ? $hora . ' h' : '';

    ob_start();
    ?>

    
    <div class="ps-contenedor">
        
        <!-- TITULAR -->
        <div class="ps-titular">            
            <h5>Momento y lugar de encuentro</h5>
        </div>

        <!-- CONTENIDO NUEVO -->
        <div class="ps-contenido-momento">  

            <!-- Fecha -->

            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_fecha1]'); ?>
                    </span>
                    <span>Fecha:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($fecha_formateada); ?>
                </div>

            </div>


            <!-- Hora -->

            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_hora]'); ?>
                    </span>
                    <span>Hora:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($hora_formateada); ?>
                </div>

            </div>

            <!-- Lugar -->

            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <span class="ps-icono">
                        <?php echo do_shortcode('[icon_lugar]'); ?>
                    </span>
                    <span>Lugar:</span>
                </div>

                <div class="ps-fila-col2">                    
                </div>

            </div>

            <div class="ps-contenido-fila-direccion">

                <?php echo esc_html($lugar_encuentro); ?>

            </div>

            <div class="ps-contenido-fila">

                <a href="<?php echo esc_url($google_maps); ?>" class="encuentro-boton" target="_blank" rel="noopener noreferrer">Abrir en Google Maps</a>

            </div>

        </div>

    </div>
    
    <?php

    return ob_get_clean();
});
