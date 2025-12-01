<?php
if (!defined('ABSPATH')) exit;

/**
 * Shortcode: [checkout_datos_acompanantes]
 * Muestra y guarda los datos de acompañantes en el checkout de WooCommerce.
 */
function trekkium_checkout_datos_acompanantes_shortcode() {
	if (!is_checkout()) return '';

	$cart = WC()->cart->get_cart();
	if (empty($cart)) return '';

	$first = reset($cart);
	$quantity = intval($first['quantity']);
	$companions_needed = max(0, $quantity - 1);
	if ($companions_needed <= 0) return '';

	// Valores previos desde POST
	$post_acomps = $_POST['trekkium_acompanantes'] ?? [];

	$get_value = function($index, $field) use ($post_acomps) {
		return $post_acomps[$index][$field] ?? '';
	};

	ob_start(); ?>
	<div class="checkout-contenedor-seccion trekkium-acompanantes-checkout">
		<div class="checkout-seccion-titulo">
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="white" viewBox="0 0 24 24">
				<path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
			</svg>
			<span class="checkout-seccion-titulo-texto">Datos de acompañantes</span>
		</div>


		<div class="checkout-seccion-contenido">
			<?php for ($i = 0; $i < $companions_needed; $i++): ?>
				<div class="acompanante-item">
					<div class="acompanante-header">
						<div class="acompanante-title">Acompañante <?php echo ($i + 1); ?></div>
					</div>
					<div class="acompanante-body">
						<div class="acompanante-grid-2">
							<div class="acompanante-columna">
								<label>Nombre completo *</label>
								<input type="text" name="trekkium_acompanantes[<?php echo $i; ?>][nombre]" required
									   value="<?php echo esc_attr($get_value($i, 'nombre')); ?>">
							</div>
							<div class="acompanante-columna">
								<label>Correo electrónico</label>
								<input type="email" name="trekkium_acompanantes[<?php echo $i; ?>][email]"
									   value="<?php echo esc_attr($get_value($i, 'email')); ?>">
							</div>
						</div>

						<div class="acompanante-grid-2">
							<div class="acompanante-columna">
								<label>Teléfono móvil *</label>
								<input type="text" name="trekkium_acompanantes[<?php echo $i; ?>][telefono]" required
									   value="<?php echo esc_attr($get_value($i, 'telefono')); ?>">
							</div>
							<div class="acompanante-columna">
								<label>DNI / NIE / Pasaporte *</label>
								<input type="text" name="trekkium_acompanantes[<?php echo $i; ?>][dni]" required
									   value="<?php echo esc_attr($get_value($i, 'dni')); ?>">
							</div>
						</div>

						<div class="acompanante-grid-2">
							<div class="acompanante-columna">
								<label>Edad</label>
								<input type="number" min="0" name="trekkium_acompanantes[<?php echo $i; ?>][edad]"
									   value="<?php echo esc_attr($get_value($i, 'edad')); ?>">
							</div>
						</div>
					</div>
				</div>
			<?php endfor; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('checkout_datos_acompanantes', 'trekkium_checkout_datos_acompanantes_shortcode');

// -------------------------
// Validación en el checkout
// -------------------------
add_action('woocommerce_checkout_process', function() {
	$cart = WC()->cart->get_cart();
	if (empty($cart)) return;

	$first = reset($cart);
	$quantity = intval($first['quantity']);
	$companions_needed = max(0, $quantity - 1);
	if ($companions_needed <= 0) return;

	$acomps = $_POST['trekkium_acompanantes'] ?? [];

	if (count($acomps) < $companions_needed) {
		wc_add_notice('Por favor completa los datos de todos los acompañantes.', 'error');
		return;
	}

	foreach ($acomps as $i => $a) {
		if (empty($a['nombre']) || empty($a['telefono']) || empty($a['dni'])) {
			wc_add_notice("Por favor completa todos los campos obligatorios del acompañante " . ($i + 1) . ".", 'error');
		}
	}

	// Guardar en sesión temporal (opcional)
	WC()->session->set('trekkium_acompanantes', $acomps);
});

// --------------------------------------
// Guardar acompañantes al crear el pedido
// --------------------------------------
add_action('woocommerce_checkout_create_order', function($order, $data) {
	if (isset($_POST['trekkium_acompanantes']) && is_array($_POST['trekkium_acompanantes'])) {
		$acomps = array_map('wc_clean', $_POST['trekkium_acompanantes']);
		$order->update_meta_data('_trekkium_acompanantes', $acomps);
	}
}, 10, 2);

// -------------------------------
// Limpiar sesión tras finalizar
// -------------------------------
add_action('woocommerce_thankyou', function($order_id) {
	WC()->session->__unset('trekkium_acompanantes');
});

