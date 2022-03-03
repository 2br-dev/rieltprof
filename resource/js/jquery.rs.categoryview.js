/**
 * Плагин, активирует просмотр древовидных списков в административной панели ReadyScript
 *
 * @author ReadyScript lab.
 */
(function ($) {

    $.fn.categoryview = function (type) {

        return this.each(function () {
            if ($(this).data('categoryview')) return false;
            $(this).data('categoryview', {});

            var activecategory = this;
            var $categoryview = $('.categorybody', this);

            if ($categoryview.is('.categorysort')) {
                //Инициализируем сортировку внутри дерева
                $categoryview.nestedSortable({
                    maxLevels: 0,
                    tabSize: 30,
                    disableParentChange: false,
                    isTree: false,
                    forcePlaceholderSize: true,
                    handle: '.move',
                    placeholder: {
                        element: function (currentItem, ui) {
                            var placeholder = $(currentItem)
                                .clone()
                                .addClass('tree-placeholder')
                                .removeClass('current')
                                .css({
                                    position: 'static',
                                    width: 'auto'
                                });

                            placeholder.find('.chk').empty();
                            placeholder.find('.line').empty();
                            return placeholder[0];

                        },
                        update: function (container, p) {
                            return;
                        }
                    },
                    listType: 'ul',
                    items: 'li[data-id]:not([data-notmove])',
                    disabledClass: 'noDraggable',
                    expandOnHover: false,
                    toleranceElement: '>.item',
                    update: function (event, ui) {

                        var source_id = ui.item.data('id');

                        if (ui.item.parent().is('.categorysort')) {
                            var parent_id = 0;
                        } else {
                            var parent_id = ui.item.parents('li[data-id]').data('id');
                        }

                        if (ui.item.next().length) {
                            var destination_id = ui.item.next().data('id');
                            var destination_direction = 'up';
                        } else {
                            var destination_id = ui.item.prev().data('id');
                            var destination_direction = 'down';
                        }

                        $.ajaxQuery({
                            url: $categoryview.data('sortUrl'),
                            data: {
                                from: source_id,
                                to: destination_id,
                                flag: destination_direction,
                                parent: parent_id
                            }
                        });
                    }
                });
            }

            /**
             * Устанавливаем красные маркеры
             */
            $($categoryview).on('change', '.chk input[type="checkbox"]', function () {
                if (this.checked) {
                    $(this).closest('li').find('>.item > .line > .redmarker').addClass('r_on');
                    $(this).parents('. li').each(function () {
                        $('>.item > .line > .redmarker', this).addClass('r_on');
                    })

                } else {
                    if (!$(this).closest('li').find('.r_on').length) {
                        $(this).closest('li').find('> .item > .line > .redmarker').removeClass('r_on');
                    }

                    $(this).parents('.categorybody li').each(function () {
                        if (!$('ul .r_on', this).length && !$('.chk input:checked', this).length) {
                            $('>.item > .line > .redmarker', this).removeClass('r_on');
                        }
                    });
                }
            });

            //Инициализируем все checkbox'ы которые уже активны
            $('.chk input[type="checkbox"]:checked', $categoryview).trigger('change');

        }); //each

    };

    $.contentReady(function () {
        $('.activecategory').categoryview();
    });

})(jQuery);