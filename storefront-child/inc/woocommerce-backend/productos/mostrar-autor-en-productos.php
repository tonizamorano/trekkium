<?php
// Mostrar el campo Autor en Productos

function trekkium_habilitar_autor_en_productos() {
    add_post_type_support('product', 'author');
}
add_action('init', 'trekkium_habilitar_autor_en_productos');