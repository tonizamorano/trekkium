<?php
// 1. Registrar estado de producto "Cancelado"
add_action('init', 'registrar_estado_producto_cancelado');
function registrar_estado_producto_cancelado() {
    register_post_status('wc-cancelado', array(
        'label'                     => 'Cancelado',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Cancelado <span class="count">(%s)</span>', 'Cancelado <span class="count">(%s)</span>')
    ));
}

// 2. Añadir el estado "Cancelado" a WooCommerce
add_filter('wc_product_statuses', function($statuses){
    $new_statuses = array();
    foreach($statuses as $key => $label){
        if($key === 'draft'){
            $new_statuses['wc-cancelado'] = 'Cancelado';
        }
        $new_statuses[$key] = $label;
    }
    return $new_statuses;
});

// 3. Mostrar en dropdown de edición rápida y completa
add_action('admin_footer-post.php', 'añadir_estado_cancelado_dropdown');
add_action('admin_footer-post-new.php', 'añadir_estado_cancelado_dropdown');
function añadir_estado_cancelado_dropdown() {
    global $post;
    if('product' !== $post->post_type) return;
    ?>
    <script>
    jQuery(document).ready(function($){
        $('select#post_status').append('<option value="wc-cancelado">Cancelado</option>');
        <?php if($post->post_status === 'wc-cancelado') : ?>
            $('select#post_status').val('wc-cancelado');
        <?php endif; ?>
    });
    </script>
    <?php
}

// 4. Mostrar junto al título en admin
add_filter('display_post_states', function($post_states, $post){
    if($post->post_type === 'product' && $post->post_status === 'wc-cancelado'){
        $post_states['wc-cancelado'] = __('Cancelado');
    }
    return $post_states;
}, 10, 2);
?>
