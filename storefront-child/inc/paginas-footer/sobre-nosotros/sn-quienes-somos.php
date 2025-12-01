<?php
function sn_quienes_somos_shortcode() {
    ob_start();
    ?>
    <div class="sn-contenedor">

        <!-- Título -->
        <div class="sn-titulo">
            <h2>¿Quiénes somos?</h2>
        </div>

        <!-- Contenido -->
        <div class="sn-contenido">

            <div>
             
                <div style="font-size: 16px;">
                    ¡Bienvenido a Trekkium! Si te gusta la aventura, estar en contacto con la naturaleza, …<br><br>
                    <h2 style="color: var(--naranja1-100);">¿Qué es Trekkium?</h2><br>
                    <span style="font-weight: 700;">Trekkium</span> es una plataforma de reservas online donde puedes encontrar anuncios de actividades de montaña de distintas modalidades deportivas, organizadas por guías de montaña oficiales. <br><br>
                    También puedes comprar la reserva de tu plaza para la actividad y conocer en todo momento el estado de la misma, así como realizar su cancelación. Una vez hayas hecho una reserva, podrás estar en contacto con el/la guía organizador/a para hacer consultas o resolver cualquier duda que tengas.<br><br>
                    Trekkium conecta a personas que buscan experiencias de montaña guiadas, con guías de montaña profesionales que organizan actividades y las ofrecen en la plataforma.<br><br>
                    Trekkium también ofrece contenidos didácticos sobre el montañismo, a través de su blog.

                </div>
            </div>

        </div>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('sn_quienes_somos', 'sn_quienes_somos_shortcode');
?>
