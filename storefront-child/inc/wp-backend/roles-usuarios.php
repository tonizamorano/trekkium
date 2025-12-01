<?php
/*
Plugin Name: Custom Roles Simplified
Description: Define los roles Administrador, Cliente, Autor, Gestor de la tienda y Guía (combina Autor + Gestor).
Version: 1.1
Author: Toni
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Definir roles
 */
function custom_define_roles_simplified() {
    // Cliente
    if ( ! get_role('customer') ) {
        add_role('customer', 'Cliente', []);
    }

    // Autor
    if ( ! get_role('author') ) {
        add_role('author', 'Autor', get_role('author') ? get_role('author')->capabilities : []);
    }

    // Gestor de la tienda (Shop Manager)
    if ( ! get_role('shop_manager') ) {
        add_role('shop_manager', 'Gestor de la tienda', get_role('shop_manager') ? get_role('shop_manager')->capabilities : []);
    }

    // Crear Guía: combina capacidades de Autor y Gestor de la tienda
    if ( ! get_role('guia') ) {
        $author_caps = get_role('author')->capabilities ?? [];
        $shop_caps = get_role('shop_manager')->capabilities ?? [];
        $combined_caps = array_merge($author_caps, $shop_caps);
        add_role('guia', 'Guía', $combined_caps);
    }

    // Administrador ya existe por defecto
}
add_action('init', 'custom_define_roles_simplified');
