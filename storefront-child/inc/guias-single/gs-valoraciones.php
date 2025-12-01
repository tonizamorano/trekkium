<?php
function trekkium_seccion_valoraciones($atts) {
    $atts = shortcode_atts([
        'author_id' => 0
    ], $atts, 'seccion_valoraciones');

    $author_id = intval($atts['author_id']);

    // Si no hay ID, usar el autor de la página
    if (!$author_id) {
        $author_id = get_queried_object_id();
    }

    if (!$author_id) return ''; // si no hay autor, no mostrar nada

    // Comprobación opcional: si no hay valoraciones, no mostrar nada
    $valoraciones = do_shortcode('[valoraciones_guias_seccion author_id="' . $author_id . '"]');
    if (empty(trim($valoraciones))) return '';

    ob_start();
    ?>
    <div class="seccion-valoraciones">
        <div class="seccion-valoraciones-titulo">
            
            <h6>Opiniones</h6>
        </div>
        <div class="seccion-valoraciones-contenido">
            <?php echo $valoraciones; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('seccion_valoraciones', 'trekkium_seccion_valoraciones');
