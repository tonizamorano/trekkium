<?php
add_shortcode('query_guias', 'trekkium_shortcode_query_guias');

function trekkium_shortcode_query_guias() {
    ob_start();
    
    $users = get_users([
        'role'    => 'guia',
        'orderby' => 'display_name',
        'order'   => 'ASC',
    ]);

    if (empty($users)) {
        return '<p>No hay guías disponibles.</p>';
    }
    
    // Iniciar la salida del HTML
    trekkium_render_guias_html($users);
    
    return ob_get_clean();
}

function trekkium_render_guias_html($users) {
    ?>
    <div class="ga-query-wrapper">
        <!-- Mostrar filtros activos (rellenado por JS) -->
        <div class="filtros-activos">
            <div class="filtros-lista"></div>
        </div>

        <div class="ga-query-grid">
            <?php foreach ($users as $user) : 
                $user_data = trekkium_prepare_user_data($user);
                if ($user_data) {
                    trekkium_render_guia_item($user_data);
                }
            endforeach; ?>
        </div> <!-- .ga-query-grid -->
    </div> <!-- .ga-query-wrapper -->
    <?php
}

function trekkium_prepare_user_data($user) {
    $user_id = $user->ID;
    
    // Datos básicos del usuario
    $data = [
        'user_id'    => $user_id,
        'nombre'     => esc_html($user->display_name),
        'author_url' => get_author_posts_url($user_id),
    ];
    
    // Avatar
    $avatar_id = get_user_meta($user_id, 'avatar_del_usuario', true);
    if ($avatar_id && is_numeric($avatar_id)) {
        $data['avatar_html'] = '<img src="' . esc_url(wp_get_attachment_image_url($avatar_id, 'full')) . 
                              '" alt="' . esc_attr($data['nombre']) . '" class="guia-avatar">';
    } else {
        $data['avatar_html'] = get_avatar($user_id, 110, '', $data['nombre'], ['class' => 'guia-avatar']);
    }
    
    // Banner
    $data['banner_url'] = trekkium_get_banner_url($user_id);
    
    // Provincia y regiones
    list($data['provincia'], $data['regiones_slugs'], $data['regiones_nombres']) = 
        trekkium_get_ubicacion_data($user_id);
    
    // Modalidades
    list($data['modalidades_slugs'], $data['modalidades_html']) = 
        trekkium_get_modalidades_data($user_id);
    
    return $data;
}

function trekkium_get_banner_url($user_id) {
    $banner_meta = get_user_meta($user_id, 'imagen_banner', true);
    
    if ($banner_meta && is_numeric($banner_meta)) {
        return wp_get_attachment_image_url($banner_meta, 'full');
    } elseif ($banner_meta && filter_var($banner_meta, FILTER_VALIDATE_URL)) {
        return $banner_meta;
    }
    
    return '';
}

function trekkium_get_ubicacion_data($user_id) {
    $codigo_estado = get_user_meta($user_id, 'billing_state', true);
    $codigo_pais = get_user_meta($user_id, 'billing_country', true) ?: 'ES';
    $provincia = obtener_nombre_estado_wc($codigo_estado, $codigo_pais);
    
    $regiones_slugs = [];
    $regiones_nombres = [];
    $regiones = get_user_meta($user_id, 'comunidad_autonoma', true);
    
    if ($regiones) {
        if (is_string($regiones)) {
            $decoded = json_decode($regiones, true);
            $regiones = $decoded ?: [$regiones];
        }
        
        if (!is_array($regiones)) {
            $regiones = [$regiones];
        }
        
        foreach ($regiones as $region) {
            if (is_object($region)) {
                $slug = $region->slug ?? sanitize_title($region->name);
                $name = $region->name ?? '';
                if ($slug && $name) {
                    $regiones_slugs[] = $slug;
                    $regiones_nombres[] = $name;
                }
            } elseif (is_numeric($region)) {
                $term = get_term($region);
                if ($term && !is_wp_error($term)) {
                    $regiones_slugs[] = $term->slug;
                    $regiones_nombres[] = $term->name;
                }
            } elseif (is_string($region)) {
                $regiones_slugs[] = sanitize_title($region);
                $regiones_nombres[] = $region;
            }
        }
    }
    
    return [$provincia, $regiones_slugs, $regiones_nombres];
}

function trekkium_get_modalidades_data($user_id) {
    $modalidades = wp_get_object_terms($user_id, 'modalidad');
    $modalidades_slugs = [];
    $modalidades_html = '';
    
    if (!is_wp_error($modalidades) && !empty($modalidades)) {
        $modalidades_items = [];
        foreach ($modalidades as $m) {
            $modalidades_slugs[] = $m->slug;
            $modalidades_items[] = '<span class="guia-query-modalidad-item">' . esc_html($m->name) . '</span>';
        }
        $modalidades_html = '<div class="guia-query-modalidades">' . implode('', $modalidades_items) . '</div>';
    }
    
    return [$modalidades_slugs, $modalidades_html];
}

function trekkium_render_guia_item($data) {
    ?>
    <a href="<?php echo esc_url($data['author_url']); ?>" class="ga-query-item ga-query-item-link" 
        data-regiones="<?php echo esc_attr(implode(',', $data['regiones_slugs'])); ?>"
        data-modalidades="<?php echo esc_attr(implode(',', $data['modalidades_slugs'])); ?>">

        <div class="ga-imagen-contenedor">
            <?php if ($data['banner_url']) : ?>
                <div class="ga-banner-ratio">
                    <img src="<?php echo esc_url($data['banner_url']); ?>" 
                         alt="Banner de <?php echo esc_attr($data['nombre']); ?>" 
                         class="ga-banner">
                </div>
            <?php endif; ?>

            <div class="ga-avatar-overlay">
                <?php echo $data['avatar_html']; ?>
            </div>
        </div>

        <div class="ga-query-contenido">

            <div class="ga-nombre-ubicacion">
                <div class="ga-link">
                    <span class="ga-nombre"><?php echo $data['nombre']; ?></span>
                </div>

                <?php if ($data['provincia']) : ?>
                    <span class="ga-ubicacion">
                        <?php echo esc_html($data['provincia']); ?>
                        <?php if (!empty($data['regiones_nombres'])) : ?>
                            (<?php echo esc_html(implode(', ', $data['regiones_nombres'])); ?>)
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>

        
            <div class="guia-rating">
                <?php
                /*
                <div class="guia-rating">
                    <?php echo do_shortcode('[valoracion-media-guia id="' . $data['user_id'] . '"]'); ?>
                </div>
                */
                ?>
            </div>

            <?php
            $titulaciones = wp_get_object_terms($data['user_id'], 'titulacion');
            if (!is_wp_error($titulaciones) && !empty($titulaciones)) : ?>
                <div class="guia-titulaciones" style="font-weight: 500;">
                    <?php foreach ($titulaciones as $t) : ?>
                        <div class="guia-titulacion-item"><?php echo esc_html($t->name); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>


            <?php echo $data['modalidades_html']; ?>

        </div>
        
    </a>
    <?php
}