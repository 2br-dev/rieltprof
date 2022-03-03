/**
 * Скрипт инициализирует колонку просмотра фотографий в таблице
 *
 * @author ReadyScript lab.
 */
(function($) {

    $.contentReady(function() {
        $('.cell-image[data-preview-url]').each(function() {
            $(this).hover(function() {
                var previewUrl = $(this).data('previewUrl');
                if (previewUrl != '') {
                    $('#imagePreviewWin').remove();
                    var win = $('<div id="imagePreviewWin" />')
                        .append('<i />')
                        .append($('<img />').attr('src', previewUrl ))
                        .css({
                            top: $(this).offset().top,
                            left: $(this).offset().left + $(this).width() + 20
                        }).appendTo('body');
                }
            }, function() {
                $('#imagePreviewWin').remove();
            });
        });
    });

})(jQuery);