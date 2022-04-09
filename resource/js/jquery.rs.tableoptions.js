/**
 * Скрипт инициалищиирует работу диалога настройки таблицы
 */
$.contentReady(function() {
    var $context = $('#tableOptions');
    $context.tableOptions();

    var cookie_options = {
        expires: new Date((new Date()).valueOf() + 1000*3600*24*365*5)
    };
    var columns_key = $context.data('tableId')+'[columns]';
    var sort_key = $context.data('tableId')+'[sort]';
    var order_key = $context.data('tableId')+'[order]';

    //Сохраняем настройки
    $('.saveToCookie').click(function() {
        //Подготавливаем параметры
        var columns_value = new Array();
        $('.column', $context).each(function() {
            columns_value.push($(this).val()+'='+(this.checked ? 'Y' : 'N'));
        });
        $.cookie(columns_key, columns_value.join(','), cookie_options);

        $('.asc, .desc', $context).each(function() {
            var sort_direction = $(this).hasClass('asc') ? 'asc' : 'desc';
            var sort_field = $(this).closest('[data-field]').data('field');
            $.cookie(sort_key, sort_field+'='+sort_direction, cookie_options);
        });

        var order_value = [];
        var order_changed = false;
        $('tr', $context).each(function(index, item) {
            var field = $(item).data('field');
            order_value.push(field);

            if ((index+1) != field) {
                order_changed = true;
            }
        });

        if (order_changed) {
            $.cookie(order_key, order_value.join(','), cookie_options);
        } else {
            $.cookie(order_key, null);
        }

        var dialog = $context.closest('.dialog-window');
        var crudOptions = dialog.dialog('option', 'crudOptions');
        dialog.dialog('close');

        //Обновим зону таблицы
        $.rs.updatable.updateTarget(crudOptions.openerElement);
    });

    //Сбрасываем настройки
    $('.reset').click(function() {
        $.cookie(columns_key, null);
        $.cookie(sort_key, null);
        $.cookie(order_key, null);

        var dialog = $context.closest('.dialog-window');
        var crudOptions = dialog.dialog('option', 'crudOptions');
        dialog.dialog('close');

        $.rs.updatable.updateTarget(crudOptions.openerElement);
    });

    $('table#tableOptions').tableDnD({
        dragHandle: ".drag-handle",
        onDragClass: "in-drag"
    });
});