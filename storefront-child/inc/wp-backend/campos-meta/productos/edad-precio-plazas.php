<?php
// 1️⃣ Registro de meta fields
add_action('init', function() {
    $fields = [
        'edad_minima'     => 'number',
        'plazas_totales'  => 'number',
        'plazas_minimas'  => 'number',
        'precio_guia'     => 'number',
        'precio'          => 'number', // PVP final
    ];

    foreach ($fields as $key => $type) {
        register_post_meta('product', $key, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => $type,
            'auth_callback' => function() {
                return current_user_can('edit_products');
            },
        ]);
    }
});

// 2️⃣ Crear el metabox para productos
add_action('add_meta_boxes', function() {
    add_meta_box(
        'precio_plazas_metabox',
        'Precio y plazas',
        'precio_plazas_metabox_callback',
        'product',
        'side',
        'default'
    );
});

// Callback del metabox
function precio_plazas_metabox_callback($post) {
    $edad_minima    = get_post_meta($post->ID, 'edad_minima', true);
    if ($edad_minima === '' || $edad_minima === null) {
        $edad_minima = 18;
    }
    $plazas_totales = get_post_meta($post->ID, 'plazas_totales', true);
    $plazas_minimas = get_post_meta($post->ID, 'plazas_minimas', true);
    $precio_guia    = get_post_meta($post->ID, 'precio_guia', true);
    $wc_precio      = get_post_meta($post->ID, '_price', true);
    $pvp_final      = floatval($wc_precio) + floatval($precio_guia);

    wp_nonce_field('guardar_precio_plazas', 'precio_plazas_nonce');
    ?>
    <style>
        .precio-plazas-grid { 
            display: grid; 
            grid-template-columns: repeat(5, 1fr); 
            gap: 10px; 
            margin-bottom: 8px; 
        }
        .field-group { display: flex; flex-direction: column; }
        .field-group label { margin-bottom: 4px; font-weight: 600; font-size: 11px; color: #1d2327; }
        .precio-plazas-input { width: 100%; padding: 5px 6px; border: 1px solid #8c8f94; border-radius: 4px; }
        .precio-plazas-input:focus { border-color: #007cba; box-shadow: 0 0 0 1px #007cba; outline: none; }
    </style>

    <div class="precio-plazas-grid">

        <!-- Edad mínima -->
        <div class="field-group">
            <label for="edad_minima_field">Edad mínima</label>
            <input type="number" id="edad_minima_field" name="edad_minima_field"
                   value="<?php echo esc_attr($edad_minima); ?>" step="1" min="0" class="precio-plazas-input" />
        </div>

        <!-- Plazas totales -->
        <div class="field-group">
            <label for="plazas_totales_field">Plazas totales</label>
            <input type="number" id="plazas_totales_field" name="plazas_totales_field"
                   value="<?php echo esc_attr($plazas_totales); ?>" step="1" min="0" class="precio-plazas-input" />
        </div>

        <!-- Plazas mínimas -->
        <div class="field-group">
            <label for="plazas_minimas_field">Plazas mínimas</label>
            <input type="number" id="plazas_minimas_field" name="plazas_minimas_field"
                   value="<?php echo esc_attr($plazas_minimas); ?>" step="1" min="0" class="precio-plazas-input" />
        </div>

        <!-- Precio guía -->
        <div class="field-group">
            <label for="precio_guia_field">Precio guía (€)</label>
            <input type="number" id="precio_guia_field" name="precio_guia_field"
                   value="<?php echo esc_attr($precio_guia); ?>" step="0.01" min="0" class="precio-plazas-input" />
        </div>

        <!-- PVP final -->
        <div class="field-group">
            <label for="precio_field">PVP final (€)</label>
            <input type="number" id="precio_field" name="precio_field"
                   value="<?php echo esc_attr($pvp_final); ?>" step="0.01" min="0"
                   class="precio-plazas-input" readonly />
        </div>
    </div>
    <?php
}

// 3️⃣ Guardar meta fields y recalcular precios
add_action('save_post', function($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['precio_plazas_nonce']) || !wp_verify_nonce($_POST['precio_plazas_nonce'], 'guardar_precio_plazas')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $edad_minima    = isset($_POST['edad_minima_field']) ? intval($_POST['edad_minima_field']) : 0;
    $plazas_totales = isset($_POST['plazas_totales_field']) ? intval($_POST['plazas_totales_field']) : 0;
    $plazas_minimas = isset($_POST['plazas_minimas_field']) ? intval($_POST['plazas_minimas_field']) : 0;
    $precio_guia    = isset($_POST['precio_guia_field']) ? floatval($_POST['precio_guia_field']) : 0;

    update_post_meta($post_id, 'edad_minima', $edad_minima);
    update_post_meta($post_id, 'plazas_totales', $plazas_totales);
    update_post_meta($post_id, 'plazas_minimas', $plazas_minimas);
    update_post_meta($post_id, 'precio_guia', $precio_guia);

    // 🔹 Calcular precio base según plazas_totales
    $precio_base = 0;
    switch ($plazas_totales) {
        case 1:  $precio_base = 30.00; break;
        case 2:  $precio_base = 20.00; break;
        case 3:  $precio_base = 17.50; break;
        case 4:  $precio_base = 15.00; break;
        case 5:  $precio_base = 13.00; break;
        case 6:  $precio_base = 12.00; break;
        case 7:  $precio_base = 11.00; break;
        case 8:  $precio_base = 10.00; break;
        case 9:  $precio_base = 9.00;  break;
        case 10: $precio_base = 8.00;  break;
        case 11: $precio_base = 7.50;  break;
        case 12: $precio_base = 7.00;  break;
        case 13: $precio_base = 6.50;  break;
        default: if ($plazas_totales >= 14) $precio_base = 6.00; break;
    }

    // Guardar en WooCommerce
    update_post_meta($post_id, '_price', $precio_base);
    update_post_meta($post_id, '_regular_price', $precio_base);
    wc_delete_product_transients($post_id);

    // 🔹 PVP final = precio_base + precio_guia
    $pvp_final = $precio_base + $precio_guia;
    update_post_meta($post_id, 'precio', $pvp_final);

}, 25);

// 4️⃣ JS para actualizar PVP final y _price en tiempo real
add_action('admin_footer-post.php', 'actualizar_pvp_final_js');
add_action('admin_footer-post-new.php', 'actualizar_pvp_final_js');

function actualizar_pvp_final_js() {
    global $post_type;
    if ($post_type !== 'product') return;
    ?>
    <script>
    (function($){
        function calcularPrecioBase(plazas) {
            plazas = parseInt(plazas) || 0;
            switch (plazas) {
                case 1: return 30.00;
                case 2: return 20.00;
                case 3: return 17.50;
                case 4: return 15.00;
                case 5: return 13.00;
                case 6: return 12.00;
                case 7: return 11.00;
                case 8: return 10.00;
                case 9: return 9.00;
                case 10: return 8.00;
                case 11: return 7.50;
                case 12: return 7.00;
                case 13: return 6.50;
                default: return plazas >= 14 ? 6.00 : 0;
            }
        }

        function actualizarPrecios() {
            let plazasTotales = $('#plazas_totales_field').val() || 0;
            let precioBase = calcularPrecioBase(plazasTotales);

            $('#_price').val(precioBase.toFixed(2));
            $('#_regular_price').val(precioBase.toFixed(2));

            let precioGuia = parseFloat($('#precio_guia_field').val()) || 0;
            $('#precio_field').val((precioBase + precioGuia).toFixed(2));
        }

        $(document).ready(function() {
            $('#plazas_totales_field, #precio_guia_field').on('input', actualizarPrecios);
            actualizarPrecios(); // al cargar
        });
    })(jQuery);
    </script>
    <?php
}
