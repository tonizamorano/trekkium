<?php
/**
 * Trekkium - Acompanantes: Campos meta en pedidos de WooCommerce (compatible HPOS)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ----------------------------------------------------------
// 1. Crear metabox “Acompañantes” en el panel de pedidos
// ----------------------------------------------------------
add_action( 'add_meta_boxes', function() {
	// Compatibilidad con pedidos en modo clásico
	add_meta_box(
		'acompanantes_box',
		'Acompañantes',
		'trekkium_acompanantes_metabox_content',
		'shop_order',
		'normal',
		'default'
	);
});

// Compatibilidad con HPOS (High Performance Order Storage)
add_action( 'woocommerce_admin_order_data_after_order_details', function( $order ) {
	ob_start();
	?>
	<style>
		.trekkium-acompanantes-fullwidth { 
			width: 100%; 
			margin: 20px 0; 
			clear: both; 
		}
		.trekkium-acompanantes-title { 
			margin: 0; 
			font-size: 1.3em;			
			padding: 10px 0; 
		}
		.trekkium-acompanante-item { 
			padding: 10px; 
			border: 1px solid #ddd; 
			border-radius: 5px; 
			background: #f9f9f9; 
		}
		.trekkium-acompanante-header { 
			font-weight: bold; 
			margin-bottom: 12px; 
			font-size: 1em;
			color: #333;
		}
		.trekkium-data-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 5px;
		}
		.trekkium-data-item {
			display: flex;
			flex-direction: column;
		}
		.trekkium-data-label {
			font-weight: 600;
			color: #555;
			margin-bottom: 0;
			font-size: 12px;
		}
		.trekkium-data-value {
			min-height: 15px;
		}
		.trekkium-no-acompanantes { 
			font-style: italic; 
			color: #666; 
			padding: 20px;
			text-align: center;
			background: #f9f9f9;
			border-radius: 8px;
		}
	</style>
	<div class="trekkium-acompanantes-fullwidth">
		<h3 class="trekkium-acompanantes-title">Acompañantes</h3>
		<?php trekkium_acompanantes_metabox_content( $order ); ?>
	</div>
	<?php
	echo ob_get_clean();
});

// ----------------------------------------------------------
// 2. Mostrar contenido del metabox (solo lectura)
// ----------------------------------------------------------
function trekkium_acompanantes_metabox_content( $order_or_post ) {
	ob_start();

	// Si llega un objeto de pedido (HPOS)
	if ( is_a( $order_or_post, 'WC_Order' ) ) {
		$order = $order_or_post;
		$order_id = $order->get_id();
	}
	// Si llega un post (modo clásico)
	elseif ( is_object( $order_or_post ) && isset( $order_or_post->ID ) ) {
		$order_id = $order_or_post->ID;
		$order = wc_get_order( $order_id );
	} else {
		?>
		<p>No hay datos de pedido disponibles.</p>
		<?php
		echo ob_get_clean();
		return;
	}

	$acompanantes = get_post_meta( $order_id, '_trekkium_acompanantes', true );
	?>

	<div class="trekkium-acompanantes-container">
		<?php if ( ! empty( $acompanantes ) && is_array( $acompanantes ) ) : ?>
			<?php foreach ( $acompanantes as $index => $acompanante ) : ?>
				<div class="trekkium-acompanante-item">
					<div class="trekkium-acompanante-header">Acompañante <?php echo ( $index + 1 ); ?></div>
					
					<div class="trekkium-data-grid">

						<div class="trekkium-data-item">
							<span class="trekkium-data-label">Nombre completo:</span>
							<div class="trekkium-data-value"><?php echo esc_html( $acompanante['nombre'] ?? 'No especificado' ); ?></div>
						</div>
						
						<div class="trekkium-data-item">
							<span class="trekkium-data-label">Correo electrónico:</span>
							<div class="trekkium-data-value"><?php echo esc_html( $acompanante['email'] ?? 'No especificado' ); ?></div>
						</div>
						
						<div class="trekkium-data-item">
							<span class="trekkium-data-label">Teléfono móvil:</span>
							<div class="trekkium-data-value"><?php echo esc_html( $acompanante['telefono'] ?? 'No especificado' ); ?></div>
						</div>
						
						<div class="trekkium-data-item">
							<span class="trekkium-data-label">Edad:</span>
							<div class="trekkium-data-value"><?php echo esc_html( $acompanante['edad'] ?? 'No especificado' ); ?></div>
						</div>

					</div>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="trekkium-no-acompanantes">No hay acompañantes registrados para este pedido.</div>
		<?php endif; ?>
	</div>
	<?php

	echo ob_get_clean();
}

// ----------------------------------------------------------
// 3. Guardar los datos al actualizar el pedido
// ----------------------------------------------------------
add_action( 'woocommerce_process_shop_order_meta', function( $order_id, $post ) {
	if ( isset( $_POST['trekkium_acompanantes'] ) ) {
		$acompanantes = array_map( 'wc_clean', $_POST['trekkium_acompanantes'] );
		update_post_meta( $order_id, '_trekkium_acompanantes', $acompanantes );
	}
}, 10, 2 );

// ----------------------------------------------------------
// 4. Crear el campo meta vacío al generar un pedido
// ----------------------------------------------------------
add_action( 'woocommerce_checkout_create_order', function( $order, $data ) {
	if ( ! get_post_meta( $order->get_id(), '_trekkium_acompanantes', true ) ) {
		update_post_meta( $order->get_id(), '_trekkium_acompanantes', [] );
	}
}, 10, 2 );
