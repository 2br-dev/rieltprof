$.contentReady(function() {
    //Включаем всегда фиксированную позицию для нижней панели таблицы
    $('.column.right-column')
        .addClass('sticky-on')
        .trigger('stickyUpdate')
        .find('form')
        .on('stickyUpdate', function (e) {
            e.stopPropagation();
            $(this).closest('.column').addClass('sticky-on');
        });
});