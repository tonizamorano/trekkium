<?php

// Shortcode: [ut_contenido]
function ut_contenido_shortcode() {
    ob_start();
    ?>

    <section class="ut-contenedor">

        <div class="ut-image">
            <img src="https://staging2.trekkium.com/wp-content/uploads/2025/12/unete-a-trekkium.jpg" alt="Guías de montaña Trekkium">
        </div>

        <div class="ut-contenido">            
            
            <div class="ut-titular"> 
                <h2>¿Eres guía de montaña profesional?</h2>
                <p class="ut-subtitle">¿Te gustaría planificar, programar y liderar tus propias actividades?</p>
            </div>

            <div class="ut-description">
                <p>Únete a <strong>Trekkium</strong> y dirige tu propio proyecto profesional, sin depender de agencias y con total autonomía sobre tus actividades.</p>
            </div>

            <div class="ut-column-contenido">
                <ul>
                    <li>Alta gratuita en la plataforma, sin costes de mantenimiento.</li>
                    <li>Fácil gestión de tus actividades a través de <strong>Mi cuenta</strong>.</li>
                    <li>Seguros de accidentes de montaña incluídos.</li>
                    <li>Promoción y marketing de tus actividades.</li>                  
                    <li>Lista detallada de participantes con reserva completada.</li>
                </ul>
            </div>

         </div>

    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('ut_contenido', 'ut_contenido_shortcode');
