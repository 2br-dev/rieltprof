/* Скрипт загружает лучшие предложения */

(function($) {
    $(function() {

        var body = $('body');
        if (body.data('bestSellerInitialized')) return; //Запрещаем повторную инициализацию
        body.data('bestSellerInitialized', true);

        /**
         * Открывает диалоговое окно с лочшими предложениями
         */
        var openBestSellersDialog = function(url) {
            $.rs.openDialog({
                url: url,
                dialogOptions: {
                    title: lang.t('Рекомендуем'),
                    width: 800,
                    height:300
                },
                afterOpen: function(dialog) {

                    var dialogWrapper = dialog.closest('.ui-dialog');
                    dialogWrapper.find('.contentbox').css('overflow', 'hidden');

                    var owl = $('#bestsellers-dialog .best-sellers', dialog);
                    owl.owlCarousel({items:1});

                    var height = dialogWrapper.find('.middlebox').outerHeight(true)
                        + dialogWrapper.find('.ui-dialog-titlebar').outerHeight(true)
                        + 5;

                    var maxHeight = 0.95 * $(window).height();
                    if (height > maxHeight) height = maxHeight;

                    dialog.dialog("option", "height", height);
                    dialog.dialog("option", "position", {my: "center", at: "center", of: window});
                    dialogWrapper.find('.contentbox').css('overflow', 'auto');
                }
            });
        };


        /**
         * Инициализирует карусель
         */
        var initCarousel = function(bestsellers, is_first_time) {
            var carousel = $('.best-sellers', bestsellers);

            carousel.owlCarousel({
                items:1,
                autoplay:true,
                autoplayTimeout:20000,
                autoplayHoverPause:true
            });

            $('body')
                .off('resize.bestsellers')
                .on('resize.bestsellers', function(e, params) {
                    if (params !== null && typeof(params) === 'object' && params.source) {
                        if ($(params.source).hasClass('side-collapse')) {
                            carousel.trigger('refresh.owl.carousel');
                        }
                    }
                });

            if (is_first_time && bestsellers.data('needShowDialog')) {
                openBestSellersDialog(bestsellers.data('urlDialog'));
            }
        };

        /**
         * Инициализирует виджет Лучшие предложения
         */
        var init = function(is_first_time) {
            var bestsellers = $('#bestsellers');

            var refresh = function() {
                var loader = bestsellers.find('.loading').removeClass('hidden');
                var container = bestsellers.find('.bestsellers-container').addClass('hidden');
                var requestUrl = bestsellers.data('url');

                if (requestUrl) {
                    $.ajax({
                        url: requestUrl,
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                container.removeClass('hidden').html(response.html);
                                initCarousel(bestsellers, is_first_time);
                            }
                        },
                        complete: function() {
                            loader.addClass('hidden');
                        }
                    });
                }
            };

            if (bestsellers.hasClass('need-refresh')) {
                refresh();
            } else {
                initCarousel(bestsellers, is_first_time);
            }
        };

        init(true); //Инициализируем виджет, если он уже добавлен на рабочий стол

        $(window).on('widgetAfterAdd.bestsellers', function(e, wclass) {
            if (wclass == 'main-widget-bestsellers') {
                init(); //Инициализируем виджет, если он только что добавлен на рабочий стол без перезагрузки страницы
            }
        });


    });
})(jQuery);