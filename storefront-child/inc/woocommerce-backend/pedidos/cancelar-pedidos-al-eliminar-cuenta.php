<?php
add_action('delete_user', 'cancelar_pedidos_al_eliminar_usuario');

function cancelar_pedidos_al_eliminar_usuario($user_id) {
    $user = get_userdata($user_id);
    if (!$user) return;

    // Comprobar que el usuario tiene rol 'customer' (cliente)
    if (!in_array('customer', (array) $user->roles)) {
        return;
    }

    // Obtener todos los pedidos del usuario en estados relevantes
    $args = [
        'customer_id' => $user_id,
        'limit'       => -1,
        'return'      => 'ids',
        'status'      => ['pending', 'processing', 'on-hold', 'completed'], // estados que pueden necesitar cancelarse
    ];

    $orders = wc_get_orders($args);

    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);
        if ($order && !$order->has_status('cancelled')) {
            $order->update_status('cancelled', 'Pedido cancelado automáticamente por eliminación de cuenta del cliente.');
        }
    }
}
