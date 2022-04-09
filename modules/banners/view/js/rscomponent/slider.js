/**
 * Инициализирует работу баннерных слайдеров
 * Зависит от swiper.js
 */
new class Slider extends RsJsCore.classes.component {
    onDocumentReady() {
        let sliderElement = document.querySelector('.swiper-banner');
        if (sliderElement) {
            const delay = +(sliderElement.dataset && sliderElement.dataset.autoplayDelay);
            const swiper = new Swiper('.swiper-banner', {
                slidesPerView: 1,
                spaceBetween: 16,
                autoplay: (delay > 0 ? { delay: delay * 1000 } : false),
                pagination: {
                    el: '.swiper-pagination',
                    dynamicBullets: true,
                    clickable: true,
                },
                preloadImages: true,
                lazy: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        }
    }
};