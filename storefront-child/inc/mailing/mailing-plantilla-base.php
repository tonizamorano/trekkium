<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plantilla base para emails de Trekkium
 * 
 * $email_title  → Título del email
 * $email_content → HTML del contenido específico del correo
 */

// Evitar que se cargue fuera de contexto
if ( empty( $email_content ) || empty( $email_title ) ) {
    return; // No hacer nada si no hay contenido
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?php echo esc_html($email_title); ?></title>

<style>
<?php
$styles_path = dirname(__FILE__) . '/mailing-estilos-css.php';
if ( file_exists( $styles_path ) ) {
    echo include $styles_path;
}
?>
</style>

</head>

<body>

<div class="mail-container">

    <!-- CABECERA FIJA -->
    <div class="mail-header">
        <img src="https://staging2.trekkium.com/wp-content/uploads/2025/09/trekkium_logowhite.png" alt="Trekkium">
    </div>

    <!-- CONTENIDO VARIABLE -->
    <div class="mail-content">
        <?php echo wp_kses_post( $email_content ); ?>
    </div>

    <!-- PIE DE PÁGINA FIJO -->
    <div class="mail-footer">

        <!-- Logo encima del menú -->
        <div class="mail-footer-logo">
            <img src="https://trekkium.com/wp-content/uploads/2025/09/trekkium_logowhite.png" alt="Trekkium logo" />
        </div>

        <!-- Línea de copyright -->
        <div class="mail-footer-copy">
            &copy; <?php echo date('Y'); ?> Trekkium. Todos los derechos reservados.
        </div>

        <div class="mail-footer-contacto">
           <span style="display:inline-block;margin-right:10px;">www.trekkium.com</span> 
           <span style="display:inline-block;">hola@trekkium.com</span>
        </div>

        <div class="mail-footer-info">
            <span>Has recibido este correo porque has realizado una acción en Trekkium. <br>
            Este email es informativo, por favor no respondas.</span>
        </div>
    </div>

</div>
</body>
</html>
