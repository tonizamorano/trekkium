<?php
/*
Plugin Name: Sección Organizador Producto (BS Autor)
Description: Muestra la sección del organizador o autor de un producto o entrada con enlace a su perfil.
Version: 1.5
Author: Toni
*/

function bs_autor_shortcode() {
    if (!is_singular()) return;

    ob_start();
    ?>

    <div class="bs-autor-contenedor">

        <div class="bs-autor-titular">
            <h5>Autor</h5>
        </div>

        <div class="bs-autor-contenido" style="align-content: top;">
            <?php
            $author_id = get_post_field('post_author', get_the_ID());

            // Avatar
            $avatar_meta = get_user_meta($author_id, 'avatar_del_usuario', true);
            $avatar_url  = $avatar_meta ? wp_get_attachment_url($avatar_meta) : '';

            // Nombre, provincia (legible) y comunidad
            $nombre = get_the_author_meta('display_name', $author_id);
            $provincia_codigo = get_user_meta($author_id, 'billing_state', true);

            // Si existe la función, obtenemos el nombre legible del estado
            if (function_exists('obtener_nombre_estado_wc')) {
                $pais_codigo = get_user_meta($author_id, 'billing_country', true);
                $provincia   = obtener_nombre_estado_wc($provincia_codigo, $pais_codigo);
            } else {
                $provincia = $provincia_codigo;
            }

            $comunidad = get_user_meta($author_id, 'comunidad_autonoma', true);

            // URL del autor
            $author_url = get_author_posts_url($author_id);
            ?>

            <div class="bs-autor-info">
                
                <?php if ($avatar_url): ?>
                    <a href="<?php echo esc_url($author_url); ?>" class="bs-autor-avatar-enlace">
                        <img src="<?php echo esc_url($avatar_url); ?>" alt="Avatar de <?php echo esc_attr($nombre); ?>" class="bs-avatar-del-autor">
                    </a>
                <?php endif; ?>

                <div class="bs-autor-detalles">
                    <p class="bs-autor-nombre"><?php echo esc_html($nombre); ?></p>
                    <?php if ($provincia || $comunidad): ?>
                        <p class="bs-autor-comunidad">
                            <?php echo esc_html($provincia); ?>
                            <?php if ($provincia && $comunidad) echo ' '; ?>
                            <?php if ($comunidad) echo '(' . esc_html($comunidad) . ')'; ?>
                        </p>
                    <?php endif; ?>
                   
                </div>    

            </div>             

        </div>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('bs_autor', 'bs_autor_shortcode');
