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
	<div class="fc-facturacion-contenedor">
		<div class="fc-facturacion-titulo">
			<span class="fc-facturacion-titulo-texto">Datos de acompañantes</span>
		</div>

		<div class="fc-facturacion-contenido">
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
								<label>Edad *</label>
								<input type="number" min="0" required
									name="trekkium_acompanantes[<?php echo $i; ?>][edad]"
									value="<?php echo esc_attr($get_value($i, 'edad')); ?>">
							</div>
						</div>

						<div class="acompanante-grid-2">
							<div class="acompanante-columna">
								<label>Teléfono móvil *</label>
								<input type="text" name="trekkium_acompanantes[<?php echo $i; ?>][telefono]" required
									value="<?php echo esc_attr($get_value($i, 'telefono')); ?>">
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

	$acomps = isset($_POST['trekkium_acompanantes']) && is_array($_POST['trekkium_acompanantes'])
		? $_POST['trekkium_acompanantes']
		: [];

	// Asegurarnos de que hay al menos N acompañantes
	if (count($acomps) < $companions_needed) {
		wc_add_notice('Por favor completa los datos de todos los acompañantes.', 'error');
		return;
	}

	// Comprobamos campo por campo sólo hasta $companions_needed (evita índices extra)
	for ($i = 0; $i < $companions_needed; $i++) {
		$a = isset($acomps[$i]) && is_array($acomps[$i]) ? $acomps[$i] : [];

		$nombre   = isset($a['nombre'])   ? trim($a['nombre'])   : '';
		$edad     = isset($a['edad'])     ? trim($a['edad'])     : '';
		$telefono = isset($a['telefono']) ? trim($a['telefono']) : '';

		// Validaciones básicas
		if ($nombre === '' || $telefono === '' || $edad === '') {
			wc_add_notice("Por favor completa todos los campos obligatorios del acompañante " . ($i + 1) . ".", 'error');
			continue;
		}

		// Edad numérica y razonable
		if (!is_numeric($edad) || intval($edad) < 0 || intval($edad) > 120) {
			wc_add_notice("Edad inválida para el acompañante " . ($i + 1) . ".", 'error');
		}

		// Teléfono: al menos 6 caracteres (puedes ajustar)
		if (strlen(preg_replace('/\D+/', '', $telefono)) < 6) {
			wc_add_notice("Teléfono inválido para el acompañante " . ($i + 1) . ".", 'error');
		}
	}

	// Guardar en sesión temporal (opcional)
	// limpiamos los valores antes de guardar
	$acomps_clean = [];
	for ($i = 0; $i < $companions_needed; $i++) {
		$a = isset($acomps[$i]) && is_array($acomps[$i]) ? $acomps[$i] : [];
		$acomps_clean[$i] = array(
			'nombre'   => isset($a['nombre']) ? wc_clean($a['nombre']) : '',
			'edad'     => isset($a['edad']) ? wc_clean($a['edad']) : '',
			'telefono' => isset($a['telefono']) ? wc_clean($a['telefono']) : '',
		);
	}
	WC()->session->set('trekkium_acompanantes', $acomps_clean);
});
 
// --------------------------------------
// Guardar acompañantes al crear el pedido
// --------------------------------------
add_action('woocommerce_checkout_create_order', function($order, $data) {
	if (isset($_POST['trekkium_acompanantes']) && is_array($_POST['trekkium_acompanantes'])) {
		// Guardar sólo hasta el número correcto (por seguridad)
		$cart = WC()->cart->get_cart();
		$first = reset($cart);
		$quantity = intval($first['quantity']);
		$companions_needed = max(0, $quantity - 1);

		$raw = $_POST['trekkium_acompanantes'];
		$acomps = [];
		for ($i = 0; $i < $companions_needed; $i++) {
			$a = isset($raw[$i]) && is_array($raw[$i]) ? $raw[$i] : [];
			$acomps[$i] = array(
				'nombre'   => isset($a['nombre']) ? wc_clean($a['nombre']) : '',
				'edad'     => isset($a['edad']) ? wc_clean($a['edad']) : '',
				'telefono' => isset($a['telefono']) ? wc_clean($a['telefono']) : '',
			);
		}

		$order->update_meta_data('_trekkium_acompanantes', $acomps);
	}
}, 10, 2);

// -------------------------------
// Limpiar sesión tras finalizar
// -------------------------------
add_action('woocommerce_thankyou', function($order_id) {
	if (WC()->session) {
		WC()->session->__unset('trekkium_acompanantes');
	}
});


