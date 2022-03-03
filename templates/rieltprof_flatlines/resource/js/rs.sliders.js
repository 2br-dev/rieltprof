$(function() {
    $('.rs-js-slider').owlCarousel({
        dots:true, //Переключение сладйо внизу
        nav:true, //Навигация влево вправо
        loop:true, //Зацикливать крутилку
        autoplay:true, //Автопереключение
        autoplayTimeout:5000, //Через сколько переключать
        smartSpeed: 1000, //Пролистываение слайда
        items : 1 //По сколько слайдов листать
    });
});