<?php
/* ----------------------------------------------
 * Shortcode: [in_ventajas_trekkium]
 * ---------------------------------------------- */
add_shortcode('in_ventajas_trekkium', function () {
ob_start(); ?>

<section class="trekkium-ventajas">
    <h2 class="ventajas-title">Ventajas de Trekkium</h2>

    <div class="ventajas-wrapper">
        <button class="ventaja-arrow left" aria-label="Anterior">&#10094;</button>

        <div class="ventajas-carousel">
            
            <!-- Guías oficiales -->
            <div class="ventaja-item">
                <?php echo do_shortcode('[icon_guias2]'); ?> 
                <h3>Guías<br>oficiales</h3>
                <p>Verificados y habilitados para organizar y guiar actividades en sus competencias.</p>
            </div>
            
            <!-- Cancelación gratuita -->
            <div class="ventaja-item">
                <?php echo do_shortcode('[icon_cancelacion_gratuita]'); ?>
                <h3>Cancelación<br>gratuita</h3>
                <p>Hasta 24 horas antes de la actividad, con un solo click y sin compromiso.</p>
            </div>
            
            <!-- Seguros incluidos -->
            <div class="ventaja-item">
                <?php echo do_shortcode('[icon_escudo]'); ?>
                <h3>Seguros<br>incluidos</h3>
                <p>Responsabilidad Civil y Accidentes de Montaña, en todas las actividades.</p>
            </div>
            
            <!-- Grupos reducidos -->
            <div class="ventaja-item">
                <?php echo do_shortcode('[icon_grupo_min]'); ?>
                <h3>Grupos<br>reducidos</h3>
                <p>Adaptados a los márgenes de seguridad según la modalidad y el nivel de dificultad.</p>
            </div>
            
            <!-- Sin costes -->
            <div class="ventaja-item">
                <?php echo do_shortcode('[icon_sin_costes]'); ?>
                <h3>Sin costes<br>adicionales</h3>
                <p>Ni de alta, ni de mantenimiento, ni de baja de tu cuenta. Puedes irte cuando quieras.</p>
            </div>
            
            <!-- Reserva online -->
            <div class="ventaja-item">
                <?php echo do_shortcode('[icon_reserva_online]'); ?>
                <h3>Reserva<br>online</h3>
                <p>Sencillo, rápido y sin pagar nada hasta 24 horas antes de la actividad.</p>
            </div>

            <!-- Pago seguro -->
            <div class="ventaja-item">
                <?php echo do_shortcode('[icon_pago_seguro]'); ?>
                <h3>Pago<br>seguro</h3>
                <p>Pasarela Stripe, con los más estrictos sistemas de protección de datos.</p>
            </div>

            <!-- Contacto permanente -->
            <div class="ventaja-item">
                <?php echo do_shortcode('[icon_contacto_permanente]'); ?>
                <h3>Contacto<br>permanente</h3>
                <p>Con tu guía y el resto del grupo de participantes, a través de grupo de Whatsapp.</p>
            </div>

            <!-- Blog profesional -->
            <div class="ventaja-item">
                <?php echo do_shortcode('[icon_blog1]'); ?>
                <h3>Blog<br>profesional</h3>
                <p>Artículos, consejos y recomendaciones sobre montañismo de la mano de guías profesionales.</p>
            </div>
            
            
            

        </div>

        <button class="ventaja-arrow right" aria-label="Siguiente">&#10095;</button>
    </div>
</section>

<style>
/* Contenedor general */
.trekkium-ventajas {
    width: 900px;
    max-width: 100%;
    margin: 15px auto;
    padding: 15px;
}

/* Título */
.ventajas-title {
    text-align: center;
    font-size: 24px;
    color: #ffffff;
    margin-bottom: 20px;
    font-weight: 500;
}

/* Wrapper */
.ventajas-wrapper {
    position: relative;
    width: 100%;
}

/* Flechas */
.ventaja-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 20;
    background: rgba(0,0,0,0.4);
    color: #fff;
    border: none;
    font-size: 32px;
    padding: 6px 12px;
    cursor: pointer;
    border-radius: 6px;
    display: none;
}
.ventaja-arrow.left { left: 10px; }
.ventaja-arrow.right { right: 10px; }

@media (min-width: 768px) {
    .ventaja-arrow { display: block; }
}

/* Carrusel */
.ventajas-carousel {
    display: flex;
    gap: 15px;
    overflow-x: auto;
    padding: 10px 5px;
    scroll-behavior: smooth;
    scroll-snap-type: x mandatory;
}

/* Ocultar scrollbar */
.ventajas-carousel::-webkit-scrollbar { display: none; }
.ventajas-carousel { -ms-overflow-style: none; scrollbar-width: none; }

/* Tarjeta */
.ventaja-item {
    flex: 0 0 calc(25% - 10px);
    background: #ffffff;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    scroll-snap-align: start;
    display: flex;
    flex-direction: column;
    
    align-items: flex-start;
    align-content: flex-start;
    align-self: flex-start;
    
}

/* SVG */
.ventaja-item svg {
    margin-bottom: 10px;
    width: 50px; 
    height: 50px; 
    fill: var(--azul1-80);
    align-self: center;
    box-shadow: #fff;

    filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.2));
}

/* Título item */
.ventaja-item h3 {
    font-size: 22px;
    font-weight: 600;
    color: var(--naranja1-100);
    margin-bottom: 10px !important;
    align-self: center;
}

/* Texto */
.ventaja-item p {
    font-size: 18px;
    font-weight: 500;
    color: var(--azul1-100);
    margin-bottom: 0 !important;
    line-height: 1.2;
    align-self: center;
}

/* Vista móvil: 2 columnas */
@media (max-width: 768px) {

    .ventaja-item {
        flex: 0 0 calc(50% - 3px);
        padding: 10px 7px;
    }

}

/* Si quieres más de 6 items, ajusta automáticamente el tamaño */
@media (min-width: 768px) {

    .ventajas-carousel {
        flex-wrap: nowrap;
    }
    
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const carousel = document.querySelector(".ventajas-carousel");
    const btnLeft = document.querySelector(".ventaja-arrow.left");
    const btnRight = document.querySelector(".ventaja-arrow.right");

    if (btnLeft && btnRight && carousel) {
        btnLeft.addEventListener("click", () => {
            carousel.scrollBy({ left: -carousel.clientWidth, behavior: "smooth" });
        });
        btnRight.addEventListener("click", () => {
            carousel.scrollBy({ left: carousel.clientWidth, behavior: "smooth" });
        });
    }
});
</script>

<?php
return ob_get_clean();
});