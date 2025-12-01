<?php
// Desactivar Categorías y Marcas de los Productos
add_action( 'init', function() {
    // Desregistrar marcas de producto
    unregister_taxonomy( 'product_brand' ); // Sustituye 'product_brand' si usas otro slug

    // Desregistrar categorías de producto
    unregister_taxonomy( 'product_cat' );
}, 20 );
