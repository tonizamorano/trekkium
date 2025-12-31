<?php
function faq_reservas_shortcode() {
    ob_start();
    ?>
    <div class="faq-contenedor">

        <!-- Contenido FAQ -->
        <div class="faq-contenido">

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Cómo puedo reservar una actividad en Trekkium?</span>
                </div>
                <div class="faq-respuesta">
                    <ol class="faq-list">
                        <li>Regístrate o accede a tu cuenta en <strong>https://trekkium.com/acceso</strong>.</li>
                        <li>Busca tu actividad en <strong>https://trekkium.com/actividades</strong>.</li>
                        <li>Selecciona el número de plazas que deseas reservar y pulsa <strong>RESERVAR POR…</strong>.</li>
                        <li>Introduce los datos de los acompañantes, si reservas más de una plaza.</li>
                        <li>Introduce tus datos de pago.</li>
                        <li>Pulsa el botón <strong>FINALIZAR RESERVA</strong>.</li>
                        <li>Recibirás un correo electrónico de confirmación de tu reserva.</li>
                        <li>Desde tu panel de usuario, podrás consultar el estado de tu reserva o cancelarla si lo deseas.</li>
                    </ol>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Puedo reservar una actividad para otras personas que no estén registradas en Trekkium?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Sí, puedes reservar plazas para personas que no tengan cuenta en Trekkium, siempre que sean <strong>acompañantes de un usuario registrado</strong>.</p>
                    <p>Los datos de todos los acompañantes deben introducirse mediante un formulario antes de finalizar la reserva.</p>
                    <p>Una vez realizada la reserva, los datos de los acompañantes se pueden modificar desde el apartado <strong>Mis Reservas</strong> en tu panel de usuario.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Recibiré una confirmación de mi reserva?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Sí. Trekkium enviará una <strong>notificación de confirmación por correo electrónico</strong> tras completar tu reserva.</p>
                    <p>Además, podrás consultar en cualquier momento los datos y el estado de tu reserva desde tu <strong>panel de usuario</strong>.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Qué métodos de pago están disponibles?</span>
                </div>
                <div class="faq-respuesta">
                    <ul class="faq-list">
                        <li><strong>Pago de la reserva en Trekkium:</strong> mediante tarjeta de crédito o débito a través de la pasarela Stripe.</li>
                        <li><strong>Pago del importe pendiente al/la guía:</strong> puede realizarse en efectivo, Bizum o transferencia bancaria inmediata, según las indicaciones del/de la guía organizador/a.</li>
                    </ul>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Qué ocurre si no puedo asistir después de reservar?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Puedes cancelar tu reserva <strong>hasta 24 horas antes</strong> de la fecha y hora de la actividad desde el panel de usuario.</p>
                    <p>Una vez pasado ese plazo, la reserva no se puede cancelar y se cobrará el importe correspondiente a la reserva.</p>
                    <p>Si no puedes asistir el día de la actividad, no se cobrará ni se reclamará el importe pendiente que corresponda al/la guía.</p>
                    <p>Siempre que sea posible, informa al guía a través del grupo de WhatsApp de la actividad.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Es seguro pagar a través de Trekkium?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Sí, totalmente seguro. Trekkium utiliza <strong>Stripe como pasarela de pago</strong>, que incorpora las medidas de seguridad más estrictas para transacciones de comercio electrónico.</p>
                    <p>Trekkium <strong>no almacena</strong> en sus bases de datos ninguna información de tu tarjeta de crédito o débito.</p>
                    <p>Todos los datos relativos a los pagos son gestionados por Stripe, que cumple con los estándares más exigentes de seguridad y protección de datos, incluyendo la normativa <strong>PCI DSS</strong> y el <strong>Reglamento General de Protección de Datos (RGPD)</strong>.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿En qué estados puede estar mi reserva y qué significan?</span>
                </div>
                <div class="faq-respuesta">
                    <p>El estado de tu reserva indica en todo momento la situación de la misma:</p>
                    <ul class="faq-list">
                        <li><strong>Pendiente:</strong> tu reserva ha sido validada pero está pendiente de pago. El pago se realizará automáticamente durante las últimas 24 horas antes del inicio de la actividad.</li>
                        <li><strong>Completado:</strong> el pago se ha procesado correctamente y la reserva se ha completado satisfactoriamente.</li>
                        <li><strong>Cancelado:</strong> la reserva ha sido cancelada y no tiene validez.</li>
                    </ul>
                    <p>Puedes consultar el estado de tu reserva en cualquier momento desde la sección <strong>"Mis reservas"</strong> en tu panel de usuario.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Puedo transferir mi reserva a otra persona?</span>
                </div>
                <div class="faq-respuesta">
                    <p><strong>No, no es posible transferir una reserva a otra persona.</strong></p>
                    <p>Cada reserva está asociada a los datos del usuario registrado, necesarios para:</p>
                    <ul class="faq-list">
                        <li>Realizar las comunicaciones relacionadas con la actividad.</li>
                        <li>Validar las coberturas de los seguros.</li>
                        <li>Establecer el contacto con el/la guía organizador/a.</li>
                    </ul>
                    <p><strong>Alternativa recomendada:</strong> si otra persona desea asistir, lo más seguro es cancelar tu reserva y que esa persona realice una nueva reserva con sus propios datos de usuario. Esto garantiza que todas las gestiones y coberturas se realicen correctamente.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Cuándo se realiza el pago de la reserva en mi tarjeta?</span>
                </div>
                <div class="faq-respuesta">
                    <p>El pago se cargará en tu tarjeta <strong>durante las últimas 24 horas antes del inicio de la actividad</strong>.</p>
                    <p>Si cancelas tu reserva antes de las 24 horas previas al inicio de la actividad, el pago quedará cancelado y no se cargará ningún importe en tu tarjeta.</p>
                    <p>En ningún caso se realizará el cobro en tu tarjeta mientras el plazo de cancelación gratuita esté vigente.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Qué ocurre si mi tarjeta bancaria falla en el momento del cobro de la reserva?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Si tu tarjeta no está activa o funcional en el momento en que se realiza el pago (durante las últimas 24 horas antes del inicio de la actividad) —ya sea por extraviarla, robo, cancelación o cambio de tarjeta o entidad bancaria— o no dispone de saldo suficiente para cubrir el importe de la reserva, la pasarela Stripe no podrá procesar el pago.</p>
                    <p>En este caso, tu <strong>reserva quedará cancelada automáticamente</strong>.</p>
                </div>
            </div>           

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Cómo pago al guía el importe restante el día de la actividad?</span>
                </div>
                <div class="faq-respuesta">
                    <p>El importe pendiente se puede abonar al/de la guía organizador/a mediante:</p>
                    <ul class="faq-list">
                        <li><strong>Efectivo</strong></li>
                        <li><strong>Bizum</strong></li>
                        <li><strong>Transferencia bancaria inmediata</strong></li>
                    </ul>
                    <p>Cada guía comunicará al grupo, a través del grupo de WhatsApp de la actividad, sus preferencias de pago.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Puedo traer acompañantes que no tengan reserva?</span>
                </div>
                <div class="faq-respuesta">
                    <p><strong>No, ninguna persona puede participar en una actividad sin haber realizado una reserva previa</strong> y con el pago correspondiente completado.</p>
                    <p>Solo tras realizar la reserva correctamente a través de la plataforma, el/la participante queda cubierto/a por el <strong>Seguro de Accidentes de Montaña</strong>.</p>
                    <p>Los guías verifican antes del inicio de la actividad la lista de participantes con reserva completada, ningún usuario que no esté en la lista puede participar en la actividad.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Cómo puedo consultar el estado de mis reservas?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Puedes consultar todas tus reservas desde el apartado <strong>"Mis Reservas"</strong> en el panel de usuario. Allí podrás:</p>
                    <ul class="faq-list">
                        <li>Ver tus datos de reserva.</li>
                        <li>Consultar el estado de cada reserva.</li>
                        <li>Revisar la lista de participantes que también han reservado la actividad.</li>
                        <li>Cancelar tu reserva hasta 24 horas antes del inicio de la actividad.</li>
                    </ul>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Puedo reservar dos veces la misma actividad?</span>
                </div>
                <div class="faq-respuesta">
                    <p>No, no puedes reservar dos veces la misma actividad. Cada plaza disponible en las actividades solo puede ser ocupada por una única persona.</p>
                    <p>Sí es posible incluir a otros participantes en la misma reserva, adquiriendo varias plazas para una misma actividad.</p>
                    <p>Sí que puedes reservar a la vez tantas actividades como desees.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Puedo cancelar mi reserva sin penalización?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Sí, puedes cancelar tu reserva de forma gratuita <strong>hasta 24 horas antes del inicio de la actividad</strong>.</p>
                    <p>Solo tienes que acceder al apartado <strong>"Mis reservas"</strong> en tu panel de usuario y pulsar <strong>"Cancelar reserva"</strong>.</p>
                    <p>Una vez transcurrido ese plazo no se puede cancelar la reserva.</p>
                    <p>Trekkium cobrará el importe de la reserva en tu tarjeta únicamente después de que haya finalizado el plazo de cancelación gratuita.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Trekkium o los guías pueden cancelar una actividad?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Sí. Trekkium y los guías organizadores se reservan el derecho de cancelar una actividad en los siguientes casos:</p>
                    <ul class="faq-list">
                        <li>Cuando el número de reservas sea inferior al grupo mínimo requerido.</li>
                        <li>Cuando la previsión meteorológica sea desfavorable y no se haya alcanzado un acuerdo con los participantes para cambiar la fecha o la ubicación.</li>
                        <li>Cuando la persona guía no pueda asistir por accidente, enfermedad o causa de fuerza mayor que impida su presencia en la fecha y lugar previstos.</li>
                        <li>Por otras causas de fuerza mayor que impidan la realización de la actividad en condiciones óptimas de seguridad.</li>
                    </ul>
                    <p>En todos los casos, la cancelación será comunicada a los usuarios con reserva con un mínimo de 24 horas de antelación al inicio de la actividad. Las reservas se cancelarán automáticamente y no se realizará ningún cargo a los participantes.</p>
                    <p>En situaciones excepcionales no descritas, en las que la actividad no pueda llevarse a cabo pese a haberse efectuado el cobro de la reserva, Trekkium reembolsará íntegramente el importe correspondiente a los usuarios afectados.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Puede cancelar el/la guía una actividad durante su desarrollo?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Sí. Los/las guías pueden suspender o cancelar una actividad durante su desarrollo si se produce alguna circunstancia que impida continuar con la actividad de forma segura:</p>
                    <ul class="faq-list">
                        <li>Accidente o malestar suyo o de algún participante que requiera de una evacuación o rescate.</li>
                        <li>Cambio brusco en las condiciones meteorológicas.</li>
                        <li>Mal estado del terreno, por causas de avalanchas, desprendimientos y otro tipo de erosión.</li>
                        <li>Pérdida o extravío de alguno de los participantes.</li>
                        <li>Cualquier otra circunstancia imprevista no descrita que pueda ocurrir durante la actividad.</li>
                    </ul>
                </div>
            </div>


        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('faq_reservas', 'faq_reservas_shortcode');
?>