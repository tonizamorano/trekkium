<?php
function tc_politica_privacidad_shortcode() {
    ob_start();
    ?>
    <div class="tc-contenedor">

        <!-- Contenido Política de Privacidad -->
        <div class="tc-contenido">

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">1. Identidad del responsable del tratamiento</span>
                </div>
                <div class="tc-respuesta">
                    <p>En cumplimiento del Reglamento (UE) 2016/679 del Parlamento Europeo y del Consejo (RGPD), así como de la Ley Orgánica 3/2018 de Protección de Datos Personales y garantía de los derechos digitales (LOPDGDD), se informa de los siguientes datos del responsable del tratamiento:</p>
                    <p><strong>Responsable:</strong> Antonio Zamorano Torres<br>
                    <strong>Nombre comercial:</strong> Trekkium<br>
                    <strong>NIF:</strong> 46789781-F<br>
                    <strong>Domicilio:</strong> Av. Montserrat, 47 – 08820 El Prat de Llobregat (Barcelona)<br>
                    <strong>Correo electrónico:</strong> hola@trekkium.com<br>
                    <strong>Teléfono:</strong> +34 711 200 697<br>
                    <strong>Dominio:</strong> www.trekkium.com</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">2. Finalidad del tratamiento de los datos personales</span>
                </div>
                <div class="tc-respuesta">
                    <p>En Trekkium tratamos la información que los usuarios nos facilitan a través del sitio web con las siguientes finalidades:</p>
                    <ol class="tc-list">
                        <li><strong>Gestión de reservas de actividades de montaña:</strong>
                            <ul class="tc-list">
                                <li>Tramitar la reserva realizada en el sitio web.</li>
                                <li>Gestionar el cobro correspondiente a la reserva.</li>
                                <li>Comunicar los datos del participante al guía responsable de la actividad y tramitar el seguro de accidentes correspondiente.</li>
                            </ul>
                        </li>
                        <li><strong>Gestión de usuarios registrados:</strong>
                            <ul class="tc-list">
                                <li>Permitir el acceso al área privada “Mi cuenta”.</li>
                                <li>Facilitar el historial de reservas, pagos y datos personales.</li>
                            </ul>
                        </li>
                        <li><strong>Gestión de guías colaboradores:</strong>
                            <ul class="tc-list">
                                <li>Recopilar, verificar y publicar la información profesional de los guías (nombre, titulación, idioma, imagen y descripción).</li>
                                <li>Gestionar pagos, comisiones y comunicaciones internas.</li>
                            </ul>
                        </li>
                        <li><strong>Envío de información comercial (solo si el usuario lo autoriza):</strong>
                            <ul class="tc-list">
                                <li>Enviar comunicaciones sobre actividades, novedades o promociones mediante herramientas como MailPoet o FluentCRM.</li>
                                <li>El usuario puede darse de baja en cualquier momento.</li>
                            </ul>
                        </li>
                        <li><strong>Mejora del servicio:</strong>
                            <ul class="tc-list">
                                <li>Analizar el uso del sitio web mediante cookies y herramientas analíticas (Google Analytics, etc.) para mejorar la experiencia de usuario.</li>
                            </ul>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">3. Legitimación para el tratamiento de los datos</span>
                </div>
                <div class="tc-respuesta">
                    <p>La base legal para el tratamiento de los datos personales es:</p>
                    <ul class="tc-list">
                        <li>Gestión de reservas y servicios solicitados: para tramitar las reservas realizadas a través del sitio web y gestionar la relación con los guías colaboradores.</li>
                        <li>Consentimiento del usuario: para el envío de comunicaciones informativas, comerciales o boletines, siempre que se haya otorgado consentimiento expreso.</li>
                        <li>Interés legítimo del responsable: para garantizar la seguridad del sitio web y mejorar la experiencia de navegación y los servicios ofrecidos.</li>
                        <li>Cumplimiento de obligaciones legales: en relación con la gestión fiscal, contable y administrativa derivada de las reservas.</li>
                    </ul>
                </div>
            </div>


            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">4. Plazo de conservación de los datos</span>
                </div>
                <div class="tc-respuesta">
                    <p>Los datos personales se conservarán durante el tiempo necesario para cumplir con la finalidad para la que se recabaron:</p>
                    <ul class="tc-list">
                        <li>Datos de reservas: mientras dure la relación contractual y durante los plazos legales de responsabilidad (generalmente, 5 años).</li>
                        <li>Datos de guías colaboradores: mientras dure la colaboración y durante los plazos exigidos por ley.</li>
                        <li>Datos para comunicaciones comerciales: hasta que el usuario revoque su consentimiento o elimine su cuenta de usuario.</li>
                        <li>Datos recogidos mediante cookies: según el periodo establecido en la política de cookies.</li>
                    </ul>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">5. Comunicación de datos a terceros</span>
                </div>
                <div class="tc-respuesta">
                    <p>Los datos personales podrán ser comunicados a los siguientes destinatarios:</p>
                    <ul class="tc-list">
                        <li>Guías responsables de la actividad contratada, para gestionar asistencia y seguridad del cliente.</li>
                        <li>Compañías aseguradoras, para la tramitación del seguro de accidentes incluido en la reserva.</li>
                        <li>Entidades financieras (Stripe, etc.), para la gestión de cobros y pagos.</li>
                        <li>Proveedores tecnológicos necesarios para el funcionamiento de la web (SiteGround, Google Analytics, WooCommerce, etc.).</li>
                    </ul>
                    <p>Todos los proveedores de servicios externos cumplen con la normativa europea de protección de datos (RGPD). Trekkium no cede datos personales a terceros fuera del Espacio Económico Europeo sin las garantías adecuadas.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">6. Derechos de los interesados</span>
                </div>
                <div class="tc-respuesta">
                    <p>Cualquier usuario tiene derecho a:</p>
                    <ul class="tc-list">
                        <li>Acceder a sus datos personales.</li>
                        <li>Rectificar los datos inexactos.</li>
                        <li>Solicitar su supresión cuando los datos ya no sean necesarios.</li>
                        <li>Oponerse al tratamiento o solicitar la limitación del mismo.</li>
                        <li>Portar sus datos a otro responsable, cuando sea técnicamente posible.</li>
                    </ul>
                    <p>Si considera que sus derechos no han sido atendidos, puede presentar una reclamación ante la Agencia Española de Protección de Datos (AEPD): <a href="https://www.aepd.es" target="_blank">www.aepd.es</a></p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">7. Seguridad de los datos</span>
                </div>
                <div class="tc-respuesta">
                    <p>Trekkium aplica medidas técnicas y organizativas adecuadas para garantizar la confidencialidad, integridad y disponibilidad de los datos personales, incluyendo:</p>
                    <ul class="tc-list">
                        <li>Conexión segura HTTPS/SSL.</li>
                        <li>Copias de seguridad cifradas.</li>
                        <li>Control de acceso al área privada.</li>
                        <li>Contraseñas seguras y doble autenticación (cuando aplica).</li>
                    </ul>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">8. Datos personales de terceros</span>
                </div>
                <div class="tc-respuesta">
                    <p>El usuario garantiza que los datos personales facilitados a través del sitio web son veraces y se responsabiliza de comunicar cualquier modificación. Si facilita datos de terceros (por ejemplo, acompañantes en una actividad), declara haber obtenido su consentimiento previo.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">9. Tratamiento de datos de guías colaboradores</span>
                </div>
                <div class="tc-respuesta">
                    <p>Trekkium tratará los datos de los guías colaboradores con las siguientes finalidades:</p>
                    <ul class="tc-list">
                        <li>Gestionar la relación contractual y económica.</li>
                        <li>Publicar su perfil profesional y actividades en la web.</li>
                        <li>Cumplir con las obligaciones fiscales, laborales o de seguridad.</li>
                    </ul>
                    <p>Los datos se conservarán mientras dure la colaboración y durante los plazos exigidos por ley. Los guías pueden ejercer sus derechos de acceso, rectificación, supresión, etc., mediante los mismos canales descritos en el punto 6.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">10. Enlaces a sitios de terceros</span>
                </div>
                <div class="tc-respuesta">
                    <p>El sitio web puede contener enlaces a sitios web de terceros. Trekkium no se hace responsable de las políticas de privacidad de dichos sitios. Se recomienda leer las políticas correspondientes antes de facilitar datos personales.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">11. Modificaciones de la política de privacidad</span>
                </div>
                <div class="tc-respuesta">
                    <p>Trekkium se reserva el derecho de modificar esta política para adaptarla a novedades legislativas o cambios en sus servicios. En caso de cambios sustanciales, se informará a los usuarios mediante aviso en la web o por correo electrónico.</p>
                </div>
            </div>

        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('tc_politica_privacidad', 'tc_politica_privacidad_shortcode');
