<?php

// === Filtro Región ===
add_shortcode('filtro_productos_region', 'trekkium_shortcode_filtro_productos_region');
function trekkium_shortcode_filtro_productos_region() {
    $regiones = get_terms([
        'taxonomy' => 'region',
        'hide_empty' => true,
        'orderby' => 'name',
        'order' => 'ASC'
    ]);

    if (empty($regiones) || is_wp_error($regiones)) return '';

    ob_start(); ?>
    <div class="filtro-contenedor" id="filtro-regiones">
        <?php foreach ($regiones as $region): ?>
            <span class="filtro-item" data-region="<?php echo esc_attr($region->slug); ?>">
                <?php echo esc_html($region->name); ?>
            </span>
        <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
}

// === Filtro Modalidad ===
add_shortcode('filtro_productos_modalidad', 'trekkium_shortcode_filtro_productos_modalidad');
function trekkium_shortcode_filtro_productos_modalidad() {
    $modalidades = get_terms([
        'taxonomy' => 'modalidad',
        'hide_empty' => true,
        'orderby' => 'name',
        'order' => 'ASC'
    ]);

    if (empty($modalidades) || is_wp_error($modalidades)) return '';

    ob_start(); ?>
    <div class="filtro-contenedor" id="filtro-modalidades">
        <?php foreach ($modalidades as $modalidad): ?>
            <span class="filtro-item" data-modalidad="<?php echo esc_attr($modalidad->slug); ?>">
                <?php echo esc_html($modalidad->name); ?>
            </span>
        <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
}

// === Filtro Dificultad ===
add_shortcode('filtro_productos_dificultad', 'trekkium_shortcode_filtro_productos_dificultad');
function trekkium_shortcode_filtro_productos_dificultad() {
    $dificultades = get_terms([
        'taxonomy' => 'dificultad',
        'hide_empty' => true,
        'orderby' => 'name',
        'order' => 'ASC'
    ]);

    if (empty($dificultades) || is_wp_error($dificultades)) return '';

    ob_start(); ?>
    <div class="filtro-contenedor" id="filtro-dificultades">
        <?php foreach ($dificultades as $dificultad): ?>
            <span class="filtro-item" data-dificultad="<?php echo esc_attr($dificultad->slug); ?>">
                <?php echo esc_html($dificultad->name); ?>
            </span>
        <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
}

// === Script de Filtrado Mejorado ===
add_action('wp_footer', function() { ?>
<script>
(function(){
    function normalizeTextToSlug(str){
        if(!str && str !== "") return "";
        try {
            return String(str)
                .normalize('NFD')
                .replace(/\p{Diacritic}/gu,'')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)/g,'');
        } catch(e) {
            return String(str).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g,'');
        }
    }

    function actualizarURL(region, modalidad, dificultad) {
        const params = new URLSearchParams();
        if (region) params.set('region', region);
        if (modalidad) params.set('modalidad', modalidad);
        if (dificultad) params.set('dificultad', dificultad);
        
        const nuevaURL = params.toString() 
            ? `${window.location.pathname}?${params.toString()}`
            : window.location.pathname;
            
        window.location.href = nuevaURL;
    }

    function inicializarFiltros() {
        const regionBtns = Array.from(document.querySelectorAll(".filtro-item[data-region]"));
        const modalidadBtns = Array.from(document.querySelectorAll(".filtro-item[data-modalidad]"));
        const dificultadBtns = Array.from(document.querySelectorAll(".filtro-item[data-dificultad]"));
        const productos = Array.from(document.querySelectorAll(".pa-query-wrapper .pa-query-item"));

        if (!productos.length) return;

        let activeRegion = null;
        let activeModalidad = null;
        let activeDificultad = null;

        // Obtener parámetros iniciales de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const regionParam = urlParams.get('region');
        const modalidadParam = urlParams.get('modalidad');
        const dificultadParam = urlParams.get('dificultad');

        // Establecer filtros activos desde URL
        if (regionParam) activeRegion = regionParam;
        if (modalidadParam) activeModalidad = modalidadParam;
        if (dificultadParam) activeDificultad = dificultadParam;

        function getAvailableProductOptions() {
            const availableRegiones = new Set();
            const availableModalidades = new Set();
            const availableDificultades = new Set();
            
            productos.forEach(producto => {
                if (producto.style.display !== 'none') {
                    const regiones = (producto.getAttribute('data-region') || '').split(',').map(normalizeTextToSlug);
                    const modalidades = (producto.getAttribute('data-modalidad') || '').split(',').map(normalizeTextToSlug);
                    const dificultades = (producto.getAttribute('data-dificultad') || '').split(',').map(normalizeTextToSlug);
                    
                    regiones.forEach(r => r && availableRegiones.add(r));
                    modalidades.forEach(m => m && availableModalidades.add(m));
                    dificultades.forEach(d => d && availableDificultades.add(d));
                }
            });

            return { 
                regiones: Array.from(availableRegiones), 
                modalidades: Array.from(availableModalidades), 
                dificultades: Array.from(availableDificultades) 
            };
        }

        function updateProductFilterButtons() {
            const available = getAvailableProductOptions();

            // Ocultar botones que no tienen productos disponibles
            regionBtns.forEach(btn => {
                const slug = normalizeTextToSlug(btn.getAttribute('data-region'));
                const tieneProductos = available.regiones.includes(slug);
                btn.style.display = tieneProductos ? '' : 'none';
                
                // Marcar como activo si coincide con el filtro activo
                btn.classList.toggle('active', activeRegion === slug);
            });

            modalidadBtns.forEach(btn => {
                const slug = normalizeTextToSlug(btn.getAttribute('data-modalidad'));
                const tieneProductos = available.modalidades.includes(slug);
                btn.style.display = tieneProductos ? '' : 'none';
                
                // Marcar como activo si coincide con el filtro activo
                btn.classList.toggle('active', activeModalidad === slug);
            });

            dificultadBtns.forEach(btn => {
                const slug = normalizeTextToSlug(btn.getAttribute('data-dificultad'));
                const tieneProductos = available.dificultades.includes(slug);
                btn.style.display = tieneProductos ? '' : 'none';
                
                // Marcar como activo si coincide con el filtro activo
                btn.classList.toggle('active', activeDificultad === slug);
            });
        }

        function filtrarProductos() {
            // Primero mostrar todos los productos
            productos.forEach(producto => {
                producto.style.display = '';
            });

            // Luego aplicar filtros
            productos.forEach(producto => {
                const regiones = (producto.getAttribute('data-region') || '').split(',').map(normalizeTextToSlug);
                const modalidades = (producto.getAttribute('data-modalidad') || '').split(',').map(normalizeTextToSlug);
                const dificultades = (producto.getAttribute('data-dificultad') || '').split(',').map(normalizeTextToSlug);

                const matchRegion = !activeRegion || regiones.includes(activeRegion);
                const matchModalidad = !activeModalidad || modalidades.includes(activeModalidad);
                const matchDificultad = !activeDificultad || dificultades.includes(activeDificultad);

                producto.style.display = (matchRegion && matchModalidad && matchDificultad) ? '' : 'none';
            });

            updateProductFilterButtons();
        }

        function setupButtons(buttons, type) {
            buttons.forEach(btn => {
                btn.addEventListener('click', function(){
                    const key = `data-${type}`;
                    const value = normalizeTextToSlug(this.getAttribute(key));
                    const isActive = this.classList.contains('active');

                    if (isActive) {
                        // Desactivar filtro
                        if (type === 'region') activeRegion = null;
                        if (type === 'modalidad') activeModalidad = null;
                        if (type === 'dificultad') activeDificultad = null;
                    } else {
                        // Activar filtro
                        if (type === 'region') activeRegion = value;
                        if (type === 'modalidad') activeModalidad = value;
                        if (type === 'dificultad') activeDificultad = value;
                    }

                    // Actualizar URL y recargar para aplicar filtros combinados
                    actualizarURL(activeRegion, activeModalidad, activeDificultad);
                });
            });
        }

        // Configurar eventos
        setupButtons(regionBtns, 'region');
        setupButtons(modalidadBtns, 'modalidad');
        setupButtons(dificultadBtns, 'dificultad');

        // Aplicar filtros iniciales y actualizar botones
        filtrarProductos();
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Pequeño delay para asegurar que el DOM esté completamente cargado
        setTimeout(inicializarFiltros, 100);
    });
})();
</script>
<?php
});