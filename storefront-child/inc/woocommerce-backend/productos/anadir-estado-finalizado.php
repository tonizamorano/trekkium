<?php
// 1. Registrar el estado de producto "Finalizado"
add_action('init', 'registrar_estado_producto_finalizado');
function registrar_estado_producto_finalizado() {
    register_post_status( 'wc-finalizado', array(
        'label'                     => 'Finalizado',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Finalizado <span class="count">(%s)</span>', 'Finalizado <span class="count">(%s)</span>' )
    ));
}

// 2. Añadir el estado "Finalizado" a la lista de estados de WooCommerce
add_filter('wc_product_statuses', 'añadir_estado_producto_finalizado');
function añadir_estado_producto_finalizado( $statuses ) {
    $new_statuses = array();
    foreach($statuses as $key => $label){
        if($key === 'draft'){
            $new_statuses['wc-finalizado'] = 'Finalizado';
        }
        $new_statuses[$key] = $label;
    }
    return $new_statuses;
}

// 3. Mostrar "Finalizado" en el dropdown de edición rápida y edición completa
add_action( 'admin_footer-post.php', 'añadir_estado_finalizado_dropdown' );
add_action( 'admin_footer-post-new.php', 'añadir_estado_finalizado_dropdown' );
function añadir_estado_finalizado_dropdown() {
    global $post;
    if ( 'product' !== $post->post_type ) return;
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($){
        $('select#post_status').append('<option value="wc-finalizado">Finalizado</option>');
        <?php if($post->post_status === 'wc-finalizado') : ?>
            $('select#post_status').val('wc-finalizado');
        <?php endif; ?>
    });
    </script>
    <?php
}

// 4. Mostrar "Finalizado" junto al título del producto en el admin
add_filter('display_post_states', 'mostrar_estado_finalizado_admin', 10, 2);
function mostrar_estado_finalizado_admin($post_states, $post) {
    if ($post->post_type === 'product' && $post->post_status === 'wc-finalizado') {
        $post_states['wc-finalizado'] = __('Finalizado');
    }
    return $post_states;
}

// 5. Cambiar automáticamente a "Finalizado" los productos cuya fecha y hora hayan pasado
add_action('init', 'marcar_productos_finalizados');
function marcar_productos_finalizados() {
    $args = array(
        'post_type'      => 'product',
        'post_status'    => array('publish', 'pending', 'draft', 'future'),
        'posts_per_page' => -1,
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'fecha',
                'value'   => current_time('Y-m-d'),
                'compare' => '<=',
                'type'    => 'DATE',
            ),
            array(
                'key'     => 'hora',
                'value'   => current_time('H:i'),
                'compare' => '<=',
                'type'    => 'CHAR',
            ),
        ),
    );

    $query = new WP_Query($args);
    if($query->have_posts()){
        foreach($query->posts as $producto){
            if($producto->post_status !== 'wc-finalizado'){
                wp_update_post(array(
                    'ID'          => $producto->ID,
                    'post_status' => 'wc-finalizado'
                ));
            }
        }
    }
    wp_reset_postdata();
}
