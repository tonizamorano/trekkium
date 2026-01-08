<?php

function gs_seccion_principal($atts) {
    $atts = shortcode_atts([
        'author_id' => 0
    ], $atts, 'gs_seccion_principal');

    $author_id = intval($atts['author_id']);

    if (!$author_id) {
        $author_id = get_queried_object_id();
    }

    if (!$author_id) return ''; // si no hay autor, no mostrar nada

    // Obtener campos del usuario
    $sobremi = get_user_meta($author_id, 'sobre_mi', true);
    $banner_id = get_user_meta($author_id, 'imagen_banner', true);
    $banner    = $banner_id ? wp_get_attachment_image_url($banner_id, 'full') : '';

    $avatar = get_avatar_url($author_id, ['size' => 300]);

    // Datos adicionales del guía
    $display_name = get_the_author_meta('display_name', $author_id);
    $billing_state_code   = get_user_meta($author_id, 'billing_state', true);
    $billing_country_code = get_user_meta($author_id, 'billing_country', true) ?: 'ES';
    $billing_state        = function_exists('obtener_nombre_estado_wc') ? obtener_nombre_estado_wc($billing_state_code, $billing_country_code) : '';
    $comunidad_autonoma   = get_user_meta($author_id, 'comunidad_autonoma', true);

    // Obtener términos de la taxonomía "modalidad" y "titulacion" del usuario
    $modalidades = wp_get_object_terms($author_id, 'modalidad');
    $titulaciones = get_the_terms($author_id, 'titulacion'); // versión más robusta

    if (empty($sobremi)) {
        return '';
    }

    // Mapeo de titulaciones
    $mapa_titulaciones = [
        'TD2 Media montaña' => 'Técnico Deportivo en Media Montaña',
        'TD2 Barrancos' => 'Técnico Deportivo en Barrancos',
        'TD2 Escalada' => 'Técnico Deportivo en Escalada',
        'TD3 Escalada' => 'Técnico Deportivo Superior en Escalada',
        'TD3 Alta montaña' => 'Técnico Deportivo Superior en Alta Montaña'
    ];

    // Mapeo de acreditaciones
    $mapa_acreditaciones = [
        'AEGM' => 'aegm.png',
        'AEGM - Alta montaña' => 'aegm_altamontana.png',
        'AEGM - Barrancos' => 'aegm_barrancos.png',
        'AEGM - Escalada' => 'aegm_escalada.png',
        'UIMLA' => 'uimla.png',
        'UIAGM' => 'uiagm.png'
    ];

    ob_start();
    ?>
    <div class="gs-seccion-ppal-contenedor">

        <!-- Imagen -->
        <div class="imagen">
            <?php if ($banner): ?>
                <div class="gs-banner-wrapper">
                    <img src="<?php echo esc_url($banner); ?>" alt="Banner del guía" class="gs-banner-img">
                    <div class="gs-avatar-wrapper">
                        <img src="<?php echo esc_url($avatar); ?>" alt="Avatar del guía" class="gs-avatar-img">
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Datos del guía -->
        <div class="gs-seccion-ppal-guia">
            <div class="gs-guia-nombre"><?php echo esc_html($display_name); ?></div>

            <?php if ($billing_state): ?>
                <div class="gs-guia-localidad">
                    <?php 
                        echo esc_html($billing_state);
                        if ($comunidad_autonoma) {
                            echo ' (' . esc_html($comunidad_autonoma) . ')';
                        }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Valoracion de estrella -->
            <?php
            /*
            <div class="gs-guia-valoracion-media">
                <?php echo do_shortcode('[valoracion-media-guia]'); ?>
            </div>
            */
            ?>

            <!-- Titulaciones -->
            <?php
            $titulaciones = wp_get_object_terms($author_id, 'titulacion');

            if (!empty($titulaciones) && !is_wp_error($titulaciones)) :
            ?>
                <div class="gs-titulaciones">
                    <?php
                    foreach ($titulaciones as $titulacion) {
                        $nombre = $titulacion->name;
                        if (isset($mapa_titulaciones[$nombre])) {
                            echo '<div class="gs-titulacion-item">' . esc_html($mapa_titulaciones[$nombre]) . '</div>';
                        } else {
                            echo '<div class="gs-titulacion-item">' . esc_html($nombre) . '</div>';
                        }
                    }
                    ?>
                </div>
            <?php endif; ?>

                  

        </div>

        <!-- Modalidades -->
        <?php if (!empty($modalidades) && !is_wp_error($modalidades)): ?>
            <div class="gs-modalidades">
                <?php foreach ($modalidades as $modalidad): ?>
                    <span class="gs-modalidad-item"><?php echo esc_html($modalidad->name); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Acreditaciones -->
        <?php
        $acreditaciones = get_user_meta($author_id, 'acreditaciones', true);
        if (!empty($acreditaciones)) :
            $acreditaciones_array = is_array($acreditaciones) ? $acreditaciones : explode(',', $acreditaciones);
            ?>
            <div class="gs-acreditaciones">
                <?php 
                $img_url_base = trekkium_asset_url('img/credits');
                foreach ($acreditaciones_array as $acred) :
                    $acred = trim($acred);
                    if (isset($mapa_acreditaciones[$acred])) :
                        ?>
                        <div class="gs-acreditacion-item">
                            <img src="<?php echo esc_url($img_url_base . $mapa_acreditaciones[$acred]); ?>" alt="<?php echo esc_attr($acred); ?>">
                        </div>
                    <?php endif;
                endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Descripción de la bio -->
        <div class="gs-sobremi-contenido">
            <?php echo wp_kses_post($sobremi); ?>
        </div>

    </div>

    <style>
        .gs-acreditaciones {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 15px 15px 0 15px;
        }
        .gs-acreditacion-item img {
            max-height: 80px;
            display: block;
        }
    </style>

    <?php
    return ob_get_clean();
}
add_shortcode('gs_seccion_principal', 'gs_seccion_principal');
