<?php

// Registramos el shortcode
function pagina_mis_actividades_shortcode() {
    ob_start();
    ?>

    <div class="pagina-grid-3366">

        <!-- Columna izquierda -->

        <div class="pagina-columna33-sticky">

            <!-- Sección Card de Mi cuenta con menú -->
            <?php echo do_shortcode('[seccion_card_micuenta]'); ?>

        </div>

        <!-- Columna derecha -->

        <div class="pagina-columna66">

            <!-- Sección Mis actividades -->
            <?php echo do_shortcode('[contenido_mis_actividades]'); ?>

        </div>

    </div>    

    <?php
    return ob_get_clean();
}
add_shortcode('pagina_mis_actividades', 'pagina_mis_actividades_shortcode');
