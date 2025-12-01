<?php
// Shortcode [pagina_inicio] que ahora llama al buscador
function trekkium_pagina_inicio_shortcode() {
    ob_start();

    // Sección Slider Principal
    echo do_shortcode('[in_slider_ppal]');


    // Sección Buscador de Actividades
    echo do_shortcode('[in_buscador_actividades]');

    // Sección Próximas Actividades
    echo do_shortcode('[in_proximas_actividades]');

    // Sección Próximas Actividades
    echo do_shortcode('[in_ultimas_entradas]');

    
    

    return ob_get_clean();
}
add_shortcode('pagina_inicio', 'trekkium_pagina_inicio_shortcode');
