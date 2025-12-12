<?php
/*
Plugin Name: Shortcode Página Single Productos
Description: Crea un layout de archive de productos con filtros y columna de productos.
Version: 1.2
Author: Tu Nombre
*/

// Función para mostrar los datos del organizador de un producto
function trekkium_mostrar_organizador() {
    if (!is_singular('product')) return;

    $autor_id = get_post_field('post_author', get_the_ID());
    if (!$autor_id) return '';

    $nombre = esc_html(get_the_author_meta('display_name', $autor_id));
    $billing_state_code = get_user_meta($autor_id, 'billing_state', true);
    $billing_country_code = get_user_meta($autor_id, 'billing_country', true) ?: 'ES';
    $provincia = esc_html(obtener_nombre_estado_wc($billing_state_code, $billing_country_code));

    echo '<div class="organizador-producto">';
    echo '<div class="organizador-nombre">' . $nombre . '</div>';
    if ($provincia) {
        echo '<div class="organizador-provincia">' . $provincia . '</div>';
    }
    echo '</div>';
}

// Shortcode principal
function pagina_single_productos_shortcode() {
    if (!is_singular('product')) return;

    ob_start();
    ?>

    <!-- Contenedor Grid 6633 -->

    <div class="pagina-grid-6633">

        <!-- Columna izquierda -->
            
        <div class="pagina-columna66-sticky">    
            <?php echo do_shortcode('[seccion_contenido]'); ?>
        </div>

        <!-- Columna derecha -->

        <div class="ps-pagina-columna33">
            <?php echo do_shortcode('[seccion_organizador]'); ?>
            <?php echo do_shortcode('[seccion_fichatecnica]'); ?> 
            <?php echo do_shortcode('[seccion_fecha_hora_encuentro]'); ?>            
            <?php echo do_shortcode('[seccion_plazas]'); ?>  
            <?php echo do_shortcode('[seccion_dificultad_tecnica]'); ?>
            <?php echo do_shortcode('[seccion_experiencia_requisitos]'); ?>
            <?php echo do_shortcode('[seccion_planificacion]'); ?>
            <?php echo do_shortcode('[seccion_material_necesario]'); ?>   
            <?php echo do_shortcode('[seccion_incluye]'); ?>
            <?php echo do_shortcode('[seccion_precio_reserva]'); ?> 
             
            <!-- <?php echo do_shortcode('[ps_entradas_relacionadas]'); ?> -->          
        </div>
    
    </div>

    <div>
        <?php echo do_shortcode('[seccion_entradas_relacionadas]'); ?> 
    </div>
   
    <?php
    return ob_get_clean();
}
add_shortcode('pagina_single_productos', 'pagina_single_productos_shortcode');
