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
        background:#f5f5f5; 
    }

    .container { 
        max-width:600px; 
        margin:0 auto; 
        background:var(--azul1-20); 
    }

    .header { 
        background:var(--azul1-100);
        padding:20px; 
        text-align:center; 
    }

    .header img { 
        max-width:200px; 
    }

    .content { 
        padding:20px; 
        color:#333; 
    }

    .button { 
        display:inline-block; 
        padding:12px 20px; 
        background:#E67E22; 
        color:#fff; 
        text-decoration:none; 
        border-radius:50px; 
        margin:10px 0; 
    }

    .footer { 
        background: var(--azul1-100); 
        padding:15px; 
        font-size:12px; 
        color:#666; 
        text-align:center; 
    }
    
    hr { 
        border:none; 
        border-top:1px solid #ddd; 
        margin:20px 0; 
    }

</style>
</head>
<body>

<div class="container">

    <!-- CABECERA FIJA -->
    <div class="header">
        <img src="https://staging2.trekkium.com/wp-content/uploads/2025/09/trekkium_logowhite.png" alt="Trekkium">
    </div>

    <!-- CONTENIDO VARIABLE -->
    <div class="content">
        <?php echo wp_kses_post( $email_content ); ?>
    </div>

    <!-- PIE DE PÁGINA FIJO -->
    <div class="footer">
        Trekkium · Actividades guiadas de montaña<br>
        🌐 www.trekkium.com · ✉️ hola@trekkium.com<br>
        Has recibido este correo porque has realizado una acción en Trekkium. Este email es informativo, por favor no respondas.
    </div>

</div>
</body>
</html>
