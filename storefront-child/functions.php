<?php
// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Cargar recursivamente todos los archivos PHP dentro de /inc
 */
function cargar_archivos_inc() {
    $inc_dir = get_stylesheet_directory() . '/inc/';
    if ( ! is_dir( $inc_dir ) ) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator( $inc_dir, FilesystemIterator::SKIP_DOTS ),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ( $iterator as $file ) {
        if ( $file->isFile() && strtolower( $file->getExtension() ) === 'php' ) {
            require_once $file->getPathname();
        }
    }
}
add_action( 'after_setup_theme', 'cargar_archivos_inc' );

/**
 * Cargar helpers de SVG (sprites consolidados)
 */
require_once get_stylesheet_directory() . '/inc/svg/svg-helpers.php';

/**
 * Encolar estilos y scripts del tema hijo
 */
function storefront_child_enqueue_styles() {
    // --- 1) Style del padre ---
    $parent_style_path = get_template_directory() . '/style.css';
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        [],
        file_exists( $parent_style_path ) ? filemtime( $parent_style_path ) : null
    );

    $assets_dir = get_stylesheet_directory() . '/assets/';
    $assets_url = get_stylesheet_directory_uri() . '/assets/';

    $asset_css_handles = [];

    // --- 2) CSS en assets/css y subcarpetas ---
    $css_base = $assets_dir . 'css/';
    if ( is_dir( $css_base ) ) {
        // Minimal: enqueue canonical blog-single bs-principal if present.
        $bs_file = get_stylesheet_directory() . '/assets/css/blog-single/bs-principal.css';
        $bs_basename = '';
        if ( file_exists( $bs_file ) ) {
            wp_enqueue_style( 'bs-principal', get_stylesheet_directory_uri() . '/assets/css/blog-single/bs-principal.css', [ 'parent-style' ], @filemtime( $bs_file ) ?: null );
            $bs_basename = basename( $bs_file );
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $css_base, FilesystemIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ( $iterator as $file ) {
            if ( $file->isFile() && strtolower( $file->getExtension() ) === 'css' ) {
                $file_path = $file->getPathname();
                // Skip bs-principal if it was enqueued above to avoid duplicates
                if ( $bs_basename && basename( $file_path ) === $bs_basename ) {
                    continue;
                }

                $rel_path  = str_replace( $assets_dir, '', $file_path );
                $rel_path  = str_replace( '\\', '/', $rel_path );

                $handle = 'asset-css-' . md5( $rel_path );
                wp_enqueue_style(
                    $handle,
                    $assets_url . $rel_path,
                    [ 'parent-style' ],
                    filemtime( $file_path )
                );
                $asset_css_handles[] = $handle;
            }
        }
    }

    // --- 2.5) Agregar media="print" para CSS no críticos para optimizar renderización ---
    add_filter( 'style_loader_tag', function( $tag, $handle ) {
        // CSS no críticos (cargan después del render inicial)
        $non_critical = [ 'asset-css-' ];
        foreach ( $non_critical as $critical ) {
            if ( strpos( $handle, $critical ) !== false && $handle !== 'bs-principal' ) {
                return str_replace( " rel='stylesheet'", " rel='stylesheet' media='print' onload=\"this.media='all'\"", $tag );
            }
        }
        return $tag;
    }, 10, 2 );

    // --- 3) Style del hijo al final ---
    $child_style_path = get_stylesheet_directory() . '/style.css';
    if ( file_exists( $child_style_path ) ) {
        $deps = array_merge( [ 'parent-style' ], $asset_css_handles );
        wp_enqueue_style(
            'child-style',
            get_stylesheet_directory_uri() . '/style.css',
            $deps,
            filemtime( $child_style_path )
        );
    }

    // --- 4) JS en assets/js y subcarpetas ---
    $js_base = $assets_dir . 'js/';
    if ( is_dir( $js_base ) ) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $js_base, FilesystemIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ( $iterator as $file ) {
            if ( $file->isFile() && strtolower( $file->getExtension() ) === 'js' ) {
                $file_path = $file->getPathname();
                $rel_path  = str_replace( $assets_dir, '', $file_path );
                $rel_path  = str_replace( '\\', '/', $rel_path );

                $handle = 'asset-js-' . md5( $rel_path );
                wp_enqueue_script(
                    $handle,
                    $assets_url . $rel_path,
                    [ 'jquery' ],
                    filemtime( $file_path ),
                    true
                );
            }
        }
    }
}
add_action( 'wp_enqueue_scripts', 'storefront_child_enqueue_styles' );

/**
 * Obtener URL de assets
 */
function trekkium_asset_url( $path = 'img' ) {
    return get_stylesheet_directory_uri() . '/assets/' . $path . '/';
}

/**
 * Enqueue JS para sliders y carruseles
 */
function trekkium_enqueue_sliders() {
    // Slider de productos
    wp_enqueue_script(
        'slider-productos',
        trekkium_asset_url('js') . 'sliders/slider-productos.js',
        [],
        filemtime(get_stylesheet_directory() . '/assets/js/sliders/slider-productos.js'),
        true
    );

    // Carrusel de relacionados
    wp_enqueue_script(
        'carousel-relacionados',
        trekkium_asset_url('js') . 'sliders/carousel-relacionados.js',
        [],
        filemtime(get_stylesheet_directory() . '/assets/js/sliders/carousel-relacionados.js'),
        true
    );
}
add_action('wp_enqueue_scripts', 'trekkium_enqueue_sliders');

/**
 * Enqueue JS para editor de avatar (solo en páginas de usuario)
 */
function trekkium_enqueue_avatar_editor() {
    if (!is_user_logged_in()) {
        return;
    }

    // Enqueue el JS
    wp_enqueue_script(
        'avatar-editor',
        trekkium_asset_url('js') . 'user/avatar-editor.js',
        ['jquery'],
        filemtime(get_stylesheet_directory() . '/assets/js/user/avatar-editor.js'),
        true
    );

    // Generar nonces AQUI, una sola vez
    wp_localize_script('avatar-editor', 'my_avatar_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'upload_nonce' => wp_create_nonce('subir_avatar_nonce'),
        'delete_nonce' => wp_create_nonce('eliminar_avatar_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'trekkium_enqueue_avatar_editor');

function trekkium_author_base() {
    global $wp_rewrite;
    $wp_rewrite->author_base = 'guia';
}
add_action('init', 'trekkium_author_base');


// Excluir de la caché la página seccion-mis-reservas.php
add_action('template_redirect', 'excluir_mis_reservas_de_cache');
function excluir_mis_reservas_de_cache() {
    if (is_page() && has_shortcode(get_post()->post_content, 'seccion_mis_reservas')) {
        if (!defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }
        if (function_exists('sg_cachepress_purge_everything')) {
            remove_action('template_redirect', 'sg_cachepress_purge_everything');
        }
    }
}

// Oculta completamente la barra de administración (admin bar) para todos
add_filter('show_admin_bar', '__return_false');


// Candidatos

function trekkium_enqueue_admin_scripts($hook) {
    // Solo cargar en edición de CPT 'candidato'
    global $post_type;
    if( $hook === 'post.php' || $hook === 'post-new.php' ){
        if($post_type === 'candidato'){
            wp_enqueue_script(
                'trekkium-candidato',
                get_stylesheet_directory_uri() . '/assets/js/trekkium-candidato.js',
                ['jquery'],
                filemtime(get_stylesheet_directory() . '/assets/js/trekkium-candidato.js'),
                true
            );

            wp_localize_script('trekkium-candidato', 'trekkium_ajax', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('trekkium_crear_guia_nonce')
            ]);
        }
    }
}
add_action('admin_enqueue_scripts', 'trekkium_enqueue_admin_scripts');


/**
 * Cargar Google Fonts (Anton y Roboto)
 */
function trekkium_enqueue_google_fonts() {
    wp_enqueue_style(
        'trekkium-google-fonts',
        'https://fonts.googleapis.com/css2?family=Anton&family=Roboto:wght@400;500;700&display=swap',
        [],
        null
    );
}
// prioridad 5 (por defecto es 10)
add_action('wp_enqueue_scripts', 'trekkium_enqueue_google_fonts', 5);


// Contador de vistas por post (usa cookie para evitar múltiples sumas desde el mismo navegador)
function trekkium_contador_vistas_cookie() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    global $post;
    if ( empty( $post->ID ) ) {
        return;
    }

    $post_id = (int) $post->ID;
    $cookie_name = 'trekkium_viewed_' . $post_id;

    // Si no existe la cookie, incrementa y crea la cookie
    if ( empty( $_COOKIE[ $cookie_name ] ) ) {
        $views = (int) get_post_meta( $post_id, 'post_views_count', true );
        $views++;
        update_post_meta( $post_id, 'post_views_count', $views );

        // Poner cookie por 30 días (ajusta si quieres menos/más)
        $expire = time() + 30 * DAY_IN_SECONDS;
        setcookie( $cookie_name, '1', $expire, COOKIEPATH ?: '/' );
        // También escribir en $_COOKIE para que al recargar la petición actual no vuelva a contar
        $_COOKIE[ $cookie_name ] = '1';
    }
}
add_action( 'template_redirect', 'trekkium_contador_vistas_cookie' );

/*
// Entrar con cuenta registrada, medida temporal hasta que se inaugure la tienda
function restringir_acceso_usuarios_no_registrados() {
    // Permitir acceso a la página de login y admin
    if ( is_user_logged_in() || is_page('wp-login.php') || is_admin() ) {
        return;
    }
    // Redirigir a login si no está logueado
    wp_redirect( wp_login_url() );
    exit;
}
add_action('template_redirect', 'restringir_acceso_usuarios_no_registrados');
*/

/**
 * Desactivar detección automática de teléfonos y emails en móviles
 */
add_action('wp_head', function() {
    echo '<meta name="format-detection" content="telephone=no,email=no">';
});
