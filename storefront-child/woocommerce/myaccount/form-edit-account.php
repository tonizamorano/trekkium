<?php
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );

	echo do_shortcode('[contenido_datos_personales]');
	echo do_shortcode('[contenido_contrasena_eliminar]');


do_action( 'woocommerce_after_edit_account_form' );
