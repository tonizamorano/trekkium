<?php
// === METABOX SOLICITAR CAMBIO ===
add_action('add_meta_boxes', function () {
    add_meta_box(
        'solicitar_cambio_actividad',
        'Solicitar cambio de actividad',
        'trekkium_render_solicitar_cambio_metabox',
        'product',
        'normal',
        'default'
    );
});

function trekkium_render_solicitar_cambio_metabox($post) {
    $ubicaciones = trekkium_get_ubicaciones();

    // Obtener valores actuales de la actividad y de la solicitud
    $pais_actual = wp_get_post_terms($post->ID, 'pais', ['fields' => 'names']);
    $pais_actual = $pais_actual[0] ?? '';

    $region_actual = wp_get_post_terms($post->ID, 'region', ['fields' => 'names']);
    $region_actual = $region_actual[0] ?? '';

    $provincia_actual = wp_get_post_terms($post->ID, 'provincia', ['fields' => 'names']);
    $provincia_actual = $provincia_actual[0] ?? '';

    $entorno_natural_actual = get_post_meta($post->ID, 'espacio_natural', true);

    // Meta de la solicitud
    $tipo_cambio = get_post_meta($post->ID, 'tipo_cambio', true);
    $nueva_fecha = get_post_meta($post->ID, 'nueva_fecha', true);
    $pais_cambio = get_post_meta($post->ID, 'pais_cambio', true) ?: $pais_actual;
    $region_cambio = get_post_meta($post->ID, 'region_cambio', true) ?: $region_actual;
    $provincia_cambio = get_post_meta($post->ID, 'provincia_cambio', true) ?: $provincia_actual;
    $entorno_natural_cambio = get_post_meta($post->ID, 'entorno_natural_cambio', true) ?: $entorno_natural_actual;
    $motivo_cambio = get_post_meta($post->ID, 'motivo_cambio', true);
    $estado_solicitud = get_post_meta($post->ID, 'estado_solicitud', true) ?: 'Pendiente';
    $fecha_solicitud = get_post_meta($post->ID, 'fecha_solicitud', true);

    wp_nonce_field('trekkium_guardar_solicitud_cambio', 'trekkium_solicitud_cambio_nonce');

    ?>
    <style>
        .trekkium-cambio-container { display: flex; flex-direction: column; gap: 12px; }
        .trekkium-cambio-field label { font-weight: 600; display: block; margin-bottom: 4px; }
        .trekkium-cambio-field select, .trekkium-cambio-field input, .trekkium-cambio-field textarea {
            width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px;
        }
    </style>

    <div class="trekkium-cambio-container">

        <div class="trekkium-cambio-field">
            <label for="tipo_cambio">Tipo de cambio</label>
            <select name="tipo_cambio" id="tipo_cambio">
                <option value="">-- Selecciona tipo --</option>
                <option value="Fecha" <?php selected($tipo_cambio, 'Fecha'); ?>>Fecha</option>
                <option value="Ubicación" <?php selected($tipo_cambio, 'Ubicación'); ?>>Ubicación</option>
                <option value="Cancelación" <?php selected($tipo_cambio, 'Cancelación'); ?>>Cancelación</option>
            </select>
        </div>

        <div class="trekkium-cambio-field" id="campo_fecha" style="display: <?php echo $tipo_cambio=='Fecha'?'block':'none'; ?>">
            <label for="nueva_fecha">Nueva fecha</label>
            <input type="date" name="nueva_fecha" id="nueva_fecha" value="<?php echo esc_attr($nueva_fecha); ?>">
        </div>

        <div class="trekkium-cambio-field" id="campo_ubicacion" style="display: <?php echo $tipo_cambio=='Ubicación'?'block':'none'; ?>">
            <label>Nueva ubicación</label>

            <select id="pais_cambio" name="pais_cambio">
                <option value="">-- País --</option>
                <?php foreach($ubicaciones as $pais => $regiones): ?>
                    <option value="<?php echo esc_attr($pais); ?>" <?php selected($pais_cambio, $pais); ?>><?php echo esc_html($pais); ?></option>
                <?php endforeach; ?>
            </select>

            <select id="region_cambio" name="region_cambio">
                <option value="">-- Región --</option>
                <?php if($pais_cambio && isset($ubicaciones[$pais_cambio])): ?>
                    <?php foreach($ubicaciones[$pais_cambio] as $region => $provincias): ?>
                        <option value="<?php echo esc_attr($region); ?>" <?php selected($region_cambio, $region); ?>><?php echo esc_html($region); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <select id="provincia_cambio" name="provincia_cambio">
                <option value="">-- Provincia --</option>
                <?php if($pais_cambio && $region_cambio && isset($ubicaciones[$pais_cambio][$region_cambio])): ?>
                    <?php foreach($ubicaciones[$pais_cambio][$region_cambio] as $prov): ?>
                        <option value="<?php echo esc_attr($prov); ?>" <?php selected($provincia_cambio, $prov); ?>><?php echo esc_html($prov); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <!-- NUEVO CAMPO ENTORNO NATURAL -->
            <input type="text" name="entorno_natural_cambio" id="entorno_natural_cambio" placeholder="Entorno natural" value="<?php echo esc_attr($entorno_natural_cambio); ?>">
        </div>

        <div class="trekkium-cambio-field">
            <label for="motivo_cambio">Motivo del cambio</label>
            <textarea name="motivo_cambio" id="motivo_cambio" rows="3"><?php echo esc_textarea($motivo_cambio); ?></textarea>
        </div>

        <div class="trekkium-cambio-field">
            <label for="estado_solicitud">Estado de la solicitud</label>
            <select name="estado_solicitud" id="estado_solicitud">
                <?php 
                $estados = ['Pendiente', 'Aprobada', 'Rechazada']; 
                foreach($estados as $estado_option): ?>
                    <option value="<?php echo esc_attr($estado_option); ?>" <?php selected($estado_solicitud, $estado_option); ?>>
                        <?php echo esc_html($estado_option); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>

    <script>
    (function($){
        function mostrarCampos() {
            let tipo = $('#tipo_cambio').val();
            $('#campo_fecha').toggle(tipo=='Fecha');
            $('#campo_ubicacion').toggle(tipo=='Ubicación');
        }
        $('#tipo_cambio').on('change', mostrarCampos);
        $(document).ready(mostrarCampos);

        const ubicaciones = <?php echo wp_json_encode($ubicaciones); ?>;

        function actualizarRegiones(pais) {
            let $region = $('#region_cambio'), $provincia = $('#provincia_cambio');
            let region_val = $region.val();
            $region.html('<option value="">-- Región --</option>');
            $provincia.html('<option value="">-- Provincia --</option>');
            if(pais && ubicaciones[pais]){
                $.each(ubicaciones[pais], function(region){ 
                    $region.append('<option value="'+region+'">'+region+'</option>');
                });
                if(region_val) $region.val(region_val).trigger('change');
            }
        }

        function actualizarProvincias(pais, region) {
            let $provincia = $('#provincia_cambio');
            let prov_val = $provincia.val();
            $provincia.html('<option value="">-- Provincia --</option>');
            if(pais && region && ubicaciones[pais][region]){
                $.each(ubicaciones[pais][region], function(i, prov){
                    $provincia.append('<option value="'+prov+'">'+prov+'</option>');
                });
                if(prov_val) $provincia.val(prov_val);
            }
        }

        $('#pais_cambio').on('change', function(){ actualizarRegiones($(this).val()); });
        $('#region_cambio').on('change', function(){ actualizarProvincias($('#pais_cambio').val(), $(this).val()); });
    })(jQuery);
    </script>
    <?php
}

// === GUARDAR DATOS DE SOLICITUD ===
add_action('save_post_product', function($post_id){
    if(!isset($_POST['trekkium_solicitud_cambio_nonce']) || !wp_verify_nonce($_POST['trekkium_solicitud_cambio_nonce'],'trekkium_guardar_solicitud_cambio')) return;
    if(!current_user_can('edit_post', $post_id)) return;
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    update_post_meta($post_id, 'tipo_cambio', sanitize_text_field($_POST['tipo_cambio'] ?? ''));
    update_post_meta($post_id, 'nueva_fecha', sanitize_text_field($_POST['nueva_fecha'] ?? ''));
    update_post_meta($post_id, 'pais_cambio', sanitize_text_field($_POST['pais_cambio'] ?? ''));
    update_post_meta($post_id, 'region_cambio', sanitize_text_field($_POST['region_cambio'] ?? ''));
    update_post_meta($post_id, 'provincia_cambio', sanitize_text_field($_POST['provincia_cambio'] ?? ''));
    update_post_meta($post_id, 'entorno_natural_cambio', sanitize_text_field($_POST['entorno_natural_cambio'] ?? ''));
    update_post_meta($post_id, 'motivo_cambio', sanitize_textarea_field($_POST['motivo_cambio'] ?? ''));
    update_post_meta($post_id,'estado_solicitud',sanitize_text_field($_POST['estado_solicitud'] ?? 'Pendiente'));
    update_post_meta($post_id,'fecha_solicitud',current_time('mysql'));
});
