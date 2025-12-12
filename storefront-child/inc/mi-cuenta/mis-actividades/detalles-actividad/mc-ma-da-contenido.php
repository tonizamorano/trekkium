<?php

function contenido_detalles_actividad_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>Debes iniciar sesión para ver los detalles de la actividad.</p>';
    }

    // Obtener ID desde la URL
    $actividad_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if (!$actividad_id) {
        return '<p>No se ha especificado ninguna actividad.</p>';
    }

    // Verificar que el post exista y sea del tipo correcto
    $actividad = get_post($actividad_id);
    if (!$actividad || $actividad->post_type !== 'product') {
        return '<p>La actividad no existe o no es válida.</p>';
    }

    // Obtener campos meta
    $fecha = get_post_meta($actividad_id, 'fecha', true);
    $hora = get_post_meta($actividad_id, 'hora', true);
    $estado_actividad = get_post_meta($actividad_id, 'estado_actividad', true);

    // Traducir el estado de publicación
    $estado_publicacion = traducir_estado(get_post_status($actividad_id));

    ob_start();
    ?>

    <div class="mc-ma-da-contenedor">  
        
        <div class="mc-ma-da-titulo">
            <h2>Detalles de la actividad</h2>
        </div>

        <div class="mc-ma-da-grid">

            <!-- COLUMNA IZQUIERDA -->
            <div class="mc-ma-da-grid-colizq">

                <!-- Detalles de la actividad -->
                <?php echo do_shortcode('[mc_ma_da_detalles_actividad id="'.$actividad_id.'"]'); ?>

            </div>

            <!-- COLUMNA DERECHA -->
            <div class="mc-ma-da-grid-colder">

                <!-- Estado de la actividad-->
                <?php echo do_shortcode('[mc_ma_da_estado_actividad id="'.$actividad_id.'"]'); ?>
                <?php echo do_shortcode('[mc_ma_da_cambios_actividad id="'.$actividad_id.'"]'); ?>

            </div>

        </div>
    </div>

    <!-- Aquí insertamos EL SHORTCODE DE LISTA DE PARTICIPANTES -->
    <div style="margin-top:15px;">
        <?php echo do_shortcode('[mc_ma_da_lista_participantes id="'.$actividad_id.'"]'); ?>
    </div>

    <?php
    return ob_get_clean();
}

add_shortcode('contenido_detalles_actividad', 'contenido_detalles_actividad_shortcode');
