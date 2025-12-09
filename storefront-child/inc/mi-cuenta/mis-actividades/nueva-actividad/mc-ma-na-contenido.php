<?php
/**
 * Shortcode Formulario Nueva Actividad (Productos WooCommerce)
 */

function contenido_nueva_actividad_shortcode() {
    ob_start();

    // Encolar scripts para el media uploader
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }

    // Guardar nueva actividad al enviar formulario
    if ( isset($_POST['formulario_nueva_actividad_nonce']) && wp_verify_nonce($_POST['formulario_nueva_actividad_nonce'], 'crear_actividad') ) {

        $titulo      = sanitize_text_field( $_POST['actividad_titulo'] );
        $descripcion = wp_kses_post( $_POST['actividad_descripcion'] );

        if ( ! empty($titulo) && ! empty($descripcion) ) {

            // Crear el nuevo producto como borrador pendiente
            $new_product_id = wp_insert_post([
                'post_title'   => $titulo,
                'post_content' => $descripcion,
                'post_type'    => 'product',
                'post_status'  => 'pending',
                'post_author'  => get_current_user_id(),
            ]);

            if ( is_wp_error($new_product_id) ) {
                echo '<p style="color:red;font-weight:bold;">⚠️ Error al crear la actividad: ' . esc_html($new_product_id->get_error_message()) . '</p>';

            } elseif ( $new_product_id ) {

                // IMÁGENES - procesar desde el formulario de imágenes
                if ( isset($_POST['actividad_imagen_1']) && intval($_POST['actividad_imagen_1']) ) {
                    set_post_thumbnail( $new_product_id, intval($_POST['actividad_imagen_1']) );
                }

                // Galería
                $galeria_ids = [];
                for ($i=1; $i<=4; $i++) {
                    if ( isset($_POST['actividad_galeria_'.$i]) && intval($_POST['actividad_galeria_'.$i]) ) {
                        $galeria_ids[] = intval($_POST['actividad_galeria_'.$i]);
                    }
                }
                if (! empty($galeria_ids)) {
                    update_post_meta( $new_product_id, '_product_image_gallery', implode(',', $galeria_ids) );
                }

                // Taxonomías
                $taxonomias = ['tipo','modalidad','pais','region','provincia','dificultad'];
                foreach ( $taxonomias as $tax ) {
                    if ( isset($_POST["actividad_{$tax}"]) ) {
                        $term_id = intval($_POST["actividad_{$tax}"]);
                        if ($term_id > 0) {
                            wp_set_post_terms( $new_product_id, [ $term_id ], $tax, false );
                        }
                    }
                }

                // Campos meta
                $campos_meta = [
                    'distancia','duracion','desnivel_positivo','desnivel_negativo',
                    'encuentro','google_maps','dificultad_tecnica','planificacion',
                    'material','incluye','hora','espacio_natural','dias','fecha_fin',
                    'plazas_totales','plazas_minimas','edad_minima','precio_guia',
                    'enlace_whatsapp','experiencia_requisitos'
                ];

                foreach ( $campos_meta as $campo ) {
                    if ( isset($_POST["actividad_{$campo}"]) ) {

                        if ($campo === 'distancia') {
                            $valor = number_format(floatval($_POST["actividad_distancia"]), 2, '.', '');
                        }
                        elseif ($campo === 'duracion') {
                            $valor = number_format(floatval($_POST["actividad_duracion"]), 2, '.', '');
                        }
                        elseif (in_array($campo, ['desnivel_positivo','desnivel_negativo','plazas_totales','plazas_minimas','edad_minima'])) {
                            $valor = intval($_POST["actividad_{$campo}"]);
                        }
                        elseif ($campo === 'precio_guia') {
                            $valor = number_format(floatval($_POST["actividad_{$campo}"]), 2, '.', '');
                        }
                        elseif ($campo === 'google_maps' || $campo === 'enlace_whatsapp') {
                            $valor = esc_url_raw($_POST["actividad_{$campo}"]);
                        }
                        elseif ($campo === 'espacio_natural') {
                            $valor = sanitize_text_field($_POST["actividad_{$campo}"]);
                        }
                        else {
                            $valor = wp_kses_post($_POST["actividad_{$campo}"]);
                        }

                        update_post_meta($new_product_id, $campo, $valor);
                    }
                }

                // Fecha inicio
                if ( isset($_POST['actividad_fecha']) ) {
                    $fecha_input = sanitize_text_field($_POST['actividad_fecha']);
                    $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_input);
                    if ($fecha_obj) {
                        update_post_meta($new_product_id, 'fecha', $fecha_obj->format('Y-m-d'));
                    }
                }

                // Fecha fin
                if (isset($_POST['actividad_fecha_fin'])) {
                    $fecha_input = sanitize_text_field($_POST['actividad_fecha_fin']);
                    $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_input);
                    if ($fecha_obj) {
                        update_post_meta($new_product_id, 'fecha_fin', $fecha_obj->format('Y-m-d'));
                    }
                }

                // Días
                if (isset($_POST['actividad_dias'])) {
                    update_post_meta($new_product_id, 'dias', intval($_POST['actividad_dias']));
                }

                // Redirigir finalmente a mis-actividades
                wp_safe_redirect( home_url('/mis-actividades/?creado=1') );
                exit;

                echo '<p style="color:green;font-weight:bold;">✅ Actividad creada correctamente y pendiente de revisión.</p>';
            }

        } else {
            echo '<p style="color:red;font-weight:bold;">⚠️ El título y la descripción son obligatorios.</p>';
        }
    }

    ?>

    <div class="mc-ma-na-titular">
        <h2>Nueva actividad</h2>  
    </div>

    <form method="post" class="mc-ma-na-form-contenedor">

        <?php wp_nonce_field('crear_actividad', 'formulario_nueva_actividad_nonce'); ?>

        <!-- Título -->
        <div class="mc-ma-na-grid-1col">
            <label class="edit-form-titular">Título de la actividad*</label>
            <input type="text" name="actividad_titulo" class="edit-form-text" required>
        </div>

        <!-- Descripción -->
        <div class="mc-ma-na-grid-1col">
            <label class="edit-form-titular">Descripción*</label>

            <?php
            wp_editor(
                '',
                'actividad_descripcion',
                [
                    'textarea_name' => 'actividad_descripcion',
                    'textarea_rows' => 12,
                    'media_buttons' => false,
                    'teeny'         => false,
                    'quicktags'     => false,
                    'tinymce'       => [
                        'toolbar1' => 'formatselect bold italic underline bullist numlist alignleft aligncenter alignright link unlink fullscreen',
                        'toolbar2' => 'undo redo removeformat charmap outdent indent table',
                        'plugins'  => 'lists link paste wordpress'
                    ],
                ]
            );
            ?>
        </div>

        <!-- IMÁGENES - REEMPLAZADO POR SHORTCODE -->
        <?php echo do_shortcode('[mc_ma_na_form_imagen_principal]'); ?>
        <?php echo do_shortcode('[mc_ma_na_form_imagenes]'); ?>

        <!-- Tipo y Modalidad -->
        <div class="mc-ma-na-grid-2col">
            <!-- Tipo -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Tipo*</label>
                <select name="actividad_tipo" class="edit-form-text" required>
                    <option value="">Selecciona tipo</option>
                    <?php
                    $terms_tipo = get_terms(['taxonomy'=>'tipo','hide_empty'=>false]);
                    if (! is_wp_error($terms_tipo)) {
                        foreach($terms_tipo as $term)
                            echo '<option value="'.esc_attr($term->term_id).'">'.esc_html($term->name).'</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- Modalidad -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Modalidad*</label>
                <select name="actividad_modalidad" class="edit-form-text" required>
                    <option value="">Selecciona modalidad</option>
                    <?php
                    $user_id = get_current_user_id();
                    $user_modalidades = wp_get_object_terms($user_id, 'modalidad', ['fields'=>'names']);

                    if (! is_wp_error($user_modalidades)) {
                        foreach ($user_modalidades as $mod_name) {
                            $term = get_term_by('name', $mod_name, 'modalidad');
                            if ($term) {
                                echo '<option value="'.esc_attr($term->term_id).'">'.esc_html($term->name).'</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- País / Región / Provincia / Dificultad -->
        <div class="mc-ma-na-grid-2col-imagenes">

            <!-- País -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">País*</label>
                <select name="actividad_pais" id="actividad_pais" class="edit-form-text" required>
                    <option value="">Selecciona país</option>
                    <?php
                    $terms_pais = get_terms(['taxonomy'=>'pais','hide_empty'=>false]);
                    if (! is_wp_error($terms_pais)) {
                        // Solo mostrar términos sin padre (nivel superior)
                        foreach($terms_pais as $term):
                            if ($term->parent == 0): ?>
                                <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                            <?php endif;
                        endforeach;
                    }
                    ?>
                </select>
            </div>

            <!-- Región -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Región*</label>
                <select name="actividad_region" id="actividad_region" class="edit-form-text" required>
                    <option value="">Selecciona región</option>
                </select>
            </div>

            <!-- Provincia -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Provincia*</label>
                <select name="actividad_provincia" id="actividad_provincia" class="edit-form-text" required>
                    <option value="">Selecciona provincia</option>
                </select>
            </div>

            <!-- Dificultad -->
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Dificultad física*</label>
                <select name="actividad_dificultad" class="edit-form-text" required>
                    <option value="">Selecciona dificultad</option>
                    <?php
                    $terms_dif = get_terms(['taxonomy'=>'dificultad','hide_empty'=>false]);
                    if (! is_wp_error($terms_dif)) {
                        foreach($terms_dif as $term)
                            echo '<option value="'.esc_attr($term->term_id).'">'.esc_html($term->name).'</option>';
                    }
                    ?>
                </select>
            </div>

        </div>

        <!-- Espacio natural -->
        <div class="mc-ma-na-grid-1col">
            <label class="edit-form-titular">Espacio natural*</label>
            <input type="text" name="actividad_espacio_natural" class="edit-form-text" required>
        </div>


        <!-- Días + Fecha -->
        <div class="mc-ma-na-grid-2col">

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Días*</label>
                <input type="number" name="actividad_dias" id="actividad_dias" class="edit-form-text" min="1" required>
            </div>

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Fecha*</label>
                <input type="date" name="actividad_fecha" id="actividad_fecha" class="edit-form-text" required>
            </div>

        </div>

        <!-- Fecha fin + Hora -->
        <div class="mc-ma-na-grid-2col">

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Fecha fin*</label>
                <input type="date" name="actividad_fecha_fin" id="actividad_fecha_fin" class="edit-form-text" required>
            </div>

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Hora*</label>
                <input type="time" name="actividad_hora" class="edit-form-text" required>
            </div>

        </div>

        <!-- Encuentro + Maps + WhatsApp -->
        <?php
        $campos = [
            'encuentro'       => ['text','Punto de encuentro*'],
            'google_maps'     => ['url','Enlace a Google Maps*'],
            'enlace_whatsapp' => ['url','Enlace al grupo de Whatsapp*']
        ];

        foreach ($campos as $slug => $data):
            [$tipo, $label] = $data;
            echo '<div class="mc-ma-na-grid-1col"><label class="edit-form-titular">'.$label.'</label>';

            if ($tipo==='text'){
                echo '<input type="text" name="actividad_'.$slug.'" class="edit-form-text" required>';
            }
            if ($tipo==='url'){
                echo '<input type="url" name="actividad_'.$slug.'" class="edit-form-text" required>';
            }

            echo '</div>';
        endforeach;
        ?>


        <!-- Distancia y Duración -->
        <div class="mc-ma-na-grid-2col">
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Distancia (Km)*</label>
                <input type="number" name="actividad_distancia" class="edit-form-text" required step="0.01">
            </div>

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Duración (h)*</label>
                <input type="number" name="actividad_duracion" class="edit-form-text" required step="0.01">

            </div>
        </div>

        <!-- Desnivel ± -->
        <div class="mc-ma-na-grid-2col">
            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Desnivel positivo (m)*</label>
                <input type="number" name="actividad_desnivel_positivo" class="edit-form-text" required>
            </div>

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Desnivel negativo (m)*</label>
                <input type="number" name="actividad_desnivel_negativo" class="edit-form-text" required>
            </div>
        </div>

        <!-- Ratios -->
        <div class="mc-ma-na-grid-2col">

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Ratio máximo*</label>
                <input type="number" name="actividad_plazas_totales" class="edit-form-text" min="1" required>
            </div>

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Ratio mínimo*</label>
                <input type="number" name="actividad_plazas_minimas" class="edit-form-text" min="1" required>
            </div>

        </div>

        <!-- Edad mínima + Precio guía -->
        <div class="mc-ma-na-grid-2col">

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Edad mínima*</label>
                <input type="number" name="actividad_edad_minima" class="edit-form-text" min="1" required>
            </div>

            <div class="mc-ma-na-grid-1col">
                <label class="edit-form-titular">Precio del guía*</label>
                <input type="number" name="actividad_precio_guia" class="edit-form-text" min="0" step="0.01" required>
            </div>

        </div>

        <!-- Campos editor -->
        <?php echo do_shortcode('[mc_ma_na_form_edit_text]'); ?>

        <div class="mc-ma-na-botones-contenedor">
            <button class="mis-actividades-boton-guardar-cambios" type="submit">Crear actividad</button>
            <button type="button" class="mis-actividades-boton-cancelar" onclick="window.location.href='<?php echo esc_url(home_url('/mis-actividades/')); ?>'">Cancelar</button>
        </div>
    </form>

    <script>
    // Dependencias país -> región -> provincia
    jQuery(document).ready(function($){
        function cargarOpciones(taxonomia, parentId, targetSelect){
            $.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "POST",
                data: {
                    action: "cargar_terminos_condicionales",
                    taxonomia: taxonomia,
                    parent: parentId
                },
                success: function(response){
                    $(targetSelect).html(response);
                }
            });
        }
        
        $("#actividad_pais").change(function(){
            let paisId = $(this).val();
            let paisName = $(this).find('option:selected').text();
            cargarOpciones("region", paisName, "#actividad_region");
            $("#actividad_provincia").html('<option value="">Selecciona provincia</option>');
        });
        
        $("#actividad_region").change(function(){
            let regionId = $(this).val();
            cargarOpciones("provincia", regionId, "#actividad_provincia");
        });
    });

    // Lógica para Tipo = Actividad/Viaje
    jQuery(document).ready(function($){

        function actualizarDiasYFechas() {
            let tipo = $('select[name="actividad_tipo"] option:selected').text().toLowerCase();
            let diasInput = $('#actividad_dias');
            let fechaInicio = $('#actividad_fecha');
            let fechaFin = $('#actividad_fecha_fin');

            if (tipo.includes("actividad")) {
                diasInput.val(1).prop('readonly', true).attr('min', 1);

                fechaInicio.on('change', function(){
                    fechaFin.val($(this).val()).prop('readonly', true);
                });

                if (fechaInicio.val()) {
                    fechaFin.val(fechaInicio.val()).prop('readonly', true);
                }

            } else if (tipo.includes("viaje")) {

                diasInput.prop('readonly', false).attr('min', 2);
                fechaFin.prop('readonly', false);

            } else {

                diasInput.val('').prop('readonly', false).attr('min', 1);
                fechaFin.prop('readonly', false);
            }
        }

        $('select[name="actividad_tipo"]').change(actualizarDiasYFechas);
        actualizarDiasYFechas();
    });

    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('contenido_nueva_actividad', 'contenido_nueva_actividad_shortcode');