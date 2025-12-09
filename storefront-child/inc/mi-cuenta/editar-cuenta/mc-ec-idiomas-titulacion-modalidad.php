<?php
// Shortcode para idiomas, modalidades y etiquetas
add_shortcode('mc_ec_idiomas_modalidades_etiquetas', 'mc_ec_idiomas_modalidades_etiquetas_shortcode');
function mc_ec_idiomas_modalidades_etiquetas_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>Debes iniciar sesión para ver esta sección.</p>';
    }

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    ob_start();
    ?>
    
    <div class="mc-idiomas-modalidades-etiquetas-container">
        <?php
        // Sección de preferencias para clientes PUROS (no para guías)
        if (in_array('customer', $current_user->roles) && !in_array('guia', $current_user->roles)):
            // Obtener modalidades
            $selected_modalidades = wp_get_object_terms($user_id, 'modalidad', ['fields' => 'ids']);
            if (is_wp_error($selected_modalidades)) $selected_modalidades = [];

            $modalidades_terms = get_terms([
                'taxonomy' => 'modalidad',
                'hide_empty' => false,
            ]);
            
            $selected_modalidades_ids = !empty($selected_modalidades) ? $selected_modalidades : [];
            
            // Obtener etiquetas
            $selected_etiquetas = wp_get_object_terms($user_id, 'etiquetas_actividad', ['fields' => 'ids']);
            if (is_wp_error($selected_etiquetas)) $selected_etiquetas = [];

            $etiquetas_terms = get_terms([
                'taxonomy' => 'etiquetas_actividad',
                'hide_empty' => false,
            ]);
            
            $selected_etiquetas_ids = !empty($selected_etiquetas) ? $selected_etiquetas : [];
            ?>
            
            <div class="mc-form-row mc-form-row-wide mc-preferencias-container" style="margin-top: 25px;">
                <label for="user_preferencias">Mis preferencias <span class="mc-required">*</span></label>
                
                <!-- Modalidades -->
                <?php if (!empty($modalidades_terms) && !is_wp_error($modalidades_terms)): ?>
                    <div class="mc-preferencias-row" style="margin-top:10px; margin-bottom:15px;">
                        <div class="mc-preferencias-toggle-list" style="display:flex; gap:10px; flex-wrap:wrap;">
                            <?php foreach ($modalidades_terms as $term): 
                                $is_active = in_array($term->term_id, $selected_modalidades_ids) ? 'active' : '';
                                $active_style = $is_active ? 'background-color: #0b568b; color: #ffffff;' : 'background-color: #ffffff; color: #0b568b; border: 2px solid #0b568b;';
                            ?>
                                <button type="button" 
                                    class="mc-modalidad-toggle <?php echo $is_active; ?>" 
                                    data-term-id="<?php echo esc_attr($term->term_id); ?>"
                                    data-type="modalidad"
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
                        </div>
                    </div>
                <?php else: ?>
                    <p style="margin:10px 0 15px 0;">No hay modalidades disponibles.</p>
                <?php endif; ?>
                
                <!-- Etiquetas -->
                <?php if (!empty($etiquetas_terms) && !is_wp_error($etiquetas_terms)): ?>
                    <div class="mc-preferencias-row">
                        <div class="mc-preferencias-toggle-list" style="display:flex; gap:10px; flex-wrap:wrap;">
                            <?php foreach ($etiquetas_terms as $term): 
                                $is_active = in_array($term->term_id, $selected_etiquetas_ids) ? 'active' : '';
                                $active_style = $is_active ? 'background-color: #0b568b; color: #ffffff;' : 'background-color: #ffffff; color: #0b568b; border: 2px solid #0b568b;';
                                
                                // Formatear el nombre de la etiqueta: si empieza con #, quitar espacios y caracteres especiales
                                $display_name = $term->name;
                                if (strpos($display_name, '#') === 0) {
                                    // Quitar acentos y caracteres especiales, mantener # al inicio
                                    $display_name = '#' . preg_replace('/[^a-zA-Z0-9]/', '', substr($display_name, 1));
                                }
                            ?>
                                <button type="button" 
                                    class="mc-etiqueta-toggle <?php echo $is_active; ?>" 
                                    data-term-id="<?php echo esc_attr($term->term_id); ?>"
                                    data-type="etiqueta"
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
                                    <?php echo esc_html($display_name); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p style="margin:10px 0;">No hay etiquetas disponibles.</p>
                <?php endif; ?>
                
                <!-- Campos ocultos para ambos tipos -->
                <input type="hidden" name="user_modalidades" id="user_modalidades" value="<?php echo esc_attr(implode(',', $selected_modalidades_ids)); ?>" />
                <input type="hidden" name="user_etiquetas" id="user_etiquetas" value="<?php echo esc_attr(implode(',', $selected_etiquetas_ids)); ?>" />
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Función para actualizar campos ocultos
                function updateHiddenField(type) {
                    let hiddenField, toggleButtons;
                    
                    if (type === 'modalidad') {
                        hiddenField = document.getElementById('user_modalidades');
                        toggleButtons = document.querySelectorAll('.mc-modalidad-toggle.active');
                    } else if (type === 'etiqueta') {
                        hiddenField = document.getElementById('user_etiquetas');
                        toggleButtons = document.querySelectorAll('.mc-etiqueta-toggle.active');
                    }
                    
                    if (!hiddenField) return;
                    
                    const selectedIds = Array.from(toggleButtons).map(button => button.dataset.termId);
                    hiddenField.value = selectedIds.join(',');
                    
                    // Validación visual
                    if (selectedIds.length === 0) {
                        hiddenField.style.borderColor = 'red';
                    } else {
                        hiddenField.style.borderColor = '';
                    }
                }

                // Configurar botones de modalidad
                const modalidadButtons = document.querySelectorAll('.mc-modalidad-toggle');
                modalidadButtons.forEach(button => {
                    const termId = button.dataset.termId;
                    const currentValues = document.getElementById('user_modalidades').value ? 
                        document.getElementById('user_modalidades').value.split(',') : [];
                    
                    // Establecer estado inicial
                    if (currentValues.includes(termId)) {
                        button.classList.add('active');
                        button.style.backgroundColor = '#0b568b';
                        button.style.color = '#ffffff';
                    }

                    // Agregar event listener
                    button.addEventListener('click', function() {
                        this.classList.toggle('active');

                        if (this.classList.contains('active')) {
                            this.style.backgroundColor = '#0b568b';
                            this.style.color = '#ffffff';
                            this.style.border = 'none';
                        } else {
                            this.style.backgroundColor = '#ffffff';
                            this.style.color = '#0b568b';
                            this.style.border = '2px solid #0b568b';
                        }

                        updateHiddenField('modalidad');
                    });
                });

                // Configurar botones de etiqueta
                const etiquetaButtons = document.querySelectorAll('.mc-etiqueta-toggle');
                etiquetaButtons.forEach(button => {
                    const termId = button.dataset.termId;
                    const currentValues = document.getElementById('user_etiquetas').value ? 
                        document.getElementById('user_etiquetas').value.split(',') : [];
                    
                    // Establecer estado inicial
                    if (currentValues.includes(termId)) {
                        button.classList.add('active');
                        button.style.backgroundColor = '#0b568b';
                        button.style.color = '#ffffff';
                    }

                    // Agregar event listener
                    button.addEventListener('click', function() {
                        this.classList.toggle('active');

                        if (this.classList.contains('active')) {
                            this.style.backgroundColor = '#0b568b';
                            this.style.color = '#ffffff';
                            this.style.border = 'none';
                        } else {
                            this.style.backgroundColor = '#ffffff';
                            this.style.color = '#0b568b';
                            this.style.border = '2px solid #0b568b';
                        }

                        updateHiddenField('etiqueta');
                    });
                });

                // Inicializar campos ocultos
                updateHiddenField('modalidad');
                updateHiddenField('etiqueta');
            });
            </script>
        <?php endif; ?>

        <?php
        // Sección idiomas para guías y administradores (NO para clientes puros)
        if (in_array('guia', $current_user->roles) || in_array('administrator', $current_user->roles)):
            ?>
            <div class="mc-form-row mc-form-row-wide mc-idiomas-container">
                <label for="user_idiomas">Idiomas <span class="mc-required">*</span></label>
                <?php
                $selected_idiomas = wp_get_object_terms($user_id, 'idiomas', ['fields' => 'ids']);
                if (is_wp_error($selected_idiomas)) $selected_idiomas = [];

                $idiomas_terms = get_terms([
                    'taxonomy' => 'idiomas',
                    'hide_empty' => false,
                ]);

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

                        function updateHiddenField() {
                            const selectedButtons = document.querySelectorAll('.mc-idioma-toggle.active');
                            const selectedIds = Array.from(selectedButtons).map(button => button.dataset.termId);
                            hiddenField.value = selectedIds.join(',');
                            
                            if (selectedIds.length === 0) {
                                hiddenField.style.borderColor = 'red';
                            } else {
                                hiddenField.style.borderColor = '';
                            }
                        }

                        toggleButtons.forEach(button => {
                            const termId = button.dataset.termId;
                            const currentValues = hiddenField.value ? hiddenField.value.split(',') : [];
                            
                            if (currentValues.includes(termId)) {
                                button.classList.add('active');
                                button.style.backgroundColor = '#0b568b';
                                button.style.color = '#ffffff';
                            }

                            button.addEventListener('click', function() {
                                this.classList.toggle('active');

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

                        updateHiddenField();
                    });
                    </script>
                <?php else: ?>
                    <p>No hay idiomas disponibles.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php
    return ob_get_clean();
}

// Función de guardado para idiomas, modalidades y etiquetas
add_action('woocommerce_save_account_details', 'mc_ec_save_idiomas_modalidades_etiquetas', 101, 1);
function mc_ec_save_idiomas_modalidades_etiquetas($user_id) {
    if (isset($_POST['save-account-details-nonce']) && !wp_verify_nonce($_POST['save-account-details-nonce'], 'save_account_details')) return;

    $current_user = get_userdata($user_id);
    $user_roles = $current_user->roles;
    
    // GUARDADO PARA MODALIDADES (solo clientes PUROS, NO guías)
    if (in_array('customer', $user_roles) && !in_array('guia', $user_roles) && isset($_POST['user_modalidades'])) {
        $modalidades_string = sanitize_text_field($_POST['user_modalidades']);
        $modalidades_array = !empty($modalidades_string) ? 
            array_map('intval', explode(',', $modalidades_string)) : 
            [];
        
        wp_set_object_terms($user_id, $modalidades_array, 'modalidad', false);
    }

    // GUARDADO PARA ETIQUETAS (solo clientes PUROS, NO guías)
    if (in_array('customer', $user_roles) && !in_array('guia', $user_roles) && isset($_POST['user_etiquetas'])) {
        $etiquetas_string = sanitize_text_field($_POST['user_etiquetas']);
        $etiquetas_array = !empty($etiquetas_string) ? 
            array_map('intval', explode(',', $etiquetas_string)) : 
            [];
        
        wp_set_object_terms($user_id, $etiquetas_array, 'etiquetas_actividad', false);
    }

    // GUARDADO PARA IDIOMAS (solo guías y administradores)
    if ((in_array('guia', $user_roles) || in_array('administrator', $user_roles)) && isset($_POST['user_idiomas'])) {
        $idiomas_string = sanitize_text_field($_POST['user_idiomas']);
        $idiomas_array = !empty($idiomas_string) ? 
            array_map('intval', explode(',', $idiomas_string)) : 
            [];
        
        wp_set_object_terms($user_id, $idiomas_array, 'idiomas', false);
    }
}

// Validaciones para idiomas, modalidades y etiquetas
add_action('woocommerce_save_account_details_errors', 'mc_ec_validate_idiomas_modalidades_etiquetas', 25, 2);
function mc_ec_validate_idiomas_modalidades_etiquetas($errors, $user) {
    $current_user = wp_get_current_user();
    $user_roles = $current_user->roles;
    
    // Validación modalidades para clientes PUROS (no guías)
    if (in_array('customer', $user_roles) && !in_array('guia', $user_roles)) {
        if (!isset($_POST['user_modalidades']) || empty($_POST['user_modalidades'])) {
            $errors->add('user_modalidades_error', __('Por favor, selecciona al menos una modalidad favorita.', 'woocommerce'));
        } else {
            $modalidades_string = sanitize_text_field($_POST['user_modalidades']);
            if (empty(trim($modalidades_string, ','))) {
                $errors->add('user_modalidades_error', __('Por favor, selecciona al menos una modalidad favorita.', 'woocommerce'));
            }
        }
    }

    // Validación etiquetas para clientes PUROS (no guías)
    if (in_array('customer', $user_roles) && !in_array('guia', $user_roles)) {
        if (!isset($_POST['user_etiquetas']) || empty($_POST['user_etiquetas'])) {
            $errors->add('user_etiquetas_error', __('Por favor, selecciona al menos una etiqueta favorita.', 'woocommerce'));
        } else {
            $etiquetas_string = sanitize_text_field($_POST['user_etiquetas']);
            if (empty(trim($etiquetas_string, ','))) {
                $errors->add('user_etiquetas_error', __('Por favor, selecciona al menos una etiqueta favorita.', 'woocommerce'));
            }
        }
    }

    // Validación idiomas para guías y administradores
    if (in_array('guia', $user_roles) || in_array('administrator', $user_roles)) {
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
?>