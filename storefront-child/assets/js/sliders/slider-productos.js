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

            this.currentIndex = 0;
            this.startX = 0;
            this.isPointerDown = false;

            this.init();
        }

        init() {
            this.attachEventListeners();
            this.ensureActiveSlide();
            this.exposePublicMethods();
        }

        attachEventListeners() {
            // Botones de navegación
            if (this.container) {
                const prevBtn = this.container.querySelector('.ps-slider-btn.prev');
                const nextBtn = this.container.querySelector('.ps-slider-btn.next');

                if (prevBtn) prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.changeSlide(-1);
                });
                if (nextBtn) nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.changeSlide(1);
                });
            }

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
                if (e.key === 'ArrowLeft') this.changeSlide(-1);
                if (e.key === 'ArrowRight') this.changeSlide(1);
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
        }

        changeSlide(direction) {
            let next = this.getActiveIndex() + direction;
            if (next < 0) next = this.slides.length - 1;
            if (next >= this.slides.length) next = 0;
            this.showSlide(next);
        }

        handleSwipe(sx, ex) {
            const diff = sx - ex;
            const minSwipe = 40;
            if (Math.abs(diff) > minSwipe) {
                if (diff > 0) this.changeSlide(1);
                else this.changeSlide(-1);
            }
        }

        ensureActiveSlide() {
            if (!this.slider.querySelector('.ps-slide.active')) {
                this.showSlide(0);
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
            new ProductSlider(slider.id);
        });
    }

    // Exponer globalmente para acceso desde inline scripts si es necesario
    window.ProductSlider = ProductSlider;
})();
