<?php
/**
 * Sección Card Mi Cuenta - Shortcode para mostrar editor de avatar en myaccount.php
 */

// === Registrar y crear el shortcode ===
function seccion_card_micuenta_shortcode() {
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
    
    <div class="card-micuenta-contenedor-principal">

        <!-- Contenedor para la imagen de fondo -->
        <div class="card-micuenta-imagen-fondo">
            <img src="https://trekkium.com/wp-content/uploads/2025/11/240_F_879251769_zLoZJwcIRRtshYRAFNirocDdQ9zJxppF.jpg" alt="Fondo Trekkium">
        </div>

        <!-- Editar avatar -->
        <div class="card-micuenta-editar-avatar">
            <?php echo do_shortcode('[editar-avatar-usuario]'); ?>
        </div>

        <!-- Nombre del usuario logueado -->
        <div class="card-micuenta-nombre-usuario">
            <?php echo esc_html($nombre_usuario); ?>
        </div>

        <!-- Tipo de cuenta -->
        <div class="card-micuenta-tipo-cuenta">
            <div class="tipo_cuenta"><?php echo esc_html($role_text); ?></div>
        </div>

         <!-- Menú de Mi cuenta -->
        <div class="card-micuenta-menu-micuenta">
            <?php echo do_shortcode('[menu_mi_cuenta]'); ?>
        </div>

    </div>
    
    <?php
    return ob_get_clean();
}
add_shortcode('seccion_card_micuenta', 'seccion_card_micuenta_shortcode');