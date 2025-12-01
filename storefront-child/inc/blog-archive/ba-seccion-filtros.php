<?php

function seccion_blog_archive_filtros_shortcode() {
    ob_start();
    ?>
    
    <div class="contenedor-filtros">

        <!-- Titular -->

        <div class="contenedor-filtros-titular">
            <svg class="filtros-titular-icono" viewBox="0 0 512 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M496 384H160v-16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v16H16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h80v16c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-16h336c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm0-160h-80v-16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v16H16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h336v16c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-16h80c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm0-160H288V48c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v16H16C7.2 64 0 71.2 0 80v32c0 8.8 7.2 16 16 16h208v16c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-16h208c8.8 0 16-7.2 16-16V80c0-8.8-7.2-16-16-16z"></path>
            </svg>
            <h4>FILTROS</h4>
            <span class="desplegable-indicador">+</span>
        </div>

        <!-- Contenido -->

        <div class="contenedor-filtros-contenido">
        
            <!-- Sección Categorías -->

            <div class="filtros-seccion">

                <!-- Sección Categorías - Titular -->
            
                <div class="filtros-seccion-titular">
                              
                    <h5>Categorías</h5>
                </div>

                <!-- Sección Categorías - Contenido -->

                <div class="filtros-seccion-contenido">
                    <?php echo do_shortcode('[filtro_blog_categorias]'); ?>
                
                </div>

            </div>

            <!-- Sección Autores -->

            <div class="filtros-seccion">

                <!-- Sección Categorías - Titular -->

                <div class="filtros-seccion-titular">
                                  
                    <h5>Autores</h5>
                </div>

                 <!-- Sección Categorías - Contenido -->

                <div class="filtros-seccion-contenido">
                    <?php echo do_shortcode('[filtro_blog_autor]'); ?>
                </div>

            </div>

        </div>

    </div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const filtros = document.querySelector('.contenedor-filtros');
    const titular = filtros.querySelector('.contenedor-filtros-titular');
    const contenido = filtros.querySelector('.contenedor-filtros-contenido');
    const indicador = filtros.querySelector('.desplegable-indicador');

    function activarMovil() {
        contenido.classList.remove('desplegado');
        titular.onclick = function() {
            contenido.classList.toggle('desplegado');
            indicador.textContent = contenido.classList.contains('desplegado') ? '-' : '+';
        };
    }

    function activarDesktop() {
        contenido.classList.add('desplegado');
        titular.onclick = null;
        indicador.textContent = '+';
    }

    if(window.innerWidth <= 768) activarMovil();
    else activarDesktop();

    window.addEventListener('resize', function(){
        if(window.innerWidth <= 768) activarMovil();
        else activarDesktop();
    });
});
</script>

    <?php
    return ob_get_clean();
}
add_shortcode('seccion_blog_archive_filtros', 'seccion_blog_archive_filtros_shortcode');
