/**
 * Инициализирует работу блока со слайдером товаров
 */
new class ProductsSlider extends RsJsCore.classes.component {

    initSwipers() {
        if (document.querySelector('.swiper-products')) {
            const swiper = new Swiper('.swiper-products', {
                slidesPerView: 2,
                spaceBetween: 0,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    1400: {
                        slidesPerView: 5,
                        spaceBetween: 24
                    },
                    1200: {
                        slidesPerView: 4,
                        spaceBetween: 24
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 24
                    },
                }
            });
        }
    }

    onDocumentReady() {
        this.initSwipers();
    }

};