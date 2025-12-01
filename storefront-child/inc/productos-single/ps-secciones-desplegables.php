<?php

// Planificación
add_shortcode('seccion_planificacion', function() {
    global $post;
    $planificacion = get_post_meta($post->ID, 'planificacion', true);

    if (empty($planificacion)) return '';

    ob_start(); ?>
    <div class="ps-contenedor-desplegable">
        <div class="ps-titular-desplegable">
            <h5>Planificación</h5>
            <span class="ps-icono-toggle">+</span>
        </div>
        <div class="ps-contenido-desplegable">
            <div class="ps-textos"><?php echo wpautop($planificacion); ?></div>
        </div>
    </div>
    <?php return ob_get_clean();
});


// Material necesario
add_shortcode('seccion_material_necesario', function() {
    global $post;
    $material = get_post_meta($post->ID, 'material', true);
    if (empty($material)) return '';

    ob_start(); ?>
    <div class="ps-contenedor-desplegable">
        <div class="ps-titular-desplegable">
            <h5>Material necesario</h5>
            <span class="ps-icono-toggle">+</span>
        </div>
        <div class="ps-contenido-desplegable">
            <div class="ps-textos"><?php echo wpautop($material); ?></div>
        </div>
    </div>
    <?php return ob_get_clean();
});


// Experiencia y requisitos
add_shortcode('seccion_experiencia_requisitos', function() {
    global $post;
    $material = get_post_meta($post->ID, 'experiencia_requisitos', true);
    if (empty($material)) return '';

    ob_start(); ?>
    <div class="ps-contenedor-desplegable">
        <div class="ps-titular-desplegable">
            <h5>Experiencia previa y requisitos</h5>
            <span class="ps-icono-toggle">+</span>
        </div>
        <div class="ps-contenido-desplegable">
            <div class="ps-textos"><?php echo wpautop($material); ?></div>
        </div>
    </div>
    <?php return ob_get_clean();
});


// Incluye
add_shortcode('seccion_incluye', function() {
    global $post;
    $incluye = get_post_meta($post->ID, 'incluye', true);
    if (empty($incluye)) return '';

    ob_start(); ?>
    <div class="ps-contenedor-desplegable">
        <div class="ps-titular-desplegable">
            <h5>Incluye</h5>
            <span class="ps-icono-toggle">+</span>
        </div>
        <div class="ps-contenido-desplegable">
            <div class="ps-textos"><?php echo wpautop($incluye); ?></div>
        </div>
    </div>
    <?php return ob_get_clean();
});

// Dificultad técnica
add_shortcode('seccion_dificultad_tecnica', function() {
    global $post;
    $dificultad_tecnica = get_post_meta($post->ID, 'dificultad_tecnica', true);

    if (empty($dificultad_tecnica)) return '';

    ob_start(); ?>
    <div class="ps-contenedor-desplegable">
        <div class="ps-titular-desplegable">
            <h5>Dificultad técnica</h5>
            <span class="ps-icono-toggle">+</span>
        </div>
        <div class="ps-contenido-desplegable">
            <div class="ps-textos"><?php echo wpautop($dificultad_tecnica); ?></div>
        </div>
    </div>
    <?php return ob_get_clean();
});



// Condiciones de reserva
add_shortcode('seccion_condiciones_reserva', function() {
    global $post;

    // obtener datos
    $precio = (float) get_post_meta($post->ID, 'precio', true);

    // intentar obtener producto WC para precio de reserva
    $product = wc_get_product($post->ID);
    if (!$product) {
        return '';
    }

    $precio_reserva = (float) $product->get_price();
    $resto_pago = $precio - $precio_reserva;

    // obtener fecha límite de cancelación
    $limite_datetime = get_limite_cancelacion_datetime($post->ID);
    $fecha_hora_formateada = $limite_datetime ? $limite_datetime->format('d/m/Y \a \l\a H:i') : '';

    ob_start(); ?>
    <div class="ps-contenedor-desplegable" style="margin-top: 10px;">

        <div class="ps-titular-desplegable">
            <h4>Condiciones de reserva</h4>
            <span class="ps-icono-toggle">+</span>
        </div>

        <div class="ps-contenido-desplegable" style="padding: 10px 10px;">

            <?php if ($limite_datetime) : ?>
                <div class="ps-textos-condiciones">
                    <?php if (puede_cancelar_gratis($post->ID)) : ?>
                        Cancelación gratis hasta el <?php echo esc_html($fecha_hora_formateada); ?> h.
                    <?php else : ?>
                        Esta reserva no se podrá cancelar porque se ha superado el plazo límite de cancelación gratuita.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="ps-textos-condiciones">
                El día de la actividad deberás pagar al guía el importe pendiente.
            </div>

        </div>

    </div>
    <?php return ob_get_clean();
});

// Script para activar todos los desplegables
add_action('wp_footer', function() { ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const titulos = document.querySelectorAll(".ps-titular-desplegable");

        titulos.forEach(titulo => {
            const contenedor = titulo.closest(".ps-contenedor-desplegable");
            if (!contenedor) return;
            const contenido = contenedor.querySelector(".ps-contenido-desplegable");
            const icono = titulo.querySelector(".ps-icono-toggle");

            // asegurar que exista contenido antes de añadir listener
            if (!contenido || !icono) return;

            // estado inicial: ocultar el contenido
            contenido.classList.remove('activo');

            titulo.addEventListener("click", () => {
                contenido.classList.toggle("activo");
                icono.textContent = contenido.classList.contains("activo") ? "−" : "+";
            });
        });
    });
    </script>
<?php });
