<?php
/**
 * Sección Card Mi Cuenta - Shortcode para mostrar editor de avatar en myaccount.php
 */

// === Registrar y crear el shortcode ===
function mc_user_card_shortcode() {
    ob_start();
    $current_user = wp_get_current_user();
    $nombre_usuario = $current_user->display_name; // Nombre visible del usuario
    
    // Determinar el tipo de cuenta
    if (in_array('administrator', $current_user->roles)) {
        $role_text = 'Cuenta de administrador';
    } elseif (in_array('guia', $current_user->roles)) {
        $role_text = 'Cuenta de organizador';
    } elseif (in_array('customer', $current_user->roles)) {
        $role_text = 'Cuenta de cliente';
    } else {
        $role_text = 'Cuenta de usuario';
    }
    ?>
    
    <div class="mc-user-card-contenedor">

        <!-- Contenedor para la imagen de fondo -->
        <div class="mc-user-card-imagen-fondo">
            <img src="https://trekkium.com/wp-content/uploads/2025/11/240_F_879251769_zLoZJwcIRRtshYRAFNirocDdQ9zJxppF.jpg" alt="Fondo Trekkium">
        </div>

        <!-- Editar avatar -->
        <div class="mc-user-card-editar-avatar">
            <?php echo do_shortcode('[mc_uc_editar_avatar]'); ?>
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
        <div class="mc-user-card-menu-micuenta">
            <?php echo do_shortcode('[mc_uc_menu_principal]'); ?>
        </div>

    </div>
    
    <?php
    return ob_get_clean();
}
add_shortcode('mc_user_card', 'mc_user_card_shortcode');