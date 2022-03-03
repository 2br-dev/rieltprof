/* Скрипт инициализирует промо-страницу для сервиса ReadyScript Mobile */
$(function() {
    $('.mobile-site-app .screenshots .images').owlCarousel({
        center:true,
        items:1,
        loop:true,
        dotsContainer: '.mobile-site-app .screenshots .pages',
        autoplay:true,
        autoplayHoverPause:true
    });
});