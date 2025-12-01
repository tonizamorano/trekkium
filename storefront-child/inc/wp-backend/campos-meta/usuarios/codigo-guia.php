<?php
/**
 * 1. Generar automáticamente codigo_guia al crear usuarios con rol "guia"
 */
add_action( 'user_register', 'trekkium_asignar_codigo_guia', 10, 1 );
function trekkium_asignar_codigo_guia( $user_id ) {
    $user_info = get_userdata( $user_id );
    if ( ! in_array( 'guia', (array) $user_info->roles, true ) ) {
        return;
    }

    // Evitar sobrescritura
    if ( get_user_meta( $user_id, 'codigo_guia', true ) ) {
        return;
    }

    // Calcular el siguiente número disponible
    $ultimo_codigo = trekkium_obtener_ultimo_codigo_guia();
    $siguiente_numero = $ultimo_codigo + 1;

    $codigo = 'TG' . $siguiente_numero;

    update_user_meta( $user_id, 'codigo_guia', $codigo );
}

/**
 * 2. Bloquear cualquier intento de modificar el codigo_guia desde perfil
 */
add_action( 'personal_options_update', 'trekkium_bloquear_codigo_guia' );
add_action( 'edit_user_profile_update', 'trekkium_bloquear_codigo_guia' );

function trekkium_bloquear_codigo_guia( $user_id ) {
    $codigo_actual = get_user_meta( $user_id, 'codigo_guia', true );
    update_user_meta( $user_id, 'codigo_guia', $codigo_actual );
}

/**
 * 3. Mostrar el codigo_guia en el perfil del usuario (solo lectura)
 */
add_action( 'show_user_profile', 'trekkium_mostrar_codigo_guia_perfil' );
add_action( 'edit_user_profile', 'trekkium_mostrar_codigo_guia_perfil' );

function trekkium_mostrar_codigo_guia_perfil( $user ) {
    if ( in_array( 'guia', (array) $user->roles, true ) ) {
        $codigo = get_user_meta( $user->ID, 'codigo_guia', true );
        ?>
        <h2>Código guía</h2>
        <table class="form-table">
            <tr>
                <th> Código guía </th>
                <td>
                    <input type="text" value="<?php echo esc_attr( $codigo ); ?>" readonly style="background:#f1f1f1; border:none; padding:4px 8px;"/>
                    <p class="description">Código único asignado automáticamente.</p>
                </td>
            </tr>
        </table>
        <?php
    }
}

/**
 * 4. Agregar columna "Código guía" en la tabla de usuarios de WP Admin
 */
add_filter( 'manage_users_columns', 'trekkium_agregar_columna_codigo_guia' );
function trekkium_agregar_columna_codigo_guia( $columns ) {
    // Insertar después de "Nombre"
    $new_columns = [];
    foreach ( $columns as $key => $value ) {
        $new_columns[$key] = $value;
        if ( $key === 'name' ) {
            $new_columns['codigo_guia'] = 'Código guía';
        }
    }
    return $new_columns;
}

add_action( 'manage_users_custom_column', 'trekkium_mostrar_codigo_guia_columna', 10, 3 );
function trekkium_mostrar_codigo_guia_columna( $value, $column_name, $user_id ) {
    if ( 'codigo_guia' === $column_name ) {
        return get_user_meta( $user_id, 'codigo_guia', true );
    }
    return $value;
}

/**
 * 5. Función para obtener el último número usado en los códigos TG
 */
function trekkium_obtener_ultimo_codigo_guia() {
    global $wpdb;
    $resultados = $wpdb->get_col("
        SELECT meta_value 
        FROM {$wpdb->usermeta}
        WHERE meta_key = 'codigo_guia'
        AND meta_value LIKE 'TG%'
    ");

    if ( empty( $resultados ) ) {
        return 0;
    }

    $numeros = [];
    foreach ( $resultados as $codigo ) {
        $numero = intval( str_replace( 'TG', '', $codigo ) );
        if ( $numero > 0 ) {
            $numeros[] = $numero;
        }
    }
    return ! empty( $numeros ) ? max( $numeros ) : 0;
}

/**
 * 6. Asignar codigo_guia a usuarios existentes que no tengan uno aún
 * Solo se ejecuta una vez. Posteriormente puedes comentar/eliminar el hook.
 */
add_action( 'admin_init', 'trekkium_asignar_codigo_guia_existentes' );
function trekkium_asignar_codigo_guia_existentes() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Evitar ejecutar varias veces
    if ( get_option( 'trekkium_codigo_guia_actualizado' ) ) {
        return;
    }

    $guia_users = get_users( [
        'role'    => 'guia',
        'orderby' => 'ID',
        'order'   => 'ASC',
    ] );

    foreach ( $guia_users as $user ) {
        $codigo_actual = get_user_meta( $user->ID, 'codigo_guia', true );
        if ( ! $codigo_actual ) {
            $ultimo_codigo = trekkium_obtener_ultimo_codigo_guia();
            $siguiente_numero = $ultimo_codigo + 1;
            $codigo = 'TG' . $siguiente_numero;
            update_user_meta( $user->ID, 'codigo_guia', $codigo );
        }
    }

    update_option( 'trekkium_codigo_guia_actualizado', 1 );
}
