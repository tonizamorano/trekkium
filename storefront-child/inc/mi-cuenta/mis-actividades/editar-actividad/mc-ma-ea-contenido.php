<?php
/**
 * Shortcode Formulario Editar Actividad (Productos WooCommerce)
 */

function contenido_editar_actividad_shortcode() {
    ob_start();

    // Obtener ID de producto si se pasa por GET (?post_id=...) o por ?product_id=...
    if ( isset($_GET['post_id']) ) {
        $product_id = intval($_GET['post_id']);
    } elseif ( isset($_GET['product_id']) ) {
        $product_id = intval($_GET['product_id']);
    } else {
        $product_id = 0;
    }

    $product = null;
    if ( $product_id ) {
        $post_obj = get_post( $product_id );
        if ( $post_obj && $post_obj->post_type === 'product' ) {
            $product = wc_get_product( $product_id );
        }
    }

    if ( ! $product ) {
        echo '<p>No se encontró el producto a editar.</p>';
        return ob_get_clean();
    }

    // Encolar scripts para el media uploader
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }

    // Guardar cambios al enviar formulario
    if ( isset($_POST['formulario_editar_actividad_nonce']) && wp_verify_nonce($_POST['formulario_editar_actividad_nonce'], 'guardar_actividad') ) {
        $titulo      = sanitize_text_field( $_POST['actividad_titulo'] );
        $descripcion = wp_kses_post( $_POST['actividad_descripcion'] );

        // Imagen destacada
        if ( isset($_POST['actividad_imagen_principal']) && intval($_POST['actividad_imagen_principal']) ) {
            set_post_thumbnail( $product_id, intval($_POST['actividad_imagen_principal']) );
        }

        // Galería de producto (imagenes 2, 3, 4)
        $galeria_ids = [];
        if ( isset($_POST['actividad_imagen_2']) && intval($_POST['actividad_imagen_2']) ) {
            $galeria_ids[] = intval($_POST['actividad_imagen_2']);
        }
        if ( isset($_POST['actividad_imagen_3']) && intval($_POST['actividad_imagen_3']) ) {
            $galeria_ids[] = intval($_POST['actividad_imagen_3']);
        }
        if ( isset($_POST['actividad_imagen_4']) && intval($_POST['actividad_imagen_4']) ) {
            $galeria_ids[] = intval($_POST['actividad_imagen_4']);
        }

        // Guardar en la metabox de WooCommerce
        update_post_meta( $product_id, '_product_image_gallery', implode(',', $galeria_ids) );

        // Guardar términos de taxonomías
        if ( isset($_POST['actividad_tipo']) ) {
            wp_set_post_terms( $product_id, array(intval($_POST['actividad_tipo'])), 'tipo', false );
        }
        if ( isset($_POST['actividad_modalidad']) ) {
            wp_set_post_terms( $product_id, array(intval($_POST['actividad_modalidad'])), 'modalidad', false );
        }

        // Guardar País, Región y Provincia
        if ( isset($_POST['actividad_pais']) ) {
            wp_set_post_terms( $product_id, array(intval($_POST['actividad_pais'])), 'pais', false );
        }
        if ( isset($_POST['actividad_region']) ) {
            wp_set_post_terms( $product_id, array(intval($_POST['actividad_region'])), 'region', false );
        }
        if ( isset($_POST['actividad_provincia']) ) {
            wp_set_post_terms( $product_id, array(intval($_POST['actividad_provincia'])), 'provincia', false );
        }


        // Guardar otros campos personalizados
        if ( isset($_POST['actividad_distancia']) ) {
            update_post_meta($product_id, 'distancia', sanitize_text_field($_POST['actividad_distancia']));
        }
        if ( isset($_POST['actividad_duracion']) ) {
            update_post_meta($product_id, 'duracion', sanitize_text_field($_POST['actividad_duracion']));
        }
        if ( isset($_POST['actividad_desnivel_positivo']) ) {
            update_post_meta($product_id, 'desnivel_positivo', sanitize_text_field($_POST['actividad_desnivel_positivo']));
        }
        if ( isset($_POST['actividad_desnivel_negativo']) ) {
            update_post_meta($product_id, 'desnivel_negativo', sanitize_text_field($_POST['actividad_desnivel_negativo']));
        }

        if ( isset($_POST['actividad_encuentro']) ) {
            update_post_meta($product_id, 'encuentro', sanitize_textarea_field($_POST['actividad_encuentro']));
        }

        if ( isset($_POST['actividad_google_maps']) ) {
            update_post_meta($product_id, 'google_maps', esc_url_raw($_POST['actividad_google_maps']));
        }

        if ( isset($_POST['actividad_dificultad']) ) {
            wp_set_post_terms( $product_id, array(intval($_POST['actividad_dificultad'])), 'dificultad', false );
        }

        if ( isset($_POST['actividad_dificultad_tecnica']) ) {
            update_post_meta($product_id, 'dificultad_tecnica', wp_kses_post($_POST['actividad_dificultad_tecnica']));
        }
        if ( isset($_POST['actividad_planificacion']) ) {
            update_post_meta($product_id, 'planificacion', wp_kses_post($_POST['actividad_planificacion']));
        }
        if ( isset($_POST['actividad_material']) ) {
            update_post_meta($product_id, 'material', wp_kses_post($_POST['actividad_material']));
        }
        if ( isset($_POST['actividad_incluye']) ) {
            update_post_meta($product_id, 'incluye', wp_kses_post($_POST['actividad_incluye']));
        }


        // Guardar fecha y hora
        if ( isset($_POST['actividad_fecha']) ) {
            $fecha_input = sanitize_text_field($_POST['actividad_fecha']);
            // Normalizar a Y-m-d
            $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_input);
            if ($fecha_obj) {
                $fecha_guardar = $fecha_obj->format('Y-m-d');
                update_post_meta($product_id, 'fecha', $fecha_guardar);
            }
        }

        if ( isset($_POST['actividad_hora']) ) {
            update_post_meta($product_id, 'hora', sanitize_text_field($_POST['actividad_hora']));
        }

        if ( ! empty($titulo) && ! empty($descripcion) ) {
            wp_update_post( [
                'ID'           => $product_id,
                'post_title'   => $titulo,
                'post_content' => $descripcion,
            ] );
            echo '<p style="color:green;font-weight:bold;">✅ Actividad actualizada correctamente.</p>';
            $product = wc_get_product( $product_id );
        } else {
            echo '<p style="color:red;font-weight:bold;">⚠️ Todos los campos son obligatorios.</p>';
        }
    }

    // Valores actuales
    $thumbnail_id   = get_post_thumbnail_id( $product_id );
    $thumbnail_url  = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : '';

    // Galería de imágenes WooCommerce
    $galeria_ids = $product->get_gallery_image_ids();
    $imagen2_id  = $galeria_ids[0] ?? 0;
    $imagen3_id  = $galeria_ids[1] ?? 0;
    $imagen4_id  = $galeria_ids[2] ?? 0;

    $imagen2_url = $imagen2_id ? wp_get_attachment_image_url($imagen2_id, 'medium') : '';
    $imagen3_url = $imagen3_id ? wp_get_attachment_image_url($imagen3_id, 'medium') : '';
    $imagen4_url = $imagen4_id ? wp_get_attachment_image_url($imagen4_id, 'medium') : '';
    
    // Obtener valores actuales de Fecha y Hora
    $fecha_actual = get_post_meta( $product_id, 'fecha', true );
    $hora_actual  = get_post_meta( $product_id, 'hora', true );

    // Normalizar la hora a formato HH:MM
    if ( ! empty( $hora_actual ) ) {
        $hora_obj = DateTime::createFromFormat('H:i', $hora_actual);
        if ( ! $hora_obj ) {
            $hora_obj = DateTime::createFromFormat('H:i:s', $hora_actual);
        }
        if ( $hora_obj ) {
            $hora_actual = $hora_obj->format('H:i');
        }
    }
    ?>

    <!-- Titular de sección -->

    <div class="editar-actividad-seccion-titular">
        <h2 class="editar-actividad-titular">
            <span>Editar actividad</span>
        </h2>	
    </div>

    <!-- Formulario de Editar Actividad -->

    <form method="post" class="producto-edit-form-contenedor-ppal">

        <?php wp_nonce_field('guardar_actividad', 'formulario_editar_actividad_nonce'); ?>

        <!-- Título -->
        <div class="contenedor-1col">
            <label class="edit-form-titular">Título de la actividad*</label>
            <input type="text" name="actividad_titulo" class="edit-form-text" value="<?php echo esc_attr( $product->get_name() ); ?>" required>
        </div>

        <!-- Descripción -->
        <div class="contenedor-1col">
            <label class="edit-form-titular">Descripción*</label>
            <?php
            wp_editor(
                $product->get_description(),
                'actividad_descripcion',
                [
                    'textarea_name' => 'actividad_descripcion',
                    'textarea_rows' => 12,
                    'media_buttons' => false,
                    'teeny'         => false,
                    'quicktags'     => false,
                    'tinymce'       => [
                        'toolbar1' => 'formatselect bold italic underline strikethrough bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more fullscreen',
                        'toolbar2' => 'undo redo forecolor backcolor pastetext removeformat charmap outdent indent table',
                        'toolbar4' => '',
                        'plugins'  => 'charmap colorpicker hr lists paste tabfocus textcolor wordpress wpeditimage wpemoji wplink wpdialogs wpview',
                    ],
                ]
            );
            ?>
        </div>

        <!-- Imagenes 1 y 2 -->
        <div class="contenedor-2col">
            <!-- Imagen principal -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Imagen principal*</label>
                <div class="edit-form-image-box" onclick="abrirMediaUploader(this, 'actividad_imagen_principal')">
                    <?php if ( $thumbnail_url ) : ?>
                        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="">
                    <?php else : ?>
                        <span>Haz clic para seleccionar</span>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="actividad_imagen_principal" id="actividad_imagen_principal" value="<?php echo esc_attr($thumbnail_id); ?>">
            </div>

            <!-- Imagen 2 -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Imagen 2*</label>
                <div class="edit-form-image-box" onclick="abrirMediaUploader(this, 'actividad_imagen_2')">
                    <?php if ( $imagen2_url ) : ?>
                        <img src="<?php echo esc_url($imagen2_url); ?>" alt="">
                    <?php else : ?>
                        <span>Haz clic para seleccionar</span>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="actividad_imagen_2" id="actividad_imagen_2" value="<?php echo esc_attr($imagen2_id); ?>">
            </div>
        </div>

        <!-- Imagenes 3 y 4 -->
        <div class="contenedor-2col">
            <!-- Imagen 3 -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Imagen 3*</label>
                <div class="edit-form-image-box" onclick="abrirMediaUploader(this, 'actividad_imagen_3')">
                    <?php if ( $imagen3_url ) : ?>
                        <img src="<?php echo esc_url($imagen3_url); ?>" alt="">
                    <?php else : ?>
                        <span>Haz clic para seleccionar</span>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="actividad_imagen_3" id="actividad_imagen_3" value="<?php echo esc_attr($imagen3_id); ?>">
            </div>

            <!-- Imagen 4 -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Imagen 4*</label>
                <div class="edit-form-image-box" onclick="abrirMediaUploader(this, 'actividad_imagen_4')">
                    <?php if ( $imagen4_url ) : ?>
                        <img src="<?php echo esc_url($imagen4_url); ?>" alt="">
                    <?php else : ?>
                        <span>Haz clic para seleccionar</span>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="actividad_imagen_4" id="actividad_imagen_4" value="<?php echo esc_attr($imagen4_id); ?>">
            </div>
        </div>

        <!-- Tipo y Modalidad -->
        <div class="contenedor-2col">
            <!-- Tipo -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Tipo*</label>
                <?php
                $terms_tipo = get_terms(['taxonomy'=>'tipo','hide_empty'=>false]);
                $selected_tipo = wp_get_post_terms($product_id,'tipo',['fields'=>'ids']);
                ?>
                <select name="actividad_tipo" class="edit-form-text" required>
                    <option value="">Selecciona tipo</option>
                    <?php foreach($terms_tipo as $term): ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>" <?php echo in_array($term->term_id,$selected_tipo)?'selected':''; ?>>
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Modalidad -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Modalidad*</label>
                <?php
                $terms_modalidad = get_terms(['taxonomy'=>'modalidad','hide_empty'=>false]);
                $selected_modalidad = wp_get_post_terms($product_id,'modalidad',['fields'=>'ids']);
                ?>
                <select name="actividad_modalidad" class="edit-form-text" required>
                    <option value="">Selecciona modalidad</option>
                    <?php foreach($terms_modalidad as $term): ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>" <?php echo in_array($term->term_id,$selected_modalidad)?'selected':''; ?>>
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>



        <!-- País y Región -->
        <div class="contenedor-2col">
            <!-- País -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">País*</label>
                <?php
                $terms_pais = get_terms(['taxonomy'=>'pais','hide_empty'=>false]);
                $selected_pais = wp_get_post_terms($product_id,'pais',['fields'=>'ids']);
                ?>
                <select name="actividad_pais" id="actividad_pais" class="edit-form-text" required>
                    <option value="">Selecciona país</option>
                    <?php foreach($terms_pais as $term): ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>" <?php echo in_array($term->term_id,$selected_pais)?'selected':''; ?>>
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Región -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Región*</label>
                <?php
                $terms_region = get_terms(['taxonomy'=>'region','hide_empty'=>false]);
                $selected_region = wp_get_post_terms($product_id,'region',['fields'=>'ids']);
                ?>
                <select name="actividad_region" id="actividad_region" class="edit-form-text" required>
                    <option value="">Selecciona región</option>
                    <?php foreach($terms_region as $term): ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>" <?php echo in_array($term->term_id,$selected_region)?'selected':''; ?>>
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Provincia -->
        <div class="contenedor-2col">
            <!-- Provincia -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Provincia*</label>
                <?php
                $terms_provincia = get_terms(['taxonomy'=>'provincia','hide_empty'=>false]);
                $selected_provincia = wp_get_post_terms($product_id,'provincia',['fields'=>'ids']);
                ?>
                <select name="actividad_provincia" id="actividad_provincia" class="edit-form-text" required>
                    <option value="">Selecciona provincia</option>
                    <?php foreach($terms_provincia as $term): ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>" <?php echo in_array($term->term_id,$selected_provincia)?'selected':''; ?>>
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Columna vacía -->
            <div class="contenedor-1col">
                <!-- Espacio vacío -->
            </div>
        </div>


        <!-- Fecha y Hora -->
        <div class="contenedor-2col">
            <!-- Fecha -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Fecha*</label>
                <input type="date" name="actividad_fecha" class="edit-form-text" value="<?php echo esc_attr($fecha_actual); ?>" required>
            </div>

            <!-- Hora -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Hora*</label>
                <input type="time" name="actividad_hora" class="edit-form-text" value="<?php echo esc_attr($hora_actual); ?>" required>
            </div>
        </div>

        <!-- Punto de encuentro -->
        <div class="contenedor-1col">
            <label class="edit-form-titular">Punto de encuentro*</label>
            <textarea name="actividad_encuentro" class="edit-form-text" rows="3" required><?php echo esc_textarea( get_post_meta($product_id, 'encuentro', true) ); ?></textarea>
        </div>

        <!-- Enlace a Google Maps -->
        <div class="contenedor-1col">
            <label class="edit-form-titular">Enlace a GoogleMaps*</label>
            <textarea name="actividad_google_maps" class="edit-form-text" rows="3" required><?php echo esc_textarea( get_post_meta($product_id, 'google_maps', true) ); ?></textarea>
        </div>

        <!-- Distancia y Duración -->
        <div class="contenedor-2col">
            <!-- Distancia (Km) -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Distancia*</label>
                <div class="input-with-suffix">
                    <input type="number" name="actividad_distancia" class="edit-form-text" 
                        value="<?php echo esc_attr( get_post_meta($product_id, 'distancia', true) ); ?>" 
                        step="any" required>
                    <span class="input-suffix">Km</span>
                </div>
            </div>

            <!-- Duración (h) -->

            <div class="contenedor-1col">
                <label class="edit-form-titular">Duración*</label>
                <div class="input-with-suffix">
                    <input type="number" name="actividad_duracion" class="edit-form-text" 
                        value="<?php echo esc_attr( get_post_meta($product_id, 'duracion', true) ); ?>" required>
                    <span class="input-suffix">h</span>
                </div>
            </div>
        </div>

        <!-- Desnivel positivo y negativo -->

        <div class="contenedor-2col">

            <!-- Desnivel positivo (m) -->

            <div class="contenedor-1col">

                <label class="edit-form-titular">Desnivel positivo*</label>

                <div class="input-with-suffix">
                    <input type="number" name="actividad_desnivel_positivo" class="edit-form-text" 
                        value="<?php echo esc_attr( get_post_meta($product_id, 'desnivel_positivo', true) ); ?>" required>
                    <span class="input-suffix">m</span>
                </div>

            </div>

            <!-- Desnivel negativo (m) -->

            <div class="contenedor-1col">

                <label class="edit-form-titular">Desnivel negativo*</label>

                <div class="input-with-suffix">
                    <input type="number" name="actividad_desnivel_negativo" class="edit-form-text" 
                        value="<?php echo esc_attr( get_post_meta($product_id, 'desnivel_negativo', true) ); ?>" required>
                    <span class="input-suffix">m</span>
                </div>

            </div>

        </div>

        <!-- Dificultad física -->

        <div class="contenedor-2col">

            <!-- Columna 1: selector de dificultad -->

            <div class="contenedor-1col">
                <label class="edit-form-titular">Dificultad física*</label>
                <?php
                $terms_dificultad = get_terms([
                    'taxonomy'   => 'dificultad',
                    'hide_empty' => false
                ]);
                $selected_dificultad = wp_get_post_terms($product_id, 'dificultad', ['fields' => 'ids']);
                ?>
                <select name="actividad_dificultad" class="edit-form-text" required>
                    <option value="">Selecciona dificultad</option>
                    <?php foreach ($terms_dificultad as $term): ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>" <?php echo in_array($term->term_id, $selected_dificultad) ? 'selected' : ''; ?>>
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Columna 2: vacía -->

            <div class="contenedor-1col">

                <!-- Espacio vacío -->

            </div>
        </div>

    <?php

    // Dificultad técnica

    $dificultad_tecnica = get_post_meta($product_id, 'dificultad_tecnica', true);
    ?>
    <div class="contenedor-1col">
        <label class="edit-form-titular">Dificultad técnica*</label>
        <?php
        wp_editor(
            $dificultad_tecnica,
            'actividad_dificultad_tecnica',
            [
                'textarea_name' => 'actividad_dificultad_tecnica',
                'textarea_rows' => 10,
                'media_buttons' => false, // sin subir medios
                'teeny'         => false, // editor completo
                'quicktags'     => false,  // Oculta la pestaña "Texto"
                'tinymce'       => [
                    'toolbar1' => 'formatselect bold italic underline strikethrough bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more fullscreen',
                    'toolbar2' => 'undo redo forecolor backcolor pastetext removeformat charmap outdent indent table hr code',
                    'plugins'  => 'lists link paste wordpress'
                ],
            ]
        );
        ?>
    </div>

    <?php
    // Planificación
    $planificacion = get_post_meta($product_id, 'planificacion', true);
    ?>
    <div class="contenedor-1col">
        <label class="edit-form-titular">Planificación*</label>
        <?php
        wp_editor(
            $planificacion,
            'actividad_planificacion',
            [
                'textarea_name' => 'actividad_planificacion',
                'textarea_rows' => 10,
                'media_buttons' => false,
                'teeny'         => false,
                'quicktags'     => false,
                'tinymce'       => [
                    'toolbar1' => 'formatselect bold italic underline strikethrough bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more fullscreen',
                    'toolbar2' => 'undo redo forecolor backcolor pastetext removeformat charmap outdent indent table hr code',
                    'plugins'  => 'lists link paste wordpress'
                ],
            ]
        );
        ?>
    </div>

    <?php
    // Material necesario
    $material = get_post_meta($product_id, 'material', true);
    ?>
    <div class="contenedor-1col">
        <label class="edit-form-titular">Material necesario*</label>
        <?php
        wp_editor(
            $material,
            'actividad_material',
            [
                'textarea_name' => 'actividad_material',
                'textarea_rows' => 10,
                'media_buttons' => false,
                'teeny'         => false,
                'quicktags'     => false,
                'tinymce'       => [
                    'toolbar1' => 'formatselect bold italic underline strikethrough bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more fullscreen',
                    'toolbar2' => 'undo redo forecolor backcolor pastetext removeformat charmap outdent indent table hr code',
                    'plugins'  => 'lists link paste wordpress'
                ],
            ]
        );
        ?>
    </div>

    <?php
    // Incluído en la actividad
    $incluye = get_post_meta($product_id, 'incluye', true);
    ?>
    <div class="contenedor-1col">
        <label class="edit-form-titular">Incluído en la actividad*</label>
        <?php
        wp_editor(
            $incluye,
            'actividad_incluye',
            [
                'textarea_name' => 'actividad_incluye',
                'textarea_rows' => 10,
                'media_buttons' => false,
                'teeny'         => false,
                'quicktags'     => false,
                'tinymce'       => [
                    'toolbar1' => 'formatselect bold italic underline strikethrough bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more fullscreen',
                    'toolbar2' => 'undo redo forecolor backcolor pastetext removeformat charmap outdent indent table hr code',
                    'plugins'  => 'lists link paste wordpress'
                ],
            ]
        );
        ?>
    </div>

    <!-- Botón Guardar -->

        <button class="mis-actividades-boton-guardar-cambios" type="submit">Guardar cambios</button>
        
    </form>






    <script>
    function abrirMediaUploader(box, inputId){
        var frame = wp.media({
            title: 'Seleccionar imagen',
            button: { text: 'Usar esta imagen' },
            multiple: false
        });
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            box.innerHTML = '<img src="'+attachment.url+'" alt="">';
            document.getElementById(inputId).value = attachment.id;
        });
        frame.open();
    }
    </script>
    


    <!-- jQuery para cargar regiones y provincias según país seleccionado -->
    <script>
    jQuery(document).ready(function($){
        function cargarOpciones(taxonomia, parentId, targetSelect, parentName){
            $.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "POST",
                data: {
                    action: "cargar_terminos_condicionales",
                    taxonomia: taxonomia,
                    parent: parentName // Pasamos el nombre, no el ID
                },
                success: function(response){
                    $(targetSelect).html(response);
                }
            });
        }

        $("#actividad_pais").change(function(){
            let paisId = $(this).val();
            let paisName = $(this).find('option:selected').text();
            cargarOpciones("region", paisId, "#actividad_region", paisName);
            $("#actividad_provincia").html('<option value="">Selecciona provincia</option>');
        });

        $("#actividad_region").change(function(){
            let regionId = $(this).val();
            cargarOpciones("provincia", regionId, "#actividad_provincia", regionId);
        });
    });
    </script>

    <?php



    return ob_get_clean();
}

add_shortcode('contenido_editar_actividad', 'contenido_editar_actividad_shortcode');


// AJAX para cargar términos condicionales (país -> región -> provincia)

add_action('wp_ajax_cargar_terminos_condicionales', 'cargar_terminos_condicionales');
add_action('wp_ajax_nopriv_cargar_terminos_condicionales', 'cargar_terminos_condicionales');

function cargar_terminos_condicionales(){
    $taxonomia = sanitize_text_field($_POST['taxonomia']);
    $parent = sanitize_text_field($_POST['parent']); // Ahora es texto, no ID
    
    $ubicaciones = trekkium_get_ubicaciones();
    
    echo '<option value="">Selecciona '.$taxonomia.'</option>';
    
    if ($taxonomia === 'region' && !empty($parent)) {
        // Cargar regiones del país seleccionado
        if (isset($ubicaciones[$parent])) {
            foreach ($ubicaciones[$parent] as $region => $provincias) {
                // Buscar el término por nombre para obtener el ID
                $term = get_term_by('name', $region, 'region');
                if ($term) {
                    echo '<option value="'.$term->term_id.'">'.$term->name.'</option>';
                }
            }
        }
    } 
    elseif ($taxonomia === 'provincia' && !empty($parent)) {
        // Cargar provincias de la región seleccionada
        // Necesitamos encontrar a qué país pertenece esta región
        $region_term = get_term_by('term_id', intval($parent), 'region');
        if ($region_term) {
            $region_name = $region_term->name;
            
            // Buscar en qué país está esta región
            foreach ($ubicaciones as $pais => $regiones) {
                if (isset($regiones[$region_name])) {
                    // Encontramos el país, ahora mostrar las provincias
                    foreach ($regiones[$region_name] as $provincia) {
                        $term = get_term_by('name', $provincia, 'provincia');
                        if ($term) {
                            echo '<option value="'.$term->term_id.'">'.$term->name.'</option>';
                        }
                    }
                    break;
                }
            }
        }
    }
    
    wp_die();
}