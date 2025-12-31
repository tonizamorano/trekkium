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
                    <span class="faq-texto">¿Qué indica el número máximo y mínimo de participantes?</span>
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
                    <span class="faq-texto">¿Para qué sirve el grupo de Whatsapp de la actividad?</span>
                </div>
                <div class="faq-respuesta">
                    <p>El grupo de WhatsApp de cada actividad es creado y administrado por el guía responsable, y su función principal es la de <strong>resolver dudas</strong>, facilitar información práctica y <strong>comunicar actualizaciones</strong> o cambios relacionados con la actividad.</p>
                    
                    <p>El resto del grupo está formado exclusivamente por las personas que han realizado una reserva y cuyo estado se encuentra en <strong>Pendiente</strong> o <strong>Completado</strong>.</p>

                    <p>A través del grupo de Whatsapp, los participantes pueden organizar lugares de encuentro para <strong>compartir vehículos</strong>, o <strong>compartir fotos</strong> de la actividad una vez ésta haya finalizado.</p>
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
                    <span class="faq-texto">¿Cómo llego al punto de encuentro de la actividad si no tengo vehículo?</span>
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