<?php
// Shortcode para Detalles de la actividad
add_shortcode('mc_ma_da_detalles_actividad', 'mc_ma_da_mostrar_detalles_actividad');

function mc_ma_da_mostrar_detalles_actividad($atts) {
    if (!is_user_logged_in()) return '';

    // Atributos: pasamos el ID de la actividad
    $atts = shortcode_atts([
        'id' => 0,
    ], $atts, 'mc_ma_da_detalles_actividad');

    $product_id = intval($atts['id']);
    if (!$product_id) return '';

    $producto_nombre = get_the_title($product_id);

    $parent_product_id = wp_get_post_parent_id($product_id);
    if ($parent_product_id) $product_id = $parent_product_id;

    // Metadatos
    $fecha_raw = get_post_meta($product_id, 'fecha', true);
    $hora_meta = get_post_meta($product_id, 'hora', true);

    $fecha_actividad = $fecha_raw ? date('d/m/Y', strtotime($fecha_raw)) : '—';
    $hora_actividad = $hora_meta ? esc_html($hora_meta) . 'h' : '—';

    // Estado de la publicación
    $estado_post = get_post_status($product_id);
    $estados_traducidos = [
        'publish'   => 'Publicada',
        'draft'     => 'Borrador',
        'pending'   => 'Pendiente',
        'private'   => 'Privado',
        'future'    => 'Programado',
        'trash'     => 'Papelera',
        'wc-finalizado' => 'Finalizada',
        'wc-cancelado' => 'Cancelada',
    ];
    $estado_traducido = $estados_traducidos[$estado_post] ?? ucfirst($estado_post);

    // Provincia y región
    $provincia_terms = wp_get_post_terms($product_id, 'provincia');
    $region_terms = wp_get_post_terms($product_id, 'region');

    $provincia = !empty($provincia_terms) ? $provincia_terms[0]->name : '—';
    $region = !empty($region_terms) ? $region_terms[0]->name : '—';

    ob_start(); ?>

    <div class="mc-ma-da-contenedor">

        <?php if (has_post_thumbnail($product_id)) : ?>
            <div class="imagen-actividad" style="position: relative;">
                <?php echo get_the_post_thumbnail($product_id,'large',[
                    'style'=>'width:100%;aspect-ratio:16/9;border-radius:0;object-fit:cover;'
                ]); ?>
                
                <!-- Estado de la publicación sobre la imagen -->
                <div class="mc-ma-da-estado-sobre-imagen"><?php echo esc_html($estado_traducido); ?></div>
            </div>
        <?php endif; ?>

        <div class="mc-ma-da-contenido"> 
            
            <div class="mc-ma-da-titulo-reserva">
                <?php echo esc_html($producto_nombre); ?>
            </div>

            <div class="mc-ma-da-ubicacion"><?php echo esc_html($provincia . ' (' . $region . ')'); ?></div>

            <!-- Fecha y Hora del producto -->
            <div class="mc-ma-da-fila-datos">
                <span class="etiqueta">Fecha</span><span class="valor"><?php echo esc_html($fecha_actividad); ?></span>
            </div>
            <div class="mc-ma-da-fila-datos">
                <span class="etiqueta">Hora</span><span class="valor"><?php echo esc_html($hora_actividad); ?></span>
            </div>

            <!-- Botón Ver Actividad -->

            <div class="mc-ma-da-boton-wrapper">

                <!-- Botón Ver -->
                <a href="<?php echo get_permalink($product_id); ?>" class="mc-ma-da-boton">
                    <?php echo do_shortcode('[icon_ojo1]'); ?>
                    <span>Ver</span>
                </a>

                <!-- Botón Editar actividad -->
                <a href="<?php echo site_url('/editar-actividad/?post_id=' . $product_id); ?>" 
                class="mc-ma-da-boton mc-ma-da-boton-editar">
                    <?php echo do_shortcode('[icon_ojo1]'); ?>
                    <span>Editar</span>
                </a>

            </div>


        </div>

    </div>

    <?php
    return ob_get_clean();
}
