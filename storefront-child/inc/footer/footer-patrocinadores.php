<?php
// Shortcode: [footer_patrocinadores]
function footer_patrocinadores_shortcode() {
    ob_start(); 
    
    // Consulta para obtener los patrocinadores activos
    $args = array(
        'post_type' => 'patrocinador',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'pat_estado',
                'value' => 'ACTIVO',
                'compare' => '='
            )
        ),
        'orderby' => 'title',
        'order' => 'ASC'
    );
    
    $patrocinadores = new WP_Query($args);
    ?>

    <style>
    .footer-patrocinadores-contenedor {
        width: 900px;
        max-width: 100%;
        margin: 20px auto;
        text-align: center;
    }

    .footer-patrocinadores-contenedor h2 {
        font-size: 18px;
        font-weight: 500;
        color: #ffffff;
        margin-bottom: 20px !important;
    }

    .footer-patrocinadores-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        row-gap: 20px;
        column-gap: 30px;
        align-items: center;
    }

    .footer-patrocinadores-wrapper a {
        display: inline-block;
    }

    .footer-patrocinadores-wrapper img {
        height: 40px; /* Altura por defecto */
        width: auto;
        transition: all 0.3s ease;
    }

    @media (max-width: 768px) {

        .footer-patrocinadores-contenedor {
            width: 100%;
            padding: 0 15px;
        }

         .footer-patrocinadores-wrapper {
            row-gap: 15px;
            column-gap: 20px;
        }

        .footer-patrocinadores-wrapper img {
            transition: all 0.3s ease;
        }

    }
    </style>

    <div class="footer-patrocinadores-contenedor">
        <h2>PATROCINADORES</h2>
        <div class="footer-patrocinadores-wrapper">
            <?php if ($patrocinadores->have_posts()) : ?>
                <?php while ($patrocinadores->have_posts()) : $patrocinadores->the_post(); 
                    $post_id = get_the_ID();
                    $logo_blanco = get_post_meta($post_id, 'pat_logo_blanco', true);
                    $logo_color = get_post_meta($post_id, 'pat_logo_color', true);
                    $web = get_post_meta($post_id, 'pat_web', true);
                    $marca = get_post_meta($post_id, 'pat_marca', true);
                    $logo_height = get_post_meta($post_id, 'pat_logo_height', true);
                    
                    // Si no hay altura personalizada, usar 40px por defecto
                    $height_style = $logo_height ? 'height: ' . intval($logo_height) . 'px;' : 'height: 40px;';
                    
                    // Obtener URLs de las imágenes
                    $logo_blanco_url = $logo_blanco ? wp_get_attachment_url($logo_blanco) : '';
                    $logo_color_url = $logo_color ? wp_get_attachment_url($logo_color) : '';
                    
                    // Si no hay logo color, usar el blanco como fallback
                    if (!$logo_color_url && $logo_blanco_url) {
                        $logo_color_url = $logo_blanco_url;
                    }
                    
                    // Solo mostrar si tiene al menos un logo
                    if ($logo_blanco_url) : ?>
                        <a href="<?php echo esc_url($web); ?>" target="_blank" title="<?php echo esc_attr($marca); ?>">
                            <img src="<?php echo esc_url($logo_blanco_url); ?>" 
                                 alt="<?php echo esc_attr($marca); ?>" 
                                 onmouseover="this.src='<?php echo esc_url($logo_color_url); ?>';" 
                                 onmouseout="this.src='<?php echo esc_url($logo_blanco_url); ?>';"
                                 style="<?php echo $height_style; ?>">
                        </a>
                    <?php endif; ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <!-- Mensaje opcional si no hay patrocinadores activos -->
                <p style="color: #ffffff; font-size: 14px;">No hay patrocinadores activos en este momento.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('footer_patrocinadores', 'footer_patrocinadores_shortcode');