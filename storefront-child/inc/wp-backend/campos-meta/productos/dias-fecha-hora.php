<?php
// Registrar metabox para Fecha y Hora
add_action('add_meta_boxes', function() {
    add_meta_box(
        'fecha_hora_meta_box',
        'Fecha y Hora',
        'render_fecha_hora_meta_box',
        'product',
        'normal',
        'default'
    );
});

// Renderizar metabox de Fecha y Hora
function render_fecha_hora_meta_box($post) {
    $dias = get_post_meta($post->ID, 'dias', true) ?: 1; // Siempre mínimo 1
    $fecha = get_post_meta($post->ID, 'fecha', true);
    $fecha_fin = get_post_meta($post->ID, 'fecha_fin', true);

    wp_nonce_field('fecha_hora_nonce', 'fecha_hora_nonce_field');
    ?>
    <div style="margin: 15px 0;">
        <div style="display: flex; gap: 20px;">
            <!-- Columna Días -->
            <div style="flex:1; min-width:100px;">
                <label for="dias" style="display:block; margin-bottom:5px; font-weight:bold;">Días</label>
                <input type="number" id="dias" name="dias" value="<?php echo esc_attr($dias); ?>" min="1" step="1" style="width:100%;">
            </div>

            <!-- Columna Fecha inicio -->
            <div style="flex:1; min-width:150px;">
                <label for="fecha" style="display:block; margin-bottom:5px; font-weight:bold;">Fecha</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo esc_attr($fecha); ?>" style="width:100%;">
            </div>

            <!-- Columna Fecha fin -->
            <div style="flex:1; min-width:150px;">
                <label for="fecha_fin" style="display:block; margin-bottom:5px; font-weight:bold;">Fecha Fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo esc_attr($fecha_fin); ?>" style="width:100%;">
            </div>

            <!-- Columna Hora -->
            <div style="flex:1; min-width:100px;">
                <label for="hora" style="display:block; margin-bottom:5px; font-weight:bold;">Hora</label>
                <input type="time" id="hora" name="hora" value="<?php echo esc_attr(get_post_meta($post->ID, 'hora', true)); ?>" style="width:100%;">
            </div>
        </div>
    </div>

    <!-- JS para controlar dinámicamente fecha_fin -->
    <script>
    (function($){
        function actualizarFechaFin() {
            const dias = parseInt($('#dias').val()) || 1;
            const fechaInicio = $('#fecha').val();
            const fechaFin = $('#fecha_fin');

            if (dias === 1) {
                fechaFin.val(fechaInicio);
                fechaFin.prop('disabled', true);
            } else {
                fechaFin.val('');
                fechaFin.prop('disabled', false);
            }
        }

        $(document).ready(function(){
            actualizarFechaFin();

            $('#dias, #fecha').on('input change', function(){
                actualizarFechaFin();
            });
        });
    })(jQuery);
    </script>
    <?php
}

// Guardar campos meta
add_action('save_post', function($post_id) {
    if (!isset($_POST['fecha_hora_nonce_field']) || !wp_verify_nonce($_POST['fecha_hora_nonce_field'], 'fecha_hora_nonce')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $campos = ['dias', 'fecha', 'fecha_fin', 'hora'];
    foreach ($campos as $campo) {
        if (isset($_POST[$campo])) {
            if ($campo === 'dias') {
                update_post_meta($post_id, $campo, intval($_POST[$campo]));
            } else {
                update_post_meta($post_id, $campo, sanitize_text_field($_POST[$campo]));
            }
        }
    }

    // Si días <= 1, sincronizar fecha_fin con fecha
    if (isset($_POST['dias']) && intval($_POST['dias']) <= 1 && isset($_POST['fecha'])) {
        update_post_meta($post_id, 'fecha_fin', sanitize_text_field($_POST['fecha']));
    }
});
