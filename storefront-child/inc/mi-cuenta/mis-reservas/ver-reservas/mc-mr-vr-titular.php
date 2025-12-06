<?php
add_shortcode( 'mc_vr_titular', function() {

    if ( ! is_user_logged_in() ) {
        return '<p>Debes iniciar sesión para ver esta información.</p>';
    }

    $user_id = get_current_user_id();
    $user    = wp_get_current_user();

    // Datos básicos
    $nombre      = $user->first_name;
    $apellidos   = $user->last_name;
    $email       = $user->user_email;

    // Datos billing
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

    $provincia_code = get_user_meta( $user_id, 'billing_state', true );
    $pais        = get_user_meta( $user_id, 'billing_country', true );
    $comunidad   = get_user_meta( $user_id, 'comunidad_autonoma', true );

    $estados = WC()->countries->get_states( $pais );
    $provincia = '';
    if ( ! empty( $estados ) && isset( $estados[ $provincia_code ] ) ) {
        $provincia = $estados[ $provincia_code ];
    } elseif ( ! empty( $provincia_code ) ) {
        $provincia = $provincia_code;
    }

    ob_start();
    ?>

<div class="mc-mr-vr-contenedor">

    <div class="mc-mr-vr-titular">
        <h2>Titular de la reserva</h2>
    </div>

    <div class="mc-mr-vr-contenido">

        <div class="mc-mr-vr-nombre">
            <?php echo esc_html( $nombre . ' ' . $apellidos ); ?>
        </div>

        <div class="mc-mr-vr-ubicacion">
            <?php echo esc_html( $provincia ); ?><?php echo $comunidad ? " ({$comunidad})" : ''; ?>
        </div>

        <div class="mc-mr-vr-label" style="margin-top:10px;">
            <?php echo do_shortcode('[icon_whatsapp]'); ?>
            <?php echo esc_html( $telefono ); ?>
        </div>

        <div class="mc-mr-vr-label">
            <?php echo do_shortcode('[icon_email]'); ?>
            <?php echo esc_html( $email ); ?>
        </div>    

    </div>

</div>

<?php
    return ob_get_clean();
});
