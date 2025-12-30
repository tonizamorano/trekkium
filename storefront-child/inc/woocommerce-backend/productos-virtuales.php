<?php
// Forzar productos solo virtuales y nunca descargables (WooCommerce).
// Pegar en functions.php del tema hijo o en un snippet (Code Snippets).

// 1) Forzar comportamiento en todas las comprobaciones programáticas:
add_filter( 'woocommerce_product_is_virtual', function( $virtual, $product ) {
    return true; // Siempre virtual -> no necesita envío.
}, 10, 2 );

add_filter( 'woocommerce_product_is_downloadable', function( $downloadable, $product ) {
    return false; // Nunca descargable.
}, 10, 2 );

// 2) Al guardar un producto simple o variable, actualizar sus meta para persistir el estado.
add_action( 'save_post_product', function( $post_id, $post, $update ) {
    // Evitar autosaves, revisiones y guardados no deseados.
    if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
        return;
    }

    // Forzar meta: virtual = yes, downloadable = no
    update_post_meta( $post_id, '_virtual', 'yes' );
    update_post_meta( $post_id, '_downloadable', 'no' );

    // Eliminar archivos de descarga si existieran
    delete_post_meta( $post_id, '_downloadable_files' );
}, 20, 3 );

// 3) Al guardar variaciones, forzar las metas de cada variación.
add_action( 'woocommerce_save_product_variation', function( $variation_id, $index ) {
    update_post_meta( $variation_id, '_virtual', 'yes' );
    update_post_meta( $variation_id, '_downloadable', 'no' );
    delete_post_meta( $variation_id, '_downloadable_files' );
}, 10, 2 );

// 4) Evitar que alguien cambie desde el admin: ocultar/forzar las casillas en el editor del producto (JS/CSS en admin).
add_action( 'admin_enqueue_scripts', function( $hook ) {
    // Solo en pantalla de producto (editor).
    $screen = get_current_screen();
    if ( ! $screen || $screen->id !== 'product' ) {
        return;
    }

    // Inline CSS para ocultar checkboxes en la pestaña "General" (product data) y en variaciones.
    $css = "
    /* Ocultar casillas Virtual / Downloadable en metabox de producto */
    #_virtual, label[for='_virtual'], #_downloadable, label[for='_downloadable'] { display: none !important; }
    /* Ocultar opciones de descarga en panel de variaciones */
    .variation_downloadable, .woocommerce_variation .downloadable, .variable_downloadable { display: none !important; }
    ";
    wp_add_inline_style( 'woocommerce_admin_styles', $css );

    // Inline JS: además de ocultar, forzamos que estén desmarcadas/marcadas correctamente cada vez que se abre el panel.
    $js = <<<'JS'
    jQuery(document).ready(function($){
        // Forzar inyección correcta para producto simple
        $('#_virtual').prop('checked', true).trigger('change');
        $('#_downloadable').prop('checked', false).trigger('change');

        // Para las variaciones: interceptar la apertura y forzar valores
        $(document).on('woocommerce_variations_loaded wc_variation_form', function(){
            $('.woocommerce_variation').each(function(){
                var $v = $(this);
                $v.find("input[type='checkbox'][name*='_virtual']").prop('checked', true).trigger('change');
                $v.find("input[type='checkbox'][name*='_downloadable']").prop('checked', false).trigger('change');
            });
        });

        // Si se añaden nuevas variaciones dinámicamente, asegurar los valores
        $(document).on('click', '.add_variation', function(){
            setTimeout(function(){
                $( '.woocommerce_variation' ).each(function(){
                    var $v = $(this);
                    $v.find("input[type='checkbox'][name*='_virtual']").prop('checked', true).trigger('change');
                    $v.find("input[type='checkbox'][name*='_downloadable']").prop('checked', false).trigger('change');
                });
            }, 300);
        });
    });
    JS;
    wp_add_inline_script( 'jquery', $js );
});
