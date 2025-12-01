<?php
/**
 * Metaboxes para el CPT Patrocinadores
 */
function trekkium_add_metabox_patrocinadores() {

    // Metabox Principal
    add_meta_box(
        'trekkium_metabox_patrocinadores',
        'Datos del patrocinador',
        'trekkium_render_metabox_patrocinadores',
        'patrocinador',
        'normal',
        'high'
    );

    // Metabox Estado
    add_meta_box(
        'trekkium_metabox_patrocinadores_estado',
        'Estado del patrocinador',
        'trekkium_render_metabox_patrocinadores_estado',
        'patrocinador',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'trekkium_add_metabox_patrocinadores' );


/**
 * Cargar scripts y estilos necesarios
 */
function trekkium_admin_scripts($hook) {
    global $post;
    if ( in_array( $hook, ['post-new.php','post.php'] ) && isset($post) && $post->post_type === 'patrocinador' ) {
        wp_enqueue_media();
        wp_enqueue_script( 'trekkium-media-upload', get_template_directory_uri() . '/js/trekkium-media-upload.js', array('jquery'), '1.0.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'trekkium_admin_scripts' );


/**
 * Render del Metabox Principal (modificado)
 */
function trekkium_render_metabox_patrocinadores( $post ) {

    wp_nonce_field( 'trekkium_save_patrocinadores', 'trekkium_patrocinadores_nonce' );

    $fields = [
        'pat_marca','pat_razon_social','pat_cif','pat_direccion',
        'pat_telefono','pat_contacto','pat_mail','pat_web',
        'pat_logo_color','pat_logo_blanco','pat_descripcion',
        'pat_comentarios','pat_fecha_inicio', 'pat_logo_height' // ← Nuevo campo
    ];

    // Obtener valores
    foreach( $fields as $field ) {
        $$field = get_post_meta( $post->ID, $field, true );
    }
    ?>

    <style>
        .trekkium-section { margin-bottom:25px; padding:15px; background:#fafafa; border:1px solid #e5e5e5; border-radius:6px; }
        .trekkium-section h3 { margin-top:0; font-size:16px; font-weight:600; }
        .trekkium-grid-2, .trekkium-grid-3 { display:grid; grid-gap:15px; margin-bottom:15px; }
        .trekkium-grid-2 { grid-template-columns: repeat(2,1fr); }
        .trekkium-grid-3 { grid-template-columns: repeat(3,1fr); }
        .trekkium-input, .trekkium-textarea { width:100%; }
        .trekkium-textarea { min-height:100px; }
        .trekkium-image-field { margin-bottom:15px; }
        .trekkium-image-preview { margin:10px 0; max-width:200px; }
        .trekkium-image-preview img { max-width:100%; height:auto; border:2px solid #e5e5e5; border-radius:4px; }
        .trekkium-image-actions { margin-top:8px; }
        .trekkium-image-actions button { margin-right:5px; margin-bottom:5px; }
        .trekkium-no-image { color:#999; font-style:italic; }
    </style>

    <!-- DATOS PRINCIPALES -->
    <div class="trekkium-section">
        <h3>Datos principales</h3>
        <div class="trekkium-grid-2">
            <div>
                <label>Marca</label>
                <input type="text" name="pat_marca" class="trekkium-input" value="<?php echo esc_attr($pat_marca); ?>">
            </div>
            <div>
                <label>Razón social</label>
                <input type="text" name="pat_razon_social" class="trekkium-input" value="<?php echo esc_attr($pat_razon_social); ?>">
            </div>
        </div>
        <div class="trekkium-grid-2">
            <div>
                <label>CIF</label>
                <input type="text" name="pat_cif" class="trekkium-input" value="<?php echo esc_attr($pat_cif); ?>">
            </div>
            <div>
                <label>Dirección</label>
                <input type="text" name="pat_direccion" class="trekkium-input" value="<?php echo esc_attr($pat_direccion); ?>">
            </div>
        </div>
    </div>

    <!-- CONTACTO -->
    <div class="trekkium-section">
        <h3>Contacto</h3>
        <div class="trekkium-grid-3">
            <div>
                <label>Teléfono</label>
                <input type="text" name="pat_telefono" class="trekkium-input" value="<?php echo esc_attr($pat_telefono); ?>">
            </div>
            <div>
                <label>Persona de contacto</label>
                <input type="text" name="pat_contacto" class="trekkium-input" value="<?php echo esc_attr($pat_contacto); ?>">
            </div>
            <div>
                <label>Mail</label>
                <input type="email" name="pat_mail" class="trekkium-input" value="<?php echo esc_attr($pat_mail); ?>">
            </div>
        </div>
        <div class="trekkium-grid-2">
            <div>
                <label>Web</label>
                <input type="url" name="pat_web" class="trekkium-input" value="<?php echo esc_url($pat_web); ?>">
            </div>
            <div>
                <label>Fecha de inicio</label>
                <input type="date" name="pat_fecha_inicio" class="trekkium-input" value="<?php echo esc_attr($pat_fecha_inicio); ?>">
            </div>
        </div>
    </div>

    <!-- IMÁGENES -->
    <div class="trekkium-section">
        <h3>Imágenes</h3>
        <div class="trekkium-grid-2">
            <?php foreach ( ['color','blanco'] as $type ) :
                $field = 'pat_logo_'.$type;
                $value = $$field; ?>
                <div class="trekkium-image-field">
                    <label><strong>Logo <?php echo $type; ?></strong></label>
                    <input type="hidden" name="<?php echo $field; ?>" id="<?php echo $field; ?>" value="<?php echo esc_attr($value); ?>">
                    <div class="trekkium-image-preview" id="<?php echo $field; ?>_preview">
                        <?php if ($value) echo wp_get_attachment_image($value,'medium'); else echo '<div class="trekkium-no-image">No hay imagen seleccionada</div>'; ?>
                    </div>
                    <div class="trekkium-image-actions">
                        <button type="button" class="button trekkium-upload-image" data-target="<?php echo $field; ?>">
                            <?php echo $value ? 'Cambiar imagen' : 'Seleccionar imagen'; ?>
                        </button>
                        <?php if ($value): ?>
                            <button type="button" class="button button-link trekkium-remove-image" data-target="<?php echo $field; ?>">Eliminar</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- NUEVO CAMPO PARA ALTURA DEL LOGO -->
        <div style="margin-top: 20px;">
            <label><strong>Altura del logo (px)</strong></label>
            <input type="number" name="pat_logo_height" class="trekkium-input" 
                   value="<?php echo esc_attr($pat_logo_height); ?>" 
                   placeholder="40" min="20" max="100" step="5"
                   style="max-width: 150px;">
            <p class="description">Altura en píxeles para mostrar el logo en el footer (por defecto: 40px)</p>
        </div>
    </div>

    <!-- DESCRIPCIÓN Y COMENTARIOS -->
    <div class="trekkium-section">
        <h3>Descripción</h3>
        <textarea name="pat_descripcion" class="trekkium-textarea"><?php echo wp_kses_post($pat_descripcion); ?></textarea>
    </div>

    <div class="trekkium-section">
        <h3>Comentarios internos</h3>
        <textarea name="pat_comentarios" class="trekkium-textarea"><?php echo wp_kses_post($pat_comentarios); ?></textarea>
    </div>

    <script>
    jQuery(document).ready(function($){
    
    function openMediaFrame(target){
        var mediaFrame = wp.media({
            title: 'Seleccionar imagen',
            button: { text: 'Usar esta imagen' },
            multiple: false
        });

        mediaFrame.on('select', function(){
            var attachment = mediaFrame.state().get('selection').first().toJSON();

            // Actualizar el campo hidden
            $('#' + target).val(attachment.id);

            // Actualizar la vista previa
            $('#' + target + '_preview').html('<img src="' + attachment.url + '" alt="' + attachment.alt + '" />');

            // Actualizar el texto del botón
            $('.trekkium-upload-image[data-target="' + target + '"]').text('Cambiar imagen');

            // Mostrar el botón eliminar si no existe
            if (!$('.trekkium-remove-image[data-target="' + target + '"]').length) {
                $('.trekkium-upload-image[data-target="' + target + '"]').after(
                    '<button type="button" class="button button-link trekkium-remove-image" data-target="' + target + '">Eliminar</button>'
                );
            }
        });

        mediaFrame.open();
    }

    // Evento para el botón de subir imagen
    $('.trekkium-upload-image').on('click', function(e){
        e.preventDefault();
        var target = $(this).data('target');
        openMediaFrame(target);
    });

    // Evento para eliminar imagen
    $(document).on('click', '.trekkium-remove-image', function(e){
        e.preventDefault();
        var target = $(this).data('target');

        // Limpiar el campo
        $('#' + target).val('');

        // Limpiar la vista previa
        $('#' + target + '_preview').html('<div class="trekkium-no-image">No hay imagen seleccionada</div>');

        // Actualizar el texto del botón
        $('.trekkium-upload-image[data-target="' + target + '"]').text('Seleccionar imagen');

        // Eliminar el botón eliminar
        $(this).remove();
    });

});

    </script>

<?php
}


/**
 * Render del Metabox Estado
 */
function trekkium_render_metabox_patrocinadores_estado( $post ) {
    $estado = get_post_meta( $post->ID, 'pat_estado', true );
    $options = [
        'PENDIENTE'   => 'No se ha contactado todavía',
        'EN PROCESO'  => 'Se ha contactado y la propuesta está en estudio',
        'ACTIVO'      => 'Patrocinio activo y anuncios funcionando',
        'PAUSA'       => 'Patrocinio pausado temporalmente',
        'CANCELADO'   => 'Patrocinador ha cancelado o no le interesa'
    ];
    ?>
    <label for="pat_estado"><strong>Estado</strong></label>
    <select name="pat_estado" id="pat_estado" style="width:100%; margin-top:8px;">
        <?php foreach ($options as $key=>$label): ?>
            <option value="<?php echo esc_attr($key); ?>" <?php selected($estado,$key); ?>><?php echo esc_html($key); ?></option>
        <?php endforeach; ?>
    </select>
<?php
}


/**
 * Guardado de todos los metacampos (modificado)
 */
function trekkium_save_metabox_patrocinadores( $post_id ) {

    if ( ! isset($_POST['trekkium_patrocinadores_nonce']) ) return;
    if ( ! wp_verify_nonce($_POST['trekkium_patrocinadores_nonce'], 'trekkium_save_patrocinadores') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( ! current_user_can('edit_post',$post_id) ) return;

    $fields = [
        'pat_marca','pat_razon_social','pat_cif','pat_direccion',
        'pat_telefono','pat_contacto','pat_mail','pat_web',
        'pat_logo_color','pat_logo_blanco','pat_descripcion',
        'pat_comentarios','pat_fecha_inicio','pat_estado', 'pat_logo_height' // ← Nuevo campo
    ];

    foreach($fields as $field){
        if(isset($_POST[$field])){
            if($field==='pat_mail'){
                update_post_meta($post_id,$field,sanitize_email($_POST[$field]));
            } elseif($field==='pat_web'){
                update_post_meta($post_id,$field,esc_url_raw($_POST[$field]));
            } elseif($field==='pat_descripcion' || $field==='pat_comentarios'){
                update_post_meta($post_id,$field,wp_kses_post($_POST[$field]));
            } elseif($field==='pat_logo_height'){
                // Sanitizar como número
                $height = intval($_POST[$field]);
                update_post_meta($post_id,$field,$height);
            } else {
                update_post_meta($post_id,$field,sanitize_text_field($_POST[$field]));
            }
        }
    }
}
add_action('save_post','trekkium_save_metabox_patrocinadores');


/**
 * Columnas personalizadas
 */
function trekkium_patrocinadores_custom_columns($columns){
    $new_columns = [
        'pat_contacto'=>'Contacto',
        'pat_telefono'=>'Teléfono',
        'pat_estado'=>'Estado'
    ];
    $position = array_search('title',array_keys($columns))+1;
    return array_slice($columns,0,$position,true)+$new_columns+array_slice($columns,$position,null,true);
}
add_filter('manage_patrocinador_posts_columns','trekkium_patrocinadores_custom_columns');

function trekkium_patrocinadores_custom_columns_content($column,$post_id){
    switch($column){
        case 'pat_contacto':
            $val=get_post_meta($post_id,'pat_contacto',true);
            echo $val?esc_html($val):'<span style="color:#bbb;">—</span>';
            break;
        case 'pat_telefono':
            $val=get_post_meta($post_id,'pat_telefono',true);
            echo $val?esc_html($val):'<span style="color:#bbb;">—</span>';
            break;
        case 'pat_estado':
            $val=get_post_meta($post_id,'pat_estado',true);
            $colors=['PENDIENTE'=>'#777','EN PROCESO'=>'#d98b00','ACTIVO'=>'#2a9d13','PAUSA'=>'#0077b6','CANCELADO'=>'#c1121f'];
            $color=isset($colors[$val])?$colors[$val]:'#555';
            echo $val?'<span style="display:inline-block;padding:3px 8px;font-size:12px;border-radius:4px;background:'.esc_attr($color).';color:#fff;">'.esc_html($val).'</span>':'<span style="color:#bbb;">—</span>';
            break;
    }
}
add_action('manage_patrocinador_posts_custom_column','trekkium_patrocinadores_custom_columns_content',10,2);


/**
 * Columnas ordenables
 */
function trekkium_patrocinadores_sortable_columns($columns){
    $columns['pat_contacto']='pat_contacto';
    $columns['pat_telefono']='pat_telefono';
    $columns['pat_estado']='pat_estado';
    return $columns;
}
add_filter('manage_edit-patrocinador_sortable_columns','trekkium_patrocinadores_sortable_columns');

function trekkium_patrocinadores_orderby($query){
    if(!is_admin()) return;
    $orderby = $query->get('orderby');
    if($orderby==='pat_contacto'){ $query->set('meta_key','pat_contacto'); $query->set('orderby','meta_value'); }
    if($orderby==='pat_telefono'){ $query->set('meta_key','pat_telefono'); $query->set('orderby','meta_value'); }
    if($orderby==='pat_estado'){ $query->set('meta_key','pat_estado'); $query->set('orderby','meta_value'); }
}
add_action('pre_get_posts','trekkium_patrocinadores_orderby');
