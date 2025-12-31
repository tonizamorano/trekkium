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
                    <strong>Correo electrónico:</strong> hola@trekkium.com<br>
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
                    <span class="tc-texto">3. Proceso de reserva</span>
                </div>
                <div class="tc-respuesta">
                    <p>Para realizar la reserva de una actividad en Trekkium es necesario acceder con una cuenta de usuario registrado.</p>

                    <p>Dentro de la información detallada de cada actividad existe un botón "Reservar" para ese fin, que conduce a la página de Finalizar Compra, donde se indica el método de pago, los detalles de la reserva y los datos de los acompañantes si los hubiera.</p>

                    <p>Una vez realizada la Reserva, el usuario recibe un correo electrónico de confirmación. También puede consultar el estado de su reserva desde el apartado "Mis reservas" de "Mi cuenta", y dispone de un plazo de hasta 24 horas antes del inicio de la actividad para cancelarla.</p>

                    <p>Durante ese plazo, el estado de la reserva permanecerá "Pendiente de pago", y no se realizará ningún cobro en tu tarjeta. Una vez finalizado ese plazo, la reserva ya no se podrá cancelar y la pasarela Stripe procederá al cobro del importe de la reserva en la tarjeta bancaria indicada.</p>

                    <p>Tras haberse realizado con éxito el cobro del importe de la reserva, ésta pasará a estado "Completado" y recibirás un correo electrónico de confirmación de Pago completado, por lo que podrás participar en la actividad.</p>

                    <p>En caso de que el cobro sea fallido (tarjeta errónea o sin saldo), la reserva pasará a estado "Cancelado" y quedará anulada, por lo que tendrás que volver a iniciar el proceso de reserva.</p>                   

                    <p>El importe pendiente del total del precio de la actividad se abonará directamente al guía al contado el mismo día de la actividad, por los medios acordados (efectivo, Bizum o transferencia inmediata).</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">4. Precio e impuestos</span>
                </div>
                <div class="tc-respuesta">
                    <p>Todos los precios mostrados en la web incluyen impuestos aplicables (IVA) y el seguro de accidentes de montaña para la actividad contratada.</p>
                    <p>Cualquier gasto adicional (alojamiento, material, transporte no incluido, comidas, etc.) se especificará claramente en la descripción de la actividad.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">5. Política de cancelación y modificaciones</span>
                </div>
                <div class="tc-respuesta">
                    <p><strong>a) Cancelación por parte del cliente</strong></p>
                    <ul class="tc-list">
                        <li>El cliente puede cancelar su reserva hasta 24 horas antes del inicio de la actividad de forma gratuita, desde el botón "Cancelar reserva" del panel de su cuenta de usuario.</li>
                        <li>Una vez finalizao ese plazo la reserva ya no se podrá cancelar.</li>
                    </ul>
                    <p><strong>b) Cancelación o modificación por parte del guía o Trekkium</strong></p>
                    <p>Trekkium se reserva el derecho de cancelar o modificar una actividad, en un plazo máximo de 24 horas antes del inicio de la actividad, en caso de:</p>
                    <ul class="tc-list">
                        <li>Condiciones meteorológicas adversas.</li>
                        <li>Falta del número mínimo de participantes.</li>
                        <li>Otras causas de fuerza mayor o imprevistos de seguridad.</li>
                    </ul>
                    <p>En tal caso, al no existir aún cobro por el importe de la reserva, ésta quedará Cancelada sin necesidad de reembolso.</p>
                    
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">6. Seguro de accidentes</span>
                </div>
                <div class="tc-respuesta">
                    <p>Todas las actividades contratadas a través de Trekkium incluyen en el precio un seguro de accidentes de montaña que cubre al participante durante la realización de la actividad.</p>
                    <p>Los detalles del seguro (compañía aseguradora, coberturas y límites) se facilitan al cliente al confirmar la reserva.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">7. Condiciones de participación</span>
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
                    <span class="tc-texto">8. Modificaciones y actualizaciones</span>
                </div>
                <div class="tc-respuesta">
                    <p>Trekkium puede modificar estas condiciones de contratación y reserva en cualquier momento. Las nuevas condiciones serán aplicables desde el momento de su publicación en la web.</p>
                </div>
            </div>

            <div class="tc-seccion">
                <div class="tc-pregunta">
                    <span class="tc-icon">></span>
                    <span class="tc-texto">9. Legislación aplicable y jurisdicción</span>
                </div>
                <div class="tc-respuesta">
                    <p>Estas condiciones se rigen por la legislación española. Para cualquier controversia, las partes se someten a los Juzgados y Tribunales de Barcelona, salvo que la normativa disponga lo contrario.</p>
                </div>
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('tc_condiciones_contratacion', 'tc_condiciones_contratacion_shortcode');
