<?php
/**
 * Sección Contenido para Entradas Individuales
 * Shortcode: [bs_principal]
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('bs_principal', 'bs_principal_shortcode');

function bs_principal_shortcode($atts) {
    if (!is_singular('post')) {
        return '';
    }

    $post_id = get_the_ID();
    $titulo = get_the_title($post_id);
    $contenido = get_the_content();
    $imagen_destacada = get_the_post_thumbnail_url($post_id, 'full');
    $categorias = get_the_category($post_id);

    ob_start();
    ?>

    <div class="bs-contenedor-ppal">

        <!-- Imagen destacada -->
        <?php if ($imagen_destacada) : ?>
        <div class="bs-imagen-destacada">
            <img src="<?php echo esc_url($imagen_destacada); ?>" alt="<?php echo esc_attr($titulo); ?>" />
        </div>
        <?php endif; ?>

        <div class="bs-contenido">            

            <!-- Título de la entrada -->
            <div class="bs-titulo-entrada">
                <h1><?php echo esc_html($titulo); ?></h1>
            </div>

            <!-- Categorías -->
            <?php if (!empty($categorias)) : ?>
                <div class="bs-categorias">
                    <?php foreach ($categorias as $categoria) : ?>
                        <div class="bs-categorias-item">
                            <?php echo esc_html($categoria->name); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Contenido de la entrada -->
            <div class="bs-contenido-entrada">
                <?php echo apply_filters('the_content', $contenido); ?>
            </div>

        </div>
    </div>

    <?php
    return ob_get_clean();
}
