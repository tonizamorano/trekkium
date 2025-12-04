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
                'post_status'  => 'pending', // o 'draft'
                'post_author'  => get_current_user_id(),
            ]);

            if ( $new_product_id ) {

                // Imagen destacada
                if ( isset($_POST['actividad_imagen_1']) && intval($_POST['actividad_imagen_1']) ) {
                    set_post_thumbnail( $new_product_id, intval($_POST['actividad_imagen_1']) );
                }

                // Galería
                $galeria_ids = [];
                for ($i=2; $i<=4; $i++) {
                    if ( isset($_POST['actividad_imagen_'.$i]) && intval($_POST['actividad_imagen_'.$i]) ) {
                        $galeria_ids[] = intval($_POST['actividad_imagen_'.$i]);
                    }
                }
                update_post_meta( $new_product_id, '_product_image_gallery', implode(',', $galeria_ids) );

                // Taxonomías tipo, modalidad, país, región, provincia, dificultad
                $taxonomias = ['tipo','modalidad','pais','region','provincia','dificultad'];
                foreach ( $taxonomias as $tax ) {
                    if ( isset($_POST["actividad_{$tax}"]) ) {
                        wp_set_post_terms( $new_product_id, [ intval($_POST["actividad_{$tax}"]) ], $tax, false );
                    }
                }

                // Campos personalizados
                $campos_meta = [
                    'distancia','duracion','desnivel_positivo','desnivel_negativo',
                    'encuentro','google_maps','dificultad_tecnica','planificacion',
                    'material','incluye','hora'
                ];
                foreach ( $campos_meta as $campo ) {
                    if ( isset($_POST["actividad_{$campo}"]) ) {
                        $valor = in_array($campo, ['google_maps']) ? esc_url_raw($_POST["actividad_{$campo}"]) : wp_kses_post($_POST["actividad_{$campo}"]);
                        update_post_meta($new_product_id, $campo, $valor);
                    }
                }

                // Fecha normalizada
                if ( isset($_POST['actividad_fecha']) ) {
                    $fecha_input = sanitize_text_field($_POST['actividad_fecha']);
                    $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_input);
                    if ($fecha_obj) {
                        update_post_meta($new_product_id, 'fecha', $fecha_obj->format('Y-m-d'));
                    }
                }

                echo '<p style="color:green;font-weight:bold;">✅ Actividad creada correctamente y pendiente de revisión.</p>';

            } else {
                echo '<p style="color:red;font-weight:bold;">⚠️ Error al crear la actividad. Inténtalo de nuevo.</p>';
            }

        } else {
            echo '<p style="color:red;font-weight:bold;">⚠️ El título y la descripción son obligatorios.</p>';
        }
    }

    ?>

    <div class="editar-actividad-seccion-titular">
        <h2 class="editar-actividad-titular"><span>Nueva actividad</span></h2>  
    </div>

    <form method="post" class="producto-edit-form-contenedor-ppal">

        <?php wp_nonce_field('crear_actividad', 'formulario_nueva_actividad_nonce'); ?>

        <!-- Título -->
        <div class="contenedor-1col">
            <label class="edit-form-titular">Título de la actividad*</label>
            <input type="text" name="actividad_titulo" class="edit-form-text" required>
        </div>

        <!-- Descripción -->
        <div class="contenedor-1col">
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

        <!-- Imágenes -->
        <div class="contenedor-2col">
            <?php for($i=1;$i<=4;$i++): ?>
            <div class="contenedor-1col">
                <label class="edit-form-titular">Imagen <?php echo $i; ?>*</label>
                <div class="edit-form-image-box" onclick="abrirMediaUploader(this, 'actividad_imagen_<?php echo $i; ?>')">
                    <span>Haz clic para seleccionar</span>
                </div>
                <input type="hidden" name="actividad_imagen_<?php echo $i; ?>" id="actividad_imagen_<?php echo $i; ?>">
            </div>
            <?php endfor; ?>
        </div>

        <!-- Tipo y Modalidad -->
        <div class="contenedor-2col">
            <!-- Tipo -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Tipo*</label>
                <select name="actividad_tipo" class="edit-form-text" required>
                    <option value="">Selecciona tipo</option>
                    <?php
                    $terms_tipo = get_terms(['taxonomy'=>'tipo','hide_empty'=>false]);
                    foreach($terms_tipo as $term) {
                        echo '<option value="'.esc_attr($term->term_id).'">'.esc_html($term->name).'</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- Modalidad (filtrada por usuario guía) -->
            <div class="contenedor-1col">
                <label class="edit-form-titular">Modalidad*</label>
                <select name="actividad_modalidad" class="edit-form-text" required>
                    <option value="">Selecciona modalidad</option>
                    <?php
                    $user_id = get_current_user_id();
                    $user_modalidades = wp_get_object_terms($user_id, 'modalidad', array('fields' => 'names'));

                    // Mapear a la taxonomía de producto
                    foreach ($user_modalidades as $mod_name) {
                        $term = get_term_by('name', $mod_name, 'modalidad');
                        if ($term) {
                            echo '<option value="'.esc_attr($term->term_id).'">'.esc_html($term->name).'</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- País / Región / Provincia -->
        <div class="contenedor-2col">
            <?php
            $ubicaciones = ['pais'=>'País','region'=>'Región','provincia'=>'Provincia'];
            foreach($ubicaciones as $slug=>$label):
                $terms = get_terms(['taxonomy'=>$slug,'hide_empty'=>false]);
            ?>
            <div class="contenedor-1col">
                <label class="edit-form-titular"><?php echo $label; ?>*</label>
                <select name="actividad_<?php echo $slug; ?>" id="actividad_<?php echo $slug; ?>" class="edit-form-text" required>
                    <option value="">Selecciona <?php echo strtolower($label); ?></option>
                    <?php foreach($terms as $term): ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Fecha y Hora -->
        <div class="contenedor-2col">
            <div class="contenedor-1col">
                <label class="edit-form-titular">Fecha*</label>
                <input type="date" name="actividad_fecha" class="edit-form-text" required>
            </div>
            <div class="contenedor-1col">
                <label class="edit-form-titular">Hora*</label>
                <input type="time" name="actividad_hora" class="edit-form-text" required>
            </div>
        </div>

        <!-- Campos numéricos / textos -->
        <?php
        $campos = [
            'encuentro' => ['textarea','Punto de encuentro*'],
            'google_maps' => ['textarea','Enlace a Google Maps*'],
            'distancia' => ['number','Distancia (Km)*'],
            'duracion' => ['number','Duración (h)*'],
            'desnivel_positivo' => ['number','Desnivel positivo (m)*'],
            'desnivel_negativo' => ['number','Desnivel negativo (m)*']
        ];

        foreach ($campos as $slug => $data):
            [$tipo, $label] = $data;
            echo '<div class="contenedor-1col"><label class="edit-form-titular">'.$label.'</label>';
            if ($tipo==='textarea'){
                echo '<textarea name="actividad_'.$slug.'" class="edit-form-text" rows="3" required></textarea>';
            } else {
                echo '<input type="number" name="actividad_'.$slug.'" class="edit-form-text" required>';
            }
            echo '</div>';
        endforeach;
        ?>

        <!-- Dificultad física -->
        <div class="contenedor-2col">
            <div class="contenedor-1col">
                <label class="edit-form-titular">Dificultad física*</label>
                <select name="actividad_dificultad" class="edit-form-text" required>
                    <option value="">Selecciona dificultad</option>
                    <?php
                    $terms_dif = get_terms(['taxonomy'=>'dificultad','hide_empty'=>false]);
                    foreach($terms_dif as $term) {
                        echo '<option value="'.esc_attr($term->term_id).'">'.esc_html($term->name).'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Campos con editor -->
        <?php
        $campos_editor = [
            'dificultad_tecnica' => 'Dificultad técnica*',
            'planificacion'      => 'Planificación*',
            'material'           => 'Material necesario*',
            'incluye'            => 'Incluido en la actividad*',
        ];
        foreach($campos_editor as $slug=>$label){
            echo '<div class="contenedor-1col"><label class="edit-form-titular">'.$label.'</label>';
            wp_editor(
                '',
                'actividad_'.$slug,
                [
                    'textarea_name' => 'actividad_'.$slug,
                    'textarea_rows' => 8,
                    'media_buttons' => false,
                    'teeny'         => false,
                    'quicktags'     => false,
                ]
            );
            echo '</div>';
        }
        ?>

        <button class="mis-actividades-boton-guardar-cambios" type="submit">Crear actividad</button>
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

    // Dependencias país -> región -> provincia
    jQuery(document).ready(function($){
        function cargarOpciones(taxonomia, parentName, targetSelect){
            $.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "POST",
                data: {
                    action: "cargar_terminos_condicionales",
                    taxonomia: taxonomia,
                    parent: parentName
                },
                success: function(response){
                    $(targetSelect).html(response);
                }
            });
        }
        $("#actividad_pais").change(function(){
            let paisName = $(this).find('option:selected').text();
            cargarOpciones("region", paisName, "#actividad_region");
            $("#actividad_provincia").html('<option value="">Selecciona provincia</option>');
        });
        $("#actividad_region").change(function(){
            let regionId = $(this).val();
            cargarOpciones("provincia", regionId, "#actividad_provincia");
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('contenido_nueva_actividad', 'contenido_nueva_actividad_shortcode');
