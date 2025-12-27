<?php
// --- Cron para cancelar actividades automáticamente 24h antes si están "Sin confirmar" ---

// 1. Programar evento si no existe
add_action('wp', function() {
    if (!wp_next_scheduled('cancelar_actividades_automaticamente')) {
        wp_schedule_event(time(), 'hourly', 'cancelar_actividades_automaticamente');
    }
});

// 2. Función que revisa las actividades
add_action('cancelar_actividades_automaticamente', function() {
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'     => 'estado_actividad',
                'value'   => 'Plazas disponibles',
                'compare' => '='
            ),
        ),
        'posts_per_page' => -1,
    );

    $actividades = get_posts($args);
    $ahora = current_time('timestamp');

    foreach ($actividades as $actividad) {
        $fecha_meta = get_post_meta($actividad->ID, 'fecha', true);
        $hora_meta  = get_post_meta($actividad->ID, 'hora', true);

        if (!$fecha_meta || !$hora_meta) continue;

        // Crear timestamp de la actividad
        $fecha_hora_actividad = strtotime($fecha_meta . ' ' . $hora_meta);

        // Si queda menos de 24h
        if ($fecha_hora_actividad - $ahora <= 24 * 3600 && $fecha_hora_actividad - $ahora > 0) {
            // Cambiar estado de WooCommerce a Cancelado
            $actividad->post_status = 'wc-cancelado';
            wp_update_post($actividad);

            // Actualizar meta estado_actividad
            update_post_meta($actividad->ID, 'estado_actividad', 'Cancelada');
            update_post_meta($actividad->ID, 'mensaje_actividad', 'Grupo mínimo insuficiente, actividad cancelada automáticamente.');
        }
    }
});

// 3. Limpiar cron al desactivar el plugin/snippet
register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('cancelar_actividades_automaticamente');
});
?>
