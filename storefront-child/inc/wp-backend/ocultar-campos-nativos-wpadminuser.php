<?php
// === LIMPIEZA DE PERFIL DE USUARIO (BACKEND) ===

// 1. Ocultar selector de esquema de colores
function quitar_esquema_colores_admin() {
    remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
}
add_action( 'admin_head', 'quitar_esquema_colores_admin' );

// 2. Ocultar sección de dirección de envío (WooCommerce)
add_filter( 'woocommerce_customer_meta_fields', function( $fields ) {
    unset( $fields['shipping'] ); // Oculta toda la sección de envío
    return $fields;
}, 10, 1 );

// 3. Ocultar campos del perfil de usuario con CSS
function quitar_opciones_perfil_usuario() {
    global $pagenow;
    if ( in_array( $pagenow, ['profile.php', 'user-edit.php'] ) ) {
        echo '<style>
            /* Editor visual */
            tr.user-rich-editing-wrap { display: none; }

            /* Atajos de teclado */
            tr.user-comment-shortcuts-wrap { display: none; }

            /* Web */
            tr.user-url-wrap { display: none; }

            /* Acerca del usuario */
            .user-description-wrap,
            tr.user-description-wrap,
            tr.user-profile-picture,
            h2:has(+ table.form-table .user-description-wrap) {
                display: none !important;
            }

            /* Dirección de facturación: Nombre, Apellidos, Empresa, Dirección, Email */
            label[for="billing_first_name"],
            #billing_first_name,
            label[for="billing_last_name"],
            #billing_last_name,
            label[for="billing_company"],
            #billing_company,
            label[for="billing_address_1"],
            #billing_address_1,
            label[for="billing_address_2"],
            #billing_address_2,
            label[for="billing_email"],
            #billing_email,
            #billing_first_name + br,
            #billing_last_name + br,
            #billing_company + br,
            #billing_address_1 + br,
            #billing_address_2 + br,
            #billing_email + br {
                display: none !important;
            }

            tr:has(#billing_first_name),
            tr:has(#billing_last_name),
            tr:has(#billing_company),
            tr:has(#billing_address_1),
            tr:has(#billing_address_2),
            tr:has(#billing_email) {
                display: none !important;
            }
        </style>';
    }
}
add_action( 'admin_head', 'quitar_opciones_perfil_usuario' );

