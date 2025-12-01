<?php
/**
 * The Header for our theme
 *
 * @package WordPress
 * @subpackage Trekkium
 * @since 1.0
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>  
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="header-contenedor-ppal">

  <div class="header-grid">
    
    <!-- Contenedor 1: Logo -->
    <div class="header-logo">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
        <img src="https://trekkium.com/wp-content/uploads/2025/09/trekkium_logowhite.png" 
          alt="<?php bloginfo('name'); ?>" class="logo-img"/>
      </a>
    </div>

    <!-- Contenedor 2: Menú -->
    <div class="header-menu">
      <?php echo do_shortcode('[menu_principal]'); ?>
    </div>

  </div>
  
  <!-- Sección de título -->
  <?php
  // Inicializamos variables
  $titulo = '';
  $color_fondo = '';

  // URL actual
  $current_url = home_url( $_SERVER['REQUEST_URI'] );

  // 1️⃣ No mostrar en portada ni en /admin-dashboard/
  if ( is_front_page() || str_contains( $current_url, '/admin-dashboard/' ) ) {
      $titulo = '';
  }

  // 2️⃣ Página /actividades/ o /producto/
  elseif ( is_post_type_archive('product') || is_singular('product') || str_contains($current_url, '/actividades/') ) {
      $titulo = 'Actividades';
      $color_fondo = 'var(--azul1-100)';
  }

  // 3️⃣ Página /guias/ o /guia/
  elseif ( is_author() || str_contains($current_url, '/guias/') || str_contains($current_url, '/guia/') ) {
      $titulo = 'Guías';
      $color_fondo = 'var(--azul1-100)';
  }

  // 4️⃣ Blog y entradas individuales del blog
  elseif (
      is_home() ||
      is_page('blog') ||
      is_category() ||
      is_tag() ||
      ( is_singular('post') && !is_page() )
  ) {
      $titulo = 'Blog';
      $color_fondo = 'var(--azul1-100)';
  }


  // 5️⃣ Mi cuenta y subpáginas
    elseif (
        is_account_page() ||
        str_contains($current_url, '/mi-cuenta/') ||
        str_contains($current_url, '/mis-actividades/') ||
        str_contains($current_url, '/nueva-actividad/') ||
        str_contains($current_url, '/editar-actividad/') ||
        str_contains($current_url, '/detalles-actividad/')
    ) {
        $titulo = 'Mi Cuenta';
        $color_fondo = 'var(--azul1-100)';
    }

    // 6️⃣ Páginas informativas
    elseif ( str_contains($current_url, '/unete-a-trekkium/') ) {
        $titulo = 'Únete a Trekkium';
        $color_fondo = 'var(--azul1-100)';
    }
    elseif ( str_contains($current_url, '/terminos-y-condiciones/') ) {
        $titulo = 'Términos y condiciones legales';
        $color_fondo = 'var(--azul1-100)';
    }
    elseif ( str_contains($current_url, '/preguntas-frecuentes/') ) {
        $titulo = 'Preguntas frecuentes';
        $color_fondo = 'var(--azul1-100)';
    }
    elseif ( str_contains($current_url, '/sobre-nosotros/') ) {
        $titulo = 'Sobre nosotros';
        $color_fondo = 'var(--azul1-100)';
    }

    // 7️⃣ Acceso y crear cuenta
    elseif ( str_contains($current_url, '/acceso/') ) {
        $titulo = 'Acceso';
        $color_fondo = 'var(--rojo-100)';
    }
    elseif ( str_contains($current_url, '/crear-cuenta/') ) {
        $titulo = 'Acceso';
        $color_fondo = 'var(--rojo-100)';
    }

  // Mostrar solo si hay título
  if ( !empty($titulo) ) :
  ?>
  <div class="header-titulo-seccion" style="background-color: <?php echo esc_attr($color_fondo); ?>;">
    <span class="titulo-texto"><?php echo esc_html($titulo); ?></span>
  </div>
  <?php endif; ?>
  
</header>
