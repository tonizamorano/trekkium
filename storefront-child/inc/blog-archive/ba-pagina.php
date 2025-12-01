<?php

// Registramos el shortcode
function pagina_blog_archive_shortcode() {
    ob_start();
    ?>

    <div class="pagina-grid-3366">

        <!-- Columna izquierda -->

        <div class="pagina-columna33-sticky">

            <?php echo do_shortcode('[seccion_blog_archive_filtros]'); ?>

        </div>

        <!-- Columna derecha -->

        <div class="pagina-columna66">
        
            <?php echo do_shortcode('[ba_query]'); ?>

        </div>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('pagina_blog_archive', 'pagina_blog_archive_shortcode');