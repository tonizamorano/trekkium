<?php
/*
Plugin Name: Sección Precio y Reserva Producto
Description: Muestra la sección de precio y reserva de un producto WooCommerce mediante shortcode. Incluye lista de espera y gestión de estado Cancelado.
Version: 2.6
Author: Toni
*/

function seccion_precio_reserva_shortcode() {
    if (!is_singular('product')) return '';
    
    global $product;
    $product_id = $product->get_id();

    // --- Detectar estado Cancelado ---
    $is_cancelado = ($product->get_status() === 'wc-cancelado');
    
    // --- Verificar roles de usuario ---
    $current_user = wp_get_current_user();
    $roles_bloqueados = array('administrator', 'guia');
    $usuario_bloqueado = false;
    
    if (is_user_logged_in()) {
        foreach ($roles_bloqueados as $rol) {
            if (in_array($rol, $current_user->roles)) {
                $usuario_bloqueado = true;
                break;
            }
        }
    }
    
    // --- Verificar si usuario ya tiene reserva activa de esta actividad ---
    $tiene_reserva_activa = false;
    if (is_user_logged_in() && !$usuario_bloqueado) {
        $tiene_reserva_activa = verificar_reserva_activa_usuario($current_user->ID, $product_id);
    }

    // Campos meta
    $precio_meta = (float) get_post_meta($product_id, 'precio', true);
    $precio_reserva = (float) $product->get_price();
    $estado_actividad = get_post_meta($product_id, 'estado_actividad', true);
    $resto_pago = $precio_meta - $precio_reserva;

    // Formatear precios
    $precio_formateado = number_format($precio_meta, 2, ',', '.');
    $reserva_formateada = number_format($precio_reserva, 2, ',', '.');
    $resto_formateado = number_format($resto_pago, 2, ',', '.');

    // Stock disponible
    $stock_disponible = $product->get_stock_quantity();
    if ($stock_disponible === null) $stock_disponible = 0;

    // URL de checkout
    $checkout_url = esc_url(wc_get_checkout_url());

    // Usuario logueado
    $is_logged_in = is_user_logged_in();

    ob_start(); ?>
    <div class="ps-contenedor-precioreserva">

        <div class="ps-titular">
            <h5>Precio y reserva</h5>
        </div>

        <div class="ps-contenido-precioreserva">

            <!-- Tabla de precios -->

            <div class="ps-tabla-precio">
                <table>
                    <tr>
                        <th align="left">Precio actividad</th>
                        <td align="right" id="ps-precio-total"><?php echo $precio_formateado; ?> €</td>
                    </tr>
                    <tr>
                        <th align="left">Total reserva</th>
                        <td align="right" id="ps-reserva-total"><?php echo $reserva_formateada; ?> €</td>
                    </tr>
                    <tr>
                        <th align="left">Importe pendiente</th>
                        <td align="right" id="ps-resto-total"><?php echo $resto_formateado; ?> €</td>
                    </tr>
                </table>
            </div>

            <!-- Contador y botón -->
             
            <?php if ( $is_cancelado ) : ?>
                <div class="ps-contador-boton">
                    <div class="ps-fila-plazas">
                        <div class="ps-pila-plazas" style="display:flex; flex-direction:column;">
                            <div class="ps-texto-plazas">Nº de plazas</div>
                            <div class="ps-quedan-plazas" style="
                                font-size:14px;
                                font-weight:bold;
                                padding:1px 3px;
                                border:2px solid #0b568b;
                                display:flex;
                                justify-content:center;
                                border-radius:5px;
                            ">
                                QUEDAN <?php echo $stock_disponible; ?>
                            </div>
                        </div>

                        <div class="ps-contador-plazas">
                            <button type="button" class="ps-btn-contador-izq" disabled>-</button>
                            <input type="number" id="ps-cantidad-plazas" name="quantity" 
                                   value="0" min="0" max="0" 
                                   class="ps-input-cantidad" readonly>
                            <button type="button" class="ps-btn-contador-der" disabled>+</button>
                        </div>
                    </div>
                </div>

                <div class="ps-boton-reserva-contenedor">
                    <button type="button" class="ps-boton-reserva ps-boton-cancelado" disabled>
                        ACTIVIDAD CANCELADA
                    </button>
                </div>

            <?php else : ?>

                <div class="ps-contador-boton">
                    <div class="ps-fila-plazas">
                        <div class="ps-pila-plazas" style="display:flex; flex-direction:column;">
                            <div class="ps-texto-plazas">Nº de plazas</div>
                            <div class="ps-quedan-plazas" style="
                                font-size:14px;
                                font-weight:bold;
                                padding:1px 3px;
                                border:2px solid #0b568b;
                                display:flex;
                                justify-content:center;
                                border-radius:5px;
                            ">
                                QUEDAN <?php echo $stock_disponible; ?>
                            </div>
                        </div>

                        <div class="ps-contador-plazas">
                            <button type="button" class="ps-btn-contador-izq" onclick="cambiarCantidad(-1)">-</button>
                            <input type="number" id="ps-cantidad-plazas" name="quantity" 
                                value="<?php echo $stock_disponible > 0 ? 1 : 0; ?>" 
                                min="<?php echo $stock_disponible > 0 ? 1 : 0; ?>" 
                                max="<?php echo $stock_disponible; ?>" 
                                class="ps-input-cantidad" 
                                <?php echo $stock_disponible === 0 ? 'readonly' : ''; ?>>
                            <button type="button" class="ps-btn-contador-der" onclick="cambiarCantidad(1)">+</button>
                        </div>
                    </div>
                </div>

                <div class="ps-boton-reserva-contenedor">
                    <button type="button" id="ps-boton-reservar" class="ps-boton-reserva">
                        <?php if ($stock_disponible > 0): ?>
                            RESERVAR POR <span id="ps-precio-reserva"><?php echo $reserva_formateada; ?></span> €
                        <?php else: ?>
                            PONME EN LISTA DE ESPERA
                        <?php endif; ?>
                    </button>
                    
                    <!-- Mensaje dinámico que aparecerá al hacer clic -->
                    <div class="ps-mensaje-lista-espera" id="ps-mensaje-lista-espera" style="display:none;"></div>
                </div>

                <!-- Modal oculto -->
                <div id="ps-modal-mensaje" style="display:none;">
                    <div class="ps-modal-contenido">
                        <span class="ps-modal-cerrar">&times;</span>
                        <div id="ps-modal-texto"></div>
                    </div>
                </div>

                <style>
                #ps-modal-mensaje {
                    position: fixed;
                    z-index: 9999;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.4);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .ps-modal-contenido {
                    background-color: #fff;
                    padding: 20px 30px;
                    border-radius: 10px;
                    text-align: center;
                    position: relative;
                    max-width: 400px;
                    width: 90%;
                }
                .ps-modal-cerrar {
                    position: absolute;
                    top: 10px;
                    right: 15px;
                    font-size: 20px;
                    font-weight: bold;
                    cursor: pointer;
                }
                #ps-modal-texto {
                    color: #0b568b;
                    font-size: 18px;
                }
                @media (max-width: 768px) {
                    #ps-modal-texto {
                        font-size: 20px;
                    }
                }
                </style>


            <?php endif; ?>

        </div>
    </div>

    <?php if ( ! $is_cancelado ) : ?>
    <script>
    const precioUnitario = <?php echo $precio_meta; ?>;
    const reservaUnitaria = <?php echo $precio_reserva; ?>;
    const restoUnitario = <?php echo $resto_pago; ?>;
    const productoId = <?php echo (int) $product_id; ?>;
    const checkoutUrl = '<?php echo $checkout_url; ?>';
    const stockDisponible = <?php echo (int) $stock_disponible; ?>;
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const usuarioBloqueado = <?php echo $usuario_bloqueado ? 'true' : 'false'; ?>;
    const tieneReservaActiva = <?php echo $tiene_reserva_activa ? 'true' : 'false'; ?>;

    function cambiarCantidad(cambio) {
        const input = document.getElementById('ps-cantidad-plazas');
        if (!input || input.readOnly) return;
        let cantidadActual = parseInt(input.value);
        const maxCantidad = parseInt(input.max);
        const minCantidad = parseInt(input.min);
        let nuevaCantidad = cantidadActual + cambio;
        if (nuevaCantidad < minCantidad) nuevaCantidad = minCantidad;
        if (nuevaCantidad > maxCantidad) nuevaCantidad = maxCantidad;
        input.value = nuevaCantidad;
        actualizarPrecios(nuevaCantidad);
        actualizarEstadoBotones(nuevaCantidad, maxCantidad, minCantidad);
    }

    function actualizarPrecios(cantidad) {
        const precioTotal = precioUnitario * cantidad;
        const reservaTotal = reservaUnitaria * cantidad;
        const restoTotal = restoUnitario * cantidad;
        const formatoMoneda = (v) => v.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        
        document.getElementById('ps-precio-total').textContent = formatoMoneda(precioTotal) + ' €';
        document.getElementById('ps-reserva-total').textContent = formatoMoneda(reservaTotal) + ' €';
        document.getElementById('ps-resto-total').textContent = formatoMoneda(restoTotal) + ' €';
        
        const precioReservaEl = document.getElementById('ps-precio-reserva');
        if(precioReservaEl) precioReservaEl.textContent = formatoMoneda(reservaTotal);
    }

    function actualizarEstadoBotones(cantidad, max, min) {
        const btnIzq = document.querySelector('.ps-btn-contador-izq');
        const btnDer = document.querySelector('.ps-btn-contador-der');
        if (btnIzq) btnIzq.disabled = (cantidad <= min);
        if (btnDer) btnDer.disabled = (cantidad >= max);
    }

    // Función para mostrar el modal
    function mostrarMensajeModal(texto) {
        let modal = document.getElementById('ps-modal-mensaje');
        let modalTexto = document.getElementById('ps-modal-texto');
        const cerrar = modal.querySelector('.ps-modal-cerrar');

        modalTexto.textContent = texto;
        modal.style.display = 'flex';

        cerrar.onclick = () => { modal.style.display = 'none'; };
        window.onclick = (e) => { if (e.target === modal) modal.style.display = 'none'; };
    }

    function reservarAhora() {
        // Primero verificar si el usuario ya tiene reserva activa
        if (tieneReservaActiva) {
            mostrarMensajeModal('No puedes reservar dos veces la misma actividad');
            return;
        }

        // Verificar si el usuario está bloqueado
        if (usuarioBloqueado) {
            mostrarMensajeModal('Los usuarios con rol de Administrador o Guía no pueden reservar actividades');
            return;
        }

        if (!isLoggedIn) {
            mostrarMensajeModal('Necesitas acceder con tu cuenta para reservar');
            return;
        }

        const cantidadInput = document.getElementById('ps-cantidad-plazas');
        const cantidad = cantidadInput ? parseInt(cantidadInput.value) : 1;

        console.log('Iniciando reserva:', { productoId, cantidad, stockDisponible });

        if (stockDisponible > 0) {
            const url = `/?add-to-cart=${productoId}&quantity=${cantidad}`;
            console.log('Redirigiendo a:', url);
            fetch(url, { method: 'GET', credentials: 'same-origin' })
            .then(response => {
                console.log('Respuesta recibida, redirigiendo a checkout');
                setTimeout(() => {
                    window.location.href = checkoutUrl + '?debug=' + Date.now();
                }, 500);
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                window.location.href = url + '&redirect=' + encodeURIComponent(checkoutUrl);
            });
        } else {
            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=trekkium_lista_espera&producto_id=${productoId}`,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensajeModal(data.data);
                } else {
                    mostrarMensajeModal('Error: ' + (data.data || 'No se pudo procesar la solicitud.'));
                }
            })
            .catch(() => {
                mostrarMensajeModal('Error de conexión');
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const botonReserva = document.getElementById('ps-boton-reservar');
        if (botonReserva) botonReserva.addEventListener('click', reservarAhora);

        const input = document.getElementById('ps-cantidad-plazas');
        if (input) {
            let cantidad = parseInt(input.value);
            const max = parseInt(input.max);
            const min = parseInt(input.min);
            actualizarEstadoBotones(cantidad, max, min);
            input.addEventListener('change', function() {
                let cantidad = parseInt(this.value);
                if (cantidad < min) cantidad = min;
                if (cantidad > max) cantidad = max;
                this.value = cantidad;
                actualizarPrecios(cantidad);
                actualizarEstadoBotones(cantidad, max, min);
            });
        }
    });
    </script>


    <?php endif; ?>    

    <?php
    return ob_get_clean();
}
add_shortcode('seccion_precio_reserva', 'seccion_precio_reserva_shortcode');

/**
 * Verifica si un usuario ya tiene una reserva activa para un producto
 * 
 * @param int $user_id ID del usuario
 * @param int $product_id ID del producto
 * @return bool True si tiene reserva activa, False si no
 */
function verificar_reserva_activa_usuario($user_id, $product_id) {
    // Estados de pedido considerados como "reserva activa"
    $estados_activos = array('pending', 'processing', 'on-hold', 'completed');
    
    // Obtener todos los pedidos del usuario
    $orders = wc_get_orders(array(
        'customer_id' => $user_id,
        'status'      => $estados_activos,
        'limit'       => -1, // Obtener todos los pedidos
    ));
    
    foreach ($orders as $order) {
        // Verificar cada item en el pedido
        foreach ($order->get_items() as $item) {
            if ($item->get_product_id() == $product_id || 
                $item->get_variation_id() == $product_id) {
                return true; // Encontró el producto en un pedido activo
            }
        }
    }
    
    return false; // No encontró el producto en pedidos activos
}