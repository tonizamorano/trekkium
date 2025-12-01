<?php
// Devuelve el nombre legible del estado/provincia en WooCommerce
function obtener_nombre_estado_wc($codigo_estado, $codigo_pais = 'ES') {
    if (empty($codigo_estado)) return '';

    if (function_exists('WC')) {
        $estados = WC()->countries->get_states($codigo_pais);
        return $estados[$codigo_estado] ?? $codigo_estado;
    }

    return $codigo_estado;
}
