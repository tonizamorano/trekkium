<?php
// =========================
// 1️⃣ AJAX lista de espera - FUNCIÓN ÚNICA
// =========================
add_action('wp_ajax_trekkium_lista_espera', 'trekkium_lista_espera_handler');
// add_action('wp_ajax_nopriv_trekkium_lista_espera', 'trekkium_lista_espera_handler'); // Elimina esta línea si solo quieres usuarios logueados

function trekkium_lista_espera_handler() {
    if (!is_user_logged_in()) {
        wp_send_json_error('Debes iniciar sesión para apuntarte a la lista de espera.');
    }

    $user_id = get_current_user_id();
    $user_info = wp_get_current_user();

    $product_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
    if (!$product_id || get_post_type($product_id) !== 'product') {
        wp_send_json_error('Producto no válido.');
    }

    $product = wc_get_product($product_id);

    // Recuperar lista de espera existente
    $lista = get_post_meta($product_id, '_lista_espera', true);
    if (!$lista || !is_array($lista)) $lista = [];

    // Evitar duplicados - BUSCAR POR USER_ID
    foreach ($lista as $item) {
        if (isset($item['user_id']) && $item['user_id'] === $user_id) {
            wp_send_json_success('Ya estás en la lista de espera.');
        }
    }

    // Añadir usuario con datos completos
    $telefono = get_user_meta($user_id, 'billing_phone', true); // Cambié 'telefono' por 'billing_phone' que es más estándar
    if (empty($telefono)) {
        $telefono = get_user_meta($user_id, 'telefono', true); // Fallback por si acaso
    }

    $lista[] = [
        'user_id'   => $user_id,
        'nombre'    => $user_info->display_name,
        'email'     => $user_info->user_email,
        'telefono'  => $telefono,
        'timestamp' => current_time('timestamp'),
        'fecha'     => current_time('d/m/Y H:i:s')
    ];

    // Guardar la lista actualizada
    update_post_meta($product_id, '_lista_espera', $lista);

    // Debug: Verificar que se guardó correctamente
    error_log("Lista de espera actualizada para producto $product_id: " . print_r($lista, true));

    // Devolver mensaje
    wp_send_json_success('¡Te hemos apuntado en la lista de espera!');
}

// =========================
// 2️⃣ Metabox en WP Admin - VERSIÓN MEJORADA
// =========================
add_action('add_meta_boxes', function() {
    add_meta_box(
        'trekkium_lista_espera',
        '📋 Lista de Espera',
        'trekkium_lista_espera_metabox',
        'product',
        'side',
        'default'
    );
});

function trekkium_lista_espera_metabox($post) {
    $lista = get_post_meta($post->ID, '_lista_espera', true);
    
    // Debug: Ver qué hay en la lista
    error_log("Lista recuperada para producto {$post->ID}: " . print_r($lista, true));

    if (!is_array($lista) || empty($lista)) {
        echo '<p>No hay usuarios en la lista de espera.</p>';
        
        // Botón para ver metadatos (solo para debugging)
        echo '<details style="margin-top:10px;font-size:12px;">';
        echo '<summary>Debug Info</summary>';
        $all_meta = get_post_meta($post->ID);
        echo '<pre>' . print_r($all_meta, true) . '</pre>';
        echo '</details>';
        
        return;
    }

    echo '<div style="max-height:300px;overflow-y:auto;">';
    echo '<table style="width:100%;font-size:12px;border-collapse:collapse;">';
    echo '<thead><tr style="background:#f5f5f5;"><th style="padding:5px;text-align:left;">Usuario</th><th style="padding:5px;text-align:left;">Fecha</th></tr></thead>';
    echo '<tbody>';
    
    foreach ($lista as $index => $item) {
        // Verificar que el item tenga la estructura correcta
        if (!isset($item['user_id'])) {
            continue;
        }

        $user = get_userdata($item['user_id']);
        
        if (!$user) {
            echo '<tr style="border-bottom:1px solid #eee;">';
            echo '<td style="padding:5px;" colspan="2"><em>Usuario #' . $item['user_id'] . ' no encontrado</em></td>';
            echo '</tr>';
            continue;
        }

        // Usar datos de la lista o del usuario como fallback
        $nombre = isset($item['nombre']) ? $item['nombre'] : $user->display_name;
        $email = isset($item['email']) ? $item['email'] : $user->user_email;
        $telefono = isset($item['telefono']) ? $item['telefono'] : get_user_meta($user->ID, 'billing_phone', true);
        $fecha = isset($item['fecha']) ? $item['fecha'] : (isset($item['timestamp']) ? date('d/m/Y H:i', $item['timestamp']) : 'Fecha desconocida');

        echo '<tr style="border-bottom:1px solid #eee;">';
        echo '<td style="padding:5px;vertical-align:top;">';
        echo '<strong>' . esc_html($nombre) . '</strong><br>';
        echo '<small>' . esc_html($email) . '</small><br>';
        if (!empty($telefono)) {
            echo '<small>Tel: ' . esc_html($telefono) . '</small>';
        }
        echo '</td>';
        echo '<td style="padding:5px;vertical-align:top;font-size:11px;color:#666;">';
        echo esc_html($fecha);
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    // Botón para limpiar lista (opcional)
    echo '<div style="margin-top:10px;padding-top:10px;border-top:1px solid #ddd;">';
    echo '<button type="button" id="limpiar_lista_espera" class="button button-secondary" style="width:100%;" data-product-id="' . $post->ID . '">Limpiar Lista de Espera</button>';
    echo '</div>';
    
    // Script para limpiar lista
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btn = document.getElementById("limpiar_lista_espera");
        if (btn) {
            btn.addEventListener("click", function() {
                if (confirm("¿Estás seguro de que quieres limpiar toda la lista de espera?")) {
                    const productId = this.getAttribute("data-product-id");
                    const data = new FormData();
                    data.append("action", "limpiar_lista_espera");
                    data.append("product_id", productId);
                    data.append("nonce", "' . wp_create_nonce('limpiar_lista_espera') . '");
                    
                    fetch("' . admin_url('admin-ajax.php') . '", {
                        method: "POST",
                        body: data
                    }).then(response => response.json()).then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert("Error: " + data.data);
                        }
                    });
                }
            });
        }
    });
    </script>';
}

// =========================
// 3️⃣ AJAX para limpiar lista (opcional)
// =========================
add_action('wp_ajax_limpiar_lista_espera', function() {
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('No tienes permisos.');
    }
    
    if (!wp_verify_nonce($_POST['nonce'], 'limpiar_lista_espera')) {
        wp_send_json_error('Nonce inválido.');
    }
    
    $product_id = intval($_POST['product_id']);
    delete_post_meta($product_id, '_lista_espera');
    
    wp_send_json_success('Lista de espera limpiada.');
});