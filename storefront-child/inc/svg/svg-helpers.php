<?php
/**
 * SVG Icon Helper Functions
 * 
 * Centraliza el manejo de iconos SVG usando un sprite consolidado.
 * Un único archivo SVG con símbolos = mejor performance (1 HTTP request en lugar de 15+)
 */

/**
 * Renderiza un icono SVG desde el sprite consolidado
 * 
 * @param string $icon_id ID del símbolo SVG (ej: 'icon-acceso', 'icon-actividades1')
 * @param string|array $class Clases CSS a aplicar (string o array)
 * @param string $aria_label Texto para aria-label (accesibilidad)
 * @param array $attrs Atributos HTML adicionales (data-*, etc)
 * @return string HTML del SVG con <use>
 */
function trekkium_svg($icon_id, $class = '', $aria_label = '', $attrs = array()) {
    // Validar que el ID sea un string válido
    if (!is_string($icon_id) || empty($icon_id)) {
        return '';
    }

    // Preparar clases — si no se pasa clase, usar clase por defecto 'icon-md'
    $classes = is_array($class) ? implode(' ', $class) : $class;
    if ( empty( $classes ) ) {
        $classes = 'icon-md';
    }
    $class_attr = !empty($classes) ? ' class="' . esc_attr($classes) . '"' : '';

    // Preparar aria-label
    $aria_attr = !empty($aria_label) ? ' aria-label="' . esc_attr($aria_label) . '"' : ' aria-hidden="true"';

    // Preparar atributos adicionales
    $extra_attrs = '';
    if (is_array($attrs) && !empty($attrs)) {
        foreach ($attrs as $key => $value) {
            $extra_attrs .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
        }
    }

    // Usar referencia de fragmento (#id) — el sprite se inyecta inline en el footer
    return sprintf(
        '<svg%s%s%s><use href="#%s"></use></svg>',
        $class_attr,
        $aria_attr,
        $extra_attrs,
        esc_attr($icon_id)
    );
}

/**
 * Shortcode para renderizar iconos
 * Uso: [icon id="icon-acceso" class="icono-pequeño" label="Acceso"]
 * 
 * @param array $atts Atributos del shortcode
 * @return string HTML del SVG
 */
function trekkium_icon_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id'    => '',
        'class' => '',
        'label' => '',
    ), $atts, 'icon');

    if (empty($atts['id'])) {
        return '';
    }

    return trekkium_svg($atts['id'], $atts['class'], $atts['label']);
}
add_shortcode('icon', 'trekkium_icon_shortcode');

/**
 * Helpers de conveniencia para iconos comunes
 * Permite: trekkium_icon_acceso() en lugar de trekkium_svg('icon-acceso')
 */

function trekkium_icon_acceso($class = '', $aria_label = 'Acceso') {
    return trekkium_svg('icon-acceso', $class, $aria_label);
}

function trekkium_icon_actividades1($class = '', $aria_label = 'Actividades') {
    return trekkium_svg('icon-actividades1', $class, $aria_label);
}

function trekkium_icon_actividades2($class = '', $aria_label = '') {
    return trekkium_svg('icon-actividades2', $class, $aria_label);
}

function trekkium_icon_actividades3($class = '', $aria_label = '') {
    return trekkium_svg('icon-actividades3', $class, $aria_label);
}

function trekkium_icon_estrella($class = '', $aria_label = 'Valoración') {
    return trekkium_svg('icon-estrella1', $class, $aria_label);
}

function trekkium_icon_blog($class = '', $aria_label = 'Blog') {
    return trekkium_svg('icon-blog1', $class, $aria_label);
}

function trekkium_icon_dificultad($class = '', $aria_label = 'Dificultad') {
    return trekkium_svg('icon-dificultad1', $class, $aria_label);
}

function trekkium_icon_duracion($class = '', $aria_label = 'Duración', $variant = '1') {
    $icon_id = $variant === '2' ? 'icon-duracion2' : 'icon-duracion1';
    return trekkium_svg($icon_id, $class, $aria_label);
}

function trekkium_icon_fecha($class = '', $aria_label = 'Fecha') {
    return trekkium_svg('icon-fecha1', $class, $aria_label);
}

function trekkium_icon_guias($class = '', $aria_label = 'Guías', $variant = '1') {
    $icon_id = $variant === '2' ? 'icon-guias2' : 'icon-guias1';
    return trekkium_svg($icon_id, $class, $aria_label);
}

function trekkium_icon_user_avatar($class = '', $aria_label = 'Avatar') {
    return trekkium_svg('icon-user-avatar', $class, $aria_label);
}

function trekkium_icon_region($class = '', $aria_label = 'Región') {
    return trekkium_svg('icon-region1', $class, $aria_label);
}

function trekkium_icon_modalidad($class = '', $aria_label = 'Modalidad') {
    return trekkium_svg('icon-modalidad1', $class, $aria_label);
}

function trekkium_icon_admin_dashboard($class = '', $aria_label = 'Panel Admin') {
    return trekkium_svg('icon-admin-dashboard', $class, $aria_label);
}

/**
 * Hook para outputear el sprite SVG una única vez en el footer
 * Esto hace que los <use> funcionen correctamente
 */
function trekkium_output_svg_sprite() {
    $sprite_url = trekkium_asset_url('svg/icons.svg');
    ?>
    <script>
    // Inyectar sprite SVG en el DOM si no está presente
    if (!document.querySelector('svg use') && !window.trekkium_sprite_loaded) {
        fetch('<?php echo esc_url($sprite_url); ?>')
            .then(response => response.text())
            .then(svg => {
                const wrapper = document.createElement('div');
                wrapper.style.display = 'none';
                wrapper.innerHTML = svg;
                document.body.appendChild(wrapper);
                window.trekkium_sprite_loaded = true;
            })
            .catch(err => console.warn('Error loading SVG sprite:', err));
    }
    </script>
    <?php
}
add_action('wp_footer', 'trekkium_output_svg_sprite', 999);

/**
 * Compatibilidad con shortcodes antiguos
 * Estos mapean los [svg_*] antiguos a los nuevos [icon]
 */

function trekkium_compat_svg_acceso() {
    return trekkium_svg('icon-acceso');
}
add_shortcode('svg_acceso', 'trekkium_compat_svg_acceso');

function trekkium_compat_svg_actividades1() {
    return trekkium_svg('icon-actividades1');
}
add_shortcode('svg_actividades1', 'trekkium_compat_svg_actividades1');

function trekkium_compat_svg_actividades2() {
    return trekkium_svg('icon-actividades2');
}
add_shortcode('svg_actividades2', 'trekkium_compat_svg_actividades2');

function trekkium_compat_svg_actividades3() {
    return trekkium_svg('icon-actividades3');
}
add_shortcode('svg_actividades3', 'trekkium_compat_svg_actividades3');

function trekkium_compat_svg_estrella1() {
    return trekkium_svg('icon-estrella1');
}
add_shortcode('svg_estrella1', 'trekkium_compat_svg_estrella1');

function trekkium_compat_svg_blog1() {
    return trekkium_svg('icon-blog1');
}
add_shortcode('svg_blog1', 'trekkium_compat_svg_blog1');

function trekkium_compat_svg_dificultad1() {
    return trekkium_svg('icon-dificultad1');
}
add_shortcode('svg_dificultad1', 'trekkium_compat_svg_dificultad1');

function trekkium_compat_svg_duracion1() {
    return trekkium_svg('icon-duracion1');
}
add_shortcode('svg_duracion1', 'trekkium_compat_svg_duracion1');

function trekkium_compat_svg_duracion2() {
    return trekkium_svg('icon-duracion2');
}
add_shortcode('svg_duracion2', 'trekkium_compat_svg_duracion2');

function trekkium_compat_svg_fecha1() {
    return trekkium_svg('icon-fecha1');
}
add_shortcode('svg_fecha1', 'trekkium_compat_svg_fecha1');

function trekkium_compat_svg_guias1() {
    return trekkium_svg('icon-guias1');
}
add_shortcode('svg_guias1', 'trekkium_compat_svg_guias1');

function trekkium_compat_svg_guias2() {
    return trekkium_svg('icon-guias2');
}
add_shortcode('svg_guias2', 'trekkium_compat_svg_guias2');

function trekkium_compat_svg_user_avatar1() {
    return trekkium_svg('icon-user-avatar');
}
add_shortcode('svg_user_avatar1', 'trekkium_compat_svg_user_avatar1');

function trekkium_compat_svg_region1() {
    return trekkium_svg('icon-region1');
}
add_shortcode('svg_region1', 'trekkium_compat_svg_region1');

function trekkium_compat_svg_modalidad1() {
    return trekkium_svg('icon-modalidad1');
}
add_shortcode('svg_modalidad1', 'trekkium_compat_svg_modalidad1');
