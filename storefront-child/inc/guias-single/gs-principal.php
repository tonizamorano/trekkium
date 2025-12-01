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
    $banner = get_user_meta($author_id, 'imagen_banner_guia', true);
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
        'TD2 Media Montaña' => 'Técnico Deportivo en Media Montaña',
        'TD2 Barrancos' => 'Técnico Deportivo en Barrancos',
        'TD2 Escalada' => 'Técnico Deportivo en Escalada',
        'TD3 Escalada' => 'Técnico Deportivo Superior en Escalada',
        'TD3 Alta montaña' => 'Técnico Deportivo Superior en Alta Montaña'
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
            <div class="gs-guia-valoracion-media">
                <?php echo do_shortcode('[valoracion-media-guia]'); ?>
            </div>

            <!-- Titulaciones -->
            <?php
            // Obtener términos con el mismo método que el otro shortcode, para evitar conflictos
            $titulaciones = wp_get_object_terms($author_id, 'titulacion');

            if (!empty($titulaciones) && !is_wp_error($titulaciones)) :
            ?>
                <div class="gs-titulaciones">
                    <?php
                    $mapa_titulaciones = [
                        'TD2 Media montaña' => 'Técnico Deportivo en Media Montaña',
                        'TD2 Barrancos' => 'Técnico Deportivo en Barrancos',
                        'TD2 Escalada' => 'Técnico Deportivo en Escalada',
                        'TD3 Escalada' => 'Técnico Deportivo Superior en Escalada',
                        'TD3 Alta montaña' => 'Técnico Deportivo Superior en Alta Montaña'
                    ];

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

            <!-- Idiomas -->
            <?php
            $idiomas = wp_get_object_terms($author_id, 'idiomas');

            if (!empty($idiomas) && !is_wp_error($idiomas)) :
                // Mapeo de idioma a archivo de icono
                $mapa_idiomas = [
                    'Español'  => 'espanol.png',
                    'Català'   => 'catala.png',
                    'Euskera'  => 'euskera.png',
                    'Galego'   => 'galego.png',
                    'Inglés'   => 'ingles.png',
                    'Francés'  => 'frances.png',
                    'Italiano' => 'italiano.png',
                    'Portugués'=> 'portugues.png',
                    'Alemán'   => 'aleman.png',
                ];

                $iconos_url = trekkium_asset_url('img/idiomas');
                ?>
                <div class="gs-idiomas">
                    <?php foreach ($idiomas as $idioma): 
                        $archivo = $mapa_idiomas[$idioma->name] ?? '';
                        if ($archivo): ?>
                            <img src="<?php echo esc_url($iconos_url . $archivo); ?>" 
                                alt="<?php echo esc_attr($idioma->name); ?>">
                    <?php endif; endforeach; ?>
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

        <!-- Descripción de la bio -->
        <div class="gs-sobremi-contenido">
            <?php echo wp_kses_post($sobremi); ?>
        </div>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('gs_seccion_principal', 'gs_seccion_principal');
