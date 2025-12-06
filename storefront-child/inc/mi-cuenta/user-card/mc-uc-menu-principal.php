<?php
add_shortcode('mc_uc_menu_principal', function () {
    if (!is_user_logged_in()) return '';

    $current_user = wp_get_current_user();
    $roles = (array) $current_user->roles;

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
    
    <nav class="mc-uc-menu" role="navigation" aria-label="Menú personalizado">
        
        <!-- Editar cuenta -->
        <a href="<?php echo esc_url(site_url('/mi-cuenta/editar-cuenta/')); ?>" 
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
    <?php
    return ob_get_clean();
});

// Forzar redirección al cerrar sesión hacia /acceso/
add_action('wp_logout', function() {
    // URL de destino después del logout
    $redirect = site_url('/acceso/');

    // Redirige de forma segura y termina la ejecución
    wp_safe_redirect( $redirect );
    exit();
});
