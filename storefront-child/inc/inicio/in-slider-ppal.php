<?php
// Shortcode del slider principal
function trekkium_in_slider_ppal() {
    ob_start(); ?>

    <section class="in-slider-ppal">

        <div class="in-slider-contenedor">

            <img src="https://trekkium.com/wp-content/uploads/2025/11/annapurna.jpg" alt="Slider principal">

        </div>

    </section>

    <?php
    return ob_get_clean();
}
add_shortcode('in_slider_ppal', 'trekkium_in_slider_ppal');
?>
