<?php get_header(); ?>

<?php
$author    = get_queried_object();
$author_id = $author->ID;
?>

<!-- NEW -->

<div class="pagina-grid-6633">

    <!-- Columna izquierda -->
    <div class="pagina-columna66">
        <!-- Sección Sobre mi -->
        <?php echo do_shortcode('[gs_seccion_principal]'); ?>                         
    </div>

    <!-- Columna derecha -->
    <div class="pagina-columna33" style="display:flex; flex-direction:column; gap: 15px;">
        <?php echo do_shortcode('[seccion_proximas_actividades]'); ?>
        <?php 
        // echo do_shortcode('[seccion_valoraciones]'); 
        ?>
        <?php 
        // echo do_shortcode('[valoraciones-guias-form]'); 
        ?>
    </div>

</div>

<?php get_footer(); ?>
