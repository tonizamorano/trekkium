<?php
/**
 * Sección Card Mi Cuenta - Mostrar avatar, datos del usuario y menú
 */
function mc_user_card_shortcode() {
    if (!is_user_logged_in()) {
        return ''; // No mostrar nada si no hay usuario logueado
    }

    $current_user   = wp_get_current_user();
    $nombre_usuario = $current_user->display_name; // Nombre visible del usuario
    $roles = (array) $current_user->roles;

    // Determinar el tipo de cuenta
    if (in_array('administrator', $roles)) {
        $role_text = 'Cuenta de administrador';
    } elseif (in_array('guia', $roles)) {
        $role_text = 'Cuenta de organizador';
    } elseif (in_array('customer', $roles)) {
        $role_text = 'Cuenta de cliente';
    } else {
        $role_text = 'Cuenta de usuario';
    }

    // Obtener avatar desde user meta
    $avatar_id = get_user_meta($current_user->ID, 'avatar_del_usuario', true);
    if ($avatar_id && is_numeric($avatar_id)) {
        $avatar_url = wp_get_attachment_image_url($avatar_id, 'thumbnail');
    } else {
        $avatar_url = 'https://trekkium.com/wp-content/uploads/2025/11/icon_user.png';
    }

    // Imagen de fondo por defecto
    $fondo_default = 'https://trekkium.com/wp-content/uploads/2025/11/240_F_879251769_zLoZJwcIRRtshYRAFNirocDdQ9zJxppF.jpg';

    // Si es guía, usar imagen_banner si existe
    if (in_array('guia', $roles)) {
        $banner_id = get_user_meta($current_user->ID, 'imagen_banner', true);
        if ($banner_id && is_numeric($banner_id)) {
            $fondo_url = wp_get_attachment_image_url($banner_id, 'full');
        } else {
            $fondo_url = $fondo_default;
        }
    } else {
        $fondo_url = $fondo_default;
    }

    // URLs locales de tu web
    $urls = [
        'editar-cuenta'   => site_url('/mi-cuenta/editar-cuenta/'),
        'reservas'        => site_url('/mi-cuenta/reservas/'),         
        'mis-actividades' => site_url('/mis-actividades/'),    
        'logout'          => wp_logout_url(site_url('/acceso/')),
    ];

    // Función para comprobar si el usuario tiene alguno de los roles indicados
    $has_role = function ($allowed_roles) use ($roles) {
        return count(array_intersect($roles, $allowed_roles)) > 0;
    };

    ob_start();
    ?>

    <div class="mc-user-card-contenedor">

        <!-- Contenedor para la imagen de fondo -->
        <div class="mc-user-card-imagen-fondo">
            <img src="<?php echo esc_url($fondo_url); ?>" alt="Fondo Trekkium">
        </div>

        <!-- Avatar del usuario -->
        <div class="mc-user-card-editar-avatar">
            <div class="mc-uc-avatar">
                <img 
                    src="<?php echo esc_url($avatar_url); ?>" 
                    alt="Avatar del usuario"
                    class="mc-uc-avatar-img"
                />
            </div>
        </div>

        <!-- Nombre del usuario logueado -->
        <div class="mc-user-card-nombre-usuario">
            <?php echo esc_html($nombre_usuario); ?>
        </div>

        <!-- Tipo de cuenta -->
        <div class="mc-user-card-tipo-cuenta">
            <div class="mc-user-card-tipo-cuenta-texto"><?php echo esc_html($role_text); ?></div>
        </div>

        <!-- Menú de Mi cuenta -->
        <nav class="mc-uc-menu" role="navigation" aria-label="Menú personalizado">

            <!-- Editar cuenta -->
            <a href="<?php echo esc_url($urls['editar-cuenta']); ?>" 
               class="<?php echo (is_wc_endpoint_url('edit-account') || is_page('editar-cuenta') || is_page('mi-cuenta/editar-cuenta')) ? 'active' : ''; ?>" 
               title="Editar cuenta" aria-label="Editar cuenta">
                <?php echo do_shortcode('[icon_editar_cuenta]'); ?>
            </a>

            <!-- Reservas -->
            <?php if ($has_role(['administrator', 'customer'])): ?>
            <a href="<?php echo esc_url($urls['reservas']); ?>" 
               class="<?php echo (is_wc_endpoint_url('orders') || strpos($_SERVER['REQUEST_URI'], 'reservas') !== false) ? 'active' : ''; ?>" 
               title="Mis reservas" aria-label="Mis reservas">
                <?php echo do_shortcode('[icon_mis_reservas]'); ?>
            </a>
            <?php endif; ?>

            <!-- Mis actividades -->
            <?php if ($has_role(['administrator', 'guia'])): ?>
            <a href="<?php echo esc_url($urls['mis-actividades']); ?>" 
               class="<?php echo (
                   is_page('mis-actividades') || 
                   is_page('editar-actividad') || 
                   is_page('ver-actividad') || 
                   is_page('nueva-actividad') || 
                   is_page('detalles-actividad')
               ) ? 'active' : ''; ?>" 
               title="Mis actividades" 
               aria-label="Mis actividades">
                <?php echo do_shortcode('[icon_mis_actividades]'); ?>
            </a>
            <?php endif; ?>

            <!-- Logout -->
            <a href="<?php echo esc_url($urls['logout']); ?>" title="Salir" aria-label="Salir">
                <?php echo do_shortcode('[icon_salir]'); ?>            
            </a>
        </nav>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('mc_user_card', 'mc_user_card_shortcode');

// Redirección al logout hacia /acceso/
add_action('wp_logout', function() {
    wp_safe_redirect(site_url('/acceso/'));
    exit();
});
