<?php
function tc_politica_cookies_shortcode() {
    ob_start();
    ?>
    <div class="tc-contenedor">

        <div class="tc-contenido">

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">1. ¿Qué son las cookies?</span>
                </div>
                <div class="tc-respuesta">
                    <p>Las cookies son pequeños archivos de texto que se guardan en tu navegador cuando visitas una web. Sirven para recordar tus preferencias, mejorar el funcionamiento del sitio y ofrecerte una experiencia más personalizada. En ningún caso las cookies almacenan información sensible o que te identifique directamente.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">2. ¿Quién utiliza las cookies?</span>
                </div>
                <div class="tc-respuesta">
                    <p>El sitio web <strong>www.trekkium.com</strong>, titularidad de <strong>Antonio Zamorano Torres (NIF 46789781F)</strong>, utiliza cookies propias y de terceros para las finalidades que se detallan a continuación.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">3. Tipos de cookies que utilizamos</span>
                </div>
                <div class="tc-respuesta">
                    <p>La base legal para el tratamiento de los datos personales es:</p>
                    <ul class="tc-list">
                        <li><strong>Cookies técnicas o necesarias:</strong> Permiten el correcto funcionamiento de la web. Incluyen cookies de WordPress, WooCommerce y Stripe necesarias para gestionar las reservas, las sesiones de usuario y los pagos seguros.<br>
                        Estas cookies se instalan siempre porque son imprescindibles.</li>
                        <li><strong>Cookies analíticas o estadísticas:</strong> Estas cookies nos permiten conocer el origen de las visitas, las páginas visitadas o el tipo de navegador, con el objetivo de mejorar la comunicación y los formularios del sitio.<br>
                        Actualmente Trekkium utiliza cookies analíticas de primera parte basadas en Sourcebuster JS, que no identifican personalmente al usuario.<br>
                        Estas cookies solo se instalan si el usuario presta su consentimiento expreso desde el banner de cookies.
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">4. Gestión y eliminación de cookies</span>
                </div>
                <div class="tc-respuesta">
                    <p>Puedes aceptar o rechazar las cookies no esenciales desde el banner que aparece al entrar en la web. En cualquier momento puedes modificar tu decisión o eliminar las cookies desde la configuración de tu navegador:</p>
                    <ul class="tc-list">
                        <li><strong>Chrome:</strong> Eliminar, permitir y administrar cookies.</li>
                        <li><strong>Firefox:</strong> Protección contra rastreo y cookies.</li>
                        <li><strong>Safari:</strong> Gestionar cookies y datos de sitios web.</li>
                        <li><strong>Edge:</strong> Eliminar cookies en Microsoft Edge.</li>
                    </ul>
                    <p>Ten en cuenta que desactivar o eliminar cookies técnicas puede afectar al funcionamiento normal de la web o impedir completar reservas.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">5. Actualización de la política de cookies</span>
                </div>
                <div class="tc-respuesta">
                    <p>Trekkium puede modificar esta Política de Cookies para adaptarla a cambios técnicos o legales. Te recomendamos revisarla periódicamente para mantenerte informado sobre cómo y por qué utilizamos las cookies.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">6. Contacto</span>
                </div>
                <div class="tc-respuesta">
                    <p>Si tienes cualquier duda o deseas ejercer tus derechos en materia de protección de datos, puedes escribirnos a: <strong>hola@trekkium.com</strong></p>
                </div>
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('tc_politica_cookies', 'tc_politica_cookies_shortcode');
