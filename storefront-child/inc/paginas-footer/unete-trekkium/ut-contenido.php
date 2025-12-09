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
                <p class="ut-subtitle">¿Te gustaría unirte a nuestro equipo?</p>
            </div>

            <div class="ut-description">
                <p>Únete a nuestra comunidad de guías de montaña y actividades outdoor profesionales, y disfruta de las ventajas de formar parte de <strong>Trekkium</strong>, el mayor portal de reservas de actividades de montaña profesionales.</p>
            </div>

            <div class="ut-columns">

                <div class="ut-column ut-ofrecemos">

                    <div class="ut-column-titulo">
                        <h3>Ventajas</h3>
                    </div>

                    <div class="ut-column-contenido">
                        <ul>
                            <li>Alta gratuita, sin costes de mantenimiento.</li>
                            <li>Gestión y cobro seguro de las reservas de tus actividades.</li>
                            <li>Anuncios profesionales de tus actividades.</li>
                            <li>Promoción y marketing de tus actividades.</li>
                            <li>Cobertura de seguros de accidentes para tus clientes.</li>
                            <li>Fácil gestión de tus actividades a través de <strong>Mi cuenta</strong>.</li>
                            <li>Lista detallada de participantes con reserva completada.</li>
                        </ul>
                    </div>

                </div>

                <div class="ut-column ut-requisitos">

                    <div class="ut-column-titulo">
                        <h3>Requisitos</h3>
                    </div>

                    <div class="ut-column-contenido">
                        <ul>
                            <li>Tener la nacionalidad española o permiso de trabajo y residencia en España.</li>
                            <li>Disponer de una titulación oficial válida para ejercer como guía profesional en España.</li>
                            <li>Estar dado de alta en los registros obligatorios correspondientes a la Comunidad Autónoma de residencia.</li>
                            <li>Disponer de Seguro Obligatorio de Responsabilidad Civil.</li>
                        </ul>                
                    </div>

                </div>

            </div>

         </div>

    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('ut_contenido', 'ut_contenido_shortcode');
