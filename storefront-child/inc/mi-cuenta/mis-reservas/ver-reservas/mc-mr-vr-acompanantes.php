<?php

add_shortcode('mc_vr_acompanantes', function() {

    if (!is_user_logged_in()) return '';

    $order_id = get_query_var('ver-reservas');
    if (!$order_id) return '';

    // Guardar cambios si el formulario fue enviado
    if (!empty($_POST['trekkium_editar_acompanantes']) && !empty($_POST['acompanantes'])) {
        $nuevo = [];

        foreach ($_POST['acompanantes'] as $item) {
            $nuevo[] = [
                'nombre'   => sanitize_text_field($item['nombre']),
                'telefono' => sanitize_text_field($item['telefono']),
                'edad'     => sanitize_text_field($item['edad']),
            ];
        }

        update_post_meta($order_id, '_trekkium_acompanantes', $nuevo);
        
        // Recargar los datos actualizados
        $acompanantes = $nuevo;
    } else {
        // Obtener acompañantes normalmente
        $acompanantes = get_post_meta($order_id, '_trekkium_acompanantes', true);
    }

    // VERIFICACIÓN PRINCIPAL: Si no hay acompañantes, retornar vacío
    if (empty($acompanantes) || !is_array($acompanantes)) {
        return '';
    }

    $order = wc_get_order($order_id);
    if (!$order) return '';

    ob_start();
    ?>

    <div class="mc-mr-vr-contenedor">

        <div class="mc-mr-vr-titular">
            <h2>Datos de acompañantes</h2>
        </div>

        <?php foreach ($acompanantes as $index => $data): ?>

            <?php 
                $nombre   = $data['nombre']   ?? '—';
                $telefono = $data['telefono'] ?? '';
                $edad     = $data['edad']     ?? '—';

                $telefono_formateado = trim(chunk_split(preg_replace('/\D/', '', $telefono), 3, ' '));
            ?>

            <div class="mc-mr-vr-acompanante-box">

                <!-- Título Acompañante -->
                <div class="mc-mr-vr-acompanante-title">
                    Acompañante <?php echo ($index + 1); ?>
                </div>

                <!-- Nombre -->
                <div class="mc-mr-vr-acompanante-nombre">
                    <?php echo esc_html($nombre); ?>
                </div>

                <!-- Teléfono + Icono WhatsApp -->
                <div class="mc-mr-vr-acompanante-line">
                    <span class="mc-mr-vr-acompanante-icono">
                        <!-- Icono WhatsApp SVG ORIGINAL -->
                        <?php echo do_shortcode('[icon_whatsapp]'); ?>
                    </span>
                    <span><?php echo esc_html($telefono_formateado ?: '—'); ?></span>
                </div>

                <!-- Edad + Icono Usuario -->
                <div class="mc-mr-vr-acompanante-line">
                    <span class="mc-mr-vr-acompanante-icono">
                        <!-- Icono Usuario SVG ORIGINAL -->
                        <?php echo do_shortcode('[icon_user_avatar]'); ?>
                    </span>
                    <span><?php echo esc_html($edad); ?> años</span>
                </div>

            </div>

        <?php endforeach; ?>

        <!-- Botón SIN <button> -->
        <div class="mc-mr-vr-btn-editar" id="abrir-modal-acompanantes" role="button">
            Editar acompañantes
        </div>
    </div>

    <!-- Modal -->
    <div class="mc-mr-vr-modal-overlay" id="modal-acompanantes">

        <div class="mc-mr-vr-modal">

            <h2>Editar acompañantes</h2>

            <form method="POST" class="mc-mr-vr-modal-form">
                <?php foreach ($acompanantes as $i => $d): ?>
                    <h3 >Acompañante <?php echo $i+1; ?></h3>

                    <label>Nombre</label>
                    <input type="text" name="acompanantes[<?php echo $i; ?>][nombre]"
                        value="<?php echo esc_attr($d['nombre']); ?>">

                    <div class="mc-mr-vr-modal-grid">
                        <div>
                            <label>Teléfono</label>
                            <input type="text" name="acompanantes[<?php echo $i; ?>][telefono]"
                                value="<?php echo esc_attr($d['telefono']); ?>">
                        </div>
                        <div>
                            <label>Edad</label>
                            <input type="text" name="acompanantes[<?php echo $i; ?>][edad]"
                                value="<?php echo esc_attr($d['edad']); ?>">
                        </div>
                    </div>


                <?php endforeach; ?>

                <input type="hidden" name="trekkium_editar_acompanantes" value="1">

                <div class="mc-mr-vr-modal-buttons">

                    <!-- Cancelar sin <button> -->
                    <div class="mc-mr-vr-btn mc-mr-vr-btn-secondary" role="button" id="cerrar-modal-acompanantes">
                        Cancelar
                    </div>

                    <!-- Guardar sin <button> -->
                    <div class="mc-mr-vr-btn mc-mr-vr-btn-primary" role="button" onclick="this.closest('form').submit();">
                        Guardar cambios
                    </div>

                </div>
            </form>

        </div>
    </div>

    <script>
        const abrir = document.getElementById('abrir-modal-acompanantes');
        const cerrar = document.getElementById('cerrar-modal-acompanantes');
        const modal  = document.getElementById('modal-acompanantes');

        abrir.addEventListener('click', () => modal.style.display = 'flex');
        cerrar.addEventListener('click', () => modal.style.display = 'none');

        modal.addEventListener('click', e => {
            if (e.target === modal) modal.style.display = 'none';
        });
    </script>

    <?php
    return ob_get_clean();
});