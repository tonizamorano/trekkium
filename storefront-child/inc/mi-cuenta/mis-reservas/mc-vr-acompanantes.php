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
    }

    $order = wc_get_order($order_id);
    if (!$order) return '';

    $acompanantes = get_post_meta($order_id, '_trekkium_acompanantes', true);

    if (empty($acompanantes) || !is_array($acompanantes)) {
        return '<div class="contenedor-detalles"><p>No hay acompañantes registrados.</p></div>';
    }

    ob_start();
    ?>

    <div class="contenedor-detalles">
        <h1>Datos de acompañantes</h1>

        <?php foreach ($acompanantes as $index => $data): ?>

            <?php 
                $nombre   = $data['nombre']   ?? '—';
                $telefono = $data['telefono'] ?? '';
                $edad     = $data['edad']     ?? '—';

                $telefono_formateado = trim(chunk_split(preg_replace('/\D/', '', $telefono), 3, ' '));
            ?>

            <div class="tk-acompanante-box">

                <!-- Título Acompañante -->
                <div class="tk-acompanante-title">
                    Acompañante <?php echo ($index + 1); ?>
                </div>

                <!-- Nombre -->
                <div class="tk-acompanante-nombre">
                    <?php echo esc_html($nombre); ?>
                </div>

                <!-- Teléfono + Icono WhatsApp -->
                <div class="tk-acompanante-line">
                    <span class="tk-acompanante-icono">
                        <!-- Icono WhatsApp SVG ORIGINAL -->
                        <svg viewBox="0 0 32 32">
                            <path d="M16 .5C7.6.5.7 7.4.7 15.8c0 2.7.7 5.2 2 7.5L.5 31.5l8.4-2.2c2.2 1.2 4.7 1.8 7.3 1.8 8.4 0 15.3-6.9 15.3-15.3C31.5 7.4 24.6.5 16 .5zm0 27.6c-2.3 0-4.6-.6-6.6-1.8l-.5-.3-5 1.3 1.3-4.9-.3-.5c-1.2-2-1.8-4.3-1.8-6.6C3.1 9 9 3.1 16 3.1s12.9 5.9 12.9 12.9-5.9 12.1-12.9 12.1z"/>
                            <path d="M23.2 19.5c-.4-.2-2.3-1.1-2.6-1.2-.4-.1-.6-.2-.9.2-.3.4-1 1.2-1.2 1.5-.2.2-.5.3-.9.1-.4-.2-1.8-.7-3.4-2.2-1.2-1.1-2-2.4-2.3-2.8-.2-.4 0-.6.2-.8.3-.3.6-.7.8-1 .2-.3.3-.5.4-.8.1-.3 0-.6 0-.8 0-.2-.9-2.2-1.2-3-.3-.8-.7-.7-.9-.7h-.8c-.3 0-.8.1-1.2.6-.4.4-1.5 1.4-1.5 3.5 0 2.1 1.5 4.1 1.7 4.4.2.3 3 4.7 7.2 6.6 4.2 1.9 4.2 1.3 5 1.2.8-.1 2.3-1 2.7-2 .3-1 .3-1.9.2-2.1-.1-.2-.3-.3-.7-.5z"/>
                        </svg>
                    </span>
                    <span><?php echo esc_html($telefono_formateado ?: '—'); ?></span>
                </div>

                <!-- Edad + Icono Usuario -->
                <div class="tk-acompanante-line">
                    <span class="tk-acompanante-icono">
                        <!-- Icono Usuario SVG ORIGINAL -->
                        <svg viewBox="0 0 24 24">
                            <path d="M12 12c2.7 0 4.8-2.2 4.8-4.8S14.7 2.4 12 2.4 7.2 4.6 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </span>
                    <span><?php echo esc_html($edad); ?> años</span>
                </div>

            </div>

        <?php endforeach; ?>

        <!-- Botón SIN <button> -->
        <div class="tk-btn-editar" id="abrir-modal-acompanantes" role="button">
            Editar acompañantes
        </div>
    </div>

    <!-- Modal -->
    <div class="tk-modal-overlay" id="modal-acompanantes">

        <div class="tk-modal">

            <h2>Editar acompañantes</h2>

            <form method="POST" class="tk-modal-form">
                <?php foreach ($acompanantes as $i => $d): ?>
                    <h3 >Acompañante <?php echo $i+1; ?></h3>

                    <label>Nombre</label>
                    <input type="text" name="acompanantes[<?php echo $i; ?>][nombre]"
                        value="<?php echo esc_attr($d['nombre']); ?>">

                    <div class="tk-modal-grid">
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

                <div class="tk-modal-buttons">

                    <!-- Cancelar sin <button> -->
                    <div class="tk-btn tk-btn-secondary" role="button" id="cerrar-modal-acompanantes">
                        Cancelar
                    </div>

                    <!-- Guardar sin <button> -->
                    <div class="tk-btn tk-btn-primary" role="button" onclick="this.closest('form').submit();">
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
