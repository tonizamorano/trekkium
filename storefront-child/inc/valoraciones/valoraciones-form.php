<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode y procesamiento de formulario de valoración
 * Uso: [trekkium_form_valoracion]
 */
function trekkium_form_valoracion_shortcode($atts) {

    // Recuperamos pedido y token de GET
    $pedido_id = isset($_GET['pedido']) ? intval($_GET['pedido']) : 0;
    $token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';

    // Mensaje inicial
    $mensaje = '';

    // Validación básica
    if (!$pedido_id || !$token) {
        return '<p>Acceso no autorizado.</p>';
    }

    if (!trekkium_verificar_token_valoracion($pedido_id, $token)) {
        return '<p>El enlace no es válido o ya se ha enviado la valoración.</p>';
    }

    // Recuperar datos de la actividad

    $order = wc_get_order( $pedido_id );
    if ( ! $order ) {
        return '<p>Error: pedido no encontrado.</p>';
    }

    $items = $order->get_items();
    if ( empty( $items ) ) {
        return '<p>Error: actividad no encontrada.</p>';
    }

    $item = array_shift( $items );
    $actividad_id = $item->get_product_id();

    if ( ! $actividad_id ) {
        return '<p>Error: actividad no encontrada.</p>';
    }

    $nombre_actividad = get_the_title( $actividad_id );
    $imagen_actividad = get_the_post_thumbnail_url( $actividad_id, 'large' );


    // Procesar envío del formulario
    if (isset($_POST['trekkium_valoracion_submit'])) {

        // Recuperar datos del usuario (puede estar logueado o invitado)
        $usuario_id = get_post_meta($pedido_id, '_customer_user', true);
        $user = get_user_by('id', $usuario_id);
        $nombre_cliente = $user ? $user->display_name : 'Invitado';
        $avatar = $user ? get_avatar_url($usuario_id) : '';

        // Recuperar datos de la actividad y guía
        $order = wc_get_order( $pedido_id );
        if ( ! $order ) {
            return '<p>Error: pedido no encontrado.</p>';
        }

        $items = $order->get_items();
        if ( empty( $items ) ) {
            return '<p>Error: actividad no encontrada.</p>';
        }

        $item = array_shift( $items );
        $actividad_id = $item->get_product_id();

        if ( ! $actividad_id ) {
            return '<p>Error: actividad no encontrada.</p>';
        }


        $guia_id = get_post_field('post_author', $actividad_id);
        $nombre_guia = get_the_author_meta('display_name', $guia_id);
        $nombre_actividad = get_the_title($actividad_id);
        $fecha_actividad = get_post_meta($actividad_id, 'fecha', true);

        // Recoger campos del formulario, limitar rangos 1–5
        $get_star = function($field){
            return max(1, min(5, intval($_POST[$field] ?? 0)));
        };

        $datos_formulario = array(
            'usuario_id' => $usuario_id,
            'nombre_cliente' => $nombre_cliente,
            'avatar_del_usuario' => $avatar,
            'actividad_id' => $actividad_id,
            'nombre_actividad' => $nombre_actividad,
            'guia_id' => $guia_id,
            'nombre_guia' => $nombre_guia,
            'fecha_actividad' => $fecha_actividad,
            'fecha_envio_formulario' => current_time('mysql'),
            'pedido_id' => $pedido_id,

            // Campos internos
            'valor_actividad_general' => $get_star('valor_actividad_general'),
            'actividad_se_ajusta_descripcion' => $get_star('actividad_se_ajusta_descripcion'),
            'organizacion_actividad' => $get_star('organizacion_actividad'),
            'experiencia_trekkium' => $get_star('experiencia_trekkium'),
            'proceso_reserva' => $get_star('proceso_reserva'),
            'sensacion_seguridad' => $get_star('sensacion_seguridad'),

            // Campos públicos del guía
            'valoracion_guia_general' => $get_star('valoracion_guia_general'),
            'comentario_guia' => sanitize_textarea_field($_POST['comentario_guia'] ?? ''),
        );

        // Crear la valoración
        $post_id = trekkium_crear_valoracion($datos_formulario);

        if (is_wp_error($post_id)) {
            $mensaje = '<p>Error al guardar la valoración. Por favor, inténtalo de nuevo.</p>';
        } else {
            // Marcar token como usado
            trekkium_marcar_token_usado($pedido_id);
            $mensaje = '<p>¡Gracias! Tu valoración ha sido enviada correctamente.</p>';
        }
    }

    // Formulario HTML
    ob_start();
    echo $mensaje; ?>

    <div class="trekkium-form-wrapper">

        <?php
        // Imagen y título de la actividad
        $thumb_url = get_the_post_thumbnail_url($actividad_id, 'large');
        ?>

        <?php if ($thumb_url): ?>
            <div class="trekkium-actividad-header">
                <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($nombre_actividad); ?>">
                
            </div>
        <?php endif; ?>

        <div class="trekkium-form-contenido">

            <h2><?php echo esc_html($nombre_actividad); ?></h2>

            <form method="post" class="trekkium-form-valoracion">

                <input type="hidden" name="pedido_id" value="<?php echo esc_attr($pedido_id); ?>">
                <input type="hidden" name="token" value="<?php echo esc_attr($token); ?>">

                <h3>Sobre la actividad</h3>

                <div class="trekkium-row">
                    <div class="trekkium-question">
                        <label>¿Cómo valorarías la actividad en general?</label>
                    </div>
                    <div class="trekkium-stars">
                        <div class="rating">
                            <?php for($i=5;$i>=1;$i--): ?>
                                <input type="radio" id="valor_actividad_general_<?php echo $i; ?>" name="valor_actividad_general" value="<?php echo $i; ?>" <?php echo $i===5? 'checked':''; ?> />
                                <label for="valor_actividad_general_<?php echo $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                </div>

                <div class="trekkium-row">
                    <div class="trekkium-question">
                        <label>¿La actividad se ha ajustado a la descripción?</label>
                    </div>
                    <div class="trekkium-stars">
                        <div class="rating">
                            <?php for($i=5;$i>=1;$i--): ?>
                                <input type="radio" id="actividad_se_ajusta_descripcion_<?php echo $i; ?>" name="actividad_se_ajusta_descripcion" value="<?php echo $i; ?>" <?php echo $i===5? 'checked':''; ?> />
                                <label for="actividad_se_ajusta_descripcion_<?php echo $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                </div>

                <div class="trekkium-row">
                    <div class="trekkium-question">
                        <label>¿Cómo valorarías la organización de la actividad?</label>
                    </div>
                    <div class="trekkium-stars">
                        <div class="rating">
                            <?php for($i=5;$i>=1;$i--): ?>
                                <input type="radio" id="organizacion_actividad_<?php echo $i; ?>" name="organizacion_actividad" value="<?php echo $i; ?>" <?php echo $i===5? 'checked':''; ?> />
                                <label for="organizacion_actividad_<?php echo $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                </div>

                <h3>Sobre el/la guía</h3>

                <div class="trekkium-row">
                    <div class="trekkium-question">
                        <label>¿Cómo valorarías al guía en general?</label>
                    </div>
                    <div class="trekkium-stars">
                        <div class="rating">
                            <?php for($i=5;$i>=1;$i--): ?>
                                <input type="radio" id="valoracion_guia_general_<?php echo $i; ?>" name="valoracion_guia_general" value="<?php echo $i; ?>" <?php echo $i===5? 'checked':''; ?> />
                                <label for="valoracion_guia_general_<?php echo $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                </div>

                <div class="trekkium-row">
                    <div class="trekkium-question">
                        <label>¿Te has sentido seguro/a durante la actividad?</label>
                    </div>
                    <div class="trekkium-stars">
                        <div class="rating">
                            <?php for($i=5;$i>=1;$i--): ?>
                                <input type="radio" id="sensacion_seguridad_<?php echo $i; ?>" name="sensacion_seguridad" value="<?php echo $i; ?>" <?php echo $i===5? 'checked':''; ?> />
                                <label for="sensacion_seguridad_<?php echo $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                </div>

                <div class="trekkium-row">
                    
                    <div class="trekkium-question">
                        <label>¿Quieres dejar algún comentario sobre el/la guía?</label>
                        <textarea name="comentario_guia" rows="4" style="width:100%;margin-top:6px;"></textarea>
                    </div>
                    <div class="trekkium-stars" style="visibility:hidden">&nbsp;</div>
                </div>

                <h3>Sobre Trekkium</h3>

                

                <div class="trekkium-row">
                    <div class="trekkium-question">
                        <label>¿Cómo valorarías tu experiencia como usuario de Trekkium?</label>
                    </div>
                    <div class="trekkium-stars">
                        <div class="rating">
                            <?php for($i=5;$i>=1;$i--): ?>
                                <input type="radio" id="experiencia_trekkium_<?php echo $i; ?>" name="experiencia_trekkium" value="<?php echo $i; ?>" <?php echo $i===5? 'checked':''; ?> />
                                <label for="experiencia_trekkium_<?php echo $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                </div>

                <div class="trekkium-row">
                    <div class="trekkium-question">
                        <label>¿El proceso de reserva ha sido satisfactorio?</label>
                    </div>
                    <div class="trekkium-stars">
                        <div class="rating">
                            <?php for($i=5;$i>=1;$i--): ?>
                                <input type="radio" id="proceso_reserva_<?php echo $i; ?>" name="proceso_reserva" value="<?php echo $i; ?>" <?php echo $i===5? 'checked':''; ?> />
                                <label for="proceso_reserva_<?php echo $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                </div>


                <div style="text-align:right;margin-top:12px;">
                    <button type="submit" name="trekkium_valoracion_submit">Enviar valoración</button>
                </div>
            </form>
                                
        </div>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('trekkium_form_valoracion', 'trekkium_form_valoracion_shortcode');
