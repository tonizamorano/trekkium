<?php
/* Shortcode: Admin Dashboard completo */
add_shortcode('admin_dashboard_full', function() {
    ob_start(); ?>

    <!-- Encabezado azul -->
    <!-- Encabezado azul con fila de dos columnas -->

    <div class="admin-dashboard-header">

        <div class="contenedor930px">

            <div class="header-left">
                ADMIN DASHBOARD
            </div>

            <div class="header-right">

                <div class="admin-dashboard-submenu">
                    <div class="admin-dashboard-tab active" data-tab="productos">Productos</div>
                    <div class="admin-dashboard-tab" data-tab="guias">Guías</div>
                    <div class="admin-dashboard-tab" data-tab="clientes">Clientes</div>
                    <div class="admin-dashboard-tab" data-tab="pedidos">Pedidos</div>
                </div>

            </div>
        
        </div>

    </div>

    <!-- Contenedor de contenido dinámico -->
    <div id="admin-dashboard-contenido">

        <!-- Sección Productos -->
        <div class="tab-content" data-tab="productos">
            <?php echo do_shortcode('[admin_dashboard_productos]'); ?>
        </div>

        <!-- Sección Guías -->
        <div class="tab-content" data-tab="guias" style="display:none;">
            <?php echo do_shortcode('[admin_dashboard_guias]'); ?>
        </div>

        <!-- Sección Clientes -->
        <div class="tab-content" data-tab="clientes" style="display:none;">
            <p>Sección Clientes (pendiente)</p>
        </div>

        <!-- Sección Pedidos -->
        <div class="tab-content" data-tab="pedidos" style="display:none;">
            <p>Sección Pedidos (pendiente)</p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.admin-dashboard-tab');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Marcar la pestaña activa
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const tabName = this.getAttribute('data-tab');

                // Mostrar la pestaña seleccionada y ocultar las demás
                contents.forEach(c => {
                    c.style.display = c.getAttribute('data-tab') === tabName ? 'block' : 'none';
                });

                // 🔧 Si se vuelve a "Productos", limpia la URL y recarga el contenido
                if (tabName === 'productos') {
                    const cleanUrl = window.location.origin + window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);

                    // Recargar el shortcode para volver al listado
                    const productosTab = document.querySelector('.tab-content[data-tab="productos"]');

                    if (productosTab) {
                        fetch(cleanUrl + '?shortcode=admin_dashboard_productos')
                            .then(response => response.text())
                            .then(html => {
                                // Extrae solo el contenido del shortcode si se devolviera la página completa
                                const tempDiv = document.createElement('div');
                                tempDiv.innerHTML = html;
                                const contenido = tempDiv.querySelector('.admin-dashboard-contenido');
                                if (contenido) {
                                    productosTab.innerHTML = contenido.outerHTML;
                                }
                            });
                    }
                }
            });
        });
    });
    </script>


<?php
    return ob_get_clean();
});
