jQuery(document).ready(function($){
    $('#crear_guia').on('click', function(){
        var btn = $(this);
        var post_id = btn.data('candidato-id');
        $('#crear_guia_msg').text('Creando usuario...');

        $.post(trekkium_ajax.ajaxurl,{
            action: 'trekkium_crear_guia',
            post_id: post_id,
            _ajax_nonce: trekkium_ajax.nonce
        }, function(response){
            if(response.success){
                $('#crear_guia_msg').html('<span style="color:green;">Nuevo usuario guía creado con éxito: '+response.data.user_login+'</span>');
                btn.prop('disabled', true);
            } else {
                $('#crear_guia_msg').html('<span style="color:red;">Error: '+response.data+'</span>');
            }
        });
    });
});
