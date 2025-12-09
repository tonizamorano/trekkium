<?php
// 1. Registrar ambos estados
add_action('init', 'registrar_estados_personalizados_productos');
function registrar_estados_personalizados_productos() {
    // Registrar estado "Finalizado"
    register_post_status('wc-finalizado', array(
        'label'                     => 'Finalizado',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Finalizado <span class="count">(%s)</span>', 'Finalizado <span class="count">(%s)</span>')
    ));
    
    // Registrar estado "Cancelado"
    register_post_status('wc-cancelado', array(
        'label'                     => 'Cancelado',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Cancelado <span class="count">(%s)</span>', 'Cancelado <span class="count">(%s)</span>')
    ));
}

// 2. Añadir ambos estados a WooCommerce
add_filter('wc_product_statuses', 'añadir_estados_personalizados_wc');
function añadir_estados_personalizados_wc($statuses) {
    // Encontrar la posición de "draft" para insertar después
    $position = array_search('draft', array_keys($statuses));
    
    if ($position !== false) {
        // Insertar después de "draft"
        $new_statuses = array();
        $current_position = 0;
        
        foreach ($statuses as $key => $label) {
            $new_statuses[$key] = $label;
            $current_position++;
            
            if ($key === 'draft') {
                // Insertar ambos estados después de "draft"
                $new_statuses['wc-finalizado'] = 'Finalizado';
                $new_statuses['wc-cancelado'] = 'Cancelado';
            }
        }
        
        return $new_statuses;
    }
    
    // Si no encuentra "draft", añadir al final
    $statuses['wc-finalizado'] = 'Finalizado';
    $statuses['wc-cancelado'] = 'Cancelado';
    
    return $statuses;
}

// 3. Mostrar en dropdown de edición
add_action('admin_footer-post.php', 'añadir_estados_personalizados_dropdown');
add_action('admin_footer-post-new.php', 'añadir_estados_personalizados_dropdown');
function añadir_estados_personalizados_dropdown() {
    global $post;
    if('product' !== $post->post_type) return;
    ?>
    <script>
    jQuery(document).ready(function($){
        $('select#post_status').append('<option value="wc-finalizado">Finalizado</option>');
        $('select#post_status').append('<option value="wc-cancelado">Cancelado</option>');
        <?php if($post->post_status === 'wc-finalizado') : ?>
            $('select#post_status').val('wc-finalizado');
        <?php elseif($post->post_status === 'wc-cancelado') : ?>
            $('select#post_status').val('wc-cancelado');
        <?php endif; ?>
    });
    </script>
    <?php
}

// 4. Mostrar junto al título en admin
add_filter('display_post_states', 'mostrar_estados_personalizados_admin', 10, 2);
function mostrar_estados_personalizados_admin($post_states, $post) {
    if($post->post_type === 'product') {
        if($post->post_status === 'wc-finalizado') {
            $post_states['wc-finalizado'] = __('Finalizado');
        } elseif($post->post_status === 'wc-cancelado') {
            $post_states['wc-cancelado'] = __('Cancelado');
        }
    }
    return $post_states;
}

// 5. Cambiar automáticamente a "Finalizado" usando WP-Cron (solo para finalizado)
if (!wp_next_scheduled('actualizar_productos_finalizados')) {
    wp_schedule_event(time(), 'hourly', 'actualizar_productos_finalizados');
}

add_action('actualizar_productos_finalizados', function(){
    $args = array(
        'post_type'   => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query'  => array(
            'relation' => 'AND',
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