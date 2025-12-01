<?php
add_shortcode('menu_principal', function () {
    ob_start(); ?>
    
    <nav class="menu-principal">

        <!-- Botón Actividades -->
        <a href="<?php echo esc_url(home_url('/actividades/')); ?>" 
           class="<?php echo (is_shop() || is_product() || is_page('actividades') || is_singular('actividad')) ? 'active' : ''; ?>"
           title="Actividades">
        
           <?php echo trekkium_icon_actividades2('', 'Actividades'); ?>            

        </a>

        <!-- Botón Guías -->
        <a href="<?php echo esc_url(home_url('/guias/')); ?>" 
           class="<?php echo (is_page('guias') || is_singular('guia') || is_author()) ? 'active' : ''; ?>" 
           title="Guías">
            <?php echo trekkium_icon_guias('', 'Guías'); ?> 
        </a>

        <!-- Botón Blog -->
        <a href="<?php echo esc_url(home_url('/blog/')); ?>" 
           class="<?php echo ((is_home() || is_page('blog') || is_singular('post')) ? 'active' : ''); ?>"
           title="Blog">
            <?php echo trekkium_icon_blog('', 'Blog'); ?>
        </a>

        <!-- Botón Mi cuenta / Acceso -->
        <?php 
        if (is_user_logged_in()) {
            $account_url = home_url('/mi-cuenta/');
            $is_active = (is_account_page() || is_page(['editar-cuenta','mis-actividades','editar-actividad','detalles-actividad','nueva-actividad'])); ?>
            
            <a href="<?php echo esc_url($account_url); ?>" 
               class="icono-avatar logged-in <?php echo $is_active ? 'active' : ''; ?>" 
               title="Mi cuenta">

                <?php echo trekkium_icon_user_avatar('', 'Mi Cuenta'); ?>

            </a>

        <?php } else { 
            $login_url = home_url('/acceso/');
            $is_active = is_page('acceso'); ?>

            <a href="<?php echo esc_url($login_url); ?>" 
               class="icono-avatar no-user <?php echo $is_active ? 'active' : ''; ?>" 
               title="Acceso">

                <?php echo trekkium_icon_acceso('', 'Acceso'); ?>

            </a>
        <?php } ?>

        <!-- Botón Admin Dashboard -->
        <?php if(current_user_can('administrator')): ?>
        <a href="<?php echo esc_url(home_url('/admin-dashboard/')); ?>"
           class="admin-dashboard-button <?php echo (is_page('admin-dashboard') || strpos($_SERVER['REQUEST_URI'], '/admin-dashboard/') !== false) ? 'active' : ''; ?>"
           title="Admin Dashboard">

            <?php echo trekkium_icon_estrella('', 'Admin'); ?>

        </a>
        <?php endif; ?>
		
    </nav>

    <?php return ob_get_clean();
});
