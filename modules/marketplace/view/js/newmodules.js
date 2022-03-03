/* Скрипт загружает новые модули, которые есть в Маркетплейсе ReadyScript */
(function($) {
    $(function() {
        var mpModules = $('#mp-modules');

        var refreshRecommendedModules = function() {
            var loader = mpModules.find('.loading').removeClass('hidden');
            var refresher = $('.mp-modules-refresh').addClass('zmdi-hc-spin');
            var container = mpModules.find('.mp-container').addClass('hidden');
            var requestUrl = mpModules.data('url');

            if (requestUrl) {
                $.ajax({
                    url: requestUrl,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            container.html(response.html).removeClass('hidden');
                        }
                    },
                    complete: function() {
                        loader.addClass('hidden');
                        refresher.removeClass('zmdi-hc-spin');
                    }
                });
            }
        }

        if (mpModules.hasClass('need-refresh')) {
            refreshRecommendedModules();
        }

        $('body').on('click', '.mp-modules-refresh', function() {
            refreshRecommendedModules();
        });
    });
})(jQuery);