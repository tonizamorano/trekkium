<?php

// Registramos el shortcode
function pagina_detalles_actividad_shortcode() {
    ob_start();
    ?>

    <div class="pagina-grid-3366">

        <!-- Columna izquierda -->

        <div class="pagina-columna33-sticky">

            <!-- Sección Card de Mi cuenta con menú -->
            <?php echo do_shortcode('[mc_user_card]'); ?>

        </div>

        <!-- Columna derecha -->

        <div class="pagina-columna66">

            <!-- Sección Mis actividades -->
            <?php echo do_shortcode('[contenido_detalles_actividad]'); ?>
            

        </div>

    </div>    

    <?php
    return ob_get_clean();
}
add_shortcode('pagina_detalles_actividad', 'pagina_detalles_actividad_shortcode');
