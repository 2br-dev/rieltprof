/* Скрипт, необходимый для корректной работы карточки товара */

$(function() {
    /**
     * Нажатие на маленькие иконки фото
     */
    $('body')
        .on('click', '.rs-gallery-thumb .rs-item', function() {
            var n = $(this).data('n');
            $('.rs-gallery-full .rs-item').addClass('hidden');
            $('.rs-gallery-full .rs-item[data-n="'+n+'"]').removeClass('hidden');

            return false;
        });
    if (location.hash=='#comments') {
        setTimeout(function() {
            $('a[href="#tab-comments"]').click();
        }, 0);
    }

    $('.rs-product-amount .rs-inc').off('click').on('click', function() {
        var amountField = $(this).closest('.rs-product-amount').find('.rs-field-amount');
        amountField.val( (+amountField.val()) + ($(this).data('amount-step')-0) );
    });

    $('.rs-product-amount .rs-dec').off('click').on('click', function() {
        var amountField = $(this).closest('.rs-product-amount').find('.rs-field-amount');
        var val = (+amountField.val());
        if (val > $(this).data('amount-step')) {
            amountField.val( val - $(this).data('amount-step') );
        }
    });

    $('body').on('reinit-gallery product.reloaded', function() {
        //Копируем для owlCarousel только те фото, что подходят для комплектации
        if ($.fn.owlCarousel) {
            var galleryThumb = $('.rs-gallery-thumb')
                .empty()
                .append($('.rs-gallery-source .rs-item').filter(":not(.hidden)").clone(true));

            // Галерея (Карточка товара).
            // Пересоздаем её всякий раз когда меняется комплектация.
            galleryThumb.owlCarousel('destroy');
            galleryThumb.owlCarousel({
                center: false,
                mouseDrag: false,
                items: 4,
                margin: 10,
                nav: true,
                responsive: {
                    0: {
                        items: 2
                    },
                    480: {
                        items: 3
                    },
                    768: {
                        items: 3
                    },
                    992: {
                        items: 3
                    },
                    1199: {
                        items: 4
                    },
                    1600: {
                        items: 4
                    }
                }
            });
        }

        /**
         * Открытие главного фото товара
         */
        if ($.fn.lightGallery) {
            var gallery = $('.product-gallery-full').data('lightGallery');
            if (gallery) {
                gallery.destroy(true);
            }
            $('.product-gallery-full').lightGallery({
                selector: "[rel='bigphotos']:not(.dont-use-in-galery)",
                showThumbByDefault: false,
                autoplay: false,
                autoplayControls: false,
                hash:false
            });
        }

    }).trigger('reinit-gallery');


});