<?php
/*
Plugin Name: Ubicaciones Jerárquicas Productos
Description: Tres taxonomías separadas (pais, region, provincia) con selects dependientes y validación, con mismas características que la taxonomía Tipo.
Version: 1.2
Author: Toni
*/

// === MAPA DE UBICACIONES ===
function trekkium_get_ubicaciones() {
    return [
        'Andorra' => [
            'Andorra' => [
                'Andorra la Vella', 'Canillo', 'Encamp', 'Escaldes-Engordany', 
                'La Massana', 'Ordino', 'Sant Julià de Lòria',
            ],
        ],
        'España' => [
            'Andalucía' => ['Almería', 'Cádiz', 'Córdoba', 'Granada', 'Huelva', 'Jaén', 'Málaga', 'Sevilla'],
            'Aragón' => ['Huesca', 'Teruel', 'Zaragoza'],
            'Asturias' => ['Asturias'],
            'Baleares' => ['Baleares'],
            'Canarias' => ['Las Palmas', 'Santa Cruz de Tenerife'],
            'Cantabria' => ['Cantabria'],
            'Castilla y León' => ['Ávila', 'Burgos', 'León', 'Palencia', 'Salamanca', 'Segovia', 'Soria', 'Valladolid', 'Zamora'],
            'Castilla-La Mancha' => ['Albacete', 'Ciudad Real', 'Cuenca', 'Guadalajara', 'Toledo'],
            'Catalunya' => ['Barcelona', 'Girona', 'Lleida', 'Tarragona'],
            'Comunidad Valenciana' => ['Alicante', 'Castellón', 'Valencia'],
            'Extremadura' => ['Badajoz', 'Cáceres'],
            'Galicia' => ['A Coruña', 'Lugo', 'Ourense', 'Pontevedra'],
            'Madrid' => ['Madrid'],
            'Murcia' => ['Murcia'],
            'Navarra' => ['Navarra'],
            'País Vasco' => ['Álava', 'Bizkaia', 'Gipuzkoa'],
            'La Rioja' => ['La Rioja'],
            'Ceuta' => ['Ceuta'],
            'Melilla' => ['Melilla'],
        ],
        'Francia' => [
            'Nouvelle-Aquitaine' => [
                'Charente', 'Charente-Maritime', 'Corrèze', 'Creuse', 'Deux-Sèvres', 
                'Dordogne', 'Gironde', 'Landes', 'Lot-et-Garonne', 'Pyrénées-Atlantiques', 
                'Vienne', 'Haute-Vienne'
            ],
            'Occitanie' => [
                'Ariège', 'Aude', 'Aveyron', 'Gard', 'Haute-Garonne', 'Gers', 
                'Hérault', 'Lot', 'Lozère', 'Hautes-Pyrénées', 'Pyrénées-Orientales', 
                'Tarn', 'Tarn-et-Garonne'
            ],
        ],
        'Portugal' => [
            'Norte' => ['Aveiro', 'Braga', 'Bragança', 'Porto', 'Viana do Castelo', 'Vila Real'],
            'Centro' => ['Castelo Branco', 'Coimbra', 'Guarda', 'Leiria', 'Santarém', 'Viseu'],
            'Lisboa' => ['Lisboa', 'Setúbal'],
            'Alentejo' => ['Beja', 'Évora', 'Portalegre'],
            'Algarve' => ['Faro'],
            'Madeira' => ['Madeira'],
            'Açores' => ['Açores'],
        ],
    ];
}

// === REGISTRAR TAXONOMÍAS ===
function trekkium_register_ubicaciones() {
    $taxonomies = [
        'pais' => 'Países',
        'region' => 'Regiones',
        'provincia' => 'Provincias'
    ];

    foreach ($taxonomies as $slug => $name) {
        $labels = [
            'name'                       => $name,
            'singular_name'              => rtrim($name, 's'),
            'menu_name'                  => $name,
            'all_items'                  => 'Todos los ' . $name,
            'parent_item'                => $slug === 'pais' ? '' : 'Padre ' . rtrim($name, 's'),
            'parent_item_colon'          => $slug === 'pais' ? '' : 'Padre ' . rtrim($name, 's') . ':',
            'new_item_name'              => 'Nuevo ' . rtrim($name, 's'),
            'add_new_item'               => 'Añadir Nuevo ' . rtrim($name, 's'),
            'edit_item'                  => 'Editar ' . rtrim($name, 's'),
            'update_item'                => 'Actualizar ' . rtrim($name, 's'),
            'view_item'                  => 'Ver ' . rtrim($name, 's'),
            'separate_items_with_commas' => 'Separar ' . $name . ' con comas',
            'add_or_remove_items'        => 'Añadir o eliminar ' . $name,
            'choose_from_most_used'      => 'Elegir de los más usados',
            'popular_items'              => $name . ' populares',
            'search_items'               => 'Buscar ' . $name,
            'not_found'                  => 'No encontrado',
            'no_terms'                   => 'No hay ' . $name,
            'items_list'                 => 'Lista de ' . $name,
            'items_list_navigation'      => 'Navegación de la lista de ' . $name,
        ];

        $args = [
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_in_rest'               => true,
            'show_tagcloud'              => true,
            'show_in_quick_edit'         => true,
        ];

        register_taxonomy($slug, 'product', $args);
    }

    // Insertar términos automáticamente
    $ubicaciones = trekkium_get_ubicaciones();
    foreach ($ubicaciones as $pais => $regiones) {
        if (!term_exists($pais, 'pais')) {
            wp_insert_term($pais, 'pais');
        }
        foreach ($regiones as $region => $provincias) {
            if (!term_exists($region, 'region')) {
                wp_insert_term($region, 'region');
            }
            foreach ($provincias as $provincia) {
                if (!term_exists($provincia, 'provincia')) {
                    wp_insert_term($provincia, 'provincia');
                }
            }
        }
    }
}
add_action('init', 'trekkium_register_ubicaciones', 0);

// === METABOX UBICACIÓN DE LA ACTIVIDAD ===
add_action('add_meta_boxes', function () {
    add_meta_box(
        'ubicacion_actividad',
        'Ubicación de la actividad',
        'trekkium_render_ubicacion_actividad_metabox',
        'product',
        'normal',
        'default'
    );
});

function trekkium_render_ubicacion_actividad_metabox($post) {
    // Obtener términos actuales del producto
    $pais_actual = wp_get_post_terms($post->ID, 'pais', ['fields' => 'names']);
    $region_actual = wp_get_post_terms($post->ID, 'region', ['fields' => 'names']);
    $provincia_actual = wp_get_post_terms($post->ID, 'provincia', ['fields' => 'names']);
    
    $pais_actual = $pais_actual ? $pais_actual[0] : '';
    $region_actual = $region_actual ? $region_actual[0] : '';
    $provincia_actual = $provincia_actual ? $provincia_actual[0] : '';

    // Obtener todas las ubicaciones para dependencias
    $ubicaciones = trekkium_get_ubicaciones();

    wp_nonce_field('trekkium_guardar_ubicacion_actividad', 'trekkium_ubicacion_actividad_nonce');
    ?>
    <style>
        .trekkium-ubicacion-container { 
            display: flex; 
            gap: 15px; 
            width: 100%;
            margin-bottom: 15px;
        }
        .trekkium-ubicacion-field { 
            flex: 1; 
            min-width: 0; 
        }
        .trekkium-ubicacion-field label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            font-size: 14px;
        }
        .trekkium-ubicacion-select { 
            width: 100%; 
            box-sizing: border-box;
            padding: 8px;
            height: 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
        }
        .trekkium-ubicacion-select:focus {
            border-color: #007cba;
            box-shadow: 0 0 0 1px #007cba;
            outline: none;
        }
        @media(max-width: 1024px){ 
            .trekkium-ubicacion-container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>

    <div class="trekkium-ubicacion-container">
        <div class="trekkium-ubicacion-field">
            <label for="trekkium_ubicacion_pais">País</label>
            <select id="trekkium_ubicacion_pais" name="trekkium_ubicacion_pais" class="trekkium-ubicacion-select">
                <option value="">-- Selecciona País --</option>
                <?php foreach ($ubicaciones as $pais => $regiones): ?>
                    <option value="<?php echo esc_attr($pais); ?>" <?php selected($pais_actual, $pais); ?>>
                        <?php echo esc_html($pais); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="trekkium-ubicacion-field">
            <label for="trekkium_ubicacion_region">Región</label>
            <select id="trekkium_ubicacion_region" name="trekkium_ubicacion_region" class="trekkium-ubicacion-select">
                <option value="">-- Selecciona Región --</option>
                <?php if ($pais_actual && isset($ubicaciones[$pais_actual])): ?>
                    <?php foreach ($ubicaciones[$pais_actual] as $region => $provincias): ?>
                        <option value="<?php echo esc_attr($region); ?>" <?php selected($region_actual, $region); ?>>
                            <?php echo esc_html($region); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="trekkium-ubicacion-field">
            <label for="trekkium_ubicacion_provincia">Provincia</label>
            <select id="trekkium_ubicacion_provincia" name="trekkium_ubicacion_provincia" class="trekkium-ubicacion-select">
                <option value="">-- Selecciona Provincia --</option>
                <?php if ($pais_actual && $region_actual && isset($ubicaciones[$pais_actual][$region_actual])): ?>
                    <?php foreach ($ubicaciones[$pais_actual][$region_actual] as $provincia): ?>
                        <option value="<?php echo esc_attr($provincia); ?>" <?php selected($provincia_actual, $provincia); ?>>
                            <?php echo esc_html($provincia); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>

    <script>
    (function($){
        const data = <?php echo wp_json_encode($ubicaciones); ?>;
        
        // Variables para almacenar los valores actuales
        const paisActual = '<?php echo esc_js($pais_actual); ?>';
        const regionActual = '<?php echo esc_js($region_actual); ?>';
        const provinciaActual = '<?php echo esc_js($provincia_actual); ?>';

        function actualizarRegiones(paisSeleccionado = null) {
            let pais = paisSeleccionado || $('#trekkium_ubicacion_pais').val();
            let $region = $('#trekkium_ubicacion_region');
            let $provincia = $('#trekkium_ubicacion_provincia');
            
            // Guardar el valor actual de región antes de actualizar
            let regionValorActual = $region.val();
            
            $region.html('<option value="">-- Selecciona Región --</option>');
            $provincia.html('<option value="">-- Selecciona Provincia --</option>');
            
            if(pais && data[pais]){
                $.each(data[pais], function(region, provincias){
                    $region.append('<option value="'+region+'">'+region+'</option>');
                });
                
                // Restaurar el valor de región si existe y es válido
                if (regionValorActual && data[pais][regionValorActual]) {
                    $region.val(regionValorActual);
                    // Actualizar provincias después de restaurar región
                    actualizarProvincias(pais, regionValorActual);
                }
            }
        }

        function actualizarProvincias(paisSeleccionado = null, regionSeleccionada = null) {
            let pais = paisSeleccionado || $('#trekkium_ubicacion_pais').val();
            let region = regionSeleccionada || $('#trekkium_ubicacion_region').val();
            let $provincia = $('#trekkium_ubicacion_provincia');
            
            // Guardar el valor actual de provincia antes de actualizar
            let provinciaValorActual = $provincia.val();
            
            $provincia.html('<option value="">-- Selecciona Provincia --</option>');
            
            if(pais && region && data[pais] && data[pais][region]){
                $.each(data[pais][region], function(i, prov){
                    $provincia.append('<option value="'+prov+'">'+prov+'</option>');
                });
                
                // Restaurar el valor de provincia si existe y es válido
                if (provinciaValorActual && data[pais][region].includes(provinciaValorActual)) {
                    $provincia.val(provinciaValorActual);
                }
            }
        }

        $('#trekkium_ubicacion_pais').on('change', function(){
            actualizarRegiones($(this).val());
        });

        $('#trekkium_ubicacion_region').on('change', function(){
            actualizarProvincias(null, $(this).val());
        });

        // Inicializar los selects cuando el documento esté listo
        $(document).ready(function() {
            // Si hay un país seleccionado, cargar sus regiones
            if (paisActual) {
                $('#trekkium_ubicacion_pais').val(paisActual);
                actualizarRegiones(paisActual);
                
                // Si hay una región seleccionada, cargar sus provincias
                if (regionActual) {
                    // Esperar un momento para que se carguen las regiones
                    setTimeout(function() {
                        $('#trekkium_ubicacion_region').val(regionActual);
                        actualizarProvincias(paisActual, regionActual);
                        
                        // Si hay una provincia seleccionada, establecerla
                        if (provinciaActual) {
                            setTimeout(function() {
                                $('#trekkium_ubicacion_provincia').val(provinciaActual);
                            }, 100);
                        }
                    }, 100);
                }
            }
        });

    })(jQuery);
    </script>
    <?php
}

// === GUARDAR LOS TÉRMINOS ===
add_action('save_post_product', 'trekkium_guardar_ubicacion_actividad');

function trekkium_guardar_ubicacion_actividad($post_id) {
    // Verificar nonce
    if (!isset($_POST['trekkium_ubicacion_actividad_nonce']) || 
        !wp_verify_nonce($_POST['trekkium_ubicacion_actividad_nonce'], 'trekkium_guardar_ubicacion_actividad')) {
        return;
    }

    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Evitar autoguardado
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $ubicaciones = trekkium_get_ubicaciones();

    // Sanitizar datos
    $pais = sanitize_text_field($_POST['trekkium_ubicacion_pais'] ?? '');
    $region = sanitize_text_field($_POST['trekkium_ubicacion_region'] ?? '');
    $provincia = sanitize_text_field($_POST['trekkium_ubicacion_provincia'] ?? '');

    // Validar coherencia de datos
    $es_valido = false;
    if ($pais && $region && $provincia) {
        if (isset($ubicaciones[$pais][$region]) && in_array($provincia, $ubicaciones[$pais][$region])) {
            $es_valido = true;
        }
    }

    // Guardar o limpiar términos
    if ($es_valido) {
        // Asegurarse de que los términos existen
        if (!term_exists($pais, 'pais')) {
            wp_insert_term($pais, 'pais');
        }
        if (!term_exists($region, 'region')) {
            wp_insert_term($region, 'region');
        }
        if (!term_exists($provincia, 'provincia')) {
            wp_insert_term($provincia, 'provincia');
        }

        // Asignar términos al producto
        wp_set_object_terms($post_id, $pais, 'pais', false);
        wp_set_object_terms($post_id, $region, 'region', false);
        wp_set_object_terms($post_id, $provincia, 'provincia', false);
    } else {
        // Limpiar términos si la selección no es válida
        wp_set_object_terms($post_id, [], 'pais');
        wp_set_object_terms($post_id, [], 'region');
        wp_set_object_terms($post_id, [], 'provincia');
    }
}

// === ELIMINAR METABOX DUPLICADO ===
add_action('add_meta_boxes', function() {
    remove_meta_box('ubicacion_box', 'product', 'side');
}, 20);