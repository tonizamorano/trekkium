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

    body { 
        font-family: inherit; 
        margin:0; 
        padding:0; 
        background: #f5f5f5; 
    }

    .mail-container { 
        max-width:600px; 
        margin:0 auto; 
        background: #cedde8; /* Azul tema 20% */
    }

    .mail-header { 
        background: #0b568b; /* Azul tema 100% */
        padding:20px; 
        text-align:center; 
    }

    .mail-header img { 
        max-width:200px; 
    }

    .mail-content { 
        padding:20px; 
        color:#333; 
    }

    .mail-button { 
        display:inline-block; 
        padding:12px 20px; 
        background:#E67E22; 
        color:#fff; 
        text-decoration:none; 
        border-radius:50px; 
        margin:10px 0; 
    }

    .mail-footer { 
        background: #0b568b;  /* Azul tema 100% */
        padding: 15px; 
        font-size: 14px; 
        color: #fff; 
        text-align:center; 
    }

    .mail-footer img { 
        max-width:150px; 
    }

    .mail-footer-copy { 
        color:#fff; 
    }

    .mail-footer-contacto {
        color: #fff !important;
        text-align: center;
        margin-top: 10px;
        text-decoration: none;
       
    }

    .mail-footer-info {
        text-align: center;
        line-height: 1.2;
        margin-top: 10px;
    }
    
    hr { 
        border:none; 
        border-top:1px solid #ddd; 
        margin:20px 0; 
    }

    /* Reset enlaces automáticos (iOS, Gmail, etc.) */
    a {
        
        text-decoration: none !important;
    }

    /* iOS Mail */
    a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }

    /* Gmail */
    u + #body a {
        color: inherit !important;
        text-decoration: none !important;
    }

    /* Outlook */
    span.MsoHyperlink {
        color: inherit !important;
        text-decoration: none !important;
    }

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
