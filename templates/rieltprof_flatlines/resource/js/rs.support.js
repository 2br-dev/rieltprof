/*
* Скрипт подключается на странице персональных сообщений пользователя.
*/

$(function() {
    $('#topic_id').change(function() {
        $('#newtopic').toggle( $(this).val() == 0 );
    });

    $('.t-response_wrapper .t-response_delete').click(function(){
        if (confirm(lang.t('Вы действительно хотите удалить переписку по теме?'))) {
            var block = $(this).closest('[data-id]').css('opacity', 0.5);
            var topic_id = block.data('id');

            $.getJSON($(this).attr('href'), function (response) {
                if (response.success) {
                    location.reload();
                }
            });
        }
        return false;
    });
});