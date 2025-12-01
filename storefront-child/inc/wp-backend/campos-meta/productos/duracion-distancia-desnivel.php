<?php
// Registrar meta box para la Ficha técnica
add_action('add_meta_boxes', function() {
    add_meta_box(
        'ficha_tecnica_meta_box',
        'Ficha técnica',
        'render_ficha_tecnica_meta_box',
        'product',
        'normal',
        'high' // Alta prioridad para colocarlo cerca del título
    );
});

// Función para renderizar el meta box
function render_ficha_tecnica_meta_box($post) {
    // Obtener valores actuales
    $duracion = get_post_meta($post->ID, 'duracion', true);
    $distancia = get_post_meta($post->ID, 'distancia', true);
    $desnivel_positivo = get_post_meta($post->ID, 'desnivel_positivo', true);
    $desnivel_negativo = get_post_meta($post->ID, 'desnivel_negativo', true);
    
    // Nonce para seguridad
    wp_nonce_field('ficha_tecnica_nonce', 'ficha_tecnica_nonce_field');
    
    ?>
    <div style="display: flex; flex-wrap: nowrap; margin: 15px 0;">
        
        <div style="flex: 0 0 25%; padding-right: 15px; box-sizing: border-box;">
            <label for="duracion" style="display: block; margin-bottom: 5px; font-weight: bold;">Duración</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input type="number" 
                    id="duracion"
                    name="duracion" 
                    value="<?php echo esc_attr($duracion); ?>" 
                    placeholder="Ej: 5,5"
                    min="0" 
                    step="0.01"
                    style="width: 100%; padding-right: 60px;">
                <span style="position: absolute; right: 10px; color: #666;">horas</span>
            </div>
        </div>

        <div style="flex: 0 0 25%; padding-right: 15px; box-sizing: border-box;">
            <label for="distancia" style="display: block; margin-bottom: 5px; font-weight: bold;">Distancia</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input type="number" 
                    id="distancia"
                    name="distancia" 
                    value="<?php echo esc_attr($distancia); ?>" 
                    placeholder="Ej: 12,5"
                    min="0" 
                    step="0.01"
                    style="width: 100%; padding-right: 45px;">
                <span style="position: absolute; right: 10px; color: #666;">km</span>
            </div>
        </div>

        <div style="flex: 0 0 25%; padding-right: 15px; box-sizing: border-box;">
            <label for="desnivel_positivo" style="display: block; margin-bottom: 5px; font-weight: bold;">Desnivel positivo</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input type="number" 
                    id="desnivel_positivo"
                    name="desnivel_positivo" 
                    value="<?php echo esc_attr($desnivel_positivo); ?>" 
                    placeholder="Ej: 1200"
                    min="0" 
                    step="1"
                    style="width: 100%; padding-right: 30px;">
                <span style="position: absolute; right: 10px; color: #666;">m</span>
            </div>
        </div>

        <div style="flex: 0 0 25%; box-sizing: border-box;">
            <label for="desnivel_negativo" style="display: block; margin-bottom: 5px; font-weight: bold;">Desnivel negativo</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input type="number" 
                    id="desnivel_negativo"
                    name="desnivel_negativo" 
                    value="<?php echo esc_attr($desnivel_negativo); ?>" 
                    placeholder="Ej: 1200"
                    min="0" 
                    step="1"
                    style="width: 100%; padding-right: 30px;">
                <span style="position: absolute; right: 10px; color: #666;">m</span>
            </div>
        </div>
    </div>


    <?php
}

// Guardar los campos meta - CORREGIDO
add_action('save_post', function($post_id) {
    // Verificar nonce
    if (!isset($_POST['ficha_tecnica_nonce_field']) || 
        !wp_verify_nonce($_POST['ficha_tecnica_nonce_field'], 'ficha_tecnica_nonce')) {
        return;
    }
    
    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Verificar autoguardado
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Array de campos a guardar
    $campos = array(
        'duracion' => 'float',
        'distancia' => 'float',
        'desnivel_positivo' => 'int',
        'desnivel_negativo' => 'int'
    );
    
    // Guardar cada campo - CORREGIDO: sin guión bajo
    foreach ($campos as $campo => $tipo) {
        if (isset($_POST[$campo])) {
            $valor = $_POST[$campo];
            
            // Sanitizar según el tipo
            if ($tipo === 'float') {
                $valor = floatval(str_replace(',', '.', $valor));
            } elseif ($tipo === 'int') {
                $valor = intval($valor);
            }
            
            // CORRECCIÓN: Guardar sin guión bajo al principio
            update_post_meta($post_id, $campo, $valor);
        } else {
            // Si el campo no está presente, eliminarlo
            delete_post_meta($post_id, $campo);
        }
    }
});

// Funciones helper para obtener los valores
function get_duracion_producto($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    return get_post_meta($post_id, 'duracion', true);
}

function get_distancia_producto($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    return get_post_meta($post_id, 'distancia', true);
}

function get_desnivel_positivo_producto($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    return get_post_meta($post_id, 'desnivel_positivo', true);
}

function get_desnivel_negativo_producto($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    return get_post_meta($post_id, 'desnivel_negativo', true);
}

// Función para mostrar toda la ficha técnica
function mostrar_ficha_tecnica($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    
    $html = '<div class="ficha-tecnica">';
    
    if ($duracion = get_duracion_producto($post_id)) {
        $html .= '<div><strong>Duración:</strong> ' . number_format($duracion, 2, ',', '.') . ' horas</div>';
    }
    
    if ($distancia = get_distancia_producto($post_id)) {
        $html .= '<div><strong>Distancia:</strong> ' . number_format($distancia, 2, ',', '.') . ' km</div>';
    }
    
    if ($desnivel_pos = get_desnivel_positivo_producto($post_id)) {
        $html .= '<div><strong>Desnivel positivo:</strong> ' . number_format($desnivel_pos, 0, ',', '.') . ' m</div>';
    }
    
    if ($desnivel_neg = get_desnivel_negativo_producto($post_id)) {
        $html .= '<div><strong>Desnivel negativo:</strong> ' . number_format($desnivel_neg, 0, ',', '.') . ' m</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}