<?php

// Registramos el shortcode
function pagina_faq_shortcode() {
    ob_start();
    ?>

    <div class="pagina-grid-3366">

        <!-- Columna izquierda -->
        <aside class="pagina-columna33-sticky">
            <nav class="faq-menu-container">
                <div class="faq-menu-item" data-shortcode="[faq_trekkium]">Sobre Trekkium</div>
                <div class="faq-menu-item" data-shortcode="[faq_actividades]">Sobre las actividades</div>
                <div class="faq-menu-item" data-shortcode="[faq_guias]">Sobre los guías</div>
                <div class="faq-menu-item" data-shortcode="[faq_cuenta]">Cuenta de usuario</div>
                <div class="faq-menu-item" data-shortcode="[faq_reservas]">Reservas, pagos y cancelaciones</div>
                <div class="faq-menu-item" data-shortcode="[faq_seguros]">Sobre seguridad</div>                
            </nav>
        </aside>

        <!-- Columna derecha -->
        <div class="pagina-columna66">
            <div class="faq-contenido" id="faq-contenido">
                <?php echo do_shortcode('[faq_trekkium]'); // Carga inicial ?>
            </div>
        </div>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuItems = document.querySelectorAll('.faq-menu-item');
        const contenido = document.getElementById('faq-contenido');

        // Detectar el shortcode inicial cargado
        const shortcodeInicial = '[faq_trekkium]'; // puedes cambiarlo si quieres otra sección por defecto

        // Función para marcar el item activo
        function marcarActivo(shortcode) {
            menuItems.forEach(item => {
                item.classList.toggle('active', item.getAttribute('data-shortcode') === shortcode);
            });
        }

        // Marcar activo al cargar la página
        marcarActivo(shortcodeInicial);

        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                const shortcode = item.getAttribute('data-shortcode');

                // Actualizar menú
                marcarActivo(shortcode);

                // Cargar contenido vía AJAX
                fetch('<?php echo admin_url("admin-ajax.php"); ?>?action=faq_shortcode&shortcode=' + encodeURIComponent(shortcode))
                    .then(response => response.text())
                    .then(html => {
                        contenido.innerHTML = html;
                        // Inicializar FAQ después de cargar el contenido
                        inicializarFAQ();
                    });
            });
        });

        // Función para inicializar FAQ
        function inicializarFAQ() {
            const preguntas = document.querySelectorAll('.faq-pregunta');
            
            preguntas.forEach(pregunta => {
                pregunta.replaceWith(pregunta.cloneNode(true));
            });

            const nuevasPreguntas = document.querySelectorAll('.faq-pregunta');
            
            nuevasPreguntas.forEach(pregunta => {
                pregunta.addEventListener('click', function() {
                    const respuesta = this.nextElementSibling;
                    const contenedor = this.closest('.faq-contenedor');
                    
                    // Cerrar otras respuestas
                    contenedor.querySelectorAll('.faq-respuesta.activo').forEach(activo => {
                        if (activo !== respuesta) {
                            activo.classList.remove('activo');
                            activo.previousElementSibling.classList.remove('activo');
                        }
                    });

                    // Alternar actual
                    this.classList.toggle('activo');
                    respuesta.classList.toggle('activo');
                });
            });
        }

        // Inicializar FAQ al cargar la página
        inicializarFAQ();
    });
    </script>


    <?php
    return ob_get_clean();
}
add_shortcode('pagina_faq', 'pagina_faq_shortcode');

add_action('wp_ajax_faq_shortcode', 'faq_shortcode_ajax');
add_action('wp_ajax_nopriv_faq_shortcode', 'faq_shortcode_ajax');

function faq_shortcode_ajax() {
    if(isset($_GET['shortcode'])) {
        echo do_shortcode(urldecode($_GET['shortcode']));
    }
    wp_die();
}