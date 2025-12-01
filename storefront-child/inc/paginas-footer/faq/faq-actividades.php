<?php
function faq_actividades_shortcode() {
    ob_start();
    ?>
    <div class="faq-contenedor">

        

        <!-- Contenido FAQ -->
        <div class="faq-contenido">

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Quién organiza las actividades anunciadas en Trekkium?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Las actividades publicadas en Trekkium son organizadas exclusivamente por guías acreditados y registrados en la plataforma.</p>
                    <p>Antes de poder publicar, cada guía debe:</p>
                    <ul class="faq-list">
                        <li>Acreditar su titulación oficial</li>
                        <li>Presentar la documentación legal requerida para ejercer como guía de montaña profesional</li>
                        <li>Aceptar las condiciones de colaboración con Trekkium</li>
                    </ul>
                    <p>Este proceso garantiza que todas las actividades están dirigidas por profesionales cualificados, con la formación y experiencia necesarias para ofrecer una experiencia segura y de calidad, siguiendo los estándares establecidos por la plataforma.</p>
                </div>
            </div>
            
            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Qué tipos de actividades puedo encontrar en Trekkium?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Actualmente, Trekkium ofrece cursos y actividades de un día de duración en distintas modalidades de deportes de montaña:</p>
                    <ul class="faq-list">
                        <li>Senderismo</li>
                        <li>Trekking</li>
                        <li>Alpinismo</li>
                        <li>Escalada</li>
                        <li>Raquetas de nieve</li>
                        <li>Vías ferratas</li>
                        <li>Descenso de barrancos</li>
                        <li>Esquí de montaña</li>
                    </ul>
                    <p>Todas las actividades están diseñadas y guiadas por profesionales acreditados, con el objetivo de garantizar una experiencia segura, formativa y adaptada a diferentes niveles.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Dónde se realizan las actividades?</span>
                </div>
                <div class="faq-respuesta">
                    <p>La mayoría de las actividades publicadas en Trekkium se desarrollan dentro del territorio español.</p>
                    <p>No obstante, siempre que se cumplan los requisitos legales, de competencias profesionales y de coberturas de seguro correspondientes, los guías pueden ofrecer actividades en otros países, exclusivamente dentro de la Unión Europea y del Espacio Económico Europeo.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Hay una edad mínima o máxima para participar en las actividades?</span>
                </div>
                <div class="faq-respuesta">
                    <p>En cada actividad se indica la edad mínima requerida para poder participar.</p>
                    <p>No existe una edad máxima establecida, siempre que la persona:</p>
                    <ul class="faq-list">
                        <li>Se encuentre en buen estado de salud</li>
                        <li>Tenga una condición física adecuada</li>
                        <li>Cuente con la experiencia necesaria para realizar la actividad con seguridad</li>
                    </ul>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Qué información se muestra en el anuncio de cada actividad?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Cada anuncio de actividad en Trekkium incluye información detallada para que los participantes puedan conocer todos los aspectos relevantes antes de realizar la reserva.</p>
                    <p>En las actividades de un día se muestran los siguientes datos:</p>
                    <ul class="faq-list">
                        <li>Título de la actividad</li>
                        <li>Región y provincia donde se desarrolla</li>
                        <li>Modalidad deportiva</li>
                        <li>Galería de imágenes</li>
                        <li>Descripción general de la actividad</li>
                        <li>Guía organizador, con enlace a su perfil profesional</li>
                        <li>Fecha, hora y lugar de encuentro</li>
                        <li>Ficha técnica con distancia, duración, desnivel positivo y negativo, y nivel de dificultad</li>
                        <li>Número mínimo y máximo de participantes</li>
                        <li>Disponibilidad de plazas (libres y reservadas)</li>
                        <li>Información adicional, como observaciones o detalles técnicos</li>
                        <li>Planificación horaria de la jornada</li>
                        <li>Lista de material y equipamiento necesarios</li>
                        <li>Servicios incluidos en el precio</li>
                        <li>Precio total de la actividad, con el importe correspondiente a la reserva y el importe a abonar al guía</li>
                    </ul>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Cómo se informa sobre el nivel de dificultad de las actividades?</span>
                </div>
                <div class="faq-respuesta">
                    <p>En el anuncio de cada actividad, dentro de la sección <strong>"Ficha técnica"</strong>, se indica el nivel de dificultad, que se relaciona directamente con la experiencia previa necesaria en la modalidad deportiva correspondiente.</p>
                    <p>Los niveles de dificultad utilizados en Trekkium son los siguientes:</p>
                    <ul class="faq-list">
                        <li><strong>Muy fácil:</strong> no se requiere experiencia previa</li>
                        <li><strong>Fácil:</strong> se requiere muy poca o ninguna experiencia</li>
                        <li><strong>Moderado:</strong> se requiere un nivel medio de experiencia</li>
                        <li><strong>Exigente:</strong> se requiere un nivel alto de experiencia</li>
                        <li><strong>Muy exigente:</strong> se requiere un nivel muy alto de experiencia</li>
                    </ul>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Es seguro que la actividad que he reservado se realizará?</span>
                </div>
                <div class="faq-respuesta">
                    <p>La realización de una actividad no está garantizada hasta que se haya alcanzado el <strong>número mínimo de participantes</strong> requerido.</p>
                    <p>En cada anuncio se indica el número máximo y mínimo de plazas, así como el estado actual de la actividad, incluyendo las plazas disponibles, las reservas confirmadas y el nivel de ocupación del grupo.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Por qué las actividades tienen un número máximo y mínimo de participantes?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Cada actividad publicada en Trekkium establece un número mínimo y máximo de participantes por razones de seguridad y viabilidad.</p>
                    <p>El <strong>número máximo</strong> se determina en función del ratio de seguridad, es decir, la cantidad máxima de personas que un guía puede conducir de forma segura y adecuada según la modalidad deportiva y el nivel de dificultad. Estos ratios pueden variar desde un máximo de 15 participantes en actividades de senderismo muy fácil, hasta un máximo de 2 personas en actividades de alpinismo o escalada muy exigente. Cada guía define el ratio correspondiente respetando los límites legales y normativos aplicables en el territorio donde se desarrolla la actividad.</p>
                    <p>El <strong>número mínimo</strong> se establece para garantizar la viabilidad económica de la actividad, ya que el guía asume unos costes fijos (como desplazamientos, dietas o material), además de su retribución profesional. Por este motivo, es necesario alcanzar un grupo mínimo que cubra los gastos básicos asociados a la organización.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Qué incluye el precio de las actividades?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Todas las actividades publicadas en Trekkium incluyen los siguientes conceptos:</p>
                    <ul class="faq-list">
                        <li>IVA (21%)</li>
                        <li>Seguro de Responsabilidad Civil</li>
                        <li>Seguro de Accidentes de Montaña</li>
                        <li>Planificación y organización de la actividad</li>
                        <li>Guía de montaña titulado durante el desarrollo de la actividad</li>
                    </ul>
                    <p>Cualquier otro concepto adicional estará detallado en el apartado <strong>"Incluye"</strong> del anuncio correspondiente.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Hay algún gasto aparte del precio de la actividad?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Las dietas y los desplazamientos no están incluidos en el precio de las actividades.</p>
                    <p>Algunas actividades pueden implicar gastos adicionales, como:</p>
                    <ul class="faq-list">
                        <li>Alquiler de material</li>
                        <li>Transfers</li>
                        <li>Telesillas o funiculares</li>
                    </ul>
                    <p>En todos los casos, cualquier gasto no incluido en el precio estará claramente indicado en el anuncio de la actividad, dentro del apartado correspondiente.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Quién puede estar dentro del grupo de Whatsapp de la actividad?</span>
                </div>
                <div class="faq-respuesta">
                    <p>El grupo de WhatsApp de cada actividad es creado y administrado por el guía responsable, quien se encarga de resolver dudas, facilitar información práctica y comunicar actualizaciones relacionadas con la actividad.</p>
                    <p>El equipo de Trekkium también está presente en los grupos, pero de forma no participativa. Su función es supervisar y testimoniar cualquier incidencia o comunicación relevante, con el fin de poder actuar adecuadamente si fuera necesario, sin intervenir en las conversaciones entre guía y participantes.</p>
                    <p>El resto de miembros del grupo está formado exclusivamente por las personas que han realizado una reserva y cuyo estado se encuentra en <strong>Pendiente</strong> o <strong>Completado</strong>.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Se requiere experiencia previa para participar en alguna actividad?</span>
                </div>
                <div class="faq-respuesta">
                    <p>El nivel de experiencia previa necesario depende directamente del grado de dificultad de la actividad.</p>
                    <p>Los requisitos orientativos para cada nivel son los siguientes:</p>
                    <ul class="faq-list">
                        <li><strong>Muy fácil:</strong> no se requiere experiencia previa</li>
                        <li><strong>Fácil:</strong> se requiere un grado bajo o nulo de experiencia</li>
                        <li><strong>Moderado:</strong> se requiere un nivel medio de experiencia</li>
                        <li><strong>Exigente:</strong> se requiere un nivel alto de experiencia</li>
                        <li><strong>Muy exigente:</strong> se requiere un nivel muy alto de experiencia</li>
                    </ul>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Qué debo llevar a cada actividad?</span>
                </div>
                <div class="faq-respuesta">
                    <p>En el anuncio de cada actividad se detalla la lista de material y equipamiento necesarios.</p>
                    <p>Es imprescindible presentarse con todo el material indicado, ya que esto garantiza la seguridad del grupo y el correcto desarrollo de la actividad.</p>
                    <p>Si alguna persona no dispone del material requerido, el guía responsable podrá decidir su no participación en la actividad por motivos de seguridad.</p>
                    <p>Ante cualquier duda sobre la lista de material, puedes consultar directamente con el guía a través del grupo de WhatsApp de la actividad.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Se permiten mascotas en las actividades?</span>
                </div>
                <div class="faq-respuesta">
                    <p>No, las mascotas no están permitidas en las actividades organizadas a través de Trekkium.</p>
                    <p>La presencia de animales puede:</p>
                    <ul class="faq-list">
                        <li>Generar incomodidad en otros participantes</li>
                        <li>Suponer riesgos para la seguridad o el correcto desarrollo de la actividad</li>
                        <li>Interferir con la fauna del entorno</li>
                    </ul>
                    <p>Además, algunos espacios naturales y parques protegidos prohíben expresamente la entrada de mascotas, por lo que su participación no está autorizada en ningún caso.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Cómo llegar a la ubicación de la actividad si no tengo vehículo?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Siempre que sea posible, se recomienda compartir vehículo entre los participantes. Esta práctica ayuda a:</p>
                    <ul class="faq-list">
                        <li>Reducir la huella de carbono</li>
                        <li>Disminuir los costes de desplazamiento</li>
                        <li>Optimizar el uso de los aparcamientos, que en muchos espacios naturales suelen tener un número limitado de plazas</li>
                    </ul>
                    <p>El grupo de WhatsApp de la actividad puede servir para coordinar el transporte compartido entre los participantes, de manera voluntaria y bajo su propia responsabilidad.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Qué ropa es recomendable según la actividad y el clima?</span>
                </div>
                <div class="faq-respuesta">
                    <p>En el anuncio de cada actividad encontrarás la lista completa de material y equipamiento necesarios, dentro del apartado <strong>"Material necesario"</strong>.</p>
                    <p>Una vez realizada la reserva, puedes consultar directamente con el guía cualquier duda sobre la ropa más adecuada o sobre otros elementos del equipamiento a través del grupo de WhatsApp de la actividad.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Qué debo hacer si llego tarde al punto de encuentro?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Si prevés que vas a llegar tarde, informa al guía y al grupo lo antes posible a través del grupo de WhatsApp de la actividad. El guía intentará gestionar la situación del mejor modo posible, siempre que no comprometa la seguridad ni la planificación prevista.</p>
                    <p>Ten en cuenta que las actividades deben realizarse dentro de los márgenes de tiempo establecidos, y la puntualidad es esencial para garantizar la seguridad y el correcto desarrollo de la jornada.</p>
                    <p>Por este motivo, el tiempo máximo de espera en caso de retraso es de <strong>15 minutos</strong>.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Puede cambiar la fecha de una actividad?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Como norma general, una vez publicada una actividad, su fecha no puede modificarse sin previo aviso ni notificación a las personas que hayan realizado una reserva.</p>
                    <p>Sin embargo, Trekkium o el guía responsable podrán proponer un cambio de fecha únicamente en los siguientes casos:</p>
                    <ul class="faq-list">
                        <li>Previsión meteorológica adversa el día de la actividad</li>
                        <li>Incapacidad del guía para asistir en la fecha prevista</li>
                        <li>Causa de fuerza mayor que impida el desarrollo normal y seguro de la actividad</li>
                    </ul>
                    <p>El cambio de fecha deberá ser propuesto por el guía responsable en el grupo de WhatsApp de la actividad, y aprobado por el resto de participantes con un mínimo de <strong>24 horas de antelación</strong> al inicio.</p>
                    <p>Los participantes podrán rechazar la propuesta y cancelar su reserva sin coste alguno.</p>
                    <p>Para que la nueva fecha sea válida, al menos un número de participantes igual al grupo mínimo deberá haber expresado claramente su conformidad en el grupo y no haber cancelado su reserva.</p>
                    <p>Dentro de las últimas 24 horas antes del inicio, no se podrá modificar la fecha de la actividad bajo ningún concepto.</p>
                </div>
            </div>

            <div class="faq-seccion">
                <div class="faq-pregunta">
                    <span class="faq-icon">></span>
                    <span class="faq-texto">¿Qué ocurre si la previsión meteorológica para el día de la actividad es adversa?</span>
                </div>
                <div class="faq-respuesta">
                    <p>Si la previsión meteorológica es desfavorable para el día y lugar previstos, el guía responsable podrá proponer al grupo, a través del grupo de WhatsApp, una de las siguientes opciones:</p>
                    
                    <p><strong>Cambio de ubicación</strong> de la actividad, siempre que:</p>
                    <ul class="faq-list">
                        <li>Mantenga la misma modalidad, características técnicas y nivel de dificultad</li>
                        <li>La nueva ubicación se encuentre lo más cerca posible del lugar inicial</li>
                    </ul>
                    
                    <p><strong>Cambio de fecha</strong> de la actividad, proponiendo una nueva fecha al grupo de participantes.</p>
                    
                    <p>Los cambios de ubicación o fecha deben ser propuestos por el guía y consensuados por el grupo dentro de un plazo máximo de <strong>24 horas antes del inicio</strong> de la actividad.</p>
                    
                    <p>Cada participante deberá manifestar claramente su decisión en el grupo de WhatsApp:</p>
                    <ul class="faq-list">
                        <li>Si no está de acuerdo con la propuesta, podrá cancelar su reserva desde la sección <strong>"Mis reservas → Cancelar reserva"</strong></li>
                        <li>Si no cancela dentro del plazo, se entenderá que acepta la propuesta de cambio y se procederá al cobro del importe de la reserva en el plazo correspondiente</li>
                    </ul>
                    
                    <p><strong>Una vez superado el límite de 24 horas</strong></p>
                    <p>Si el número de participantes que aceptan la propuesta (y no cancelan su reserva) es igual o superior al grupo mínimo, la actividad se llevará a cabo:</p>
                    <ul class="faq-list">
                        <li>En caso de cambio de ubicación, el guía comunicará la nueva localización, hora de encuentro y demás detalles en el grupo de WhatsApp</li>
                        <li>En caso de cambio de fecha, Trekkium actualizará automáticamente la información en el anuncio. Las reservas permanecerán en estado <strong>"Pendiente"</strong> y podrán cancelarse hasta 24 horas antes de la nueva fecha</li>
                    </ul>
                    
                    <p>Si el grupo mínimo no se alcanza, la actividad será cancelada automáticamente por Trekkium, sin ningún cargo a los participantes. En este caso, las reservas vinculadas a la actividad pasarán al estado <strong>"Cancelado"</strong>.</p>
                </div>
            </div>

        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('faq_actividades', 'faq_actividades_shortcode');
?>