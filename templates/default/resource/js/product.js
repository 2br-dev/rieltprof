/**
* Привязываем события в карточке товара
* 
*/

function initReady() {
    /**
     * Открытие главного фото товара в colorbox до подгрузки всей страницы и яваскрипта страницы
     */
    $('.no-touch .product .viewbox[rel="bigphotos"]').colorbox({
        rel:'bigphotos',
        className: 'titleMargin',
        opacity:0.2
    });
}


function initProductEvents()
{
   /**
   * Прокрутка нижних фото у товара в карточке
   */ 
   var carousel = $('.productGalleryWrap .gallery').jcarousel().swipeCarousel(),
       recommended = $('.recommended .gallery').jcarousel().swipeCarousel();
     
   $('.control').on({
        'inactive.jcarouselcontrol': function() {
            $(this).addClass('disabled');
        },
        'active.jcarouselcontrol': function() {
            $(this).removeClass('disabled');
        }
   });
   $('.control.prev').jcarouselControl({
        target: '-=3'
   });
   $('.control.next').jcarouselControl({
        target: '+=3'
   });

   /**
   * Нажатие на маленькие иконки фото
   */
   $('.gallery .preview').click(function() {
       var n = $(this).data('n');
       $('.product .mainPicture').addClass('hidden');
       $('.product .mainPicture[data-n="'+n+'"]').removeClass('hidden');
       return false;
   });
    
   //Переключение показа написания комментария
   $('.gotoComment').click(function() {
       $('.writeComment .title').switcher('switchOn');
   });
}

/**
* Скрипт активирует необходимые функции на странице просмотра товаров
*/
$(window).load(function() {
    initProductEvents();
});

$(document).ready(function() {
      initReady();
});
/**
* Вешаемся на события обновления контента карточки товара
* 
*/
$(window).on('product.reloaded', function(){
    initProductEvents();
    initReady();
});