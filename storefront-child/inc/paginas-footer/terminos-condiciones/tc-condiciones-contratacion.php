<?php
function tc_condiciones_contratacion_shortcode() {
    ob_start();
    ?>
    <div class="tc-contenedor">

        <!-- Contenido Condiciones de Contratación -->
        <div class="tc-contenido">

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">1. Identificación del titular</span>
                </div>
                <div class="tc-respuesta">
                    <p>En cumplimiento de la Ley 34/2002, de Servicios de la Sociedad de la Información y Comercio Electrónico (LSSI-CE), se informa que el presente sitio web www.trekkium.com es titularidad de:</p>
                    <strong>Titular:</strong> Antonio Zamorano Torres<br>
                    <strong>Nombre comercial:</strong> Trekkium<br>
                    <strong>NIF:</strong> 46789781-F<br>
                    <strong>Domicilio fiscal:</strong> Av. Montserrat, 47 – 08820 El Prat de Llobregat (Barcelona)<br>
                    <strong>Correo electrónico:</strong> tonizt@gmail.com<br>
                    <strong>Teléfono:</strong> +34 711 200 697<br>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">2. Objeto de las condiciones</span>
                </div>
                <div class="tc-respuesta">
                    <p>Las presentes condiciones regulan el proceso de contratación y reserva de actividades de montaña y naturaleza ofrecidas a través del sitio web www.trekkium.com (en adelante, Trekkium), así como los derechos y obligaciones de las partes implicadas:</p>
                    <ul class="tc-list">
                        <li>El cliente o participante, que realiza la reserva, y</li>
                        <li>Trekkium, como entidad organizadora y responsable de la gestión de pagos, seguros y comunicación con el guía colaborador.</li>
                    </ul>
                    <p>Al realizar una reserva, el cliente declara haber leído y aceptado expresamente estas condiciones.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">3. Actividades y guías colaboradores</span>
                </div>
                <div class="tc-respuesta">
                    <p>Trekkium ofrece actividades de montaña guiadas por profesionales titulados. Cada actividad publicada incluye información sobre su modalidad, dificultad, fecha, precio, guía responsable y condiciones específicas.</p>
                    <p>Los guías colaboradores actúan bajo acuerdo con Trekkium, pero es Trekkium quien gestiona las reservas, cobros del anticipo y seguros de accidentes incluidos en la actividad.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">4. Proceso de reserva</span>
                </div>
                <div class="tc-respuesta">
                    <p>El cliente selecciona una actividad disponible en la web y pulsa el botón “Reservar”.</p>
                    <p>Se muestra el importe total y la cantidad correspondiente a la reserva online.</p>
                    <p>El pago de la reserva se realiza a través de Stripe, PayPal o tarjeta bancaria mediante conexión segura.</p>
                    <p>Una vez confirmado el pago, el cliente recibe un correo de confirmación con los datos de la actividad, guía y punto de encuentro.</p>
                    <p>El importe restante se abonará directamente al guía antes o el mismo día de la actividad, por los medios acordados (efectivo, Bizum o transferencia).</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">5. Precio e impuestos</span>
                </div>
                <div class="tc-respuesta">
                    <p>Todos los precios mostrados en la web incluyen impuestos aplicables (IVA) y el seguro de accidentes de montaña para la actividad contratada.</p>
                    <p>Cualquier gasto adicional (alojamiento, transporte no incluido, comidas, etc.) se especificará claramente en la descripción de la actividad.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">6. Política de cancelación y modificaciones</span>
                </div>
                <div class="tc-respuesta">
                    <p><strong>a) Cancelación por parte del cliente</strong></p>
                    <ul class="tc-list">
                        <li>En actividades de un solo día, la cancelación es gratuita hasta 48 horas antes de la fecha de realización.</li>
                        <li>Si la cancelación se realiza con menos de 48 horas, la reserva (33,33 %) no será reembolsable, salvo causa justificada de fuerza mayor.</li>
                        <li>Para cancelar una reserva, el cliente debe escribir a tonizt@gmail.com indicando su nombre, actividad y fecha.</li>
                    </ul>
                    <p><strong>b) Cancelación o modificación por parte del guía o Trekkium</strong></p>
                    <p>Trekkium se reserva el derecho de cancelar o modificar una actividad en caso de:</p>
                    <ul class="tc-list">
                        <li>Condiciones meteorológicas adversas.</li>
                        <li>Falta del número mínimo de participantes.</li>
                        <li>Causas de fuerza mayor o imprevistos de seguridad.</li>
                    </ul>
                    <p>En tal caso, el cliente podrá:</p>
                    <ul class="tc-list">
                        <li>Cambiar la reserva a otra fecha o actividad, o</li>
                        <li>Solicitar el reembolso íntegro del importe abonado.</li>
                    </ul>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">7. Seguro de accidentes</span>
                </div>
                <div class="tc-respuesta">
                    <p>Todas las actividades contratadas a través de Trekkium incluyen en el precio un seguro de accidentes de montaña que cubre al participante durante la realización de la actividad.</p>
                    <p>Los detalles del seguro (compañía aseguradora, coberturas y límites) se facilitan al cliente al confirmar la reserva.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">8. Condiciones de participación</span>
                </div>
                <div class="tc-respuesta">
                    <p>Para participar en las actividades, el cliente debe:</p>
                    <ul class="tc-list">
                        <li>Tener la edad mínima indicada en la ficha de la actividad.</li>
                        <li>Estar en condiciones físicas y de salud adecuadas para la actividad contratada.</li>
                        <li>Seguir en todo momento las instrucciones del guía responsable.</li>
                        <li>Utilizar el material personal o de seguridad obligatorio si así se requiere.</li>
                    </ul>
                    <p>Trekkium y los guías podrán excluir de la actividad, sin derecho a reembolso, a cualquier persona cuyo comportamiento ponga en riesgo la seguridad del grupo o incumpla las normas de participación.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">9. Responsabilidad</span>
                </div>
                <div class="tc-respuesta">
                    <p>Trekkium no se hace responsable de los perjuicios derivados del mal uso del sitio web, errores en los datos introducidos por el cliente o incumplimiento de las indicaciones de seguridad por parte de los participantes.</p>
                    <p>El cliente asume los riesgos inherentes a la práctica de actividades en el medio natural y declara aceptar las condiciones específicas de cada actividad.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">10. Derecho de desistimiento</span>
                </div>
                <div class="tc-respuesta">
                    <p>De conformidad con el artículo 103.l del Real Decreto Legislativo 1/2007, el derecho de desistimiento no será aplicable a los servicios relacionados con actividades de ocio o recreativas con fecha o periodo de ejecución específicos.</p>
                    <p>No obstante, Trekkium ofrece la cancelación gratuita hasta 48 horas antes, tal y como se indica en el punto 6.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">11. Modificaciones y actualizaciones</span>
                </div>
                <div class="tc-respuesta">
                    <p>Trekkium puede modificar estas condiciones de contratación y reserva en cualquier momento. Las nuevas condiciones serán aplicables desde el momento de su publicación en la web.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">12. Legislación aplicable y jurisdicción</span>
                </div>
                <div class="tc-respuesta">
                    <p>Estas condiciones se rigen por la legislación española. Para cualquier controversia, las partes se someten a los Juzgados y Tribunales de Barcelona, salvo que la normativa disponga lo contrario.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">13. Contacto</span>
                </div>
                <div class="tc-respuesta">
                    <p>Para cualquier consulta, modificación o cancelación relacionada con tu reserva: <strong>hola@trekkium.com</strong></p>
                </div>
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('tc_condiciones_contratacion', 'tc_condiciones_contratacion_shortcode');
