<?php
// Shortcode [in_ultimas_entradas] para mostrar últimas entradas del blog
add_shortcode('in_ultimas_entradas', 'trekkium_in_ultimas_entradas');
function trekkium_in_ultimas_entradas() {

    ob_start();

    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $query = new WP_Query($args);
    ?>

    <div class="in-blog-wrapper">

        <div class="in-blog-header">
            <button class="in-blog-arrow in-blog-arrow-left">
                <?php echo do_shortcode('[icon_flecha_izq]'); ?>
            </button>

            <h2 class="in-blog-sectiontitle">Últimos artículos del Blog</h2>

            <button class="in-blog-arrow in-blog-arrow-right">
                <?php echo do_shortcode('[icon_flecha_der]'); ?>
            </button>
        </div>

        <?php if ($query->have_posts()) : ?>
            <div class="in-blog-carousel">

                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $permalink = get_permalink();
                    $categorias = get_the_category();
                    $fecha = get_the_date('d/m/Y');
                    $views = (int) get_post_meta(get_the_ID(), 'post_views_count', true);
                    ?>

                    <div class="in-blog-item">
                        <a href="<?php echo esc_url($permalink); ?>" class="in-blog-image-link">
                            <div class="in-blog-imgcontenedor">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php echo get_the_post_thumbnail(get_the_ID(), 'large', ['class' => 'in-blog-img']); ?>
                                <?php else: ?>
                                    <div class="in-blog-img-placeholder"></div>
                                <?php endif; ?>
                            </div>
                        </a>

                        <div class="in-blog-contenido">
                            <a href="<?php echo esc_url($permalink); ?>" class="in-blog-contenido-link">
                                <h3 class="in-blog-titulo"><?php the_title(); ?></h3>
                            </a>

                            <?php if (!empty($categorias)) : ?>
                                <div class="in-blog-categorias">
                                    <?php foreach ($categorias as $categoria) : ?>
                                        <a href="<?php echo esc_url(get_category_link($categoria->term_id)); ?>" class="in-blog-categoria-link">
                                            <?php echo esc_html($categoria->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="in-blog-infoextra">
                                <div class="in-blog-info-item">
                                    <?php echo do_shortcode('[icon_fecha1]'); ?>
                                    <span><?php echo esc_html($fecha); ?></span>
                                </div>

                                <div class="in-blog-info-item">
                                    <?php echo do_shortcode('[icon_ojo1]'); ?>
                                    <span><?php echo esc_html($views); ?></span>
                                </div>
                            </div>

                        </div>
                    </div>

                <?php endwhile; ?>

            </div>
            <div class="in-dots">
                <?php for ($i = 0; $i < $query->post_count; $i++) : ?>
                    <span class="in-dot <?php echo $i === 0 ? 'active' : ''; ?>"></span>
                <?php endfor; ?>
            </div>

        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const wrapper = document.querySelector('.in-blog-wrapper');
        if (!wrapper) return;
        const carousel = wrapper.querySelector(".in-blog-carousel");
        const left = wrapper.querySelector(".in-blog-arrow-left");
        const right = wrapper.querySelector(".in-blog-arrow-right");
        const dots = Array.from(wrapper.querySelectorAll('.in-dot'));
        const items = Array.from(wrapper.querySelectorAll('.in-blog-item'));

        if (!carousel || !items.length) return;

        let index = 0;

        function getStep() {
            if (items.length > 1) {
                const step = items[1].offsetLeft - items[0].offsetLeft;
                if (step && !isNaN(step)) return step;
            }
            return items[0].offsetWidth;
        }

        let step = getStep();
        window.addEventListener('resize', () => { step = getStep(); });

        function updateDots() {
            dots.forEach((dot, i) => dot.classList.toggle('active', i === index));
        }

        function scrollToIndex(i) {
            i = Math.max(0, Math.min(i, items.length - 1));
            const leftPos = items[0].offsetLeft + step * i;
            carousel.scrollTo({ left: leftPos, behavior: 'smooth' });
            index = i;
            updateDots();
        }

        right && right.addEventListener("click", () => { if (index < items.length - 1) scrollToIndex(index + 1); });
        left && left.addEventListener("click", () => { if (index > 0) scrollToIndex(index - 1); });

        dots.forEach((dot, i) => dot.addEventListener('click', () => scrollToIndex(i)));

        index = Math.max(0, Math.min(Math.round((carousel.scrollLeft - items[0].offsetLeft) / step), items.length - 1));
        updateDots();

        let scrollTimeout;
        carousel.addEventListener('scroll', () => {
            if (scrollTimeout) clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                const newIndex = Math.round((carousel.scrollLeft - items[0].offsetLeft) / step);
                index = Math.max(0, Math.min(newIndex, items.length - 1));
                updateDots();
            }, 80);
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
