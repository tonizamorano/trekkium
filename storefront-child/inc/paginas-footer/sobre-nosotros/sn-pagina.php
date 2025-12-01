<?php
// === SHORTCODE: [pagina_sobre_nosotros] === //

function pagina_sobre_nosotros_shortcode() {
    ob_start();
    ?>

    <div class="pagina-grid-3366">

        <!-- Columna izquierda -->
        <aside class="pagina-columna33-sticky">

            <nav class="sn-menu-container">
                <div class="sn-menu-item" data-shortcode="[sn_quienes_somos]">Quiénes somos</div>
                <div class="sn-menu-item" data-shortcode="[sn_mision]">Misión</div>
                <div class="sn-menu-item" data-shortcode="[sn_filosofia]">Filosofía</div>
                <div class="sn-menu-item" data-shortcode="[sn_codigo_etico]">Código ético</div>
                <div class="sn-menu-item" data-shortcode="[sn_compromiso]">Compromiso ambiental</div>
                <div class="sn-menu-item" data-shortcode="[sn_equipo]">Equipo</div>
                <div class="sn-menu-item" data-shortcode="[sn_colabora]">Colabora con nosotros</div>
            </nav>
        </aside>

        <!-- Columna derecha -->
        <div class="pagina-columna66">

            <div class="sn-contenido" id="sn-contenido">
                <?php echo do_shortcode('[sn_quienes_somos]'); // Carga inicial ?>
            </div>

        </div>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuItems = document.querySelectorAll('.sn-menu-item');
        const contenido = document.getElementById('sn-contenido');

        // Detectar el shortcode inicial cargado
        const shortcodeInicial = '[sn_quienes_somos]'; // cambiar si quieres otra sección por defecto

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
                fetch('<?php echo admin_url("admin-ajax.php"); ?>?action=sn_shortcode&shortcode=' + encodeURIComponent(shortcode))
                    .then(response => response.text())
                    .then(html => {
                        contenido.innerHTML = html;
                    });
            });
        });
    });
    </script>


    <?php
    return ob_get_clean();
}
add_shortcode('pagina_sobre_nosotros', 'pagina_sobre_nosotros_shortcode');

// === AJAX HANDLER === //
add_action('wp_ajax_sn_shortcode', 'sn_shortcode_ajax');
add_action('wp_ajax_nopriv_sn_shortcode', 'sn_shortcode_ajax');

function sn_shortcode_ajax() {
    if (isset($_GET['shortcode'])) {
        echo do_shortcode(urldecode($_GET['shortcode']));
    }
    wp_die();
}
