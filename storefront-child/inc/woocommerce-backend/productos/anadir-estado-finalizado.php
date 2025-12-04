<?php
// 1. Registrar estado de producto "Finalizado"
add_action('init', 'registrar_estado_producto_finalizado');
function registrar_estado_producto_finalizado() {
    register_post_status('wc-finalizado', array(
        'label'                     => 'Finalizado',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Finalizado <span class="count">(%s)</span>', 'Finalizado <span class="count">(%s)</span>')
    ));
}

// 2. Añadir el estado "Finalizado" a WooCommerce
add_filter('wc_product_statuses', function($statuses){
    $new_statuses = array();
    foreach($statuses as $key => $label){
        if($key === 'draft'){
            $new_statuses['wc-finalizado'] = 'Finalizado';
        }
        $new_statuses[$key] = $label;
    }
    return $new_statuses;
});

// 3. Mostrar en dropdown de edición rápida y completa
add_action('admin_footer-post.php', 'añadir_estado_finalizado_dropdown');
add_action('admin_footer-post-new.php', 'añadir_estado_finalizado_dropdown');
function añadir_estado_finalizado_dropdown() {
    global $post;
    if('product' !== $post->post_type) return;
    ?>
    <script>
    jQuery(document).ready(function($){
        $('select#post_status').append('<option value="wc-finalizado">Finalizado</option>');
        <?php if($post->post_status === 'wc-finalizado') : ?>
            $('select#post_status').val('wc-finalizado');
        <?php endif; ?>
    });
    </script>
    <?php
}

// 4. Mostrar junto al título en admin
add_filter('display_post_states', function($post_states, $post){
    if($post->post_type === 'product' && $post->post_status === 'wc-finalizado'){
        $post_states['wc-finalizado'] = __('Finalizado');
    }
    return $post_states;
}, 10, 2);

// 5. Cambiar automáticamente a "Finalizado" usando WP-Cron
if (!wp_next_scheduled('actualizar_productos_finalizados')) {
    wp_schedule_event(time(), 'hourly', 'actualizar_productos_finalizados');
}

add_action('actualizar_productos_finalizados', function(){
    $args = array(
        'post_type'   => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query'  => array(
            array(
                'key'     => 'fecha',
                'value'   => current_time('Y-m-d'),
                'compare' => '<=',
                'type'    => 'DATE'
            ),
            array(
                'key'     => 'hora',
                'value'   => current_time('H:i'),
                'compare' => '<=',
                'type'    => 'CHAR'
            ),
        ),
    );
    $query = new WP_Query($args);
    foreach($query->posts as $producto){
        if($producto->post_status !== 'wc-finalizado'){
            wp_update_post(array(
                'ID' => $producto->ID,
                'post_status' => 'wc-finalizado'
            ));
        }
    }
    wp_reset_postdata();
});
?>
