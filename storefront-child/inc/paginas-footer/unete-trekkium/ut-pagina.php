<?php

// Registramos el shortcode
function pagina_unete_a_trekkium_shortcode() {
    ob_start();
    ?>

    <div class="pagina-grid-6633">

        <!-- Columna izquierda -->

       <div class="pagina-columna66">
          
            <?php echo do_shortcode('[ut_contenido]'); ?>

        </div>

        <!-- Columna derecha -->

         <div class="pagina-columna33-sticky">

            <?php echo do_shortcode('[ut_form]'); ?>

        </div>

    </div>    

    <?php
    return ob_get_clean();
}
add_shortcode('pagina_unete_a_trekkium', 'pagina_unete_a_trekkium_shortcode');