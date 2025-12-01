<?php

add_action('pre_get_posts', function($query){
    // Solo frontend
    if (is_admin()) return;

    // Solo queries que incluyan 'product'
    $post_type = $query->get('post_type');
    if ($post_type !== 'product') return;

    // Solo productos publicados
    $query->set('post_status', 'publish');

    // Fechas
    $fecha_inicio = date('Y-m-d', strtotime('-1 day'));
    $fecha_fin    = date('Y-m-d', strtotime('+90 days'));

    $meta_query = $query->get('meta_query') ?: [];

    // Solo productos dentro del rango de fechas
    $meta_query[] = [
        'key'     => 'fecha',
        'value'   => [$fecha_inicio, $fecha_fin],
        'compare' => 'BETWEEN',
        'type'    => 'DATE',
    ];

    $query->set('meta_query', $meta_query);
});
