<?php
// Enviar email de verificación tras registro de usuario
add_action( 'user_register', 'trekkium_enviar_email_verificacion', 10, 1 );

function trekkium_enviar_email_verificacion( $user_id ) {

    $user = get_userdata( $user_id );
    if ( ! $user ) return;

    $usuario_nombre = $user->first_name ?: $user->display_name;
    $usuario_email  = $user->user_email;

    // --- 1️⃣ Generar token de verificación ---
    $token = wp_generate_password( 32, false );
    update_user_meta( $user_id, 'trekkium_email_verification_token', $token );
    update_user_meta( $user_id, 'trekkium_email_verificado', 0 );

    // URL de verificación (ajusta la página si lo deseas)
    $verificacion_url = add_query_arg( [
        'trekkium_verify' => 1,
        'uid'   => $user_id,
        'token' => $token,
    ], home_url( '/' ) );

    // --- 2️⃣ Contenido del email (MISMO DISEÑO) ---
    $email_content = '

    <div class="mail-content-contenedor">  

        <div class="mail-content-seccion">

            <p style="margin-bottom:10px !important;">
                Hola <strong>' . esc_html( $usuario_nombre ) . '</strong>,
            </p>

            <p>
                Gracias por registrarte en <strong>Trekkium</strong>.
                Para completar el proceso y activar tu cuenta, es necesario verificar tu dirección de correo electrónico.
            </p>

        </div>

        <div class="mail-content-seccion">

            <h4>Verificación de cuenta</h4>

            <p>
                Haz clic en el siguiente botón para confirmar tu email y activar tu cuenta:
            </p>

            <a href="' . esc_url( $verificacion_url ) . '" class="mail-button">
                Verificar mi cuenta
            </a>

        </div>

        <div class="mail-content-seccion">

            <p style="margin-top:10px !important;">
                Si no has creado una cuenta en Trekkium, puedes ignorar este mensaje.
            </p>

        </div>

        <div class="mail-content-seccion">

            <p>
                —<br>El equipo de Trekkium
            </p>

        </div>

    </div>
    ';

    // --- 3️⃣ Título ---
    $email_title = 'VERIFICA TU CUENTA EN TREKKIUM';

    // --- 4️⃣ Cargar plantilla HTML ---
    $helpers_path = dirname(__FILE__) . '/mailing-functions.php';
    if ( file_exists( $helpers_path ) ) {
        include_once $helpers_path;
    }

    if ( function_exists( 'trekkium_get_email_html' ) ) {
        $final_email_content = trekkium_get_email_html( $email_title, $email_content );
    } else {
        $final_email_content = '<html><body>' . $email_content . '</body></html>';
    }

    // --- 5️⃣ Enviar email ---
    $headers = [ 'Content-Type: text/html; charset=UTF-8' ];

    if ( ! wp_mail( $usuario_email, $email_title, $final_email_content, $headers ) ) {
        error_log( 'Trekkium: error enviando email de verificación a ' . $usuario_email );
    } else {
        error_log( 'Trekkium: email de verificación enviado a ' . $usuario_email );
    }
}
