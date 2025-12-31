<?php

// Registramos el shortcode
function pagina_terminos_condiciones_shortcode() {
    ob_start();
    ?>

    <div class="pagina-grid-3366">

        <!-- Columna izquierda -->
        <aside class="pagina-columna33-sticky">
            <nav class="tc-menu-container">
                <div class="tc-menu-item" data-shortcode="[tc_aviso_legal]" data-slug="aviso-legal">Aviso legal</div>
                <div class="tc-menu-item" data-shortcode="[tc_politica_privacidad]" data-slug="politica-privacidad">Política de privacidad</div>
                <div class="tc-menu-item" data-shortcode="[tc_politica_cookies]" data-slug="politica-cookies">Política de cookies</div>
                <div class="tc-menu-item" data-shortcode="[tc_condiciones_contratacion]" data-slug="condiciones-contratacion">Condiciones de contratación</div> 
                <div class="tc-menu-item" data-shortcode="[tc_descarga_responsabilidad]" data-slug="descarga-responsabilidad">Descarga de responsabilidad</div>                           
            </nav>
        </aside>

        <!-- Columna derecha -->
        <div class="pagina-columna66">
            <div class="tc-contenido" id="tc-contenido">
                <?php echo do_shortcode('[tc_aviso_legal]'); // Carga inicial ?>
            </div>
        </div>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuItems = document.querySelectorAll('.tc-menu-item');
        const contenido = document.getElementById('tc-contenido');

        // Shortcode inicial
        const shortcodeInicial = '[tc_aviso_legal]';

        // Función para actualizar clase activa
        function marcarActivo(shortcode) {
            menuItems.forEach(item => {
                item.classList.toggle('active', item.getAttribute('data-shortcode') === shortcode);
            });
        }

        // Marcar activo al cargar
        marcarActivo(shortcodeInicial);

        // Clic en menú
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                const shortcode = item.getAttribute('data-shortcode');

                // Actualizar menú
                marcarActivo(shortcode);

                // Cargar contenido vía AJAX
                fetch('<?php echo admin_url("admin-ajax.php"); ?>?action=tc_shortcode&shortcode=' + encodeURIComponent(shortcode))
                    .then(response => response.json())
                    .then(data => {
                        if(data.success){
                            contenido.innerHTML = data.data;
                            inicializartc();
                        }
                    });
            });
        });

        function inicializartc() {
            const secciones = document.querySelectorAll('.tc-seccion');

            secciones.forEach(seccion => {
                const pregunta = seccion.querySelector('.tc-pregunta');
                const respuesta = seccion.querySelector('.tc-respuesta');
                respuesta.style.display = 'none';

                pregunta.addEventListener('click', () => {
                    const abierto = respuesta.style.display === 'block';
                    document.querySelectorAll('.tc-respuesta').forEach(r => r.style.display = 'none');
                    respuesta.style.display = abierto ? 'none' : 'block';
                });
            });
        }

        inicializartc();

        // --- Cargar sección desde URL ---
        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        const seccionSlug = getQueryParam('seccion');
        if(seccionSlug) {
            const item = Array.from(menuItems).find(i => i.getAttribute('data-slug') === seccionSlug);
            if(item) item.click();
        }

    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('pagina_terminos_condiciones', 'pagina_terminos_condiciones_shortcode');

add_action('wp_ajax_tc_shortcode', 'tc_shortcode_ajax');
add_action('wp_ajax_nopriv_tc_shortcode', 'tc_shortcode_ajax');

function tc_shortcode_ajax() {
    if(isset($_GET['shortcode'])) {
        $shortcode = urldecode($_GET['shortcode']);
        ob_start();
        echo do_shortcode($shortcode);
        wp_send_json_success(ob_get_clean());
    } else {
        wp_send_json_error('No se recibió shortcode');
    }
}
