/**
 * Плагин инициализирует в административной панели работу Kanban доски
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.board = function( method ) {
        var defaults = {
                columns:'.crm-status-columns',
                columnItems: '.crm-column-items',
                columnItem: '.crm-column-item',
                columnClassPrefix: '.crm-column-items.status-id-',
                ajaxPaginator: '.ajaxPaginator'

            },
            args = arguments;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('board');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('board', data);
                    data.opt = $.extend({}, defaults, initoptions);

                    init();
                }
            };

            var init = function() {

                var allColumns = $(data.opt.columnItems);
                var columns = $(data.opt.columns);
                $(data.opt.columnItems).each(function(i) {

                    var columnClasses = [];
                    allColumns.not(this).each(function() {
                        columnClasses.push(data.opt.columnClassPrefix + $(this).data('statusId'));
                    });

                    $(this).sortable({
                        tolerance: 'pointer',
                        connectWith: columnClasses,
                        cursor: 'move',
                        placeholder: "sortable-placeholder",
                        items : data.opt.columnItem,
                        forcePlaceholderSize: true,
                        receive: function(e, ui) {
                            //Переносим пагинатор в конец списка
                            var paginator = ui.item.prev(data.opt.ajaxPaginator);
                            if (paginator.length) {
                                paginator.insertAfter(ui.item);
                            }
                        },
                        stop: function(e, ui) {
                            //Фиксируем новую позицию
                            var direction, toElement;
                            var fromElement = ui.item.data('id');
                            var status_id = ui.item.closest('[data-status-id]').data('statusId');
                            var prev = ui.item.prev(data.opt.columnItem);
                            var next = ui.item.next(data.opt.columnItem);
                            if (prev.length>0) {
                                direction = 'down';
                                toElement = prev.data('id');
                            }
                            else if (next.length>0) {
                                direction = 'up';
                                toElement = next.data('id');
                            }
                            $.ajaxQuery({
                                url:columns.data('sortUrl'),
                                data: {
                                    from: fromElement,
                                    to: toElement,
                                    direction: direction,
                                    status_id: status_id
                                }
                            });
                        }
                    });

                });

                columns.disableSelection();
            };

            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };

})( jQuery );