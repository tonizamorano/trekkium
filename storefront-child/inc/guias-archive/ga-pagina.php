<?php

// Registramos el shortcode
function pagina_guias_archive_shortcode() {
    ob_start();
    ?>

    <div class="pagina-grid-3366">

        <!-- Columna izquierda -->

        <div class="pagina-columna33-sticky">

            <?php echo do_shortcode('[seccion_guias_archive_filtros]'); ?>

        </div>

        <!-- Columna derecha -->

        <div class="pagina-columna66">

            <?php echo do_shortcode('[query_guias]'); ?>

        </div>

    </div>    

    <?php
    return ob_get_clean();
}
add_shortcode('pagina_guias_archive', 'pagina_guias_archive_shortcode');