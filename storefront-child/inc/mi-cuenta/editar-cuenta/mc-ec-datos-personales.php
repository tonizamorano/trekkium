<?php
// Removemos el hook original
remove_action('woocommerce_edit_account_form', 'trekkium_custom_edit_account_fields', 1);

// Registrar el shortcode
add_shortcode('contenido_datos_personales', 'trekkium_custom_edit_account_fields_shortcode');
function trekkium_custom_edit_account_fields_shortcode() {
    return trekkium_custom_edit_account_fields();
}

function trekkium_custom_edit_account_fields() {
    if (!is_user_logged_in()) {
        return '<p>Debes iniciar sesión para ver este formulario.</p>';
    }

    $user_id = get_current_user_id();
    $user = get_userdata($user_id);

    $first_name = $user->first_name;
    $last_name = $user->last_name;
    $display_name = $user->display_name;
    $email = $user->user_email;

    $full_phone = get_user_meta($user_id, 'billing_phone', true);
    
    $default_prefix = '+34';
    $phone_prefix = $default_prefix;
    $phone_number = '';

    if (!empty($full_phone)) {
        $clean_phone = preg_replace('/\s+/', '', $full_phone);
        if (preg_match('/^(\+\d+)(\d{9})$/', $clean_phone, $matches)) {
            $phone_prefix = $matches[1];
            $phone_number = $matches[2];
        } else {
            $phone_number = $full_phone; 
            $phone_prefix = $default_prefix;
        }
    }
    $phone_number = preg_replace('/\s+/', '', $phone_number);

    $country = get_user_meta($user_id, 'billing_country', true);
    $state = get_user_meta($user_id, 'billing_state', true);
    $fecha_nacimiento = get_user_meta($user_id, 'fecha_nacimiento', true);
    $max_date = date('Y-m-d', strtotime('-18 years'));

    ob_start(); ?>

<form class="mc-edit-account-form" action="" method="post" enctype="multipart/form-data">

    <div class="mc-datos-personales-contenedor">
        <div class="mc-datos-personales-seccion-titulo">
            <h2 class="mc-datos-personales-titulo">
                <span>Editar datos personales</span>
            </h2>
        </div>

        <div class="mc-form-fields-wrapper">
            <div class="mc-datos-personales-form-grid">

                <div class="mc-form-row mc-form-row-first">
                    <label for="account_first_name">Nombre <span class="mc-required">*</span></label>
                    <input type="text" class="mc-input-text" name="account_first_name" id="account_first_name" value="<?php echo esc_attr($first_name); ?>" required aria-required="true" />
                </div>
                <div class="mc-form-row mc-form-row-last">
                    <label for="account_last_name">Apellidos <span class="mc-required">*</span></label>
                    <input type="text" class="mc-input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr($last_name); ?>" required aria-required="true" />
                </div>

                <div class="mc-form-row mc-form-row-first">
                    <label for="account_display_name">Nombre visible <span class="mc-required">*</span></label>
                    <input type="text" class="mc-input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr($display_name); ?>" required aria-required="true" />
                </div>
                <div class="mc-form-row mc-form-row-last">
                    <label for="fecha_nacimiento">Fecha de nacimiento <span class="mc-required">*</span></label>
                    <input type="date" class="mc-input-text" name="fecha_nacimiento" id="fecha_nacimiento" value="<?php echo esc_attr($fecha_nacimiento); ?>" max="<?php echo esc_attr($max_date); ?>" required aria-required="true" />
                </div>

                <div class="mc-form-row mc-form-row-first">
                    <label for="billing_phone_number">Teléfono móvil <span class="mc-required">*</span></label>
                    <div class="mc-phone-input-group">
                        <input type="text" class="mc-input-text mc-phone-prefix" name="billing_phone_prefix" id="billing_phone_prefix" value="<?php echo esc_attr($phone_prefix); ?>" placeholder="+XX" pattern="\+\d+" title="Debe empezar con '+' seguido de dígitos." required aria-required="true"/>
                        <input type="tel" class="mc-input-text mc-phone-number" name="billing_phone_number" id="billing_phone_number" value="<?php echo esc_attr($phone_number); ?>" pattern="\d{9}" maxlength="9" title="9 dígitos sin espacios" required aria-required="true"/>
                    </div>
                    <input type="hidden" name="billing_phone" id="billing_phone" value="<?php echo esc_attr($full_phone); ?>" />
                </div>
                <div class="mc-form-row mc-form-row-last">
                    <label for="account_email">Correo electrónico <span class="mc-required">*</span></label>
                    <input type="email" class="mc-input-text" name="account_email" id="account_email" value="<?php echo esc_attr($email); ?>" required aria-required="true" />
                </div>

                <div class="mc-form-row mc-form-row-first">
                    <label for="billing_country">País de residencia <span class="mc-required">*</span></label>
                    <select name="billing_country" id="billing_country" class="mc-input-select mc-country-select" required aria-required="true">
                        <option value="">Seleccionar país</option>
                        <?php
                        $countries = WC()->countries->get_countries();
                        foreach ($countries as $key => $value) {
                            echo '<option value="' . esc_attr($key) . '" ' . selected($country, $key, false) . '>' . esc_html($value) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mc-form-row mc-form-row-last">
                    <label for="billing_state">Provincia <span class="mc-required">*</span></label>
                    <?php
                    $states = WC()->countries->get_states($country);
                    if ($states) {
                        echo '<select name="billing_state" id="billing_state" class="mc-input-select mc-state-select" required aria-required="true">';
                        echo '<option value="">Seleccionar provincia</option>';
                        foreach ($states as $key => $value) {
                            echo '<option value="' . esc_attr($key) . '" ' . selected($state, $key, false) . '>' . esc_html($value) . '</option>';
                        }
                        echo '</select>';
                    } else {
                        echo '<input type="text" class="mc-input-text" name="billing_state" id="billing_state" value="' . esc_attr($state) . '" required aria-required="true" />';
                    }
                    ?>
                </div>

            </div>

        <?php
        $current_user = wp_get_current_user();
        $allowed_roles = ['guia', 'administrator'];

        // **Sección Modalidades favoritas para clientes**
        if (in_array('customer', $current_user->roles)):
            $selected_modalidades = wp_get_object_terms($current_user->ID, 'modalidad', ['fields' => 'ids']);
            if (is_wp_error($selected_modalidades)) $selected_modalidades = [];

            $modalidades_terms = get_terms([
                'taxonomy' => 'modalidad',
                'hide_empty' => false,
            ]);
            
            // Preparar IDs seleccionados para el hidden field
            $selected_ids = !empty($selected_modalidades) ? $selected_modalidades : [];
            ?>
            <div class="mc-form-row mc-form-row-wide mc-modalidades-container" style="margin-top: 25px;">
                <label for="user_modalidades">Modalidades favoritas <span class="mc-required">*</span></label>
                
                <?php if (!empty($modalidades_terms) && !is_wp_error($modalidades_terms)): ?>
                    <div class="mc-modalidades-toggle-list" style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
                        <?php foreach ($modalidades_terms as $term): 
                            $is_active = in_array($term->term_id, $selected_ids) ? 'active' : '';
                            $active_style = $is_active ? 'background-color: #0b568b; color: #ffffff;' : 'background-color: #ffffff; color: #0b568b; border: 2px solid #0b568b;';
                        ?>
                            <button type="button" 
                                class="mc-modalidad-toggle <?php echo $is_active; ?>" 
                                data-term-id="<?php echo esc_attr($term->term_id); ?>"
                                style="
                                    border: none;
                                    border-radius: 50px;
                                    <?php echo $active_style; ?>
                                    cursor: pointer;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:16px;
                                    transition: all 0.2s;
                                    padding: 5px 10px;
                                    line-height:1;
                                    font-family: inherit;
                                ">
                                <?php echo esc_html($term->name); ?>
                            </button>
                        <?php endforeach; ?>
                        <input type="hidden" name="user_modalidades" id="user_modalidades" value="<?php echo esc_attr(implode(',', $selected_ids)); ?>" />
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const toggleButtons = document.querySelectorAll('.mc-modalidad-toggle');
                        const hiddenField = document.getElementById('user_modalidades');

                        // Función para actualizar el campo hidden
                        function updateHiddenField() {
                            const selectedButtons = document.querySelectorAll('.mc-modalidad-toggle.active');
                            const selectedIds = Array.from(selectedButtons).map(button => button.dataset.termId);
                            hiddenField.value = selectedIds.join(',');
                            
                            // Validación visual - marcar como error si no hay selección
                            if (selectedIds.length === 0) {
                                hiddenField.style.borderColor = 'red';
                            } else {
                                hiddenField.style.borderColor = '';
                            }
                        }

                        // Inicializar botones con estado actual
                        toggleButtons.forEach(button => {
                            const termId = button.dataset.termId;
                            const currentValues = hiddenField.value ? hiddenField.value.split(',') : [];
                            
                            if (currentValues.includes(termId)) {
                                button.classList.add('active');
                                button.style.backgroundColor = '#0b568b';
                                button.style.color = '#ffffff';
                            }

                            // Agregar event listener
                            button.addEventListener('click', function() {
                                this.classList.toggle('active');

                                // Cambiar colores según estado
                                if (this.classList.contains('active')) {
                                    this.style.backgroundColor = '#0b568b';
                                    this.style.color = '#ffffff';
                                } else {
                                    this.style.backgroundColor = '#ffffff';
                                    this.style.color = '#0b568b';
                                    this.style.border = '2px solid #0b568b';
                                }

                                updateHiddenField();
                            });
                        });

                        // Validación inicial
                        updateHiddenField();
                    });
                    </script>
                <?php else: ?>
                    <p>No hay modalidades disponibles.</p>
                <?php endif; ?>
            </div>

            <!-- NUEVA SECCIÓN: ETIQUETAS FAVORITAS PARA CLIENTES -->
            <?php
            $selected_etiquetas = wp_get_object_terms($current_user->ID, 'etiquetas_actividad', ['fields' => 'ids']);
            if (is_wp_error($selected_etiquetas)) $selected_etiquetas = [];

            $etiquetas_terms = get_terms([
                'taxonomy' => 'etiquetas_actividad',
                'hide_empty' => false,
            ]);
            
            // Preparar IDs seleccionados para el hidden field
            $selected_etiquetas_ids = !empty($selected_etiquetas) ? $selected_etiquetas : [];
            ?>
            <div class="mc-form-row mc-form-row-wide mc-etiquetas-container" style="margin-top: 25px;">
                <label for="user_etiquetas">Etiquetas favoritas <span class="mc-required">*</span></label>
                
                <?php if (!empty($etiquetas_terms) && !is_wp_error($etiquetas_terms)): ?>
                    <div class="mc-etiquetas-toggle-list" style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
                        <?php foreach ($etiquetas_terms as $term): 
                            $is_active = in_array($term->term_id, $selected_etiquetas_ids) ? 'active' : '';
                            $active_style = $is_active ? 'background-color: #0b568b; color: #ffffff;' : 'background-color: #ffffff; color: #0b568b; border: 2px solid #0b568b;';
                        ?>
                            <button type="button" 
                                class="mc-etiqueta-toggle <?php echo $is_active; ?>" 
                                data-term-id="<?php echo esc_attr($term->term_id); ?>"
                                style="
                                    border: none;
                                    border-radius: 50px;
                                    <?php echo $active_style; ?>
                                    cursor: pointer;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:16px;
                                    transition: all 0.2s;
                                    padding: 5px 10px;
                                    line-height:1;
                                    font-family: inherit;
                                ">
                                <?php echo esc_html($term->name); ?>
                            </button>
                        <?php endforeach; ?>
                        <input type="hidden" name="user_etiquetas" id="user_etiquetas" value="<?php echo esc_attr(implode(',', $selected_etiquetas_ids)); ?>" />
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const toggleButtons = document.querySelectorAll('.mc-etiqueta-toggle');
                        const hiddenField = document.getElementById('user_etiquetas');

                        // Función para actualizar el campo hidden
                        function updateHiddenField() {
                            const selectedButtons = document.querySelectorAll('.mc-etiqueta-toggle.active');
                            const selectedIds = Array.from(selectedButtons).map(button => button.dataset.termId);
                            hiddenField.value = selectedIds.join(',');
                            
                            // Validación visual - marcar como error si no hay selección
                            if (selectedIds.length === 0) {
                                hiddenField.style.borderColor = 'red';
                            } else {
                                hiddenField.style.borderColor = '';
                            }
                        }

                        // Inicializar botones con estado actual
                        toggleButtons.forEach(button => {
                            const termId = button.dataset.termId;
                            const currentValues = hiddenField.value ? hiddenField.value.split(',') : [];
                            
                            if (currentValues.includes(termId)) {
                                button.classList.add('active');
                                button.style.backgroundColor = '#0b568b';
                                button.style.color = '#ffffff';
                            }

                            // Agregar event listener
                            button.addEventListener('click', function() {
                                this.classList.toggle('active');

                                // Cambiar colores según estado
                                if (this.classList.contains('active')) {
                                    this.style.backgroundColor = '#0b568b';
                                    this.style.color = '#ffffff';
                                } else {
                                    this.style.backgroundColor = '#ffffff';
                                    this.style.color = '#0b568b';
                                    this.style.border = '2px solid #0b568b';
                                }

                                updateHiddenField();
                            });
                        });

                        // Validación inicial
                        updateHiddenField();
                    });
                    </script>
                <?php else: ?>
                    <p>No hay etiquetas disponibles.</p>
                <?php endif; ?>
            </div>
            <!-- FIN NUEVA SECCIÓN ETIQUETAS FAVORITAS -->
        <?php endif; ?>

        <?php
        // Sección sobre mí para guías y administradores
        if (array_intersect($allowed_roles, $current_user->roles)):
            $sobre_mi_value = get_user_meta($current_user->ID, 'sobre_mi', true);
        ?>
            <div class="mc-form-row mc-form-row-wide mc-sobremi-container">
                <label for="sobre_mi">Sobre mí <span class="mc-required">*</span></label>
                <?php
                wp_editor(
                    $sobre_mi_value,
                    'sobre_mi',
                    [
                        'textarea_name' => 'sobre_mi',
                        'media_buttons' => false,
                        'teeny'         => false,
                        'quicktags'     => false,
                        'tinymce'       => [
                            'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,undo,redo',
                            'toolbar2' => '',
                        ],
                        'editor_height' => 200,
                        'editor_class'  => 'mc-wp-editor',
                    ]
                );
                ?>
                <input type="hidden" name="sobre_mi_required" value="1" /> 
            </div>

            <div class="mc-form-row mc-form-row-wide mc-idiomas-container">
                <label for="user_idiomas">Idiomas <span class="mc-required">*</span></label>
                <?php
                $selected_idiomas = wp_get_object_terms($current_user->ID, 'idiomas', ['fields' => 'ids']);
                if (is_wp_error($selected_idiomas)) $selected_idiomas = [];

                $idiomas_terms = get_terms([
                    'taxonomy' => 'idiomas',
                    'hide_empty' => false,
                ]);

                // Preparar IDs seleccionados para el hidden field
                $selected_idiomas_ids = !empty($selected_idiomas) ? $selected_idiomas : [];
                
                if (!empty($idiomas_terms) && !is_wp_error($idiomas_terms)):
                ?>
                    <div class="mc-idiomas-toggle-list" style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
                        <?php foreach ($idiomas_terms as $term): 
                            $is_active = in_array($term->term_id, $selected_idiomas_ids) ? 'active' : '';
                            $active_style = $is_active ? 'background-color: #0b568b; color: #ffffff;' : 'background-color: #ffffff; color: #0b568b; border: 2px solid #0b568b;';
                        ?>
                            <button type="button" 
                                class="mc-idioma-toggle <?php echo $is_active; ?>" 
                                data-term-id="<?php echo esc_attr($term->term_id); ?>"
                                style="
                                    border: none;
                                    border-radius: 50px;
                                    <?php echo $active_style; ?>
                                    cursor: pointer;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:16px;
                                    transition: all 0.2s;
                                    padding: 5px 10px;
                                    line-height:1;
                                    font-family: inherit;
                                ">
                                <?php echo esc_html($term->name); ?>
                            </button>
                        <?php endforeach; ?>
                        <input type="hidden" name="user_idiomas" id="user_idiomas" value="<?php echo esc_attr(implode(',', $selected_idiomas_ids)); ?>" />
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const toggleButtons = document.querySelectorAll('.mc-idioma-toggle');
                        const hiddenField = document.getElementById('user_idiomas');

                        // Función para actualizar el campo hidden
                        function updateHiddenField() {
                            const selectedButtons = document.querySelectorAll('.mc-idioma-toggle.active');
                            const selectedIds = Array.from(selectedButtons).map(button => button.dataset.termId);
                            hiddenField.value = selectedIds.join(',');
                            
                            // Validación visual - marcar como error si no hay selección
                            if (selectedIds.length === 0) {
                                hiddenField.style.borderColor = 'red';
                            } else {
                                hiddenField.style.borderColor = '';
                            }
                        }

                        // Inicializar botones con estado actual
                        toggleButtons.forEach(button => {
                            const termId = button.dataset.termId;
                            const currentValues = hiddenField.value ? hiddenField.value.split(',') : [];
                            
                            if (currentValues.includes(termId)) {
                                button.classList.add('active');
                                button.style.backgroundColor = '#0b568b';
                                button.style.color = '#ffffff';
                            }

                            // Agregar event listener
                            button.addEventListener('click', function() {
                                this.classList.toggle('active');

                                // Cambiar colores según estado
                                if (this.classList.contains('active')) {
                                    this.style.backgroundColor = '#0b568b';
                                    this.style.color = '#ffffff';
                                } else {
                                    this.style.backgroundColor = '#ffffff';
                                    this.style.color = '#0b568b';
                                    this.style.border = '2px solid #0b568b';
                                }

                                updateHiddenField();
                            });
                        });

                        // Validación inicial
                        updateHiddenField();
                    });
                    </script>
                <?php else: ?>
                    <p>No hay idiomas disponibles.</p>
                <?php endif; ?>
            </div>
            
            <!-- SECCIÓN: Imagen del banner para guías -->
            <?php if (in_array('guia', $current_user->roles)): ?>
                <?php
                    // Compatibilidad: algunos usuarios tienen 'imagen_banner_guia' (URL), otros el nuevo 'imagen_banner' (attach ID)
                    $imagen_banner_meta = get_user_meta($current_user->ID, 'imagen_banner', true);
                    if (empty($imagen_banner_meta)) {
                        $imagen_banner_meta = get_user_meta($current_user->ID, 'imagen_banner_guia', true);
                    }
                    $imagen_banner_url = '';
                    $imagen_banner_id = 0;
                    if (is_numeric($imagen_banner_meta) && $imagen_banner_meta > 0) {
                        $imagen_banner_id = intval($imagen_banner_meta);
                        $imagen_banner_url = wp_get_attachment_image_url($imagen_banner_id, 'full');
                    } elseif (filter_var($imagen_banner_meta, FILTER_VALIDATE_URL)) {
                        $imagen_banner_url = $imagen_banner_meta;
                    }
                ?>
                <div class="mc-form-row mc-form-row-wide mc-banner-container" style="margin-top:20px;">
                    <label for="imagen_banner_file">Imagen del banner</label>
                    <?php if ($imagen_banner_url): ?>
                        <div class="mc-banner-preview" style="margin:10px 0;">
                            <img src="<?php echo esc_url($imagen_banner_url); ?>" alt="Imagen banner" style="width:100%; max-width:100%; aspect-ratio:16/7; object-fit:cover; border-radius:0;" />
                        </div>
                    <?php endif; ?>

                    <input type="file" name="imagen_banner_file" id="imagen_banner_file" accept="image/*" />
                    <div style="margin-top:8px;">
                        <label style="font-weight:normal; font-size:14px;">
                            <input type="checkbox" name="imagen_banner_delete" value="1" /> Eliminar imagen actual
                        </label>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div> 
    <div class="mc-editar-cuenta-boton-section">
        <?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
        <button type="submit" class="mc-editar-cuenta-boton" name="save_account_details" value="<?php echo esc_attr__('Guardar cambios', 'woocommerce'); ?>"><?php echo esc_html__('Guardar cambios', 'woocommerce'); ?></button>
        <input type="hidden" name="action" value="save_account_details" />
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var billing_country = document.getElementById('billing_country');
    var billing_state = document.getElementById('billing_state');
    var form = document.querySelector('.mc-edit-account-form');
    var prefixField = document.getElementById('billing_phone_prefix');
    var numberField = document.getElementById('billing_phone_number');
    var hiddenPhoneField = document.getElementById('billing_phone');
    
    function updateHiddenPhoneField() {
        if (prefixField && numberField && hiddenPhoneField) {
            var prefix = prefixField.value.trim().replace(/\s/g, '');
            var number = numberField.value.trim().replace(/\s/g, '');
            hiddenPhoneField.value = prefix + number; 
        }
    }
    
    if (form) {
        form.addEventListener('submit', updateHiddenPhoneField);
    }

    if (prefixField) prefixField.addEventListener('input', updateHiddenPhoneField);
    if (numberField) numberField.addEventListener('input', function() {
        this.value = this.value.replace(/\s/g, '');
        updateHiddenPhoneField();
    });

    if (billing_country && billing_state) {
        billing_country.addEventListener('change', function() {
            var country = this.value;
            var stateField = billing_state;

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get_states&country=' + country
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.states) {
                    var selectHTML = '<select name="billing_state" id="billing_state" class="mc-input-select mc-state-select" required aria-required="true">';
                    selectHTML += '<option value="">Seleccionar provincia</option>';
                    for (var key in data.data.states) {
                        selectHTML += '<option value="' + key + '">' + data.data.states[key] + '</option>';
                    }
                    selectHTML += '</select>';
                    stateField.outerHTML = selectHTML; 
                } else {
                    stateField.outerHTML = '<input type="text" class="mc-input-text" name="billing_state" id="billing_state" value="" required aria-required="true" />';
                }
                billing_state = document.getElementById('billing_state'); 
            })
            .catch(error => console.error('Error:', error));
        });
    }
});
</script>

<?php
return ob_get_clean();
}

// Guardado de los campos personalizados (ACTUALIZADO para incluir etiquetas)
add_action('woocommerce_save_account_details', 'trekkium_save_custom_account_fields', 100, 1);
function trekkium_save_custom_account_fields($user_id) {
    if (isset($_POST['save-account-details-nonce']) && !wp_verify_nonce($_POST['save-account-details-nonce'], 'save_account_details')) return;

    $full_phone_normalized = '';
    if (isset($_POST['billing_phone'])) {
        $full_phone_normalized = sanitize_text_field(preg_replace('/\s+/', '', $_POST['billing_phone']));
    }

    wp_update_user([
        'ID'            => $user_id,
        'first_name'    => sanitize_text_field($_POST['account_first_name']),
        'last_name'     => sanitize_text_field($_POST['account_last_name']),
        'display_name'  => sanitize_text_field($_POST['account_display_name']),
    ]);

    $meta_fields = [
        'billing_first_name' => sanitize_text_field($_POST['account_first_name']),
        'billing_last_name'  => sanitize_text_field($_POST['account_last_name']),
        'billing_phone'      => $full_phone_normalized,
        'billing_country'    => sanitize_text_field($_POST['billing_country']),
        'billing_state'      => sanitize_text_field($_POST['billing_state']),
    ];

    foreach ($meta_fields as $key => $value) {
        update_user_meta($user_id, $key, $value);
    }

    if (isset($_POST['fecha_nacimiento'])) {
        update_user_meta($user_id, 'fecha_nacimiento', sanitize_text_field($_POST['fecha_nacimiento']));
    }

    if (isset($_POST['sobre_mi'])) {
        update_user_meta($user_id, 'sobre_mi', wp_kses_post($_POST['sobre_mi']));
    }

    // GUARDADO PARA MODALIDADES
    if (isset($_POST['user_modalidades'])) {
        $modalidades_string = sanitize_text_field($_POST['user_modalidades']);
        $modalidades_array = !empty($modalidades_string) ? 
            array_map('intval', explode(',', $modalidades_string)) : 
            [];
        
        wp_set_object_terms($user_id, $modalidades_array, 'modalidad', false);
    } else {
        wp_set_object_terms($user_id, [], 'modalidad', false);
    }

    // NUEVO GUARDADO PARA ETIQUETAS
    if (isset($_POST['user_etiquetas'])) {
        $etiquetas_string = sanitize_text_field($_POST['user_etiquetas']);
        $etiquetas_array = !empty($etiquetas_string) ? 
            array_map('intval', explode(',', $etiquetas_string)) : 
            [];
        
        wp_set_object_terms($user_id, $etiquetas_array, 'etiquetas_actividad', false);
    } else {
        wp_set_object_terms($user_id, [], 'etiquetas_actividad', false);
    }

    // Guardado de idiomas (ACTUALIZADO para el nuevo formato)
    if (isset($_POST['user_idiomas'])) {
        $idiomas_string = sanitize_text_field($_POST['user_idiomas']);
        $idiomas_array = !empty($idiomas_string) ? 
            array_map('intval', explode(',', $idiomas_string)) : 
            [];
        
        wp_set_object_terms($user_id, $idiomas_array, 'idiomas', false);
    } else {
        wp_set_object_terms($user_id, [], 'idiomas', false);
    }

    if (isset($_POST['account_email'])) {
        $new_email = sanitize_email($_POST['account_email']);
        $current_user = get_userdata($user_id);
        if ($new_email !== $current_user->user_email) {
            wp_update_user(['ID' => $user_id, 'user_email' => $new_email]);
        }
    }

    // Imagen del banner (solo para guías)
    $current_user_obj = get_userdata($user_id);
    if ($current_user_obj && in_array('guia', $current_user_obj->roles)) {
        // Eliminar imagen si se solicitó
        if (!empty($_POST['imagen_banner_delete'])) {
            delete_user_meta($user_id, 'imagen_banner');
            delete_user_meta($user_id, 'imagen_banner_guia');
        }

        // Procesar subida de archivo
        if (!empty($_FILES['imagen_banner_file']) && !empty($_FILES['imagen_banner_file']['tmp_name'])) {
            // Cargar utilidades necesarias
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $file = $_FILES['imagen_banner_file'];
            $overrides = ['test_form' => false];
            $uploaded = wp_handle_upload($file, $overrides);

            if (!empty($uploaded) && empty($uploaded['error'])) {
                $file_path = $uploaded['file'];
                $filetype = wp_check_filetype($file_path, null);
                $attachment = [
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => sanitize_file_name($file['name']),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                ];

                $attach_id = wp_insert_attachment($attachment, $file_path);
                if (!is_wp_error($attach_id)) {
                    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                    // Guardar ID y URL optimizada en metas para compatibilidad
                    update_user_meta($user_id, 'imagen_banner', $attach_id);
                    $attach_url = wp_get_attachment_image_url($attach_id, 'full');
                    if ($attach_url) {
                        update_user_meta($user_id, 'imagen_banner_guia', esc_url($attach_url));
                    }
                }
            }
        }
    }
}

// Ajax para obtener estados/provincias
add_action('wp_ajax_get_states', 'mc_get_states_ajax');
add_action('wp_ajax_nopriv_get_states', 'mc_get_states_ajax');
function mc_get_states_ajax() {
    if (!isset($_POST['country'])) wp_send_json_error(['message' => 'No country provided']);
    $country = sanitize_text_field($_POST['country']);
    $states = WC()->countries->get_states($country);
    wp_send_json_success(['states' => $states]);
}

// Scripts
add_action('wp_enqueue_scripts', 'trekkium_load_scripts_on_account_page');
function trekkium_load_scripts_on_account_page() {
    if (is_account_page()) {
        wp_enqueue_script('jquery');
    }
}

// VALIDACIONES (ACTUALIZADAS para incluir etiquetas)
add_action('woocommerce_save_account_details_errors', 'trekkium_validate_required_fields', 10, 2);
function trekkium_validate_required_fields($errors, $user) {
    $required_fields = [
        'account_first_name' => __('Nombre', 'woocommerce'),
        'account_last_name' => __('Apellidos', 'woocommerce'),
        'account_display_name' => __('Nombre visible', 'woocommerce'),
        'account_email' => __('Correo electrónico', 'woocommerce'),
        'billing_country' => __('País de residencia', 'woocommerce'),
        'fecha_nacimiento' => __('Fecha de nacimiento', 'woocommerce'),
    ];

    foreach ($required_fields as $field => $label) {
        if (empty($_POST[$field])) {
            $errors->add($field . '_error', sprintf(__('%s es un campo obligatorio.', 'woocommerce'), $label));
        }
    }

    if (empty($_POST['billing_phone_prefix'])) $errors->add('billing_phone_prefix_error', __('El prefijo del teléfono es obligatorio.', 'woocommerce'));
    if (empty($_POST['billing_phone_number'])) $errors->add('billing_phone_number_error', __('El número de teléfono es obligatorio.', 'woocommerce'));

    $current_user = wp_get_current_user();
    if (array_intersect(['guia', 'administrator'], $current_user->roles)) {
        if (isset($_POST['sobre_mi_required']) && empty(wp_strip_all_tags($_POST['sobre_mi'], true))) {
             $errors->add('sobre_mi_error', __('Sobre mí es un campo obligatorio para tu rol.', 'woocommerce'));
        }
    }
}

// Validación de teléfono
add_action('woocommerce_save_account_details_errors', 'trekkium_validate_phone_format', 15, 2);
function trekkium_validate_phone_format($errors, $user) {
    if (!isset($_POST['billing_phone_prefix']) || !isset($_POST['billing_phone_number'])) return;
    $prefix = sanitize_text_field($_POST['billing_phone_prefix']);
    $number = sanitize_text_field($_POST['billing_phone_number']);
    $clean_number = preg_replace('/\s+/', '', $number);
    if (!preg_match('/^\+\d+$/', $prefix)) $errors->add('billing_phone_prefix_format_error', __('El prefijo del teléfono debe empezar con "+" y contener solo números.', 'woocommerce'));
    if (!preg_match('/^\d{9}$/', $clean_number)) $errors->add('billing_phone_number_format_error', __('El número de teléfono debe tener exactamente 9 dígitos y no puede contener espacios.', 'woocommerce'));
}

// Validación provincia
add_action('woocommerce_save_account_details_errors', 'trekkium_validate_billing_state', 20, 2);
function trekkium_validate_billing_state($errors, $user) {
    if (empty($_POST['billing_state'])) $errors->add('billing_state_error', __('Por favor, selecciona o introduce una provincia.', 'woocommerce'));
}

// Validación idiomas para guías (ACTUALIZADA para el nuevo formato)
add_action('woocommerce_save_account_details_errors', 'trekkium_validate_idiomas', 20, 2);
function trekkium_validate_idiomas($errors, $user) {
    $current_user = wp_get_current_user();
    if (array_intersect(['guia', 'administrator'], $current_user->roles)) {
        if (!isset($_POST['user_idiomas']) || empty($_POST['user_idiomas'])) {
            $errors->add('user_idiomas_error', __('Por favor, selecciona al menos un idioma.', 'woocommerce'));
        } else {
            $idiomas_string = sanitize_text_field($_POST['user_idiomas']);
            if (empty(trim($idiomas_string, ','))) {
                $errors->add('user_idiomas_error', __('Por favor, selecciona al menos un idioma.', 'woocommerce'));
            }
        }
    }
}

// Validación modalidades para clientes
add_action('woocommerce_save_account_details_errors', function($errors, $user) {
    $current_user = wp_get_current_user();
    if (in_array('customer', $current_user->roles)) {
        if (!isset($_POST['user_modalidades']) || empty($_POST['user_modalidades'])) {
            $errors->add('user_modalidades_error', __('Por favor, selecciona al menos una modalidad favorita.', 'woocommerce'));
        } else {
            $modalidades_string = sanitize_text_field($_POST['user_modalidades']);
            if (empty(trim($modalidades_string, ','))) {
                $errors->add('user_modalidades_error', __('Por favor, selecciona al menos una modalidad favorita.', 'woocommerce'));
            }
        }
    }
}, 25, 2);

// NUEVA VALIDACIÓN PARA ETIQUETAS FAVORITAS
add_action('woocommerce_save_account_details_errors', function($errors, $user) {
    $current_user = wp_get_current_user();
    if (in_array('customer', $current_user->roles)) {
        if (!isset($_POST['user_etiquetas']) || empty($_POST['user_etiquetas'])) {
            $errors->add('user_etiquetas_error', __('Por favor, selecciona al menos una etiqueta favorita.', 'woocommerce'));
        } else {
            $etiquetas_string = sanitize_text_field($_POST['user_etiquetas']);
            if (empty(trim($etiquetas_string, ','))) {
                $errors->add('user_etiquetas_error', __('Por favor, selecciona al menos una etiqueta favorita.', 'woocommerce'));
            }
        }
    }
}, 26, 2);

// Validación teléfono duplicado
add_action('woocommerce_save_account_details_errors', 'trekkium_validate_duplicate_phone', 30, 2);
function trekkium_validate_duplicate_phone($errors, $user) {
    if ($errors->get_error_code()) return;
    if (empty($_POST['billing_phone'])) return;

    $phone_to_check = sanitize_text_field(preg_replace('/\s+/', '', $_POST['billing_phone']));
    $user_id = $user->ID;

    $args = array(
        'meta_key'     => 'billing_phone',
        'meta_value'   => $phone_to_check,
        'meta_compare' => '=',
        'exclude'      => array($user_id),
        'number'       => 1,
    );
    
    $users_with_phone = get_users($args);
    if (!empty($users_with_phone)) $errors->add('billing_phone_duplicate_error', __('El teléfono móvil ya está registrado por otra cuenta.', 'woocommerce'));
}

// Validación email duplicado
add_action('woocommerce_save_account_details_errors', 'trekkium_validate_duplicate_email', 30, 2);
function trekkium_validate_duplicate_email($errors, $user) {
    if (empty($_POST['account_email'])) return;

    $new_email = sanitize_email($_POST['account_email']);
    $current_user = get_userdata($user->ID);

    if ($new_email !== $current_user->user_email && email_exists($new_email)) {
        $errors->add('account_email_duplicate_error', __('El correo electrónico ya está registrado por otra cuenta.', 'woocommerce'));
    }
}
?>