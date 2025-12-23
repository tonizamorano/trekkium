<?php
/**
 * Modal de Cookies para Trekkium
 * 
 * Este modal aparece al cargar la web y permite gestionar preferencias de cookies.
 * 
 * @package Trekkium
 */

if (!defined('ABSPATH')) {
    exit; // Salir si se accede directamente
}

/**
 * Clase para el modal de cookies
 */
class Trekkium_Cookies_Modal {
    
    /**
     * Constructor de la clase
     */
    public function __construct() {
        // Agregar el modal al footer
        add_action('wp_footer', array($this, 'render_modal'));
        
        // Agregar estilos y scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        
        // Manejar AJAX para guardar preferencias
        add_action('wp_ajax_save_cookie_preferences', array($this, 'save_cookie_preferences'));
        add_action('wp_ajax_nopriv_save_cookie_preferences', array($this, 'save_cookie_preferences'));
    }
    
    /**
     * Encolar los estilos y scripts necesarios
     */
    public function enqueue_assets() {
        // Solo si el usuario no ha aceptado/rechazado las cookies
        if (!isset($_COOKIE['trekkium_cookies_accepted']) && !isset($_COOKIE['trekkium_cookies_rejected'])) {
            // Estilos
            wp_enqueue_style(
                'trekkium-cookies-modal',
                get_stylesheet_directory_uri() . '/inc/modal/modal-cookies.css',
                array(),
                '1.0.0'
            );
            
            // Scripts
            wp_enqueue_script(
                'trekkium-cookies-modal',
                get_stylesheet_directory_uri() . '/inc/modal/modal-cookies.js',
                array('jquery'),
                '1.0.0',
                true
            );
            
            // Variables para AJAX
            wp_localize_script(
                'trekkium-cookies-modal',
                'trekkium_cookies',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('trekkium_cookies_nonce')
                )
            );
        }
    }
    
    /**
     * Renderizar el modal en el footer
     */
    public function render_modal() {
        // Solo mostrar si el usuario no ha aceptado/rechazado las cookies
        if (isset($_COOKIE['trekkium_cookies_accepted']) || isset($_COOKIE['trekkium_cookies_rejected'])) {
            return;
        }
        ?>
        <!-- Modal de Cookies Trekkium -->
        <div id="trekkium-cookies-modal" class="trekkium-cookies-modal">
            <!-- Overlay oscuro detrás del modal -->
            <div class="trekkium-cookies-overlay"></div>
            
            <!-- Contenedor principal del modal -->
            <div class="trekkium-cookies-container">
                
                <!-- Parte superior con scroll -->
                <div class="trekkium-cookies-content">
                    <!-- Título principal -->
                    <h2 class="trekkium-cookies-title">Tu Privacidad es importante para Trekkium</h2>
                    
                    <!-- Descripción introductoria -->
                    <p class="trekkium-cookies-description">
                        Trekkium usa cookies propias y de terceros para guardar datos de tu actividad, mejorar tu experiencia de navegación y personalizar el contenido que ves. Puedes gestionar tus preferencias a continuación.
                    </p>
                    
                    <!-- Enlace a política de cookies -->
                    <p class="trekkium-cookies-link">
                        Pulsa el enlace para saber más sobre nuestra 
                        <a style="color:var(--naranja1-100);" href="https://staging2.trekkium.com/terminos-y-condiciones/?seccion=politica-cookies" target="_blank" rel="noopener noreferrer">Política de Cookies</a>
                    </p>
                    
                    <!-- Separador -->
                    <hr class="trekkium-cookies-separator">
                    
                    <!-- Cookies técnicas -->
                    <div class="trekkium-cookies-section">
                        <h3 class="trekkium-cookies-subtitle">Cookies técnicas o necesarias:</h3>
                        <p class="trekkium-cookies-text">
                            Permiten el correcto funcionamiento de la web. Estas cookies se instalan siempre porque son imprescindibles para el funcionamiento básico del sitio.
                        </p>
                    </div>
                    
                    <!-- Cookies analíticas -->
                    <div class="trekkium-cookies-section">
                        <h3 class="trekkium-cookies-subtitle">Cookies analíticas o estadísticas:</h3>
                        <p class="trekkium-cookies-text">
                            Nos ayudan a entender cómo utilizan los visitantes nuestro sitio, qué páginas son las más visitadas, el tiempo de permanencia y otros datos estadísticos que nos permiten mejorar continuamente.
                        </p>
                        
                        <!-- Selector para cookies analíticas -->
                        <div class="trekkium-cookies-toggle">
                            <label class="trekkium-cookies-toggle-label">
                                <input type="radio" name="analytics_cookies" value="accept" checked class="trekkium-cookies-radio">
                                <span class="trekkium-cookies-toggle-text">Aceptar</span>
                            </label>
                            <label class="trekkium-cookies-toggle-label">
                                <input type="radio" name="analytics_cookies" value="reject" class="trekkium-cookies-radio">
                                <span class="trekkium-cookies-toggle-text">Rechazar</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Botón para confirmar preferencias -->
                    <div class="trekkium-cookies-confirm-container">
                        <button type="button" class="trekkium-cookies-confirm-btn">
                            Confirmar las preferencias
                        </button>
                    </div>
                </div>
                
                <!-- Parte inferior fija -->
                <div class="trekkium-cookies-footer">
                    <div class="trekkium-cookies-buttons">
                        <button type="button" class="trekkium-cookies-btn trekkium-cookies-reject">
                            Rechazar todas
                        </button>
                        <button type="button" class="trekkium-cookies-btn trekkium-cookies-accept">
                            Aceptar todas
                        </button>
                    </div>
                </div>
                
            </div>
        </div>
        <?php
    }
    
    /**
     * Guardar preferencias de cookies vía AJAX
     */
    public function save_cookie_preferences() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'trekkium_cookies_nonce')) {
            wp_die('No autorizado');
        }
        
        $action = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
        $analytics = isset($_POST['analytics_cookies']) ? sanitize_text_field($_POST['analytics_cookies']) : '';
        
        // Configurar cookies
        if ($action === 'accept_all') {
            // Aceptar todas las cookies
            setcookie('trekkium_cookies_accepted', 'all', time() + (365 * 24 * 60 * 60), '/');
            setcookie('trekkium_analytics_cookies', 'accepted', time() + (365 * 24 * 60 * 60), '/');
        } elseif ($action === 'reject_all') {
            // Rechazar todas excepto las técnicas
            setcookie('trekkium_cookies_rejected', 'all', time() + (365 * 24 * 60 * 60), '/');
            setcookie('trekkium_analytics_cookies', 'rejected', time() + (365 * 24 * 60 * 60), '/');
        } elseif ($action === 'confirm_preferences') {
            // Guardar preferencias personalizadas
            setcookie('trekkium_cookies_accepted', 'custom', time() + (365 * 24 * 60 * 60), '/');
            setcookie('trekkium_analytics_cookies', $analytics, time() + (365 * 24 * 60 * 60), '/');
        }
        
        // Respuesta de éxito
        wp_send_json_success(array(
            'message' => 'Preferencias guardadas correctamente'
        ));
    }
}

// Inicializar el modal de cookies
function trekkium_init_cookies_modal() {
    new Trekkium_Cookies_Modal();
}
add_action('init', 'trekkium_init_cookies_modal');