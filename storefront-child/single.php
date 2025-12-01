<?php
/**
 * The template for displaying all single posts.
 *
 * @package storefront
 */

get_header(); ?>

<!-- Contenedor principal -->    

<div class="pagina-grid-6633">

    <!-- Columna izquierda -->
    <div class="pagina-columna66">    

        <!-- Sección Contenido -->
        <?php echo do_shortcode('[bs_principal]'); ?>
        
    </div>

    <!-- Columna derecha -->
    <div class="pagina-columna33-sticky">

        <!-- Sección Autor -->
        <?php echo do_shortcode('[bs_autor]'); ?>
        <?php echo do_shortcode('[seccion_entradas_relacionadas_blog]'); ?>                

    </div>

</div>

<?php

get_footer();
