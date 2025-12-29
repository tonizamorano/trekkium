<?php
// ---------------------------------------------
// SHORTCODE FRONTEND: Solicitar cambios actividad
// ---------------------------------------------
add_shortcode('mc_ma_da_cambios_actividad', 'mc_ma_da_cambios_actividad_render');

function mc_ma_da_cambios_actividad_render($atts) {

    if (!is_user_logged_in()) return '';

    $atts = shortcode_atts([
        'id' => 0,
    ], $atts, 'mc_ma_da_cambios_actividad');

    $actividad_id = intval($atts['id']);
    if (!$actividad_id) return '';

    // Cargar ubicaciones
    if (!function_exists('trekkium_get_ubicaciones')) return "<p>Error: faltan ubicaciones.</p>";
    $ubicaciones = trekkium_get_ubicaciones();

    ob_start(); ?>

    <div class="mc-ma-da-contendor">
        
        <div>
            
            <?php
            // Obtener valores almacenados de cambios
            $tipo_cambio     = get_post_meta($actividad_id, 'tipo_cambio', true);
            $motivo_cambio   = get_post_meta($actividad_id, 'motivo_cambio', true);
            $estado_cambio   = get_post_meta($actividad_id, 'estado_solicitud', true) ?: 'Pendiente';

            // Fecha
            $nueva_fecha     = get_post_meta($actividad_id, 'nueva_fecha', true);

            // Ubicación
            $pais_cambio     = get_post_meta($actividad_id, 'pais_cambio', true);
            $region_cambio   = get_post_meta($actividad_id, 'region_cambio', true);
            $provincia_cambio= get_post_meta($actividad_id, 'provincia_cambio', true);
            $entorno_natural = get_post_meta($actividad_id, 'entorno_natural_cambio', true);

            // Mostrar solo si hay tipo de cambio
            if (!empty($tipo_cambio)) : ?>
                
                <div class="mc-solicitud-info">
                    
                    <h3 class="mc-solicitud-titulo">
                        <?php
                        if ($tipo_cambio === 'Fecha')        echo "Cambio de fecha solicitado";
                        elseif ($tipo_cambio === 'Ubicación') echo "Cambio de ubicación solicitado";
                        elseif ($tipo_cambio === 'Cancelación') echo "Cancelación solicitada";
                        ?>
                    </h3>

                    <?php if ($tipo_cambio === 'Fecha') : ?>

                        <p style="margin-bottom:0 !important;"><strong>Nueva fecha:</strong></p>
                        <p>
                        <?php 
                        if ($nueva_fecha) {
                            $fecha_obj = DateTime::createFromFormat('Y-m-d', $nueva_fecha);
                            if ($fecha_obj) echo $fecha_obj->format('d/m/Y');
                        }
                        ?>
                        </p>

                        <p style="margin-bottom:0 !important;"><strong>Motivo del cambio:</strong></p>
                        <p><?php echo nl2br(esc_html($motivo_cambio)); ?></p>

                    <?php elseif ($tipo_cambio === 'Ubicación') : ?>

                        <p style="margin-bottom:0 !important;"><strong>Nueva ubicación:</strong></p>
                        <p>
                            <?php 
                            echo esc_html($provincia_cambio);
                            if ($region_cambio) echo " (" . esc_html($region_cambio) . ", " . esc_html($pais_cambio) . ")";
                            ?>
                        </p>

                        <p style="margin-bottom:0 !important;"><strong>Entorno natural:</strong></p>
                        <p><?php echo esc_html($entorno_natural); ?></p>

                        <p style="margin-bottom:0 !important;"><strong>Motivo del cambio:</strong></p>
                        <p><?php echo nl2br(esc_html($motivo_cambio)); ?></p>

                    <?php elseif ($tipo_cambio === 'Cancelación') : ?>

                        <p style="margin-bottom:0 !important;"><strong>Motivo del cambio:</strong></p>
                        <p><?php echo nl2br(esc_html($motivo_cambio)); ?></p>

                    <?php endif; ?>

                    <div class="mc-ma-da-estado-cambio"> 
                        <?php echo esc_html($estado_cambio); ?>
                    </div>

                </div>

            <?php endif; ?>                        

            <button class="mc-cambios-boton"
                onclick="document.getElementById('mc-cambios-modal-<?php echo $actividad_id; ?>').style.display='flex'">

                <span class="mc-cambios-icono">
                    <?php echo do_shortcode('[icon_alerta1]'); ?>
                </span>
                <span class="mc-cambios-texto">
                    Solicitar cambio de fecha, ubicación o cancelación.
                </span>
            </button>

        </div>

    </div>

    <!-- MODAL -->
    <div id="mc-cambios-modal-<?php echo $actividad_id; ?>" class="mc-cambios-modal">
        <div class="mc-cambios-modal-contenido">

            <span class="mc-cerrar-modal-x"
                onclick="document.getElementById('mc-cambios-modal-<?php echo $actividad_id; ?>').style.display='none'">
                &times;
            </span>

            <h2 class="mc-cambios-modal-titulo">Solicitar cambios</h2>

            <form method="post">
                <?php wp_nonce_field('mc_cambio_general_form','mc_cambio_general_nonce'); ?>

                <input type="hidden" name="mc_cambio_submit" value="1">
                <input type="hidden" name="actividad_id" value="<?php echo $actividad_id; ?>">

                <!-- 1) TIPO DE CAMBIO -->
                <div class="mc-campo">
                    <label><strong>Tipo de cambio*</strong></label>
                    <select name="tipo_cambio" id="tipo_cambio_<?php echo $actividad_id; ?>" required>
                        <option value="">-- Selecciona --</option>
                        <option value="Fecha">Fecha</option>
                        <option value="Ubicación">Ubicación</option>
                        <option value="Cancelación">Cancelación</option>
                    </select>
                </div>

                <!-- 2) SI = FECHA -->
                <div class="mc-campo"
                     id="campo_fecha_<?php echo $actividad_id; ?>"
                     style="display:none;">
                    <label><strong>Nueva fecha propuesta*</strong></label>
                    <input type="date" name="nueva_fecha">
                </div>

                <!-- 3) SI = UBICACIÓN -->
                <div id="campo_ubicacion_<?php echo $actividad_id; ?>" style="display:none;">

                    <div class="mc-campo">
                        <label><strong>País</strong></label>
                        <select name="pais_cambio" id="pais_cambio_<?php echo $actividad_id; ?>">
                            <option value="">-- País --</option>
                            <?php foreach($ubicaciones as $pais => $regiones): ?>
                                <option value="<?php echo esc_attr($pais); ?>"><?php echo esc_html($pais); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mc-campo">
                        <label><strong>Región</strong></label>
                        <select name="region_cambio" id="region_cambio_<?php echo $actividad_id; ?>">
                            <option value="">-- Región --</option>
                        </select>
                    </div>

                    <div class="mc-campo">
                        <label><strong>Provincia</strong></label>
                        <select name="provincia_cambio" id="provincia_cambio_<?php echo $actividad_id; ?>">
                            <option value="">-- Provincia --</option>
                        </select>
                    </div>

                    <div class="mc-campo">
                        <label><strong>Entorno natural</strong></label>
                        <input type="text" name="entorno_natural_cambio">
                    </div>

                </div>

                <!-- 4) SIEMPRE: Motivo -->
                <div class="mc-campo">
                    <label><strong>Motivos del cambio*</strong></label>
                    <textarea name="motivos_cambio" rows="4" required></textarea>
                </div>

                <button type="submit" class="mc-cambios-boton">
                    Solicitar cambios
                </button>

            </form>

        </div>
    </div>

    <script>
    (function(){
        const ubicaciones = <?php echo wp_json_encode($ubicaciones); ?>;
        const id = "<?php echo $actividad_id; ?>";

        const tipo = document.getElementById("tipo_cambio_" + id);
        const fFecha = document.getElementById("campo_fecha_" + id);
        const fUbi = document.getElementById("campo_ubicacion_" + id);

        tipo.addEventListener("change", ()=>{
            let v = tipo.value;

            fFecha.style.display = (v === "Fecha") ? "block" : "none";
            fUbi.style.display   = (v === "Ubicación") ? "block" : "none";
        });

        // Cascading select
        const paisSel = document.getElementById("pais_cambio_" + id);
        const regionSel = document.getElementById("region_cambio_" + id);
        const provSel = document.getElementById("provincia_cambio_" + id);

        paisSel.addEventListener("change", ()=>{
            let pais = paisSel.value;
            regionSel.innerHTML = '<option value="">-- Región --</option>';
            provSel.innerHTML = '<option value="">-- Provincia --</option>';

            if (pais && ubicaciones[pais]) {
                Object.keys(ubicaciones[pais]).forEach(region=>{
                    regionSel.innerHTML += `<option value="${region}">${region}</option>`;
                });
            }
        });

        regionSel.addEventListener("change", ()=>{
            let pais = paisSel.value;
            let region = regionSel.value;
            provSel.innerHTML = '<option value="">-- Provincia --</option>';

            if (pais && region && ubicaciones[pais][region]) {
                ubicaciones[pais][region].forEach(prov=>{
                    provSel.innerHTML += `<option value="${prov}">${prov}</option>`;
                });
            }
        });

    })();
    </script>

    <?php
    return ob_get_clean();
}

// ---------------------------------------------
// PROCESAMIENTO DEL FORMULARIO
// ---------------------------------------------
add_action('init', function(){

    if (!isset($_POST['mc_cambio_submit'])) return;

    if (!isset($_POST['mc_cambio_general_nonce']) ||
        !wp_verify_nonce($_POST['mc_cambio_general_nonce'], 'mc_cambio_general_form')) {
        return;
    }

    if (!is_user_logged_in()) return;

    $actividad_id = intval($_POST['actividad_id'] ?? 0);
    if (!$actividad_id) return;

    if (!current_user_can('edit_post', $actividad_id)) return;

    $tipo        = sanitize_text_field($_POST['tipo_cambio'] ?? '');
    $nueva_fecha = sanitize_text_field($_POST['nueva_fecha'] ?? '');
    $pais        = sanitize_text_field($_POST['pais_cambio'] ?? '');
    $region      = sanitize_text_field($_POST['region_cambio'] ?? '');
    $provincia   = sanitize_text_field($_POST['provincia_cambio'] ?? '');
    $entorno     = sanitize_text_field($_POST['entorno_natural_cambio'] ?? '');
    $motivos     = sanitize_textarea_field($_POST['motivos_cambio'] ?? '');

    update_post_meta($actividad_id, 'tipo_cambio', $tipo);
    update_post_meta($actividad_id, 'motivo_cambio', $motivos);
    update_post_meta($actividad_id, 'estado_solicitud', 'Pendiente');
    update_post_meta($actividad_id, 'fecha_solicitud', current_time('mysql'));

    if ($tipo === 'Fecha') {
        update_post_meta($actividad_id, 'nueva_fecha', $nueva_fecha);
    }

    if ($tipo === 'Ubicación') {
        update_post_meta($actividad_id, 'pais_cambio', $pais);
        update_post_meta($actividad_id, 'region_cambio', $region);
        update_post_meta($actividad_id, 'provincia_cambio', $provincia);
        update_post_meta($actividad_id, 'entorno_natural_cambio', $entorno);
    }

    // ---------------------------------------------
    // ENVÍO DE CORREO AL ADMIN
    // ---------------------------------------------
    $actividad_title = get_the_title($actividad_id);
    $actividad_link  = admin_url('post.php?post=' . $actividad_id . '&action=edit');
    $admin_email     = get_option('admin_email');
    $subject         = "Nueva solicitud de cambio: $actividad_title";

    $message = "Se ha recibido una nueva solicitud de cambios para la actividad '$actividad_title'.\n\n";
    $message .= "Tipo de cambio: $tipo\n";
    if ($tipo === 'Fecha') $message .= "Nueva fecha: $nueva_fecha\n";
    if ($tipo === 'Ubicación') $message .= "Ubicación: $provincia, $region, $pais\nEntorno natural: $entorno\n";
    $message .= "Motivo: $motivos\n\n";
    $message .= "Gestionar la actividad: $actividad_link";

    wp_mail($admin_email, $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);

});

