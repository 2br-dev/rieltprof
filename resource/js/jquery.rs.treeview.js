/**
 * Плагин, активирует просмотр древовидных списков в административной панели ReadyScript
 *
 * @author ReadyScript lab.
 */
(function ($) {

    $.fn.treeview = function (type) {

        return this.each(function () {
            if ($(this).data('treeview')) return false;
            $(this).data('treeview', {});

            var activetree = this;
            var $treeview = $('.treebody', this);
            var maxLevels = $treeview.data('maxLevels') ? $treeview.data('maxLevels') : 0;

            if ($treeview.is('.treesort')) {
                //Инициализируем сортировку внутри дерева
                $treeview.nestedSortable({
                    maxLevels: maxLevels,
                    branchClass: 'tree-branch',
                    collapsedClass: 'tree-collapsed',
                    expandedClass: 'tree-expanded',
                    leafClass: 'tree-leaf',
                    tabSize: 30,
                    disableParentChange: $treeview.data('noExpandCollapse'),
                    isTree: !$treeview.data('noExpandCollapse'),
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

                        if (ui.item.parent().is('.treesort')) {
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
                            url: $treeview.data('sortUrl'),
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

            var load_nodes = [];
            $('.tree-branch.tree-expanded.need-initialize').each(function () {
                load_nodes.push($(this).data('id'));
            });
            if (load_nodes.length) {
                ajaxLoadNodes(load_nodes, 0, 1);
            }


            var toggle = function () {
                var node = $(this).closest('li');
                if (node.hasClass('need-initialize')) {
                    ajaxLoadNodes([node.data('id')]);
                }
                $(this).closest('li').toggleClass('tree-collapsed').toggleClass('tree-expanded');

                updatecookie.call(activetree);
                return false;
            };

            var openAll = function () {
                load_nodes = [];
                $('.tree-branch.tree-collapsed', activetree).each(function () {
                    $(this).removeClass('tree-collapsed').addClass('tree-expanded');
                    if ($(this).hasClass('need-initialize')) {
                        load_nodes.push($(this).data('id'));
                    }
                });
                ajaxLoadNodes(load_nodes, 0, 1, 1);
            };

            var closeAll = function () {
                $('.tree-branch', $treeview)
                    .addClass('tree-collapsed')
                    .removeClass('tree-expanded');
                updatecookie.call(activetree);
            };

            var updatecookie = function () {
                if (!$(this).data('uniq')) return false;
                $(this).trigger('changeSize');

                var ids = [];
                $('li.tree-expanded', $treeview).each(function () {
                    ids.push($(this).data('id'));
                });

                if (ids.length > 0) {
                    $.cookie($(this).data('uniq'), ids.join(','), {expires: 365})
                } else {
                    $.cookie($(this).data('uniq'), null);
                }
            };

            function ajaxLoadNodes(ids, recursive = 0, render_opened = 0, force_open = 0) {
                $.each(ids, function (index, value) {
                    $('.tree-branch[data-id="' + value + '"]').addClass('loading').removeClass('need-initialize');
                });

                var data = {
                    ids: ids,
                    recursive: recursive,
                    render_opened: render_opened,
                    force_open: force_open
                };

                $.ajaxQuery({
                    url: $treeview.data('treeListUrl'),
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            $.each(response.child_html, function (index, value) {
                                $('.tree-branch[data-id="' + index + '"] > .childroot').html(value);
                                $('.tree-branch[data-id="' + index + '"]').removeClass('loading');
                            });
                            updatecookie.call(activetree);
                        }
                    }
                });
            }

            $(this)
                .on('click', '.toggle', toggle)
                .on('click', '.allplus', openAll)
                .on('click', '.allminus', closeAll);

            /**
             * Устанавливаем красные маркеры
             */
            $($treeview).on('change', '.chk input[type="checkbox"]', function () {
                if (this.checked) {
                    $(this).closest('li').find('>.item > .line > .redmarker').addClass('r_on');
                    $(this).parents('.treebody li').each(function () {
                        $('>.item > .line > .redmarker', this).addClass('r_on');
                    })

                } else {
                    if (!$(this).closest('li').find('.r_on').length) {
                        $(this).closest('li').find('> .item > .line > .redmarker').removeClass('r_on');
                    }

                    $(this).parents('.treebody li').each(function () {
                        if (!$('ul .r_on', this).length && !$('.chk input:checked', this).length) {
                            $('>.item > .line > .redmarker', this).removeClass('r_on');
                        }
                    });
                }
            });

            //Инициализируем все checkbox'ы которые уже активны
            $('.chk input[type="checkbox"]:checked', $treeview).trigger('change');

        }); //each

    };

    $.contentReady(function () {
        $('.activetree').treeview();
    });

})(jQuery);