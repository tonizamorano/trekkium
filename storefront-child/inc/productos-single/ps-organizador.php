<?php
/*
Plugin Name: Sección Organizador Producto
Description: Muestra la sección del organizador de un producto con enlace al perfil del autor.
Version: 1.4
Author: Toni
*/

function seccion_organizador_shortcode() {
    if (!is_singular('product')) return;

    // Obtener todos los datos primero
    $author_id = get_post_field('post_author', get_the_ID());
    
    // Avatar
    $avatar_meta = get_user_meta($author_id, 'avatar_del_usuario', true);
    $avatar_url = $avatar_meta ? wp_get_attachment_url($avatar_meta) : '';
    
    // Nombre, provincia (legible) y comunidad
    $nombre = get_the_author_meta('display_name', $author_id);
    $provincia_codigo = get_user_meta($author_id, 'billing_state', true);
    $comunidad = get_user_meta($author_id, 'comunidad_autonoma', true);
    
    // Procesar provincia
    $provincia = $provincia_codigo; // fallback por defecto
    
    if (function_exists('obtener_nombre_estado_wc')) {
        $pais_codigo = get_user_meta($author_id, 'billing_country', true);
        $provincia_codigo = get_user_meta($author_id, 'billing_state', true);
        
        if (function_exists('obtener_nombre_estado_wc')) {
            $provincia = obtener_nombre_estado_wc($provincia_codigo, $pais_codigo);
        }
    }
    
    // URL del autor
    $author_url = get_author_posts_url($author_id);
    
    // Valoración
    $valoracion_shortcode = do_shortcode('[valoracion-media-guia id="' . $author_id . '"]');

    // Iniciar buffer de salida
    ob_start();
    ?>

    <div class="ps-contenedor">

        <div class="ps-titular">
            <h5>Guía de montaña</h5>
        </div>

        <div class="ps-contenido" style="align-content: top;">
            <div class="ps-organizador-info">

                <?php if ($avatar_url): ?>
                    <a href="<?php echo esc_url($author_url); ?>" class="ps-organizador-avatar-enlace">
                        <img src="<?php echo esc_url($avatar_url); ?>" alt="Avatar de <?php echo esc_attr($nombre); ?>" class="ps-avatar-del-usuario">
                    </a>
                <?php endif; ?>

                <div class="ps-organizador-detalles">

                    <span class="ps-organizador-nombre"><?php echo esc_html($nombre); ?></span>

                    <?php if ($provincia || $comunidad): ?>
                        <span class="ps-organizador-comunidad">
                            <?php echo esc_html($provincia); ?>
                            <?php if ($provincia && $comunidad) echo ' '; ?>
                            <?php if ($comunidad) echo '(' . esc_html($comunidad) . ')'; ?>
                        </span>
                    <?php endif; ?>

                    <!--
                    <div class="autor-valoracion-media" style="display:flex; justify-content:flex-start;">
                        <?php echo $valoracion_shortcode; ?>
                    </div>
                    -->

                </div>

            </div>

        </div>

    </div>


    <?php
    return ob_get_clean();
}
add_shortcode('seccion_organizador', 'seccion_organizador_shortcode');