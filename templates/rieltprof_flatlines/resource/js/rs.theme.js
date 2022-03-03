/**
 * Данный скрипт подключается на всех страницах темы оформления.
 * Здесь представлены инструкции, которые могут быть полезны на всех страницах темы оформления
 */

(function($) {

    /**
     * Позволяет определять тип устройства по разрешению экрана
     *
     * @param string type Идентификатор устройства
     * @return bool
     */
    $.detectMedia = function(type) {
        var map = {
            "mobile": "(max-width:767px)",
            "tablet": "(max-width:992px)"
        };

        return window.matchMedia(map[type]).matches;
    };

    $(function() {

        //Активируем стандартную разметку для переключателей
        var findTarget = function (element) {
            return $(element).data('targetClosest')
                ? $(element).data('targetClosest')
                : $(element).data('target');
        };

        $('body')
                .on('click', 'a[data-href]', function() {
                    if (!$(this).hasClass('rs-in-dialog') && !$(this).hasClass('inDialog')) { // inDialog - для совместимости
                        location.href = $(this).data('href');                             
                    }
                })
                .on('click', '.expand', function() {
                    $(this).next().slideToggle(200);
                    $(this).parent().toggleClass("open");
                })
                .on('click', '[data-toggle-class]', function () {
                    var target = findTarget(this);
                    var state = $(target)
                        .toggleClass($(this).data('toggleClass'))
                        .hasClass($(this).data('toggleClass'));

                    $(target).trigger('resize');

                    var cookieName = $(this).data('toggle-cookie');
                    if (cookieName) {
                        $.cookie(cookieName, state ? 1 : null, {
                            expires: 365 * 5,
                            path: '/'
                        });
                    }
                })
                // Перевод меню табов в режим выпадающих списков (Карточка товара)
                .on('click', '.mobile_nav-tabs', function (event) {
                    if(window.innerWidth < 992) {
                        $(this).toggleClass('open');
                        ($(this).next().hasClass('active in')) ? $(this).next().removeClass('active in') : $(this).next().addClass('active in');
                    }
                });


        // Кнопка close
        function closeDropdown() {
            var drop = $('.t-dropdown'),
                grid = $('.gridblock .hover-wrapper');

            $(drop).on('mouseenter', function (e) { $(this).addClass('open') });
            $(drop).on('mouseleave', function (e) { $(this).removeClass('open') });
            $(drop).find('.t-close').click(function () { $(drop).removeClass('open') });

            $(grid).on('click', '.icon-account', function (e) { $(this).closest(grid).toggleClass('open') });
            $(grid).find('.t-close').click(function () { $(grid).removeClass('open') });
        }
        closeDropdown();

        /* Карусели товаров */
        if ($.fn.owlCarousel) {
            $('.category-carousel').owlCarousel({
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                center: false,
                mouseDrag:false,
                loop: false,
                responsive: {
                    0: {
                        items: 1
                    },
                    480: {
                        items: 2
                    },
                    768: {
                        items: 3
                    },
                    992: {
                        items: 3
                    },
                    1200: {
                        items: 4
                    }
                }
            });

            $('.arrow-left').on('click', function (e) {
                var target = $(e.target).closest('.sec');
                $(target).find('.owl-prev').trigger('click');
            });

            $('.arrow-right').on('click', function (e) {
                var target = $(e.target).closest('.sec');
                $(target).find('.owl-next').trigger('click');
            });
        }


        // Мобильное меню
        if ($.fn.mmenu) {
            $("#mmenu").removeClass('hidden').mmenu({
                navbar: {
                    title: lang.t("Каталог")
                }
            }, {
                offCanvas: {
                    pageSelector: '#notExists'
                }
            });
        }

        //Инициализируем корзину
        $.cart();

        //Инициализируем Купить в 1 клик в корзине
        if ($.oneClickCart) {
            $.oneClickCart();
        }

        //Добавляем анимацию на нажатие на иконки в шапке
        $('.gridblock_wrapper .i-svg').click(function() {
            var animateClass = 'bounce animated';
            var icon = $(this)
                .addClass(animateClass)
                .one('webkitAnimationEnd oanimationend msAnimationEnd animationend',
                    function (e) {
                        $(this).removeClass(animateClass);
                    });
        });

        /**
         * Открытие фото в любых текстах
         */
        $('html').lightGallery({
            selector: "[rel='lightbox']",
            autoplay: false,
            autoplayControls: false,
            showThumbByDefault: false
        });

        /**
         * Инициализируем приклеивание блока корзины к верху браузера
         */
        $('.sticky-block').sticky();

        /**
         * Инициализируем открытие выпадающего списка категорий на touch устройствах
         */
        var hasTouch = 'ontouchstart' in window;
        if (hasTouch) {
            $('ul.nav.navbar-nav > li.t-dropdown > a').on('touchstart click', function(e) {
                if (e.type == 'touchstart') {
                    $('ul.nav.navbar-nav > li.t-dropdown').removeClass('open');
                    $(this).parent().addClass('open');
                }
                e.preventDefault();
            });
        }
    });

    $(function () {
        $('[data-toggle="popover"]').popover();
    });
    
})(jQuery);

$(document).ready(function(){
    $('.phone_mask').mask("+7(999) 999-9999");
    $('#login').on('submit', function(e){
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            success: (res) => {
                console.log('res', res);
                if(!res.error?.length){
                    document.location = '/';
                }else{
                    M.toast({html: '<p>'+res.error+'</p>'})
                }
            },
            error: (err) => {
                console.error(err)
            }
        });
        return false;
    })
});


