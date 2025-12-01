<?php
// Shortcode del buscador
function trekkium_in_buscador_actividades() {
    ob_start();

    // Obtener todas las taxonomías
    $regiones = get_terms(array(
        'taxonomy'   => 'region',
        'hide_empty' => true,
    ));
    
    $modalidades = get_terms(array(
        'taxonomy'   => 'modalidad',
        'hide_empty' => true,
    ));
    
    $dificultades = get_terms(array(
        'taxonomy'   => 'dificultad',
        'hide_empty' => true,
    ));

    // Obtener las relaciones entre taxonomías
    $relaciones = obtener_relaciones_taxonomias();
    ?>

    <section class="in-buscador-actividades">

        <div class="in-buscador-contenedor">

            <div class="buscador-formulario-titulo">

                <h2>Encuentra tu próxima aventura</h2>

            </div>

            <div class="buscador-formulario">

                <div class="buscador-formulario-item">

                    <div class="buscador-formulario-icono">
                        <?php echo trekkium_icon_region('', 'Región'); ?>
                    </div>
                
                    <select id="region-select">
                        <option value="">Región</option>
                        <?php foreach ($regiones as $region) : ?>
                            <option value="<?php echo esc_attr($region->slug); ?>"><?php echo esc_html($region->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                
                </div>


                <div class="buscador-formulario-item">

                    <div class="buscador-formulario-icono">
                        <?php echo trekkium_icon_modalidad('', 'Modalidad'); ?>
                    </div>

                    <select id="modalidad-select">
                        <option value="">Modalidad</option>
                        <?php foreach ($modalidades as $modalidad) : ?>
                            <option value="<?php echo esc_attr($modalidad->slug); ?>" 
                                    data-regiones='<?php echo json_encode($relaciones['modalidades'][$modalidad->slug]['regiones'] ?? []); ?>'>
                                <?php echo esc_html($modalidad->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                </div>

                <div class="buscador-formulario-item">

                    <div class="buscador-formulario-icono">
                        <?php echo trekkium_icon_dificultad('', 'Dificultad'); ?>
                    </div>

                    <select id="dificultad-select">
                        <option value="">Dificultad</option>
                        <?php foreach ($dificultades as $dificultad) : ?>
                            <option value="<?php echo esc_attr($dificultad->slug); ?>" 
                                    data-regiones='<?php echo json_encode($relaciones['dificultades'][$dificultad->slug]['regiones'] ?? []); ?>'
                                    data-modalidades='<?php echo json_encode($relaciones['dificultades'][$dificultad->slug]['modalidades'] ?? []); ?>'>
                                <?php echo esc_html($dificultad->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                </div>

                <button id="buscar-btn">Buscar</button>
            </div>

        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const regionSelect = document.getElementById('region-select');
                const modalidadSelect = document.getElementById('modalidad-select');
                const dificultadSelect = document.getElementById('dificultad-select');
                const buscarBtn = document.getElementById('buscar-btn');

                // Guardamos todas las opciones originales
                const originalModalidades = Array.from(modalidadSelect.options);
                const originalDificultades = Array.from(dificultadSelect.options);

                function filtrarModalidades() {
                    const regionSeleccionada = regionSelect.value;
                    
                    // Limpiar y restaurar opción por defecto
                    modalidadSelect.innerHTML = '<option value="">Modalidad</option>';
                    
                    originalModalidades.forEach(option => {
                        if (option.value === "") return; // Saltar opción por defecto
                        
                        const regionesDisponibles = JSON.parse(option.getAttribute('data-regiones') || '[]');
                        
                        // Mostrar la opción si no hay región seleccionada O si la región está disponible para esta modalidad
                        if (!regionSeleccionada || regionesDisponibles.includes(regionSeleccionada)) {
                            const newOption = option.cloneNode(true);
                            modalidadSelect.appendChild(newOption);
                        }
                    });
                    
                    // Disparar el filtro de dificultades después de filtrar modalidades
                    filtrarDificultades();
                }

                function filtrarDificultades() {
                    const regionSeleccionada = regionSelect.value;
                    const modalidadSeleccionada = modalidadSelect.value;
                    
                    // Limpiar y restaurar opción por defecto
                    dificultadSelect.innerHTML = '<option value="">Dificultad</option>';
                    
                    originalDificultades.forEach(option => {
                        if (option.value === "") return; // Saltar opción por defecto
                        
                        const regionesDisponibles = JSON.parse(option.getAttribute('data-regiones') || '[]');
                        const modalidadesDisponibles = JSON.parse(option.getAttribute('data-modalidades') || '[]');
                        
                        const regionValida = !regionSeleccionada || regionesDisponibles.includes(regionSeleccionada);
                        const modalidadValida = !modalidadSeleccionada || modalidadesDisponibles.includes(modalidadSeleccionada);
                        
                        // Mostrar la opción si pasa ambos filtros
                        if (regionValida && modalidadValida) {
                            const newOption = option.cloneNode(true);
                            dificultadSelect.appendChild(newOption);
                        }
                    });
                }

                // Botón de búsqueda - CORREGIDO
                buscarBtn.addEventListener('click', function() {
                    const region = regionSelect.value;
                    const modalidad = modalidadSelect.value;
                    const dificultad = dificultadSelect.value;

                    let url = '<?php echo esc_url(home_url('/actividades/')); ?>';
                    let params = [];
                    
                    if(region) params.push('region=' + encodeURIComponent(region));
                    if(modalidad) params.push('modalidad=' + encodeURIComponent(modalidad));
                    if(dificultad) params.push('dificultad=' + encodeURIComponent(dificultad));

                    if(params.length) {
                        url += '?' + params.join('&');
                    }

                    window.location.href = url;
                });

                // Event listeners
                regionSelect.addEventListener('change', filtrarModalidades);
                modalidadSelect.addEventListener('change', filtrarDificultades);

                // Inicializar filtros
                filtrarModalidades();
            });
        </script>
        
    </section>

    <?php
    return ob_get_clean();
}
add_shortcode('in_buscador_actividades', 'trekkium_in_buscador_actividades');

// Función para obtener las relaciones entre taxonomías - CORREGIDA
function obtener_relaciones_taxonomias() {
    $relaciones = array(
        'modalidades' => array(),
        'dificultades' => array()
    );

    // Obtener todos los PRODUCTOS de WooCommerce
    $productos = get_posts(array(
        'post_type' => 'product', // ✅ Usamos 'product' en lugar de 'actividad'
        'numberposts' => -1,
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'exclude-from-catalog',
                'operator' => 'NOT IN',
            ),
        ),
    ));

    foreach ($productos as $producto) {
        // Obtener términos de este producto para cada taxonomía
        $regiones_producto = wp_get_post_terms($producto->ID, 'region', array('fields' => 'slugs'));
        $modalidades_producto = wp_get_post_terms($producto->ID, 'modalidad', array('fields' => 'slugs'));
        $dificultades_producto = wp_get_post_terms($producto->ID, 'dificultad', array('fields' => 'slugs'));

        // Relacionar modalidades con regiones
        foreach ($modalidades_producto as $modalidad_slug) {
            if (!isset($relaciones['modalidades'][$modalidad_slug])) {
                $relaciones['modalidades'][$modalidad_slug] = array('regiones' => array());
            }
            foreach ($regiones_producto as $region_slug) {
                if (!in_array($region_slug, $relaciones['modalidades'][$modalidad_slug]['regiones'])) {
                    $relaciones['modalidades'][$modalidad_slug]['regiones'][] = $region_slug;
                }
            }
        }

        // Relacionar dificultades con regiones y modalidades
        foreach ($dificultades_producto as $dificultad_slug) {
            if (!isset($relaciones['dificultades'][$dificultad_slug])) {
                $relaciones['dificultades'][$dificultad_slug] = array(
                    'regiones' => array(),
                    'modalidades' => array()
                );
            }
            
            // Relacionar con regiones
            foreach ($regiones_producto as $region_slug) {
                if (!in_array($region_slug, $relaciones['dificultades'][$dificultad_slug]['regiones'])) {
                    $relaciones['dificultades'][$dificultad_slug]['regiones'][] = $region_slug;
                }
            }
            
            // Relacionar con modalidades
            foreach ($modalidades_producto as $modalidad_slug) {
                if (!in_array($modalidad_slug, $relaciones['dificultades'][$dificultad_slug]['modalidades'])) {
                    $relaciones['dificultades'][$dificultad_slug]['modalidades'][] = $modalidad_slug;
                }
            }
        }
    }

    return $relaciones;
}