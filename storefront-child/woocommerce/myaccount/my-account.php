<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * My Account navigation.
 *
 * @since 2.6.0
 */
?>

<div class="pagina-grid-3366">

    <!-- Columna izquierda -->
    <div class="pagina-columna33-sticky">

        <!-- Sección Card de Mi cuenta con menú -->
        <?php echo do_shortcode('[mc_user_card]'); ?>

    </div>

    <div class="pagina-columna66">
        <?php
        // Solo mostrar en la página principal de "Mi cuenta", no en endpoints
        if ( ! is_wc_endpoint_url() ) {
            
            echo do_shortcode('[mc_contenido]');
        } else {
            // Contenido normal de endpoints
            do_action( 'woocommerce_account_content' );
        }
        ?>
    </div>


</div>
