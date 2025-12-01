<?php
// SOLUCIÓN DEFINITIVA - Regenerar SKU con sufijos para duplicados
function trekkium_generar_sku( $post_id ) {
    // Evitar autosaves y revisiones
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) ) return;
    if ( get_post_type( $post_id ) !== 'product' ) return;

    // Obtener fecha ACTUAL
    $fecha = get_post_meta( $post_id, 'fecha', true );
    if ( empty($fecha) ) return;

    $fecha_obj = date_create( $fecha );
    if ( ! $fecha_obj ) return;

    $aammdd = date_format( $fecha_obj, 'ymd' );

    // Obtener autor ACTUAL y su código_guia
    $post = get_post( $post_id );
    $author_id = $post->post_author;
    $codigo_guia = get_user_meta( $author_id, 'codigo_guia', true );
    if ( empty($codigo_guia) ) $codigo_guia = 'TG0';

    // Obtener hora de la actividad (para ordenar)
    $hora = get_post_meta( $post_id, 'hora', true ); // formato HH:MM o similar
    if ( empty($hora) ) $hora = '00:00';

    // Buscar productos del mismo día y mismo guía
    $productos_mismo_dia = trekkium_buscar_productos_duplicados( $post_id, $fecha, $author_id );

    // Determinar el sufijo basado en la hora
    $sufijo = trekkium_determinar_sufijo( $post_id, $productos_mismo_dia, $hora );

    // Generar SKU base
    $sku_base = $aammdd . '-' . $codigo_guia;
    
    // Añadir sufijo si es necesario
    $sku = $sufijo > 0 ? $sku_base . '-' . $sufijo : $sku_base;

    // Guardar SKU
    update_post_meta( $post_id, '_sku', $sku );
    
    return $sku;
}

// Función para buscar productos duplicados (mismo día, mismo guía)
function trekkium_buscar_productos_duplicados( $post_id_actual, $fecha, $author_id ) {
    $args = array(
        'post_type' => 'product',
        'author' => $author_id,
        'posts_per_page' => -1,
        'post_status' => 'any',
        'post__not_in' => array( $post_id_actual ), // Excluir el producto actual
        'meta_query' => array(
            array(
                'key' => 'fecha',
                'value' => $fecha,
                'compare' => '='
            )
        )
    );
    
    return get_posts( $args );
}

// Función para determinar el sufijo basado en la hora
function trekkium_determinar_sufijo( $post_id_actual, $productos_duplicados, $hora_actual ) {
    // Si no hay productos duplicados, no necesita sufijo
    if ( empty($productos_duplicados) ) {
        return 0;
    }

    // Crear array con todos los productos (incluyendo el actual)
    $todos_productos = array();
    
    // Añadir producto actual
    $todos_productos[] = array(
        'ID' => $post_id_actual,
        'hora' => $hora_actual
    );
    
    // Añadir productos duplicados
    foreach ( $productos_duplicados as $producto ) {
        $hora_producto = get_post_meta( $producto->ID, 'hora', true );
        if ( empty($hora_producto) ) $hora_producto = '00:00';
        
        $todos_productos[] = array(
            'ID' => $producto->ID,
            'hora' => $hora_producto
        );
    }
    
    // Ordenar por hora (más temprano primero)
    usort( $todos_productos, function( $a, $b ) {
        return strcmp( $a['hora'], $b['hora'] );
    });
    
    // Encontrar la posición del producto actual en la lista ordenada
    foreach ( $todos_productos as $index => $producto ) {
        if ( $producto['ID'] == $post_id_actual ) {
            // Si hay más de uno, usar sufijo empezando por 1
            return count( $todos_productos ) > 1 ? ( $index + 1 ) : 0;
        }
    }
    
    return 0;
}

// EJECUTAR EN MÚLTIPLES HOOKS para asegurar que funcione
add_action( 'save_post_product', 'trekkium_generar_sku', 20, 1 );
add_action( 'acf/save_post', 'trekkium_generar_sku', 20, 1 );
add_action( 'wp_after_insert_post', 'trekkium_generar_sku', 20, 1 );

// También actualizar SKUs existentes cuando cambia la hora
add_action( 'updated_post_meta', 'trekkium_actualizar_sku_por_hora', 10, 4 );
function trekkium_actualizar_sku_por_hora( $meta_id, $post_id, $meta_key, $meta_value ) {
    if ( $meta_key === 'hora' && get_post_type( $post_id ) === 'product' ) {
        // Esperar un poco para que todos los metas se guarden
        add_action( 'shutdown', function() use ( $post_id ) {
            trekkium_generar_sku( $post_id );
            
            // También regenerar SKUs de otros productos del mismo día/guía
            $fecha = get_post_meta( $post_id, 'fecha', true );
            $author_id = get_post( $post_id )->post_author;
            
            if ( $fecha ) {
                $productos_duplicados = trekkium_buscar_productos_duplicados( $post_id, $fecha, $author_id );
                foreach ( $productos_duplicados as $producto ) {
                    trekkium_generar_sku( $producto->ID );
                }
            }
        });
    }
}

// Bloquear edición manual
add_action( 'woocommerce_product_options_inventory_product_data', function() {
    echo '<script>jQuery(document).ready(function($){ $("#_sku").prop("readonly", true); });</script>';
});