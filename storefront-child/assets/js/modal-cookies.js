/**
 * Modal de Cookies para Trekkium
 * 
 * Maneja la interacción del usuario con el modal de cookies.
 */

jQuery(document).ready(function($) {

    // --- Cerrar modal automáticamente si la URL apunta a política de cookies ---
    const seccionSlug = new URLSearchParams(window.location.search).get('seccion');
    if (seccionSlug === 'politica-cookies') {
        const modal = $('#trekkium-cookies-modal');
        if(modal.length) {
            modal.hide();
            modal.find('.trekkium-cookies-overlay').hide();
        }
    }
    
    // Ocultar el modal cuando se haga clic en el overlay
    $('.trekkium-cookies-overlay').on('click', function(e) {
        // No cerrar al hacer clic en el overlay - requerir acción explícita
        e.stopPropagation();
    });
    
    // Aceptar todas las cookies
    $('.trekkium-cookies-accept').on('click', function() {
        handleCookieAction('accept_all');
    });
    
    // Rechazar todas las cookies
    $('.trekkium-cookies-reject').on('click', function() {
        handleCookieAction('reject_all');
    });
    
    // Confirmar preferencias personalizadas
    $('.trekkium-cookies-confirm-btn').on('click', function() {
        var analyticsValue = $('input[name="analytics_cookies"]:checked').val();
        handleCookieAction('confirm_preferences', analyticsValue);
    });
    
    /**
     * Manejar la acción de cookies seleccionada
     */
    function handleCookieAction(actionType, analyticsValue = '') {
        showLoadingState(true);
        
        var data = {
            action: 'save_cookie_preferences',
            nonce: trekkium_cookies.nonce,
            action_type: actionType
        };
        
        if (actionType === 'confirm_preferences') {
            data.analytics_cookies = analyticsValue;
        }
        
        $.ajax({
            url: trekkium_cookies.ajax_url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    hideModalWithAnimation();
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                } else {
                    showLoadingState(false);
                    console.error('Error al guardar preferencias de cookies');
                }
            },
            error: function() {
                showLoadingState(false);
                console.error('Error de conexión al guardar preferencias');
                setCookiesFallback(actionType, analyticsValue);
            }
        });
    }
    
    /**
     * Ocultar el modal con animación
     */
    function hideModalWithAnimation() {
        $('.trekkium-cookies-container').animate({
            opacity: 0,
            top: '45%'
        }, 300);
        
        $('.trekkium-cookies-overlay').fadeOut(300, function() {
            $('#trekkium-cookies-modal').hide();
        });
    }
    
    /**
     * Mostrar u ocultar estado de carga
     */
    function showLoadingState(show) {
        if (show) {
            $('.trekkium-cookies-btn, .trekkium-cookies-confirm-btn').prop('disabled', true).css('opacity', '0.7');
            $('.trekkium-cookies-accept, .trekkium-cookies-reject, .trekkium-cookies-confirm-btn').each(function() {
                var originalText = $(this).text();
                $(this).data('original-text', originalText).html('<span class="trekkium-cookies-spinner"></span> Procesando...');
            });
        } else {
            $('.trekkium-cookies-btn, .trekkium-cookies-confirm-btn').prop('disabled', false).css('opacity', '1');
            $('.trekkium-cookies-accept, .trekkium-cookies-reject, .trekkium-cookies-confirm-btn').each(function() {
                var originalText = $(this).data('original-text');
                if (originalText) {
                    $(this).text(originalText);
                }
            });
        }
    }
    
    /**
     * Establecer cookies directamente como fallback si AJAX falla
     */
    function setCookiesFallback(actionType, analyticsValue) {
        var expiryDate = new Date();
        expiryDate.setFullYear(expiryDate.getFullYear() + 1);
        
        if (actionType === 'accept_all') {
            document.cookie = 'trekkium_cookies_accepted=all; expires=' + expiryDate.toUTCString() + '; path=/';
            document.cookie = 'trekkium_analytics_cookies=accepted; expires=' + expiryDate.toUTCString() + '; path=/';
        } else if (actionType === 'reject_all') {
            document.cookie = 'trekkium_cookies_rejected=all; expires=' + expiryDate.toUTCString() + '; path=/';
            document.cookie = 'trekkium_analytics_cookies=rejected; expires=' + expiryDate.toUTCString() + '; path=/';
        } else if (actionType === 'confirm_preferences') {
            document.cookie = 'trekkium_cookies_accepted=custom; expires=' + expiryDate.toUTCString() + '; path=/';
            document.cookie = 'trekkium_analytics_cookies=' + analyticsValue + '; expires=' + expiryDate.toUTCString() + '; path=/';
        }
        
        hideModalWithAnimation();
        setTimeout(function() {
            window.location.reload();
        }, 500);
    }
    
    // Spinner CSS
    $('head').append(
        '<style>' +
        '.trekkium-cookies-spinner {' +
        '   display: inline-block;' +
        '   width: 16px;' +
        '   height: 16px;' +
        '   margin-right: 8px;' +
        '   border: 2px solid rgba(255,255,255,0.3);' +
        '   border-radius: 50%;' +
        '   border-top-color: #fff;' +
        '   animation: trekkium-spin 1s ease-in-out infinite;' +
        '}' +
        '@keyframes trekkium-spin {' +
        '   to { transform: rotate(360deg); }' +
        '}' +
        '</style>'
    );

});
