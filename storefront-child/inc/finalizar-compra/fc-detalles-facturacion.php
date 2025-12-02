<?php
/**
 * Snippet: checkout-seccion-detalles-facturacion
 * Shortcode: [checkout-detalles-facturacion]
 */

function shortcode_checkout_detalles_facturacion() {

    if (!class_exists('WC_Countries')) {
        return 'WooCommerce no está activo';
    }

    if ( ! is_user_logged_in() ) {
        return '<p>Debes iniciar sesión para ver esta información.</p>';
    }

    $user_id = get_current_user_id();
    $user    = wp_get_current_user();

    // Datos básicos
    $nombre      = $user->first_name;
    $apellidos   = $user->last_name;
    $email       = $user->user_email;

    // Teléfono + formateo original
    $telefono_raw = get_user_meta( $user_id, 'billing_phone', true );

    function formatear_telefono( $telefono ) {

        if ( ! $telefono ) {
            return '';
        }

        $telefono = preg_replace('/[^\d+]/', '', $telefono);

        if ( strpos($telefono, '+34') === 0 ) {
            $codigo = '+34';
            $numero = substr($telefono, 3);
            $numero = trim(chunk_split($numero, 3, ' '));
            return $codigo . ' ' . $numero;
        }

        if ( strpos($telefono, '+') === 0 ) {
            if ( preg_match('/^(\+\d{1,4})(\d+)$/', $telefono, $matches) ) {
                $codigo = $matches[1];
                $numero = $matches[2];
                $numero = trim(chunk_split($numero, 3, ' '));
                return $codigo . ' ' . $numero;
            }
        }

        return trim(chunk_split($telefono, 3, ' '));
    }

    $telefono = formatear_telefono( $telefono_raw );

    // Ubicación
    $provincia_code = get_user_meta( $user_id, 'billing_state', true );
    $pais           = get_user_meta( $user_id, 'billing_country', true );
    $comunidad      = get_user_meta( $user_id, 'comunidad_autonoma', true );

    $estados = WC()->countries->get_states( $pais );
    $provincia = '';

    if ( ! empty( $estados ) && isset( $estados[ $provincia_code ] ) ) {
        $provincia = $estados[ $provincia_code ];
    } elseif ( ! empty( $provincia_code ) ) {
        $provincia = $provincia_code;
    }

    ob_start();
    ?>

    <div class="fc-facturacion-contenedor">

        <div class="fc-facturacion-titulo">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 24 24">
                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
            </svg>
            <span class="fc-facturacion-titulo-texto">Datos del titular de la reserva</span>
        </div>

        <div class="fc-facturacion-contenido">          

            <div class="fc-contenido-nombre">
                <?php echo esc_html( $nombre . ' ' . $apellidos ); ?>
            </div>

            <div class="fc-contenido-ubicacion">
                <?php echo esc_html( $provincia ); ?>
                <?php echo $comunidad ? " ({$comunidad})" : ''; ?>
            </div>

            <div class="fc-grid-label" style="margin-top:10px;">
                <?php echo do_shortcode('[icon_whatsapp]'); ?>
                <?php echo esc_html( $telefono ); ?>
            </div>

            <div class="fc-grid-label">
                <?php echo do_shortcode('[icon_email]'); ?>
                <?php echo esc_html( $email ); ?>
            </div>             

        </div>
    </div>

    <?php
    return ob_get_clean();
}

add_shortcode('checkout_detalles_facturacion', 'shortcode_checkout_detalles_facturacion');
