<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function trekkium_register_cpt_valoraciones() {

    $labels = array(
        'name'                  => 'Valoraciones',
        'singular_name'         => 'Valoración',
        'menu_name'             => 'Valoraciones',
        'name_admin_bar'        => 'Valoración',
        'add_new'               => 'Añadir nueva',
        'add_new_item'          => 'Añadir nueva valoración',
        'new_item'              => 'Nueva valoración',
        'edit_item'             => 'Editar valoración',
        'view_item'             => 'Ver valoración',
        'all_items'             => 'Todas las valoraciones',
        'search_items'          => 'Buscar valoraciones',
        'not_found'             => 'No se encontraron valoraciones',
        'not_found_in_trash'    => 'No hay valoraciones en la papelera',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'supports'           => array( 'title' ),
        'menu_icon'          => 'dashicons-star-filled',
        'menu_position'      => 26,
    );

    register_post_type( 'valoraciones', $args );
}
add_action( 'init', 'trekkium_register_cpt_valoraciones' );

/**
 * Funciones relacionadas con el CPT "valoraciones".
 * Estas funciones provienen de valoraciones-functions.php y se han
 * integrado aquí para poder eliminar ese archivo.
 */

if ( ! function_exists( 'trekkium_crear_valoracion' ) ) {
    function trekkium_crear_valoracion( $data ) {

        if ( empty($data['usuario_id']) || empty($data['actividad_id']) || empty($data['pedido_id']) ) {
            return new WP_Error('missing_data', 'Faltan datos obligatorios');
        }

        $titulo = sprintf(
            'Valoración – %s – %s – %s',
            $data['nombre_cliente'],
            $data['nombre_actividad'],
            date('d/m/Y', strtotime($data['fecha_actividad']))
        );

        $post_id = wp_insert_post(array(
            'post_type' => 'valoraciones',
            'post_title' => $titulo,
            'post_status' => 'publish',
        ));

        if ( is_wp_error($post_id) ) return $post_id;

        foreach ( $data as $key => $value ) {
            add_post_meta( $post_id, $key, $value, true );
        }

        return $post_id;
    }
}

if ( ! function_exists( 'trekkium_generar_token_valoracion' ) ) {
    function trekkium_generar_token_valoracion($pedido_id) {
        $token = wp_hash( $pedido_id . microtime(), 'nonce' );
        update_post_meta( $pedido_id, '_valoracion_token', $token );
        update_post_meta( $pedido_id, '_valoracion_token_usado', 0 );
        return $token;
    }
}

if ( ! function_exists( 'trekkium_verificar_token_valoracion' ) ) {
    function trekkium_verificar_token_valoracion($pedido_id, $token) {
        $token_guardado = get_post_meta( $pedido_id, '_valoracion_token', true );
        $usado = get_post_meta( $pedido_id, '_valoracion_token_usado', true );

        if ( $token_guardado === $token && !$usado ) {
            return true;
        }

        return false;
    }
}

if ( ! function_exists( 'trekkium_marcar_token_usado' ) ) {
    function trekkium_marcar_token_usado($pedido_id) {
        update_post_meta($pedido_id, '_valoracion_token_usado', 1);
    }
}

// Añadir metabox con datos de la valoración

add_action( 'add_meta_boxes', 'trekkium_add_metabox_valoraciones' );
function trekkium_add_metabox_valoraciones() {
    add_meta_box(
        'trekkium_valoracion_datos',
        'Datos de la valoración',
        'trekkium_render_metabox_valoracion',
        'valoraciones',
        'normal',
        'default'
    );
}

// Renderizar contenido del metabox
function trekkium_render_metabox_valoracion( $post ) {

    $get = function($key) use ($post) {
        return get_post_meta( $post->ID, $key, true );
    };

    ?>

    <style>
        .trekkium-meta-table{
            width:100%;
            border-collapse:collapse;
        }
        .trekkium-meta-table th{
            text-align:left;
            width:220px;
            padding:6px 10px;
            background:#f7f7f7;
            vertical-align:top;
        }
        .trekkium-meta-table td{
            padding:6px 10px;
        }
        .trekkium-stars{
            color:#ffc107;
            font-size:18px;
        }
    </style>

    <h3>Cliente</h3>
    <table class="trekkium-meta-table">
        <tr>
            <th>Nombre</th>
            <td><?php echo esc_html( $get('nombre_cliente') ); ?></td>
        </tr>
        <tr>
            <th>Usuario ID</th>
            <td><?php echo esc_html( $get('usuario_id') ); ?></td>
        </tr>
        <tr>
            <th>Pedido</th>
            <td>
                <a href="<?php echo admin_url( 'post.php?post=' . $get('pedido_id') . '&action=edit' ); ?>">
                    #<?php echo esc_html( $get('pedido_id') ); ?>
                </a>
            </td>
        </tr>
    </table>

    <h3>Actividad</h3>
    <table class="trekkium-meta-table">
        <tr>
            <th>Actividad</th>
            <td>
                <a href="<?php echo admin_url( 'post.php?post=' . $get('actividad_id') . '&action=edit' ); ?>">
                    <?php echo esc_html( $get('nombre_actividad') ); ?>
                </a>
            </td>
        </tr>
        <tr>
            <th>Fecha actividad</th>
            <td><?php echo esc_html( $get('fecha_actividad') ); ?></td>
        </tr>
    </table>

    <h3>Guía</h3>
    <table class="trekkium-meta-table">
        <tr>
            <th>Guía</th>
            <td><?php echo esc_html( $get('nombre_guia') ); ?></td>
        </tr>
    </table>

    <h3>Valoraciones</h3>
    <table class="trekkium-meta-table">
        <tr>
            <th>Actividad (general)</th>
            <td class="trekkium-stars"><?php echo str_repeat('★', intval($get('valor_actividad_general'))); ?></td>
        </tr>
        <tr>
            <th>Organización</th>
            <td class="trekkium-stars"><?php echo str_repeat('★', intval($get('organizacion_actividad'))); ?></td>
        </tr>
        <tr>
            <th>Seguridad</th>
            <td class="trekkium-stars"><?php echo str_repeat('★', intval($get('sensacion_seguridad'))); ?></td>
        </tr>
        <tr>
            <th>Guía (general)</th>
            <td class="trekkium-stars"><?php echo str_repeat('★', intval($get('valoracion_guia_general'))); ?></td>
        </tr>
        <tr>
            <th>Comentario guía</th>
            <td><?php echo nl2br( esc_html( $get('comentario_guia') ) ); ?></td>
        </tr>
        <tr>
            <th>Experiencia Trekkium</th>
            <td class="trekkium-stars"><?php echo str_repeat('★', intval($get('experiencia_trekkium'))); ?></td>
        </tr>
    </table>

    <?php
}

// Añadir columna en el listado admin que muestre el autor del producto (actividad) valorado
add_filter( 'manage_valoraciones_posts_columns', 'trekkium_valoraciones_columns' );
function trekkium_valoraciones_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $title ) {
        $new[ $key ] = $title;
        if ( 'title' === $key ) {
            $new['producto_autor'] = 'Guía';
        }
    }
    return $new;
}

add_action( 'manage_valoraciones_posts_custom_column', 'trekkium_valoraciones_custom_column', 10, 2 );
function trekkium_valoraciones_custom_column( $column, $post_id ) {
    if ( 'producto_autor' !== $column ) {
        return;
    }

    $actividad_id = get_post_meta( $post_id, 'actividad_id', true );

    if ( empty( $actividad_id ) ) {
        echo '—';
        return;
    }

    $author_id = get_post_field( 'post_author', $actividad_id );

    if ( empty( $author_id ) ) {
        echo '—';
        return;
    }

    $author_name = get_the_author_meta( 'display_name', $author_id );
    $edit_link = admin_url( 'post.php?post=' . intval( $actividad_id ) . '&action=edit' );

    echo '<a href="' . esc_url( $edit_link ) . '">' . esc_html( $author_name ) . '</a>';
}
