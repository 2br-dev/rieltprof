/*
* Скрипт обеспечивает работу полей редактирования и мультиредактирования объектов.
* Здесь должны располагаться инициализации сложных форм(выбор даты и времени, и т.д.)
*
* @author ReadyScript lab.
*/
(function($) {

    $.contentReady(function()
    {
        //Для группового редактирования
        checkOn = function() {
            var tr = $(this).parents('tr:first');
            if (this.checked) tr.addClass('editthis'); else tr.removeClass('editthis');
        };

        $('.multiedit .doedit', this).each(checkOn);
        $('.multiedit .doedit', this).click(checkOn);

        //Инициализируем поля "дата-время"
        $('input[datetime]', this).each(function() {
            $(this).attr('autocomplete','off').datetime();
        });

        //Инициализируем поля "дата"
        $('input[date]', this).each(function() {
            $(this).attr('autocomplete','off').dateselector();
        });
    });

})(jQuery);