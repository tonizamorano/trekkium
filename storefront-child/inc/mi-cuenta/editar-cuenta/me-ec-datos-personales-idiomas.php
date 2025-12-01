<?php
// Shortcode para la sección de idiomas
add_shortcode('mc_ec_datos_personales_idiomas', 'mc_custom_edit_account_idiomas_shortcode');
function mc_custom_edit_account_idiomas_shortcode() {
    return mc_custom_edit_account_idiomas();
}

function mc_custom_edit_account_idiomas() {
    if (!is_user_logged_in()) {
        return '';
    }

    $current_user = wp_get_current_user();
    $allowed_roles = ['guia', 'administrator'];
    
    // Solo mostrar para guías y administradores
    if (!array_intersect($allowed_roles, $current_user->roles)) {
        return '';
    }

    $selected_idiomas = wp_get_object_terms($current_user->ID, 'idiomas', ['fields' => 'ids']);
    if (is_wp_error($selected_idiomas)) $selected_idiomas = [];

    $idiomas_terms = get_terms([
        'taxonomy' => 'idiomas',
        'hide_empty' => false,
    ]);

    // Preparar IDs seleccionados para el hidden field
    $selected_idiomas_ids = !empty($selected_idiomas) ? $selected_idiomas : [];
    
    ob_start();
    ?>
    
    <div class="mc-form-row mc-form-row-wide mc-idiomas-container">
        <label for="user_idiomas">Idiomas <span class="mc-required">*</span></label>
        <?php if (!empty($idiomas_terms) && !is_wp_error($idiomas_terms)): ?>
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
                <!-- IMPORTANTE: Este campo hidden debe tener name="user_idiomas" -->
                <input type="hidden" name="user_idiomas" id="user_idiomas" value="<?php echo esc_attr(implode(',', $selected_idiomas_ids)); ?>" />
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Verificar si estamos dentro de un formulario con la clase correcta
                const form = document.querySelector('.mc-edit-account-form');
                if (!form) return;
                
                const toggleButtons = document.querySelectorAll('.mc-idioma-toggle');
                const hiddenField = document.getElementById('user_idiomas');
                
                if (!hiddenField) return;

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
                
                // Asegurar que el campo se incluya en el submit del formulario
                form.addEventListener('submit', function() {
                    updateHiddenField();
                });
            });
            </script>
        <?php else: ?>
            <p>No hay idiomas disponibles.</p>
        <?php endif; ?>
    </div>
    
    <?php
    return ob_get_clean();
}