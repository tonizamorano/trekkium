(function() {
    /**
     * ProductSlider - Controla sliders de productos individuales
     * Soporta navegación por botones, swipe/drag y teclado
     */
    class ProductSlider {
        constructor(sliderId) {
            this.slider = document.getElementById(sliderId);
            if (!this.slider) return;

            this.container = this.slider.closest('.ps-slider-wrapper');
            this.slides = Array.from(this.slider.querySelectorAll('.ps-slide'));
            if (this.slides.length === 0) return;

            // BUSCAR LOS PUNTOS CORRECTAMENTE - solo dentro del contenedor de paginación
            const paginationContainer = this.container.querySelector('.ps-slider-pagination');
            this.dots = paginationContainer ? Array.from(paginationContainer.querySelectorAll('.ps-dot')) : [];

            this.currentIndex = 0;
            this.startX = 0;
            this.isPointerDown = false;

            this.init();
        }

        init() {
            this.attachEventListeners();
            this.ensureActiveSlide();
            this.updatePagination();
            this.exposePublicMethods();
        }

        attachEventListeners() {
            // Botones de navegación
            if (this.container) {
                const prevBtn = this.container.querySelector('.ps-slider-btn.prev');
                const nextBtn = this.container.querySelector('.ps-slider-btn.next');

                if (prevBtn) prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.changeSlide(-1);
                });
                if (nextBtn) nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.changeSlide(1);
                });
            }

            // Puntos de paginación - solo si existen
            this.dots.forEach(dot => {
                dot.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const index = parseInt(dot.getAttribute('data-index'), 10);
                    if (!isNaN(index)) {
                        this.showSlide(index);
                    }
                });
            });

            // Touch/swipe
            this.slider.addEventListener('touchstart', (e) => {
                if (e.touches && e.touches.length) {
                    this.startX = e.touches[0].clientX;
                }
            }, { passive: true });

            this.slider.addEventListener('touchend', (e) => {
                const endX = (e.changedTouches && e.changedTouches[0]) ? e.changedTouches[0].clientX : 0;
                this.handleSwipe(this.startX, endX);
            });

            // Mouse drag
            this.slider.addEventListener('mousedown', (e) => {
                this.isPointerDown = true;
                this.startX = e.clientX;
                document.body.classList.add('ps-dragging');
            });

            document.addEventListener('mouseup', (e) => {
                if (!this.isPointerDown) return;
                this.isPointerDown = false;
                document.body.classList.remove('ps-dragging');
                this.handleSwipe(this.startX, e.clientX);
            });

            document.addEventListener('mouseleave', () => {
                if (!this.isPointerDown) return;
                this.isPointerDown = false;
                document.body.classList.remove('ps-dragging');
            });

            // Teclado
            this.slider.setAttribute('tabindex', '0');
            this.slider.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.changeSlide(-1);
                }
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.changeSlide(1);
                }
            });
        }

        getActiveIndex() {
            const active = this.slider.querySelector('.ps-slide.active');
            if (!active) return 0;
            return parseInt(active.getAttribute('data-index'), 10) || 0;
        }

        showSlide(index) {
            if (!this.slides[index]) return;
            this.slides.forEach(s => s.classList.remove('active'));
            this.slides[index].classList.add('active');
            this.currentIndex = index;
            this.updatePagination();
        }

        changeSlide(direction) {
            let next = this.currentIndex + direction;
            if (next < 0) next = this.slides.length - 1;
            if (next >= this.slides.length) next = 0;
            this.showSlide(next);
        }

        updatePagination() {
            // Actualizar solo si hay puntos de paginación
            if (this.dots.length > 0) {
                this.dots.forEach(dot => {
                    const index = parseInt(dot.getAttribute('data-index'), 10);
                    if (index === this.currentIndex) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            }
        }

        handleSwipe(sx, ex) {
            const diff = sx - ex;
            const minSwipe = 40;
            if (Math.abs(diff) > minSwipe) {
                if (diff > 0) {
                    this.changeSlide(1); // Deslizar a la izquierda -> siguiente
                } else {
                    this.changeSlide(-1); // Deslizar a la derecha -> anterior
                }
            }
        }

        ensureActiveSlide() {
            if (!this.slider.querySelector('.ps-slide.active')) {
                this.showSlide(0);
            } else {
                this.currentIndex = this.getActiveIndex();
            }
        }

        exposePublicMethods() {
            this.slider.psChangeSlide = this.changeSlide.bind(this);
            this.slider.psShowSlide = this.showSlide.bind(this);
        }
    }

    // Inicializar todos los sliders de productos cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllSliders);
    } else {
        initAllSliders();
    }

    function initAllSliders() {
        document.querySelectorAll('.ps-slider').forEach(slider => {
            // Solo inicializar sliders que tengan slides
            if (slider.querySelectorAll('.ps-slide').length > 0) {
                new ProductSlider(slider.id);
            }
        });
    }

    // Exponer globalmente para acceso desde inline scripts si es necesario
    window.ProductSlider = ProductSlider;
})();