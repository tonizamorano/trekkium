<?php



// Este snippet solo funciona para Trekkum 1




// Función para actualizar todos los productos
// Para ejecutar la actualización, accede a: https://trekkium.com/wp-admin/?actualizar_productos=1
function actualizar_todos_los_productos() {
    // Comprobamos que el usuario es administrador y que viene con el parámetro correcto
    if (!current_user_can('manage_woocommerce') || !isset($_GET['actualizar_productos'])) {
        return;
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids', // Solo necesitamos los IDs
    );

    $productos = get_posts($args);

    foreach ($productos as $producto_id) {
        // Aquí puedes hacer cambios que quieras
        // Ejemplo: actualizar el título (aunque no cambie)
        $producto = wc_get_product($producto_id);
        $producto->set_name($producto->get_name());
        $producto->save();
    }

    echo count($productos) . " productos actualizados.";
}

// Hook para ejecutar la función en init solo si el parámetro está presente
add_action('init', 'actualizar_todos_los_productos');
