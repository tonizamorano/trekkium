<?php
// Shortcode: Filtro por modalidad (ahora con datos para filtrado cruzado)
add_shortcode('filtro_guias_modalidad', 'trekkium_shortcode_filtro_guias_modalidad');
function trekkium_shortcode_filtro_guias_modalidad() {
    $users = get_users([
        'role'    => 'guia',
        'orderby' => 'display_name',
        'order'   => 'ASC',
    ]);
    if (empty($users)) return '';

    $modalidades_disponibles = [];

    foreach ($users as $user) {
        // Cambiado: obtener términos de la taxonomía "modalidad" en lugar del campo ACF
        $modalidades = wp_get_object_terms($user->ID, 'modalidad');
        // Cambiado: obtener campo meta "comunidad_autonoma" en lugar del campo ACF
        $regiones = get_user_meta($user->ID, 'comunidad_autonoma', true);
        
        if (empty($modalidades) || is_wp_error($modalidades)) continue;
        
        $arr_modalidades = $modalidades;
        $arr_regiones = $regiones ? (is_array($regiones) ? $regiones : [$regiones]) : [];
        
        $regiones_slugs = [];
        foreach ($arr_regiones as $region) {
            if (is_object($region) && isset($region->slug)) {
                $regiones_slugs[] = $region->slug;
            } elseif (is_object($region) && isset($region->name)) {
                $regiones_slugs[] = sanitize_title($region->name);
            } elseif (is_numeric($region)) {
                $term = get_term($region);
                if ($term && !is_wp_error($term)) {
                    $regiones_slugs[] = $term->slug;
                }
            } else {
                $regiones_slugs[] = sanitize_title($region);
            }
        }
        
        foreach ($arr_modalidades as $m) {
            // Si es un término de taxonomía
            if (is_object($m) && isset($m->name)) {
                $label = $m->name;
                $slug = $m->slug;
            } elseif (is_numeric($m)) {
                $term = get_term($m);
                $label = ($term && !is_wp_error($term)) ? $term->name : (string)$m;
                $slug = ($term && !is_wp_error($term)) ? $term->slug : sanitize_title($m);
            } else {
                $label = (string)$m;
                $slug = sanitize_title($m);
            }
            $label = trim($label);
            
            if ($label !== '') {
                if (!isset($modalidades_disponibles[$slug])) {
                    $modalidades_disponibles[$slug] = [
                        'label' => $label,
                        'regiones' => $regiones_slugs
                    ];
                } else {
                    // Combinar regiones si la modalidad ya existe
                    $modalidades_disponibles[$slug]['regiones'] = array_unique(array_merge(
                        $modalidades_disponibles[$slug]['regiones'],
                        $regiones_slugs
                    ));
                }
            }
        }
    }

    if (empty($modalidades_disponibles)) return '';

    // Ordenar por label
    uasort($modalidades_disponibles, function($a, $b) {
        return strnatcasecmp($a['label'], $b['label']);
    });

    ob_start();
    ?>
    <div class="filtro-contenedor" id="filtro-modalidades">
        <?php foreach ($modalidades_disponibles as $slug => $data): ?>
            <span class="filtro-item" 
                  data-modalidad="<?php echo esc_attr($slug); ?>"
                  data-regiones="<?php echo esc_attr(implode(',', $data['regiones'])); ?>">
                <?php echo esc_html($data['label']); ?>
            </span>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

// Shortcode: Filtro por región (ahora con datos para filtrado cruzado)
add_shortcode('filtro_guias_region', 'trekkium_shortcode_filtro_guias_region');
function trekkium_shortcode_filtro_guias_region() {
    $users = get_users([
        'role'    => 'guia',
        'orderby' => 'display_name',
        'order'   => 'ASC',
    ]);
    if (empty($users)) return '';

    $regiones_disponibles = [];

    foreach ($users as $user) {
        // Cambiado: obtener campo meta "comunidad_autonoma" en lugar del campo ACF
        $regiones = get_user_meta($user->ID, 'comunidad_autonoma', true);
        // Cambiado: obtener términos de la taxonomía "modalidad" en lugar del campo ACF
        $modalidades = wp_get_object_terms($user->ID, 'modalidad');
        
        if (empty($regiones)) continue;
        
        $arr_regiones = is_array($regiones) ? $regiones : [$regiones];
        $arr_modalidades = !empty($modalidades) && !is_wp_error($modalidades) ? $modalidades : [];
        
        $modalidades_slugs = [];
        foreach ($arr_modalidades as $modalidad) {
            if (is_object($modalidad) && isset($modalidad->slug)) {
                $modalidades_slugs[] = $modalidad->slug;
            } elseif (is_object($modalidad) && isset($modalidad->name)) {
                $modalidades_slugs[] = sanitize_title($modalidad->name);
            } elseif (is_numeric($modalidad)) {
                $term = get_term($modalidad);
                if ($term && !is_wp_error($term)) {
                    $modalidades_slugs[] = $term->slug;
                }
            } else {
                $modalidades_slugs[] = sanitize_title($modalidad);
            }
        }
        
        foreach ($arr_regiones as $r) {
            if (is_object($r) && isset($r->name)) {
                $label = $r->name;
                $slug = $r->slug;
            } elseif (is_numeric($r)) {
                $term = get_term($r);
                $label = ($term && !is_wp_error($term)) ? $term->name : (string)$r;
                $slug = ($term && !is_wp_error($term)) ? $term->slug : sanitize_title($r);
            } else {
                $label = (string)$r;
                $slug = sanitize_title($r);
            }
            $label = trim($label);
            
            if ($label !== '') {
                if (!isset($regiones_disponibles[$slug])) {
                    $regiones_disponibles[$slug] = [
                        'label' => $label,
                        'modalidades' => $modalidades_slugs
                    ];
                } else {
                    // Combinar modalidades si la región ya existe
                    $regiones_disponibles[$slug]['modalidades'] = array_unique(array_merge(
                        $regiones_disponibles[$slug]['modalidades'],
                        $modalidades_slugs
                    ));
                }
            }
        }
    }

    if (empty($regiones_disponibles)) return '';

    // Ordenar por label
    uasort($regiones_disponibles, function($a, $b) {
        return strnatcasecmp($a['label'], $b['label']);
    });

    ob_start();
    ?>
    <div class="filtro-contenedor" id="filtro-regiones">
        <?php foreach ($regiones_disponibles as $slug => $data): ?>
            <span class="filtro-item" 
                  data-region="<?php echo esc_attr($slug); ?>"
                  data-modalidades="<?php echo esc_attr(implode(',', $data['modalidades'])); ?>">
                <?php echo esc_html($data['label']); ?>
            </span>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

// Script actualizado para filtros dependientes
add_action('wp_footer', function() {
    ?>
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

        document.addEventListener("DOMContentLoaded", function() {
            const modalidadBtns = Array.from(document.querySelectorAll(".filtro-item[data-modalidad]"));
            const regionBtns = Array.from(document.querySelectorAll(".filtro-item[data-region]"));
            const items = Array.from(document.querySelectorAll(".ga-query-grid .ga-query-item"));


            if (!items.length) return;

            let activeModalidad = null;
            let activeRegion = null;

            function getItemValues(item, selector, dataAttr){
                let nodes = Array.from(item.querySelectorAll(selector || ''));
                let values = nodes.map(n => n.textContent || '').map(t => normalizeTextToSlug(t.trim())).filter(Boolean);

                if (!values.length && dataAttr && item.dataset && item.dataset[dataAttr]) {
                    values = String(item.dataset[dataAttr]).split(',').map(s => normalizeTextToSlug(s.trim())).filter(Boolean);
                }

                if (!values.length) {
                    if (item.getAttribute && item.getAttribute(dataAttr)) {
                        values = String(item.getAttribute(dataAttr)).split(',').map(s => normalizeTextToSlug(s.trim())).filter(Boolean);
                    }
                }

                return values;
            }

            function updateFilterButtons() {
                // Actualizar botones de modalidad según región seleccionada
                modalidadBtns.forEach(btn => {
                    const regiones = btn.getAttribute('data-regiones') || '';
                    const regionesArray = regiones.split(',').filter(Boolean);
                    
                    if (activeRegion && !regionesArray.includes(activeRegion)) {
                        btn.classList.add('hidden');
                    } else {
                        btn.classList.remove('hidden');
                    }
                });

                // Actualizar botones de región según modalidad seleccionada
                regionBtns.forEach(btn => {
                    const modalidades = btn.getAttribute('data-modalidades') || '';
                    const modalidadesArray = modalidades.split(',').filter(Boolean);
                    
                    if (activeModalidad && !modalidadesArray.includes(activeModalidad)) {
                        btn.classList.add('hidden');
                    } else {
                        btn.classList.remove('hidden');
                    }
                });
            }

            function filtrar(){
                items.forEach(item => {
                    const itemModalidades = getItemValues(item, '.guia-modalidad-item', 'modalidades');
                    const itemRegiones = getItemValues(item, '.guia-region-item', 'regiones');

                    const matchModalidad = !activeModalidad || itemModalidades.indexOf(activeModalidad) !== -1;
                    const matchRegion = !activeRegion || itemRegiones.indexOf(activeRegion) !== -1;

                    if (matchModalidad && matchRegion) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                updateFilterButtons();
                // También actualizar la vista de filtros activos (pestañas con 'x')
                updateActiveFilters();
            }

            function updateActiveFilters() {
                const cont = document.querySelector('.ga-query-wrapper .filtros-activos .filtros-lista');
                if (!cont) return;
                cont.innerHTML = '';

                // Región activa
                if (activeRegion) {
                    // buscar el botón con ese slug para obtener el label
                    const btn = regionBtns.find(b => (b.getAttribute('data-region')||'') === activeRegion);
                    const label = btn ? btn.textContent.trim() : activeRegion;
                    const span = document.createElement('span');
                    span.className = 'filtros-activos-item';
                    span.textContent = label + ' ';
                    const a = document.createElement('a');
                    a.href = '#';
                    a.className = 'quitar-filtro';
                    a.setAttribute('data-type','region');
                    a.setAttribute('data-value', activeRegion);
                    a.textContent = '×';
                    span.appendChild(a);
                    cont.appendChild(span);
                }

                // Modalidad activa
                if (activeModalidad) {
                    const btn = modalidadBtns.find(b => (b.getAttribute('data-modalidad')||'') === activeModalidad);
                    const label = btn ? btn.textContent.trim() : activeModalidad;
                    const span = document.createElement('span');
                    span.className = 'filtros-activos-item';
                    span.textContent = label + ' ';
                    const a = document.createElement('a');
                    a.href = '#';
                    a.className = 'quitar-filtro';
                    a.setAttribute('data-type','modalidad');
                    a.setAttribute('data-value', activeModalidad);
                    a.textContent = '×';
                    span.appendChild(a);
                    cont.appendChild(span);
                }
            }

            // Delegación para quitar filtros desde las pestañas
            document.addEventListener('click', function(e){
                const target = e.target;
                if (target && target.classList && target.classList.contains('quitar-filtro')) {
                    e.preventDefault();
                    const type = target.getAttribute('data-type');
                    const value = target.getAttribute('data-value');
                    if (!type) return;

                    if (type === 'region') {
                        // desactivar botón de región
                        regionBtns.forEach(b => {
                            if ((b.getAttribute('data-region')||'') === value) b.classList.remove('active');
                        });
                        activeRegion = null;
                    } else if (type === 'modalidad') {
                        modalidadBtns.forEach(b => {
                            if ((b.getAttribute('data-modalidad')||'') === value) b.classList.remove('active');
                        });
                        activeModalidad = null;
                    }

                    filtrar();
                }
            });

            function setupButtons(buttons, type) {
                buttons.forEach(btn => {
                    btn.addEventListener('click', function(){
                        const key = type === 'modalidad' ? 'data-modalidad' : 'data-region';
                        const raw = this.getAttribute(key);
                        const value = normalizeTextToSlug(raw || '');
                        const currentlyActive = this.classList.contains('active');
                        
                        if (type === 'modalidad') {
                            // Para modalidad: toggle del estado active
                            if (currentlyActive) {
                                this.classList.remove('active');
                                activeModalidad = null;
                            } else {
                                // Remover active de otros botones de modalidad
                                modalidadBtns.forEach(b => b.classList.remove('active'));
                                this.classList.add('active');
                                activeModalidad = value;
                            }
                        } else {
                            // Para región: toggle del estado active
                            if (currentlyActive) {
                                this.classList.remove('active');
                                activeRegion = null;
                            } else {
                                // Remover active de otros botones de región
                                regionBtns.forEach(b => b.classList.remove('active'));
                                this.classList.add('active');
                                activeRegion = value;
                            }
                        }
                        
                        filtrar();
                    });
                });
            }

            setupButtons(modalidadBtns, 'modalidad');
            setupButtons(regionBtns, 'region');

            // Inicializar
            filtrar();
        });
    })();
    </script>
    <?php
});