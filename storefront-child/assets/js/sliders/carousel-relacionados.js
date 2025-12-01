(function() {
    /**
     * CarouselRelacionados - Controla carruseles de entradas/productos relacionados
     * Soporta navegación por botones y swipe
     */
    class CarouselRelacionados {
        constructor(sliderId) {
            this.slider = document.getElementById(sliderId);
            if (!this.slider) return;

            this.slides = this.slider.querySelectorAll('[class*="-rel-slide"]');
            if (this.slides.length === 0) return;

            this.startX = 0;
            this.endX = 0;
            this.currentSliderId = sliderId;

            this.init();
        }

        init() {
            this.attachEventListeners();
            this.ensureActiveSlide();
        }

        attachEventListeners() {
            // Touch eventos
            this.slider.addEventListener('touchstart', (e) => {
                this.startX = e.touches[0].clientX;
            }, { passive: true });

            this.slider.addEventListener('touchend', (e) => {
                this.endX = e.changedTouches[0].clientX;
                this.handleSwipe();
            });

            // Mouse eventos
            this.slider.addEventListener('mousedown', (e) => {
                this.startX = e.clientX;
                document.addEventListener('mouseup', this.onMouseUp.bind(this));
            });
        }

        onMouseUp(e) {
            this.endX = e.clientX;
            this.handleSwipe();
            document.removeEventListener('mouseup', this.onMouseUp.bind(this));
        }

        changeSlide(direction) {
            const activeSlide = this.slider.querySelector('[class*="-rel-slide"].active');
            if (!activeSlide) return;

            let currentIndex = parseInt(activeSlide.dataset.index, 10);
            currentIndex += direction;

            if (currentIndex < 0) currentIndex = this.slides.length - 1;
            if (currentIndex >= this.slides.length) currentIndex = 0;

            this.slides.forEach(slide => slide.classList.remove('active'));
            this.slides[currentIndex].classList.add('active');
        }

        handleSwipe() {
            const diff = this.startX - this.endX;
            const minSwipeDistance = 50;

            if (Math.abs(diff) > minSwipeDistance) {
                if (diff > 0) this.changeSlide(1);
                else this.changeSlide(-1);
            }
        }

        ensureActiveSlide() {
            const hasActive = this.slider.querySelector('[class*="-rel-slide"].active');
            if (!hasActive && this.slides.length > 0) {
                this.slides[0].classList.add('active');
            }
        }
    }

    // Función pública para cambiar slides (llamada desde botones inline)
    window.changeSlideRelacionados = function(direction, sliderId) {
        const slider = document.getElementById(sliderId);
        if (!slider) return;

        const slides = slider.querySelectorAll('[class*="-rel-slide"]');
        const activeSlide = slider.querySelector('[class*="-rel-slide"].active');
        let currentIndex = activeSlide ? parseInt(activeSlide.dataset.index, 10) : 0;

        currentIndex += direction;
        if (currentIndex < 0) currentIndex = slides.length - 1;
        if (currentIndex >= slides.length) currentIndex = 0;

        slides.forEach(slide => slide.classList.remove('active'));
        slides[currentIndex].classList.add('active');
    };

    // Inicializar todos los carruseles cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllCarousels);
    } else {
        initAllCarousels();
    }

    function initAllCarousels() {
        document.querySelectorAll('[id*="-rel-slider"]').forEach(carousel => {
            new CarouselRelacionados(carousel.id);
        });
    }

    window.CarouselRelacionados = CarouselRelacionados;
})();
