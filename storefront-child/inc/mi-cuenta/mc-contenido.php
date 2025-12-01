<?php
add_shortcode( 'mc_contenido', function() {

    if ( ! is_user_logged_in() ) {
        return '<p>Debes iniciar sesión para ver esta información.</p>';
    }

    $user_id = get_current_user_id();
    $user    = wp_get_current_user();

    // Datos básicos
    $nombre      = $user->first_name;
    $apellidos   = $user->last_name;
    $email       = $user->user_email;
    $fecha_alta  = date_i18n( 'j \d\e F \d\e Y', strtotime( $user->user_registered ) );

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

    $fecha_nacimiento_raw = get_user_meta( $user_id, 'fecha_nacimiento', true );
    $fecha_nacimiento     = $fecha_nacimiento_raw ? date_i18n( 'j \d\e F \d\e Y', strtotime( $fecha_nacimiento_raw ) ) : '';

    ob_start();
    ?>

<div class="mc-contenido-contenedor">

    <div class="mc-contenido-titular">
        <h2>Titular de la cuenta</h2>
    </div>

    <div class="mc-contenido-contenido">

        <div class="mc-contenido-nombre">
            <?php echo esc_html( $nombre . ' ' . $apellidos ); ?>
        </div>

        <div class="mc-contenido-ubicacion">
            <?php echo esc_html( $provincia ); ?><?php echo $comunidad ? " ({$comunidad})" : ''; ?>
        </div>

        <div class="mc-grid-label" style="margin-top:10px;">
            <?php echo trekkium_icon_blog('', 'Teléfono'); ?>
            <?php echo esc_html( $telefono ); ?>
        </div>

        <div class="mc-grid-label">
            <?php echo trekkium_icon_blog('', 'Email'); ?>
            <?php echo esc_html( $email ); ?>
        </div>    


        <?php if ( in_array( 'guia', (array) $user->roles, true ) ) : ?>


        <!-- Titulaciones -->

        <div class="mc-titulaciones">

            <h4>Mis titulaciones</h4>

            <div class="mc-titulaciones-lista">
                <?php
                $titulaciones = wp_get_object_terms( $user_id, 'titulacion', array( 'fields' => 'names' ) );
                if ( ! empty( $titulaciones ) && ! is_wp_error( $titulaciones ) ) {
                    foreach ( $titulaciones as $titulacion ) {
                        echo '<span class="mc-titulacion-item">' . esc_html( $titulacion ) . '</span>';
                    }
                } else {
                    echo '<p>No hay titulaciones asignadas.</p>';
                }
                ?>  
            </div>

        </div>

        <?php endif; ?>

        <!-- Modalidades -->        

        <div class="mc-modalidades">

            <h4>Mis modalidades</h4>

            <div class="mc-modalidades-lista">
                <?php
                $modalidades = wp_get_object_terms( $user_id, 'modalidad', array( 'fields' => 'names' ) );
                if ( ! empty( $modalidades ) && ! is_wp_error( $modalidades ) ) {
                    foreach ( $modalidades as $modalidad ) {
                        echo '<span class="mc-modalidad-item">' . esc_html( $modalidad ) . '</span>';
                    }
                } else {
                    echo '<p>No hay modalidades asignadas.</p>';
                }
                ?>  
            </div>

        </div>   

        <!-- Idiomas -->
        
        <?php if ( in_array( 'guia', (array) $user->roles, true ) ) : ?>

        <div class="mc-idiomas">

            <h4>Mis idiomas</h4>

            <div class="mc-idiomas-lista">
                <?php
                $idiomas = wp_get_object_terms( $user_id, 'idiomas', array( 'fields' => 'names' ) );

                if ( ! empty( $idiomas ) && ! is_wp_error( $idiomas ) ) {
                    foreach ( $idiomas as $idioma ) {
                        echo '<span class="mc-idioma-item">' . esc_html( $idioma ) . '</span>';
                    }
                } else {
                    echo '<p>No hay idiomas asignados.</p>';
                }
                ?>  
            </div>

        </div>

        <?php endif; ?>



        <!-- Etiquetas -->

        <?php if ( in_array( 'customer', (array) $user->roles, true ) ) : ?>

            <div class="mc-etiquetas">

                <h4>Mis etiquetas</h4>

                <div class="mc-etiquetas-lista">
                    <?php
                    // Obtenemos los términos de la taxonomía 'etiquetas_actividad'
                    $etiquetas = wp_get_object_terms( $user_id, 'etiquetas_actividad', array( 'fields' => 'names' ) );

                    if ( ! empty( $etiquetas ) && ! is_wp_error( $etiquetas ) ) {
                        foreach ( $etiquetas as $etiqueta ) {
                            // Mostramos en minúsculas con # delante
                            echo '<span class="mc-etiqueta-item">#' . esc_html( strtolower( $etiqueta ) ) . '</span>';
                        }
                    } else {
                        echo '<p>No hay etiquetas asignadas.</p>';
                    }
                    ?>  
                </div>

            </div>

        <?php endif; ?>



    </div>

</div>

<?php
    return ob_get_clean();
});
