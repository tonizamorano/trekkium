<?php
/**
 * Shortcode Formulario Editar Actividad (Productos WooCommerce)
 */

function contenido_editar_actividad_shortcode() {
    ob_start();

    // Verificar que el usuario está editando una actividad existente
    if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
        return '<p style="color:red;font-weight:bold;">⚠️ No se ha especificado una actividad para editar.</p>';
    }

    $post_id = intval($_GET['post_id']);
    $product = wc_get_product($post_id);

    // Verificar que el producto existe y pertenece al usuario actual
    if (!$product || $product->get_type() !== 'simple') {
        return '<p style="color:red;font-weight:bold;">⚠️ La actividad no existe o no es válida.</p>';
    }

    if ($product->get_post_data()->post_author != get_current_user_id()) {
        return '<p style="color:red;font-weight:bold;">⚠️ No tienes permisos para editar esta actividad.</p>';
    }

    // Encolar scripts para el media uploader
    if (!did_action('wp_enqueue_media')) {
        wp_enqueue_media();
    }

    // Variable para controlar si se mostró el modal
    $mostrar_modal_exito = false;

    // Guardar cambios al enviar formulario
    if (isset($_POST['formulario_editar_actividad_nonce']) && wp_verify_nonce($_POST['formulario_editar_actividad_nonce'], 'editar_actividad_' . $post_id)) {

        $titulo = sanitize_text_field($_POST['actividad_titulo']);
        $descripcion = wp_kses_post($_POST['actividad_descripcion']);

        if (!empty($titulo) && !empty($descripcion)) {

            // Actualizar el producto
            wp_update_post([
                'ID' => $post_id,
                'post_title' => $titulo,
                'post_content' => $descripcion,
                'post_status' => 'publish', // Mantener publicado
            ]);

            // IMÁGENES - procesar desde el formulario de imágenes
            if (isset($_POST['actividad_imagen_1']) && intval($_POST['actividad_imagen_1'])) {
                set_post_thumbnail($post_id, intval($_POST['actividad_imagen_1']));
            }

            // Galería
            $galeria_ids = [];
            for ($i = 1; $i <= 4; $i++) {
                if (isset($_POST['actividad_galeria_' . $i]) && intval($_POST['actividad_galeria_' . $i])) {
                    $galeria_ids[] = intval($_POST['actividad_galeria_' . $i]);
                }
            }
            if (!empty($galeria_ids)) {
                update_post_meta($post_id, '_product_image_gallery', implode(',', $galeria_ids));
            }

            // Campos editables (los que NO están en la lista de solo lectura)
            $campos_editables = [
                'distancia', 'duracion', 'desnivel_positivo', 'desnivel_negativo',
                'encuentro', 'google_maps', 'dificultad_tecnica', 'planificacion',
                'material', 'hora', 'espacio_natural', 'enlace_whatsapp',
                'experiencia_requisitos', 'incluye'
            ];

            foreach ($campos_editables as $campo) {
                if (isset($_POST["actividad_{$campo}"])) {

                    if ($campo === 'distancia') {
                        $valor = number_format(floatval($_POST["actividad_distancia"]), 2, '.', '');
                    } elseif ($campo === 'duracion') {
                        $valor = number_format(floatval($_POST["actividad_duracion"]), 2, '.', '');
                    } elseif (in_array($campo, ['desnivel_positivo', 'desnivel_negativo'])) {
                        $valor = intval($_POST["actividad_{$campo}"]);
                    } elseif ($campo === 'google_maps' || $campo === 'enlace_whatsapp') {
                        $valor = esc_url_raw($_POST["actividad_{$campo}"]);
                    } elseif ($campo === 'espacio_natural') {
                        $valor = sanitize_text_field($_POST["actividad_{$campo}"]);
                    } else {
                        $valor = wp_kses_post($_POST["actividad_{$campo}"]);
                    }

                    update_post_meta($post_id, $campo, $valor);
                }
            }

            // Marcar para mostrar el modal
            $mostrar_modal_exito = true;

        } else {
            echo '<p style="color:red;font-weight:bold;">⚠️ El título y la descripción son obligatorios.</p>';
        }
    }

    // Obtener valores actuales para los campos
    $titulo = get_the_title($post_id);
    $descripcion = get_post_field('post_content', $post_id);
    $imagen_principal = get_post_thumbnail_id($post_id);

    // Obtener términos actuales
    $tipo_actual = wp_get_post_terms($post_id, 'tipo', ['fields' => 'ids']);
    $modalidad_actual = wp_get_post_terms($post_id, 'modalidad', ['fields' => 'ids']);
    $pais_actual = wp_get_post_terms($post_id, 'pais', ['fields' => 'ids']);
    $region_actual = wp_get_post_terms($post_id, 'region', ['fields' => 'ids']);
    $provincia_actual = wp_get_post_terms($post_id, 'provincia', ['fields' => 'ids']);
    $dificultad_actual = wp_get_post_terms($post_id, 'dificultad', ['fields' => 'ids']);

    // Obtener valores de meta
    $valores_meta = [];
    $campos_meta = [
        'distancia', 'duracion', 'desnivel_positivo', 'desnivel_negativo',
        'encuentro', 'google_maps', 'dificultad_tecnica', 'planificacion',
        'material', 'incluye', 'hora', 'espacio_natural', 'dias', 'fecha_fin',
        'plazas_totales', 'plazas_minimas', 'edad_minima', 'precio_guia',
        'enlace_whatsapp', 'experiencia_requisitos', 'fecha'
    ];

    foreach ($campos_meta as $campo) {
        $valores_meta[$campo] = get_post_meta($post_id, $campo, true);
    }

    // Obtener galería
    $galeria_string = get_post_meta($post_id, '_product_image_gallery', true);
    $galeria_ids = $galeria_string ? explode(',', $galeria_string) : [];
    ?>

    <div class="mc-ma-na-titular">
        <h2>Editar actividad</h2>
    </div>

    <form method="post" class="mc-ma-na-form-contenedor">

        <?php wp_nonce_field('editar_actividad_' . $post_id, 'formulario_editar_actividad_nonce'); ?>

        <!-- Título -->
        <div class="mc-ma-na-grid-1col">
            <label class="edit-form-titular">Título de la actividad*</label>
            <input type="text" name="actividad_titulo" class="edit-form-text" value="<?php echo esc_attr($titulo); ?>" required>
        </div>

        <!-- Descripción -->
        <div class="mc-ma-na-grid-1col">
            <label class="edit-form-titular">Descripción*</label>
            <?php
            wp_editor(
                $descripcion,
                'actividad_descripcion',
                [
                    'textarea_name' => 'actividad_descripcion',
                    'textarea_rows' => 12,
                    'media_buttons' => false,
                    'teeny' => false,
                    'quicktags' => false,
                    'tinymce' => [
                        'toolbar1' => 'formatselect bold italic underline bullist numlist alignleft aligncenter alignright link unlink fullscreen',
                        'toolbar2' => 'undo redo removeformat charmap outdent indent table',
                        'plugins' => 'lists link paste wordpress'
                    ],
                ]
            );
            ?>
        </div>

        <!-- IMÁGENES - REEMPLAZADO POR SHORTCODE (pero con valores actuales) -->
        <div id="imagen-principal-container">
            <?php echo do_shortcode('[mc_ma_ea_form_imagen_principal edit_id="' . $imagen_principal . '"]'); ?>
        </div>
        <div id="galeria-container">
            <?php 
            $galeria_shortcode = '[mc_ma_ea_form_imagenes';
            if (!empty($galeria_ids)) {
                $galeria_shortcode .= ' edit_ids="' . implode(',', $galeria_ids) . '"';
            }
            $galeria_shortcode .= ']';
            echo do_shortcode($galeria_shortcode);
            ?>
        </div>

        <!-- Tipo (Solo lectura) -->
        <div class="mc-ma-na-grid-2col">
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Tipo*</label>
                <div class="campo-solo-lectura">
                    <?php
                    if (!empty($tipo_actual) && !is_wp_error($tipo_actual)) {
                        $tipo_term = get_term($tipo_actual[0], 'tipo');
                        echo esc_html($tipo_term ? $tipo_term->name : 'No especificado');
                    } else {
                        echo 'No especificado';
                    }
                    ?>
                </div>
                <input type="hidden" name="actividad_tipo" value="<?php echo !empty($tipo_actual[0]) ? esc_attr($tipo_actual[0]) : ''; ?>">
            </div>

            <!-- Modalidad (Solo lectura) -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Modalidad*</label>
                <div class="campo-solo-lectura">
                    <?php
                    if (!empty($modalidad_actual) && !is_wp_error($modalidad_actual)) {
                        $modalidad_term = get_term($modalidad_actual[0], 'modalidad');
                        echo esc_html($modalidad_term ? $modalidad_term->name : 'No especificado');
                    } else {
                        echo 'No especificado';
                    }
                    ?>
                </div>
                <input type="hidden" name="actividad_modalidad" value="<?php echo !empty($modalidad_actual[0]) ? esc_attr($modalidad_actual[0]) : ''; ?>">
            </div>
        </div>

        <!-- País / Región / Provincia / Dificultad (Solo lectura) -->
        <div class="mc-ma-na-grid-2col-imagenes">

            <!-- País (Solo lectura) -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">País*</label>
                <div class="campo-solo-lectura">
                    <?php
                    if (!empty($pais_actual) && !is_wp_error($pais_actual)) {
                        $pais_term = get_term($pais_actual[0], 'pais');
                        echo esc_html($pais_term ? $pais_term->name : 'No especificado');
                    } else {
                        echo 'No especificado';
                    }
                    ?>
                </div>
                <input type="hidden" name="actividad_pais" id="actividad_pais" value="<?php echo !empty($pais_actual[0]) ? esc_attr($pais_actual[0]) : ''; ?>">
            </div>

            <!-- Región (Solo lectura) -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Región*</label>
                <div class="campo-solo-lectura">
                    <?php
                    if (!empty($region_actual) && !is_wp_error($region_actual)) {
                        $region_term = get_term($region_actual[0], 'region');
                        echo esc_html($region_term ? $region_term->name : 'No especificado');
                    } else {
                        echo 'No especificado';
                    }
                    ?>
                </div>
                <input type="hidden" name="actividad_region" id="actividad_region" value="<?php echo !empty($region_actual[0]) ? esc_attr($region_actual[0]) : ''; ?>">
            </div>

            <!-- Provincia (Solo lectura) -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Provincia*</label>
                <div class="campo-solo-lectura">
                    <?php
                    if (!empty($provincia_actual) && !is_wp_error($provincia_actual)) {
                        $provincia_term = get_term($provincia_actual[0], 'provincia');
                        echo esc_html($provincia_term ? $provincia_term->name : 'No especificado');
                    } else {
                        echo 'No especificado';
                    }
                    ?>
                </div>
                <input type="hidden" name="actividad_provincia" id="actividad_provincia" value="<?php echo !empty($provincia_actual[0]) ? esc_attr($provincia_actual[0]) : ''; ?>">
            </div>

            <!-- Dificultad (Solo lectura) -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Dificultad física*</label>
                <div class="campo-solo-lectura">
                    <?php
                    if (!empty($dificultad_actual) && !is_wp_error($dificultad_actual)) {
                        $dificultad_term = get_term($dificultad_actual[0], 'dificultad');
                        echo esc_html($dificultad_term ? $dificultad_term->name : 'No especificado');
                    } else {
                        echo 'No especificado';
                    }
                    ?>
                </div>
                <input type="hidden" name="actividad_dificultad" value="<?php echo !empty($dificultad_actual[0]) ? esc_attr($dificultad_actual[0]) : ''; ?>">
            </div>

        </div>

        <!-- Espacio natural (Editable) -->
        <div class="mc-ma-na-grid-1col">
            <label class="edit-form-titular">Espacio natural*</label>
            <input type="text" name="actividad_espacio_natural" class="edit-form-text" value="<?php echo esc_attr($valores_meta['espacio_natural']); ?>" required>
        </div>

        <!-- Días (Solo lectura) -->
        <div class="mc-ma-na-grid-2col">
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Días*</label>
                <div class="campo-solo-lectura">
                    <?php echo esc_html($valores_meta['dias'] ? $valores_meta['dias'] : 'No especificado'); ?>
                </div>
                <input type="hidden" name="actividad_dias" id="actividad_dias" value="<?php echo esc_attr($valores_meta['dias']); ?>">
            </div>

            <!-- Fecha (Solo lectura) -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Fecha*</label>
                <div class="campo-solo-lectura">
                    <?php echo esc_html($valores_meta['fecha'] ? date('d/m/Y', strtotime($valores_meta['fecha'])) : 'No especificada'); ?>
                </div>
                <input type="hidden" name="actividad_fecha" id="actividad_fecha" value="<?php echo esc_attr($valores_meta['fecha']); ?>">
            </div>
        </div>

        <!-- Fecha fin (Solo lectura) + Hora (Editable) -->
        <div class="mc-ma-na-grid-2col">
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Fecha fin*</label>
                <div class="campo-solo-lectura">
                    <?php echo esc_html($valores_meta['fecha_fin'] ? date('d/m/Y', strtotime($valores_meta['fecha_fin'])) : 'No especificada'); ?>
                </div>
                <input type="hidden" name="actividad_fecha_fin" id="actividad_fecha_fin" value="<?php echo esc_attr($valores_meta['fecha_fin']); ?>">
            </div>

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Hora*</label>
                <input type="time" name="actividad_hora" class="edit-form-text" value="<?php echo esc_attr($valores_meta['hora']); ?>" required>
            </div>
        </div>

        <!-- Encuentro + Maps + WhatsApp (Editables) -->
        <div class="mc-ma-na-grid-1col">
            <label class="edit-form-titular">Punto de encuentro*</label>
            <input type="text" name="actividad_encuentro" class="edit-form-text" value="<?php echo esc_attr($valores_meta['encuentro']); ?>" required>
        </div>

        <div class="mc-ma-na-grid-1col">
            <label class="edit-form-titular">Enlace a Google Maps*</label>
            <input type="url" name="actividad_google_maps" class="edit-form-text" value="<?php echo esc_url($valores_meta['google_maps']); ?>" required>
        </div>

        <div class="mc-ma-na-grid-1col">
            <label class="edit-form-titular">Enlace al grupo de Whatsapp*</label>
            <input type="url" name="actividad_enlace_whatsapp" class="edit-form-text" value="<?php echo esc_url($valores_meta['enlace_whatsapp']); ?>" required>
        </div>

        <!-- Distancia y Duración (Editables) -->
        <div class="mc-ma-na-grid-2col">
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Distancia (Km)*</label>
                <input type="number" name="actividad_distancia" class="edit-form-text" value="<?php echo esc_attr($valores_meta['distancia']); ?>" required step="0.01">
            </div>

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Duración (h)*</label>
                <input type="number" name="actividad_duracion" class="edit-form-text" value="<?php echo esc_attr($valores_meta['duracion']); ?>" required step="0.01">
            </div>
        </div>

        <!-- Desnivel ± (Editables) -->
        <div class="mc-ma-na-grid-2col">
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Desnivel positivo (m)*</label>
                <input type="number" name="actividad_desnivel_positivo" class="edit-form-text" value="<?php echo esc_attr($valores_meta['desnivel_positivo']); ?>" required>
            </div>

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Desnivel negativo (m)*</label>
                <input type="number" name="actividad_desnivel_negativo" class="edit-form-text" value="<?php echo esc_attr($valores_meta['desnivel_negativo']); ?>" required>
            </div>
        </div>

        <!-- Ratios (Solo lectura) -->
        <div class="mc-ma-na-grid-2col">
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Ratio máximo*</label>
                <div class="campo-solo-lectura">
                    <?php echo esc_html($valores_meta['plazas_totales'] ? $valores_meta['plazas_totales'] : 'No especificado'); ?>
                </div>
                <input type="hidden" name="actividad_plazas_totales" value="<?php echo esc_attr($valores_meta['plazas_totales']); ?>">
            </div>

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Ratio mínimo*</label>
                <div class="campo-solo-lectura">
                    <?php echo esc_html($valores_meta['plazas_minimas'] ? $valores_meta['plazas_minimas'] : 'No especificado'); ?>
                </div>
                <input type="hidden" name="actividad_plazas_minimas" value="<?php echo esc_attr($valores_meta['plazas_minimas']); ?>">
            </div>
        </div>

        <!-- Edad mínima (Solo lectura) + Precio guía (Solo lectura) -->
        <div class="mc-ma-na-grid-2col">
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Edad mínima*</label>
                <div class="campo-solo-lectura">
                    <?php echo esc_html($valores_meta['edad_minima'] ? $valores_meta['edad_minima'] : 'No especificado'); ?>
                </div>
                <input type="hidden" name="actividad_edad_minima" value="<?php echo esc_attr($valores_meta['edad_minima']); ?>">
            </div>

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Precio del guía*</label>
                <div class="campo-solo-lectura">
                    <?php echo esc_html($valores_meta['precio_guia'] ? number_format(floatval($valores_meta['precio_guia']), 2, ',', '') . ' €' : 'No especificado'); ?>
                </div>
                <input type="hidden" name="actividad_precio_guia" value="<?php echo esc_attr($valores_meta['precio_guia']); ?>">
            </div>
        </div>

        <!-- Campos editor (Editables) -->
        <?php
        // Necesitarás modificar el shortcode [mc_ma_na_form_edit_text] para aceptar valores
        echo do_shortcode('[mc_ma_ea_form_edit_text post_id="' . $post_id . '"]');
        ?>

        <div class="mc-ma-na-botones-contenedor">
            <button class="mis-actividades-boton-guardar-cambios" type="submit">Guardar cambios</button>
            <button type="button" class="mis-actividades-boton-cancelar" onclick="window.location.href='<?php echo esc_url(home_url('/mis-actividades/')); ?>'">Cancelar</button>
        </div>
    </form>

    <!-- Modal de éxito -->
    <?php if ($mostrar_modal_exito): ?>
    <div id="modal-exito-actividad" class="modal-exito-overlay" style="display: flex;">
        <div class="modal-exito-contenido">
            <button class="modal-exito-cerrar" onclick="cerrarModalExito()">&times;</button>
            <div class="modal-exito-mensaje">
                ✅ Tus cambios se han actualizado correctamente en el anuncio de tu actividad.
            </div>
        </div>
    </div>

    <script>
    function cerrarModalExito() {
        var modal = document.getElementById('modal-exito-actividad');
        if (modal) {
            modal.style.display = 'none';
        }
    }
    
    // Cerrar modal al hacer clic fuera del contenido
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('modal-exito-actividad');
        if (modal) {
            modal.addEventListener('click', function(event) {
                if (event.target === this) {
                    cerrarModalExito();
                }
            });
        }
    });
    </script>
    <?php endif; ?>

    

    <script>
    // Deshabilitar la lógica de dependencias para el formulario de edición
    jQuery(document).ready(function($){
        // No necesitamos las dependencias AJAX ya que los campos son de solo lectura
        
        // Mostrar mensaje informativo sobre campos no editables
        $('.campo-solo-lectura, .campo-solo-lectura-editor').each(function() {
            var $this = $(this);
            var originalText = $this.text();
            
            // Agregar tooltip informativo
            $this.attr('title', 'Este campo no se puede editar');
            
            // Si está vacío, mostrar "No especificado"
            if (!originalText.trim()) {
                $this.text('No especificado');
                $this.css('color', '#999');
                $this.css('font-style', 'italic');
            }
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('contenido_editar_actividad', 'contenido_editar_actividad_shortcode');