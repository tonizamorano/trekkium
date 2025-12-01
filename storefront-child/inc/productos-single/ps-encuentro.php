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

            <!-- FILAS -->

            <!-- Fecha -->

            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <svg viewBox="0 0 448 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M436 160H12c-6.627 0-12-5.373-12-12v-36c0-26.51 21.49-48 48-48h48V12c0-6.627 
                        5.373-12 12-12h40c6.627 0 12 5.373 12 12v52h128V12c0-6.627 5.373-12 
                        12-12h40c6.627 0 12 5.373 12 12v52h48c26.51 0 48 21.49 48 
                        48v36c0 6.627-5.373 12-12 12zM12 192h424c6.627 0 12 5.373 
                        12 12v260c0 26.51-21.49 48-48 
                        48H48c-26.51 0-48-21.49-48-48V204c0-6.627 
                        5.373-12 12-12zm333.296 95.947l-28.169-28.398c-4.667-4.705-12.265-4.736-16.97-.068L194.12 
                        364.665l-45.98-46.352c-4.667-4.705-12.266-4.736-16.971-.068l-28.397 
                        28.17c-4.705 4.667-4.736 12.265-.068 
                        16.97l82.601 83.269c4.667 4.705 12.265 4.736 
                        16.97.068l142.953-141.805c4.705-4.667 
                        4.736-12.265.068-16.97z"/>
                    </svg>
                    <span>Fecha:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($fecha_formateada); ?>
                </div>

            </div>


            <!-- Hora -->

            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <svg viewBox="0 0 512 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm57.1 350.1L224.9 294c-3.1-2.3-4.9-5.9-4.9-9.7V116c0-6.6 5.4-12 12-12h48c6.6 0 12 5.4 12 12v137.7l63.5 46.2c5.4 3.9 6.5 11.4 2.6 16.8l-28.2 38.8c-3.9 5.3-11.4 6.5-16.8 2.6z"/></svg>
                    <span>Hora:</span>
                </div>

                <div class="ps-fila-col2">
                    <?php echo esc_html($hora_formateada); ?>
                </div>

            </div>

            <!-- Lugar -->

            <div class="ps-contenido-fila">

                <div class="ps-fila-col1">
                    <svg viewBox="0 0 384 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M168 0C75.13 0 0 75.13 0 168c0 87.25 144.1 303.5 159.3 325.5c4.75 6.75 14.75 6.75 19.5 0C239.9 471.5 384 255.3 384 168C384 75.13 308.9 0 216 0H168zM192 240c-39.8 0-72-32.2-72-72s32.2-72 72-72s72 32.2 72 72S231.8 240 192 240z"/></svg>
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
