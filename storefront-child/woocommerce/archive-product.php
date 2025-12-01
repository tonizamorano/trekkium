<?php
/**
 * Archivo de catálogo personalizado para pruebas
 * Tema hijo de Storefront
 */

defined( 'ABSPATH' ) || exit;

get_header(); 

// Ejecutar shortcode que generará todo el contenido dinámico
echo do_shortcode('[pagina_archive_productos]');

get_footer();
?>
