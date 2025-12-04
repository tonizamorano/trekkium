<?php

add_action( 'template_redirect', 'trekkium_redirect_when_session_expired' );
function trekkium_redirect_when_session_expired() {
    
    // Si el usuario NO está logueado
    if ( ! is_user_logged_in() ) {
        
        // Páginas que SIEMPRE deben ser accesibles sin login
        $always_public = array(
            home_url('/'),
            wc_get_page_permalink( 'shop' ),
            wc_get_page_permalink( 'cart' ),
            wc_get_page_permalink( 'checkout' ),
            wc_get_page_permalink( 'myaccount' ), // ¡IMPORTANTE! Para poder iniciar sesión
        );
        
        // URL actual
        $current_url = home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ) );
        
        // Si está en order-received (página de agradecimiento) → redirigir
        if ( is_wc_endpoint_url( 'order-received' ) ) {
            wp_safe_redirect( home_url() );
            exit;
        }
        
        // Si está en alguna de estas páginas WooCommerce específicas que requieren login
        $restricted_endpoints = array( 'orders', 'view-order', 'edit-account', 'payment-methods', 'edit-address', 'dashboard' );
        
        foreach ( $restricted_endpoints as $endpoint ) {
            if ( is_wc_endpoint_url( $endpoint ) ) {
                wp_safe_redirect( home_url() );
                exit;
            }
        }
        
        // Verificar si es una página de producto individual
        if ( is_product() ) {
            // Permitir acceso a páginas de productos
            return;
        }
        
        // Verificar si es una página de categoría de producto
        if ( is_product_category() || is_product_tag() ) {
            // Permitir acceso a categorías/etiquetas
            return;
        }
        
        // Para cualquier otra página, verificar si está en la lista blanca
        $is_public = false;
        foreach ( $always_public as $public_url ) {
            if ( strpos( $current_url, $public_url ) !== false ) {
                $is_public = true;
                break;
            }
        }
        
        // Si no es una página pública → redirigir
        if ( ! $is_public ) {
            wp_safe_redirect( home_url() );
            exit;
        }
    }
}