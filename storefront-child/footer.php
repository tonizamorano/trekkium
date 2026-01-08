<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */
?>

<footer class="footer-contenedor-principal">

    <div class="footer-contenido">

        <!-- Logo encima del menú -->
        <div class="footer-logo">
            <img src="https://trekkium.com/wp-content/uploads/2025/09/trekkium_logowhite.png" alt="Trekkium logo" />
        </div>
        
        <!-- Línea de copyright -->
        <div class="footer-copy">
            &copy; <?php echo date('Y'); ?> Trekkium. Todos los derechos reservados.
        </div>

         <!-- Menú del footer -->
        <div class="footer-menu">
            <a href="/sobre-nosotros/">Sobre nosotros</a>
            <a href="/preguntas-frecuentes/">Preguntas frecuentes</a>
            <a href="/terminos-y-condiciones/">Términos y condiciones legales</a>
            <a href="/unete-a-trekkium/">Únete a Trekkium</a>
        </div>

        

        

       

        <?php
        // <!-- Patrocinadores -->
        // echo do_shortcode('[footer_patrocinadores]');
        ?>


        <!-- Diseñado por 
        <div class="footer-copy">
            Diseñado por <a href="https://tusitio.com" target="_blank" rel="noopener noreferrer">23Studio</a>
        </div> 
        -->

    </div>

</footer>


<?php do_action( 'storefront_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
