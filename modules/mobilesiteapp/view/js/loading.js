/**
 * Скрипт отвечает за загрузку данных о подписке
 * на мобильное приложение
 */
$(function() {

    /**
     * Загружает сведения  с сервера ReadyScript
     */
    var refresh = function(refresh) {
        var data = {};
        if (refresh) {
            data = {refresh:1};
            $('#rs-mobile .cssload-block').show();
            $('#rs-mobile .rs-page-mobile').empty().removeClass('fade in');
        }

        $.ajaxQuery({
            url: $('#msa-loader').data('url'),
            loadingProgress: false,
            data: data,
            success: function(response) {

                $('#rs-mobile .cssload-block').hide();
                $('#rs-mobile .rs-page-mobile')
                    .addClass('fade')
                    .html(response.html)
                    .delay(100).queue(function(){
                    $(this).addClass("in").dequeue();
                });
            }
        });
    }

    refresh(); //Первично загружаем страницу

    /**
     * Обновляет сведения о подписке на мобильное приложение
     */
    $('#rs-mobile').on('click', '.refresh-app-status', function() {
        refresh(true);
    });

});