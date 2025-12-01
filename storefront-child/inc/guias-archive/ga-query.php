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
    ?>

    <div class="ga-query-wrapper">

        <!-- Mostrar filtros activos (rellenado por JS) -->
        <div class="filtros-activos">
            <div class="filtros-lista"></div>
        </div>

        <div class="ga-query-grid">
        <?php foreach ($users as $user) : 
            $user_id     = $user->ID;
            $nombre      = esc_html($user->display_name);
            $author_url  = get_author_posts_url($user_id);

            // Avatar
            $avatar_id   = get_user_meta($user_id, 'avatar_del_usuario', true);
            $avatar_html = $avatar_id && is_numeric($avatar_id) 
                ? '<img src="' . esc_url(wp_get_attachment_image_url($avatar_id, 'full')) . '" alt="' . esc_attr($nombre) . '" class="guia-avatar">'
                : get_avatar($user_id, 110, '', $nombre, ['class' => 'guia-avatar']);

            // Imagen banner guía (URL o ID)
            $banner_meta = get_user_meta($user_id, 'imagen_banner_guia', true);
            $banner_url  = '';

            if ($banner_meta && is_numeric($banner_meta)) {
                $banner_url = wp_get_attachment_image_url($banner_meta, 'full');
            } elseif ($banner_meta && filter_var($banner_meta, FILTER_VALIDATE_URL)) {
                $banner_url = $banner_meta;
            }

            // Provincia y regiones
            $codigo_estado = get_user_meta($user_id, 'billing_state', true);
            $codigo_pais   = get_user_meta($user_id, 'billing_country', true) ?: 'ES';
            $provincia     = obtener_nombre_estado_wc($codigo_estado, $codigo_pais);

            $regiones       = get_user_meta($user_id, 'comunidad_autonoma', true);
            $regiones_slugs = [];
            $regiones_nombres = [];

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
                        $slug  = $region->slug ?? sanitize_title($region->name);
                        $name  = $region->name ?? '';
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

            // Modalidades
            $modalidades       = wp_get_object_terms($user_id, 'modalidad');
            $modalidades_slugs = [];
            $modalidades_html  = '';

            if (!is_wp_error($modalidades) && !empty($modalidades)) {
                foreach ($modalidades as $m) {
                    $modalidades_slugs[] = $m->slug;
                    $modalidades_html   .= '<span class="guia-modalidad-item">' . esc_html($m->name) . '</span>';
                }
                $modalidades_html = '<div class="guia-modalidades">' . $modalidades_html . '</div>';
            }
            ?>

            <div class="ga-query-item" 
                data-regiones="<?php echo esc_attr(implode(',', $regiones_slugs)); ?>"
                data-modalidades="<?php echo esc_attr(implode(',', $modalidades_slugs)); ?>">

                <div class="ga-imagen-contenedor">
                    <?php if ($banner_url) : ?>
                        <div class="ga-banner-ratio">
                            <img src="<?php echo esc_url($banner_url); ?>" alt="Banner de <?php echo esc_attr($nombre); ?>" class="ga-banner">
                        </div>
                    <?php endif; ?>

                    <a href="<?php echo esc_url($author_url); ?>" class="ga-avatar-overlay">
                        <?php echo $avatar_html; ?>
                    </a>
                </div>

                <div class="ga-nombre-ubicacion">
                    <a href="<?php echo esc_url($author_url); ?>" class="ga-link">
                        <span class="ga-nombre"><?php echo $nombre; ?></span>
                    </a>

                    <?php if ($provincia) : ?>
                        <span class="ga-ubicacion">
                            <?php echo esc_html($provincia); ?>
                            <?php if (!empty($regiones_nombres)) : ?>
                                (<?php echo esc_html(implode(', ', $regiones_nombres)); ?>)
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="guia-rating">
                    <?php echo do_shortcode('[valoracion-media-guia id="'.$user_id.'"]'); ?>
                </div>

                <?php echo $modalidades_html; ?>
            </div>


        <?php endforeach; ?>
        </div> <!-- .ga-query-grid -->

    </div> <!-- .ga-query-wrapper -->


    <?php
    return ob_get_clean();
}
