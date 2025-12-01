<?php
/**
 * Campos Meta para CPT "Candidatos" (Versión extendida con Notas en metabox separado)
 * Autor: Toni - Trekkium
 */

add_action('add_meta_boxes', 'trekkium_add_candidato_metabox');
function trekkium_add_candidato_metabox() {
    // Metabox principal (sin notas ni estado)
    add_meta_box(
        'trekkium_candidato_datos',
        'Datos del Candidato',
        'trekkium_render_candidato_metabox',
        'candidato',
        'normal',
        'high'
    );

    // Estado en la columna lateral
    add_meta_box(
        'trekkium_candidato_estado',
        'Estado del Candidato',
        'trekkium_render_estado_metabox',
        'candidato',
        'side',
        'high'
    );

    // Notas internas en la columna lateral
    add_meta_box(
        'trekkium_candidato_notas',
        'Notas internas',
        'trekkium_render_notas_metabox',
        'candidato',
        'side',
        'high'
    );
}

// Obtener provincias españolas desde WooCommerce
function trekkium_get_spanish_states() {
    if (class_exists('WC_Countries')) {
        $wc_countries = new WC_Countries();
        $states = $wc_countries->get_states();
        return isset($states['ES']) ? $states['ES'] : [];
    }
    return [];
}

// Obtener términos de una taxonomía como array [slug => nombre]
function trekkium_get_taxonomy_terms($taxonomy) {
    $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
    $options = [];
    if (!is_wp_error($terms)) {
        foreach ($terms as $term) {
            $options[$term->slug] = $term->name;
        }
    }
    return $options;
}

// Metabox principal
function trekkium_render_candidato_metabox($post) {
    wp_nonce_field('trekkium_save_candidato', 'trekkium_candidato_nonce');

    $campos = [
        'nombre', 'apellidos', 'fecha_nacimiento', 'dni', 'telefono', 'email',
        'pais', 'provincia', 'ciudad', 'codigo_postal'
    ];

    $valores = [];
    foreach ($campos as $campo) {
        $valores[$campo] = get_post_meta($post->ID, $campo, true);
    }

    $titulaciones_sel = (array) get_post_meta($post->ID, 'titulacion_array', true);
    $modalidades_sel = (array) get_post_meta($post->ID, 'modalidad_array', true);
    $idiomas_sel = (array) get_post_meta($post->ID, 'idiomas_array', true);

    $provincias = trekkium_get_spanish_states();
    $titulaciones = trekkium_get_taxonomy_terms('titulacion');
    $modalidades = trekkium_get_taxonomy_terms('modalidad');
    $idiomas = trekkium_get_taxonomy_terms('idiomas');

    ?>
    <style>
        .trekkium-candidato-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .trekkium-candidato-field { margin-bottom: 5px; }
        .trekkium-candidato-field label { display: block; font-weight: 600; margin-bottom: 5px; }
        .trekkium-candidato-field input,
        .trekkium-candidato-field select,
        .trekkium-candidato-field textarea { width: 100%; padding: 2px 10px; border: 1px solid #ddd; border-radius: 4px; }
        .trekkium-candidato-fullwidth { grid-column: 1 / -1; }
        .trekkium-candidato-three-columns { 
            grid-column: 1 / -1; 
            display: grid; 
            grid-template-columns: 1fr 1fr 1fr; 
            gap: 20px; 
            margin-top: 10px;
        }
        .trekkium-candidato-column { }
        .trekkium-candidato-column label { display: block; font-size: 16px; font-weight: 600; margin-bottom: 10px; }
        .trekkium-candidato-multiselect { display: flex; flex-wrap: wrap; gap: 12px; }
        .trekkium-candidato-multiselect label { display: flex; align-items: center; font-size: 14px; font-weight: 500; margin-bottom: 0; }
        .trekkium-candidato-multiselect input[type="checkbox"] { margin-right: 8px; }
    </style>

    <div class="trekkium-candidato-grid">
        <div class="trekkium-candidato-field">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo esc_attr($valores['nombre']); ?>" required>
        </div>

        <div class="trekkium-candidato-field">
            <label for="apellidos">Apellidos *</label>
            <input type="text" id="apellidos" name="apellidos" value="<?php echo esc_attr($valores['apellidos']); ?>" required>
        </div>

        <div class="trekkium-candidato-field">
            <label for="fecha_nacimiento">Fecha de nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo esc_attr($valores['fecha_nacimiento']); ?>">
        </div>

        <div class="trekkium-candidato-field">
            <label for="dni">DNI</label>
            <input type="text" id="dni" name="dni" value="<?php echo esc_attr($valores['dni']); ?>" pattern="[0-9A-Za-z\-\.]{5,20}">
        </div>

        <div class="trekkium-candidato-field">
            <label for="telefono">Teléfono móvil</label>
            <input type="tel" id="telefono" name="telefono" value="<?php echo esc_attr($valores['telefono']); ?>" pattern="[0-9+\s\-]{6,20}">
        </div>

        <div class="trekkium-candidato-field">
            <label for="email">Correo electrónico *</label>
            <input type="email" id="email" name="email" value="<?php echo esc_attr($valores['email']); ?>" required>
        </div>

        <div class="trekkium-candidato-field">
            <label for="pais">País</label>
            <select id="pais" name="pais">
                <option value="ES" <?php selected($valores['pais'], 'ES'); ?>>España</option>
            </select>
        </div>

        <div class="trekkium-candidato-field">
            <label for="provincia">Provincia</label>
            <select id="provincia" name="provincia">
                <option value="">Selecciona una provincia</option>
                <?php foreach ($provincias as $code => $name): ?>
                    <option value="<?php echo esc_attr($code); ?>" <?php selected($valores['provincia'], $code); ?>><?php echo esc_html($name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="trekkium-candidato-field">
            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" value="<?php echo esc_attr($valores['ciudad']); ?>">
        </div>

        <div class="trekkium-candidato-field">
            <label for="codigo_postal">Código Postal</label>
            <input type="text" id="codigo_postal" name="codigo_postal" value="<?php echo esc_attr($valores['codigo_postal']); ?>">
        </div>

        <!-- Contenedor de 3 columnas para Titulación, Modalidad e Idiomas -->
        <div class="trekkium-candidato-three-columns">
            <div class="trekkium-candidato-column">
                <label>Titulación</label>
                <div class="trekkium-candidato-multiselect">
                    <?php foreach ($titulaciones as $slug => $nombre): ?>
                        <label><input type="checkbox" name="titulacion_array[]" value="<?php echo esc_attr($slug); ?>" <?php checked(in_array($slug, $titulaciones_sel)); ?>> <?php echo esc_html($nombre); ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="trekkium-candidato-column">
                <label>Modalidad</label>
                <div class="trekkium-candidato-multiselect">
                    <?php foreach ($modalidades as $slug => $nombre): ?>
                        <label><input type="checkbox" name="modalidad_array[]" value="<?php echo esc_attr($slug); ?>" <?php checked(in_array($slug, $modalidades_sel)); ?>> <?php echo esc_html($nombre); ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="trekkium-candidato-column">
                <label>Idiomas</label>
                <div class="trekkium-candidato-multiselect">
                    <?php foreach ($idiomas as $slug => $nombre): ?>
                        <label><input type="checkbox" name="idiomas_array[]" value="<?php echo esc_attr($slug); ?>" <?php checked(in_array($slug, $idiomas_sel)); ?>> <?php echo esc_html($nombre); ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Metabox Estado (solo una definición)
function trekkium_render_estado_metabox($post) {
    $estado = get_post_meta($post->ID, 'estado', true);
    if (empty($estado)) $estado = 'pendiente';
    ?>
    <div class="trekkium-estado-field">
        <label for="estado" style="display: block; font-weight: 600; margin-bottom: 8px;">Estado del candidato:</label>
        <select id="estado" name="estado" style="width: 100%;">
            <option value="pendiente" <?php selected($estado, 'pendiente'); ?>>Pendiente de revisión</option>
            <option value="aprobado" <?php selected($estado, 'aprobado'); ?>>Aprobado</option>
            <option value="rechazado" <?php selected($estado, 'rechazado'); ?>>Rechazado</option>
        </select>

        <?php if ($estado === 'aprobado'): ?>
            <button type="button" class="button button-primary" style="margin-top: 10px;" id="crear_guia" data-candidato-id="<?php echo $post->ID; ?>">Crear nuevo guía</button>
            <span id="crear_guia_msg" style="margin-left:10px;"></span>
        <?php endif; ?>
    </div>

    <script>
    jQuery(document).ready(function($){
        $('#crear_guia').on('click', function(){
            var btn = $(this);
            var post_id = btn.data('candidato-id');
            $('#crear_guia_msg').text('Creando usuario...');
            $.post(ajaxurl, {
                action: 'trekkium_crear_guia',
                post_id: post_id,
                _ajax_nonce: '<?php echo wp_create_nonce("trekkium_crear_guia_nonce"); ?>'
            }, function(response){
                if(response.success){
                    $('#crear_guia_msg').html('<span style="color:green;">Usuario creado: '+response.data.user_login+'</span>');
                    btn.prop('disabled', true);
                } else {
                    $('#crear_guia_msg').html('<span style="color:red;">Error: '+response.data+'</span>');
                }
            });
        });
    });
    </script>
    <?php
}


// Metabox Notas internas
function trekkium_render_notas_metabox($post) {
    $notas = get_post_meta($post->ID, 'notas_admin', true);
    ?>
    <div class="trekkium-notas-field">
        
        <textarea id="notas_admin" name="notas_admin" rows="6" style="width:100%;"><?php echo esc_textarea($notas); ?></textarea>
    </div>
    <?php
}

// Guardar metadatos con validación del Estado
add_action('save_post', 'trekkium_save_candidato_metabox');
function trekkium_save_candidato_metabox($post_id) {
    if (!isset($_POST['trekkium_candidato_nonce']) || !wp_verify_nonce($_POST['trekkium_candidato_nonce'], 'trekkium_save_candidato')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (get_post_type($post_id) !== 'candidato') return;

    // Campos básicos
    $campos = [
        'nombre', 'apellidos', 'fecha_nacimiento', 'dni', 'telefono', 'email',
        'pais', 'provincia', 'ciudad', 'codigo_postal',
        'estado', 'notas_admin'
    ];

    $valores_guardar = [];
    foreach ($campos as $campo) {
        if (isset($_POST[$campo])) {
            $valores_guardar[$campo] = sanitize_text_field($_POST[$campo]);
        }
    }

    // Guardar arrays múltiples
    $multi_campos = ['titulacion_array', 'modalidad_array', 'idiomas_array'];
    foreach ($multi_campos as $multi) {
        if (isset($_POST[$multi]) && is_array($_POST[$multi])) {
            $valores_guardar[$multi] = array_map('sanitize_text_field', $_POST[$multi]);
            update_post_meta($post_id, $multi, $valores_guardar[$multi]);
        } else {
            delete_post_meta($post_id, $multi);
            $valores_guardar[$multi] = [];
        }
    }

    // Validación antes de aprobar
    $campos_obligatorios = ['nombre', 'apellidos', 'fecha_nacimiento', 'dni', 'telefono', 'email', 'pais', 'provincia', 'ciudad', 'codigo_postal'];
    $arrays_obligatorios = ['titulacion_array', 'modalidad_array', 'idiomas_array'];
    $estado = isset($valores_guardar['estado']) ? $valores_guardar['estado'] : 'pendiente';

    $completo = true;
    foreach ($campos_obligatorios as $campo) {
        if (empty($valores_guardar[$campo])) {
            $completo = false;
            break;
        }
    }
    if ($completo) {
        foreach ($arrays_obligatorios as $campo) {
            if (empty($valores_guardar[$campo])) {
                $completo = false;
                break;
            }
        }
    }

    if ($estado === 'aprobado' && !$completo) {
        // No se puede aprobar: mostrar mensaje
        add_filter('redirect_post_location', function($location) {
            return add_query_arg('estado_error', '1', $location);
        });
        $valores_guardar['estado'] = 'pendiente';
    }

    // Guardar todos los campos
    foreach ($campos as $campo) {
        if (isset($valores_guardar[$campo])) {
            update_post_meta($post_id, $campo, $valores_guardar[$campo]);
        }
    }
}

// Mostrar mensaje de error en metabox si no se completaron campos
add_action('admin_notices', function() {
    if (isset($_GET['post']) && isset($_GET['estado_error'])) {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === 'candidato') {
            echo '<div class="notice notice-error"><p>Por favor, completa todos los campos obligatorios antes de aprobar el candidato.</p></div>';
        }
    }
});


// Crear nuevo usuario a partir de CPT

add_action('wp_ajax_trekkium_crear_guia', 'trekkium_crear_guia_callback');

function trekkium_crear_guia_callback() {
    if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'trekkium_crear_guia_nonce')) {
        wp_send_json_error('Nonce no válido');
    }

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Permiso denegado');
    }

    $post_id = intval($_POST['post_id']);
    $candidato = get_post($post_id);

    if (!$candidato || $candidato->post_type !== 'candidato') {
        wp_send_json_error('Candidato no encontrado');
    }

    // Datos del CPT
    $nombre = get_post_meta($post_id, 'nombre', true);
    $apellidos = get_post_meta($post_id, 'apellidos', true);
    $email = get_post_meta($post_id, 'email', true);
    $fecha_nacimiento = get_post_meta($post_id, 'fecha_nacimiento', true);
    $telefono = get_post_meta($post_id, 'telefono', true);
    $pais = get_post_meta($post_id, 'pais', true) ?: 'ES';
    $provincia = get_post_meta($post_id, 'provincia', true);
    $ciudad = get_post_meta($post_id, 'ciudad', true);
    $codigo_postal = get_post_meta($post_id, 'codigo_postal', true);
    $dni = get_post_meta($post_id, 'dni', true);

    $titulaciones = get_post_meta($post_id, 'titulacion_array', true) ?: [];
    $modalidades = get_post_meta($post_id, 'modalidad_array', true) ?: [];
    $idiomas = get_post_meta($post_id, 'idiomas_array', true) ?: [];

    // Crear login y password
    $user_login = sanitize_user(strtolower($nombre . '.' . $apellidos));
    $password = wp_generate_password(12, false);

    if (username_exists($user_login) || email_exists($email)) {
        wp_send_json_error('El usuario o email ya existe');
    }

    $user_id = wp_create_user($user_login, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error($user_id->get_error_message());
    }

    $user = new WP_User($user_id);
    $user->set_role('guia');

    // Campos estándar de usuario
    wp_update_user([
        'ID' => $user_id,
        'first_name' => $nombre,
        'last_name'  => $apellidos,
        'user_email' => $email,
    ]);

    // Campos meta
    update_user_meta($user_id, 'account_first_name', $nombre);
    update_user_meta($user_id, 'account_last_name', $apellidos);
    update_user_meta($user_id, 'account_email', $email);
    update_user_meta($user_id, 'fecha_nacimiento', $fecha_nacimiento);
    update_user_meta($user_id, 'billing_phone', $telefono);
    update_user_meta($user_id, 'billing_country', $pais);
    update_user_meta($user_id, 'billing_state', $provincia);
    update_user_meta($user_id, 'billing_city', $ciudad);
    update_user_meta($user_id, 'billing_postcode', $codigo_postal);
    update_user_meta($user_id, 'billing_dni', $dni);

    // Asignar taxonomías
    if (!empty($titulaciones)) {
        wp_set_object_terms($user_id, $titulaciones, 'titulacion', false);
    }
    if (!empty($modalidades)) {
        wp_set_object_terms($user_id, $modalidades, 'modalidad', false);
    }
    if (!empty($idiomas)) {
        wp_set_object_terms($user_id, $idiomas, 'idiomas', false);
    }

    // Relación con candidato
    update_post_meta($post_id, 'guia_id', $user_id);

    wp_send_json_success(['user_login' => $user_login]);
}
