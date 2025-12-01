<?php
add_shortcode('menu_mi_cuenta', function () {
    if (!is_user_logged_in()) return '';

    $current_user = wp_get_current_user();
    $roles = (array) $current_user->roles;

    $color_azul = '#0b568b';
    $color_naranja = '#E67E22';

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
    
    <nav class="menu-circular" role="navigation" aria-label="Menú personalizado">
        
        <!-- Editar cuenta -->
        <a href="<?php echo esc_url(site_url('/mi-cuenta/editar-cuenta/')); ?>" 
        class="<?php echo (is_wc_endpoint_url('edit-account') || is_page('editar-cuenta') || is_page('mi-cuenta/editar-cuenta')) ? 'active' : ''; ?>" 
        title="Editar cuenta" aria-label="Editar cuenta">

        <svg viewBox="0 0 640 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><title>Editar cuenta</title><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h274.9c-2.4-6.8-3.4-14-2.6-21.3l6.8-60.9 1.2-11.1 7.9-7.9 77.3-77.3c-24.5-27.7-60-45.5-99.9-45.5zm45.3 145.3l-6.8 61c-1.1 10.2 7.5 18.8 17.6 17.6l60.9-6.8 137.9-137.9-71.7-71.7-137.9 137.8zM633 268.9L595.1 231c-9.3-9.3-24.5-9.3-33.8 0l-37.8 37.8-4.1 4.1 71.8 71.7 41.8-41.8c9.3-9.4 9.3-24.5 0-33.9z"></path></svg>
        </a>

        <!-- Reservas -->
		<?php if ($has_role(['administrator', 'customer'])): ?>
        <a href="<?php echo esc_url($urls['reservas']); ?>" 
        class="<?php echo (is_wc_endpoint_url('orders') || strpos($_SERVER['REQUEST_URI'], 'reservas') !== false) ? 'active' : ''; ?>" 
        title="Mis reservas" aria-label="Mis reservas">
    
        <svg viewBox="0 0 640 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M320 320c0-11.1 3.1-21.4 8.1-30.5-4.8-.5-9.5-1.5-14.5-1.5h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h280.9c-5.5-9.5-8.9-20.3-8.9-32V320zm-96-64c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm384 32h-32v-48c0-44.2-35.8-80-80-80s-80 35.8-80 80v48h-32c-17.7 0-32 14.3-32 32v160c0 17.7 14.3 32 32 32h224c17.7 0 32-14.3 32-32V320c0-17.7-14.3-32-32-32zm-80 0h-64v-48c0-17.6 14.4-32 32-32s32 14.4 32 32v48z"></path></svg>
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

            <svg viewBox="0 0 640 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M610.5 373.3c2.6-14.1 2.6-28.5 0-42.6l25.8-14.9c3-1.7 4.3-5.2 3.3-8.5-6.7-21.6-18.2-41.2-33.2-57.4-2.3-2.5-6-3.1-9-1.4l-25.8 14.9c-10.9-9.3-23.4-16.5-36.9-21.3v-29.8c0-3.4-2.4-6.4-5.7-7.1-22.3-5-45-4.8-66.2 0-3.3.7-5.7 3.7-5.7 7.1v29.8c-13.5 4.8-26 12-36.9 21.3l-25.8-14.9c-2.9-1.7-6.7-1.1-9 1.4-15 16.2-26.5 35.8-33.2 57.4-1 3.3.4 6.8 3.3 8.5l25.8 14.9c-2.6 14.1-2.6 28.5 0 42.6l-25.8 14.9c-3 1.7-4.3 5.2-3.3 8.5 6.7 21.6 18.2 41.1 33.2 57.4 2.3 2.5 6 3.1 9 1.4l25.8-14.9c10.9 9.3 23.4 16.5 36.9 21.3v29.8c0 3.4 2.4 6.4 5.7 7.1 22.3 5 45 4.8 66.2 0 3.3-.7 5.7-3.7 5.7-7.1v-29.8c13.5-4.8 26-12 36.9-21.3l25.8 14.9c2.9 1.7 6.7 1.1 9-1.4 15-16.2 26.5-35.8 33.2-57.4 1-3.3-.4-6.8-3.3-8.5l-25.8-14.9zM496 400.5c-26.8 0-48.5-21.8-48.5-48.5s21.8-48.5 48.5-48.5 48.5 21.8 48.5 48.5-21.7 48.5-48.5 48.5zM224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm201.2 226.5c-2.3-1.2-4.6-2.6-6.8-3.9l-7.9 4.6c-6 3.4-12.8 5.3-19.6 5.3-10.9 0-21.4-4.6-28.9-12.6-18.3-19.8-32.3-43.9-40.2-69.6-5.5-17.7 1.9-36.4 17.9-45.7l7.9-4.6c-.1-2.6-.1-5.2 0-7.8l-7.9-4.6c-16-9.2-23.4-28-17.9-45.7.9-2.9 2.2-5.8 3.2-8.7-3.8-.3-7.5-1.2-11.4-1.2h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c10.1 0 19.5-3.2 27.2-8.5-1.2-3.8-2-7.7-2-11.8v-9.2z"></path>
            </svg>
        </a>
        <?php endif; ?>


        <!-- Logout -->
        <a href="<?php echo esc_url($urls['logout']); ?>" title="Salir" aria-label="Salir">
            <svg viewBox="0 0 512 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M497 273L329 441c-15 15-41 4.5-41-17v-96H152c-13.3 0-24-10.7-24-24v-96c0-13.3 10.7-24 24-24h136V88c0-21.4 25.9-32 41-17l168 168c9.3 9.4 9.3 24.6 0 34zM192 436v-40c0-6.6-5.4-12-12-12H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h84c6.6 0 12-5.4 12-12V76c0-6.6-5.4-12-12-12H96c-53 0-96 43-96 96v192c0 53 43 96 96 96h84c6.6 0 12-5.4 12-12z"></path></svg>
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
