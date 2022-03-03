/* Скрипт инициализирует линейку с брендами */

$(function() {
    /* Brand carusel */
    $(".brand-carousel").owlCarousel({
        autoplay: true,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,
        mouseDrag:false,
        loop: true,
        items: 8,
        responsive: {
            0: {
                items: 2
            },
            360: {
                items: 3
            },
            600: {
                items: 4
            },
            768: {
                items: 5
            },
            992: {
                items: 6
            },
            1199: {
                items: 7
            }
        }
    });
});