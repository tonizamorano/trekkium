<?php

// Registramos el shortcode
function pagina_archive_productos_shortcode() {
    ob_start();
    ?>

    <div class="pagina-grid-3366">

        <!-- Columna izquierda -->

        <div class="pagina-columna33-sticky">

            <?php echo do_shortcode('[pa_filtros]'); ?>
            

        </div>

        <!-- Columna derecha -->

        <div class="pagina-columna66">
        
           
            <?php echo do_shortcode('[query_productos]'); ?>

        </div>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('pagina_archive_productos', 'pagina_archive_productos_shortcode');