<?php
// Bloquea la Biblioteca de Medios para todos los usuarios que no sean administradores
function bloquear_biblioteca_medios() {
    if (is_admin()) {
        $user = wp_get_current_user();
        // Si el usuario no es administrador
        if (!in_array('administrator', $user->roles)) {
            $screen = get_current_screen();
            // Si intenta acceder a la Biblioteca de Medios
            if ($screen && $screen->id === 'upload') {
                wp_redirect(admin_url()); // redirige al Escritorio
                exit;
            }
        }
    }
}
add_action('current_screen', 'bloquear_biblioteca_medios');


function ocultar_tab_biblioteca_js() {
    $user = wp_get_current_user();
    if (!in_array('administrator', $user->roles)) {
        ?>
        <script>
        jQuery(document).ready(function($){
            // Oculta la pestaña 'Biblioteca' en el modal de medios
            $(document).on('click', '.media-frame-router .media-menu-item', function(){
                var tab = $(this).data('media-menu-item');
                if(tab === 'library'){
                    $(this).hide();
                }
            });
            // También oculta la biblioteca cuando el modal ya está abierto
            $('.media-frame-router .media-menu-item[data-media-menu-item="library"]').hide();
        });
        </script>
        <?php
    }
}
add_action('admin_footer', 'ocultar_tab_biblioteca_js');
add_action('wp_footer', 'ocultar_tab_biblioteca_js'); // para front-end


// Restringir acceso a la Biblioteca de Medios en el modal de selección
function restringir_biblioteca_medios_modal( $query ) {
    $user = wp_get_current_user();
    if (!in_array('administrator', $user->roles)) {
        $query['author'] = $user->ID; // solo ver sus propios archivos
    }
    return $query;
}
add_filter('ajax_query_attachments_args', 'restringir_biblioteca_medios_modal');
