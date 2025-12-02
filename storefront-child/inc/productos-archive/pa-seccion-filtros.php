<?php
// Registramos el shortcode para la sección de filtros
function pa_filtros_shortcode() {
    ob_start();
    
    // Obtener parámetros actuales de la URL
    $region_actual = isset($_GET['region']) ? sanitize_text_field($_GET['region']) : '';
    $modalidad_actual = isset($_GET['modalidad']) ? sanitize_text_field($_GET['modalidad']) : '';
    $dificultad_actual = isset($_GET['dificultad']) ? sanitize_text_field($_GET['dificultad']) : '';
    ?>
    <div class="contenedor-filtros" role="region" aria-label="Filtros de productos">

        <!-- Titular -->
        <div class="contenedor-filtros-titular" tabindex="0" role="button" aria-expanded="true">
            
            <div class="filtros-titular-icono">

                <?php echo do_shortcode('[icon_filtros]'); ?> 

            </div>


            <h4>FILTROS</h4>
            <span class="desplegable-indicador">+</span>
        </div>

        <!-- Contenido -->
        <div class="contenedor-filtros-contenido">

            <!-- Sección Región -->
            <div class="filtros-seccion">
                <div class="filtros-seccion-titular">
                    <?php echo do_shortcode('[icon_region1]'); ?> 
                    <h5>Región</h5>             
                </div>
                <div class="filtros-seccion-contenido">
                    <?php echo do_shortcode('[filtro_productos_region]'); ?>   
                </div>      
            </div>

            <!-- Sección Modalidad -->
            <div class="filtros-seccion">
                <div class="filtros-seccion-titular">
                    <?php echo do_shortcode('[icon_modalidad1]'); ?>
                    <h5>Modalidad</h5>             
                </div>
                <div class="filtros-seccion-contenido">
                    <?php echo do_shortcode('[filtro_productos_modalidad]'); ?>   
                </div>       
            </div>

            <!-- Sección Dificultad -->
            <div class="filtros-seccion">
                <div class="filtros-seccion-titular">
                    <?php echo do_shortcode('[icon_dificultad1]'); ?>
                    <h5>Dificultad</h5>             
                </div>
                <div class="filtros-seccion-contenido">
                    <?php echo do_shortcode('[filtro_productos_dificultad]'); ?>   
                </div>       
            </div>

        </div>

    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // === Acordeón para móvil ===
        const contenedores = document.querySelectorAll('.contenedor-filtros');
        contenedores.forEach(function(filtros) {
            const titular = filtros.querySelector('.contenedor-filtros-titular');
            const contenido = filtros.querySelector('.contenedor-filtros-contenido');
            const indicador = filtros.querySelector('.desplegable-indicador');
            if (!titular || !contenido) return;

            const toggleHandler = function(event) {
                if (event.type === 'keydown' && event.key !== 'Enter' && event.key !== ' ') return;
                const esta = contenido.classList.toggle('desplegado');
                titular.classList.toggle('activo', esta);
                titular.setAttribute('aria-expanded', String(esta));
                if (indicador) indicador.textContent = esta ? '-' : '+';
            };

            const mq = window.matchMedia('(max-width: 768px)');
            function activarMovil() {
                titular.style.cursor = 'pointer';
                contenido.classList.remove('desplegado');
                titular.classList.remove('activo');
                titular.setAttribute('aria-expanded', 'false');
                if (indicador) indicador.textContent = '+';
                titular.removeEventListener('click', toggleHandler);
                titular.removeEventListener('keydown', toggleHandler);
                titular.addEventListener('click', toggleHandler);
                titular.addEventListener('keydown', toggleHandler);
            }
            function activarEscritorio() {
                titular.style.cursor = 'default';
                titular.removeEventListener('click', toggleHandler);
                titular.removeEventListener('keydown', toggleHandler);
                contenido.classList.add('desplegado');
                titular.classList.remove('activo');
                titular.setAttribute('aria-expanded', 'true');
                if (indicador) indicador.textContent = '+';
            }
            if (mq.matches) activarMovil(); else activarEscritorio();
            if (typeof mq.addEventListener === 'function') {
                mq.addEventListener('change', e => e.matches ? activarMovil() : activarEscritorio());
            } else if (typeof mq.addListener === 'function') {
                mq.addListener(e => e.matches ? activarMovil() : activarEscritorio());
            }
        });

        // === Sincronizar con parámetros URL ===
        const urlParams = new URLSearchParams(window.location.search);
        const regionParam = urlParams.get('region');
        const modalidadParam = urlParams.get('modalidad');
        const dificultadParam = urlParams.get('dificultad');

        // Activar filtros basados en URL
        if (regionParam) {
            const regionBtn = document.querySelector(`.filtro-item[data-region="${regionParam}"]`);
            if (regionBtn) regionBtn.click();
        }
        if (modalidadParam) {
            const modalidadBtn = document.querySelector(`.filtro-item[data-modalidad="${modalidadParam}"]`);
            if (modalidadBtn) modalidadBtn.click();
        }
        if (dificultadParam) {
            const dificultadBtn = document.querySelector(`.filtro-item[data-dificultad="${dificultadParam}"]`);
            if (dificultadBtn) dificultadBtn.click();
        }
    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('pa_filtros', 'pa_filtros_shortcode');