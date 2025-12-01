<?php
// 1. Registrar el meta box para Datos de Cancelación
add_action('add_meta_boxes', function() {
    add_meta_box(
        'limite_cancelacion_meta_box',
        'Datos de Cancelación',
        'render_limite_cancelacion_meta_box',
        'product',
        'normal',
        'default'
    );
});

function render_limite_cancelacion_meta_box($post) {
    // Obtener el valor actual del campo meta
    $limite_cancelacion = get_post_meta($post->ID, 'limite_cancelacion', true);
    
    // Nonce para seguridad
    wp_nonce_field('limite_cancelacion_nonce', 'limite_cancelacion_nonce_field');
    
    ?>
    <div style="margin: 15px 0;">
        <label for="limite_cancelacion_display" style="display: block; margin-bottom: 5px; font-weight: bold;">
            Fecha límite de cancelación gratuita
        </label>
        <input type="text" 
               id="limite_cancelacion_display"
               name="limite_cancelacion_display" 
               value="<?php echo esc_attr($limite_cancelacion); ?>" 
               readonly
               style="width: 100%; padding: 8px; background-color: #f5f5f5; border: 1px solid #ddd; color: #666;"
               placeholder="Se calculará automáticamente al guardar"
        >
        <p style="margin-top: 5px; font-style: italic; color: #666;">
            Calculado automáticamente como 24 horas antes de la fecha/hora de la actividad
        </p>
        <input type="hidden" id="limite_cancelacion" name="limite_cancelacion" value="<?php echo esc_attr($limite_cancelacion); ?>">
    </div>
    <?php
}

// 2. Calcular y guardar automáticamente al guardar el producto
add_action('save_post', 'actualizar_limite_cancelacion_meta', 99, 2);
function actualizar_limite_cancelacion_meta($post_id, $post) {
    // Verificar permisos y autoguardado
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) != 'product') return;
    
    // Verificar nonce
    if (!isset($_POST['limite_cancelacion_nonce_field']) || 
        !wp_verify_nonce($_POST['limite_cancelacion_nonce_field'], 'limite_cancelacion_nonce')) {
        return;
    }

    // Obtener valores de campos meta nativos (usando los nombres correctos)
    $fecha_raw = get_post_meta($post_id, 'fecha', true);
    $hora_raw = get_post_meta($post_id, 'hora', true);

    if (!$fecha_raw || !$hora_raw) {
        // Si no hay fecha/hora, limpiar el campo
        update_post_meta($post_id, 'limite_cancelacion', '');
        return;
    }

    try {
        $timezone = wp_timezone();

        // Combinar fecha y hora y restar 24 horas
        $fecha_hora = new DateTime("$fecha_raw $hora_raw", $timezone);
        $fecha_hora->modify('-24 hours');

        // Guardar en formato legible
        $limite_formateado = $fecha_hora->format('d/m/Y H:i');
        update_post_meta($post_id, 'limite_cancelacion', $limite_formateado);

    } catch(Exception $e) {
        error_log("Error calculando fecha límite: ".$e->getMessage());
        // En caso de error, guardar un mensaje de error
        update_post_meta($post_id, 'limite_cancelacion', 'Error en cálculo');
    }
}

// 3. Función para obtener la fecha límite de cancelación
function get_limite_cancelacion_producto($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'limite_cancelacion', true);
}

// 4. Función para obtener la fecha límite en formato DateTime (para cálculos)
function get_limite_cancelacion_datetime($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $limite = get_post_meta($post_id, 'limite_cancelacion', true);
    if (!$limite) return null;
    
    try {
        // Convertir de formato d/m/Y H:i a DateTime
        $date = DateTime::createFromFormat('d/m/Y H:i', $limite, wp_timezone());
        return $date ?: null;
    } catch(Exception $e) {
        error_log("Error parseando fecha límite: ".$e->getMessage());
        return null;
    }
}

// 5. Función para verificar si todavía se puede cancelar gratuitamente
function puede_cancelar_gratis($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $limite_datetime = get_limite_cancelacion_datetime($post_id);
    if (!$limite_datetime) return false;
    
    $ahora = new DateTime('now', wp_timezone());
    return $ahora < $limite_datetime;
}

// 6. CSS para mejorar la apariencia del campo de solo lectura
add_action('admin_head', function() {
    echo '<style>
        #limite_cancelacion_meta_box input[readonly] {
            background-color: #f5f5f5 !important;
            border-color: #ddd !important;
            color: #666 !important;
            cursor: not-allowed;
        }
        
        .acf-disabled {
            opacity: 0.7;
        }
    </style>';
});

// 7. JavaScript para prevenir la edición del campo
add_action('admin_footer', function() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Prevenir cualquier intento de edición del campo
        $('#limite_cancelacion_display').on('focus click', function(e) {
            e.preventDefault();
            $(this).blur();
        });
        
        // También prevenir edición vía teclado
        $('#limite_cancelacion_display').on('keydown paste', function(e) {
            e.preventDefault();
            return false;
        });
    });
    </script>
    <?php
});
