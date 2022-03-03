/**
 * Плагин, активирует древовидный выпадающий список
 *
 * @author ReadyScript lab.
 */
(function ($) {
    $.fn.treeSelect = function (data) {
        if ($(this).data('treeSelect')) return false;
        $(this).data('treeSelect', {});

        let defaults = {
            selectedBox: '.tree-select_selected-box',
            selectedValues: '.tree-select_selected-values',
            selectedValueItem: '.tree-select_selected-value-item',
            selectedValueItemTitlePath: '.tree-select_selected-value-item_title-path',
            selectedValueItemRemove: '.tree-select_selected-value-item_remove',
            selectedValueStub: '.tree-select_selected-value-stub',
            searchInput: '.tree-select_search-input',
            list: '.tree-select_list',
            listItem: '.tree-select_list-item',
            listItemRow: '.tree-select_list-item_row',
            listItemTitle: '.tree-select_list-item_title',
            listItemSublistToggle: '.tree-select_list-item_sublist-toggle',
            listItemSublist: '.tree-select_list-item_sublist',
            classTitleContractionSeparator: 'tree-select_title-contraction-separator',
            classOpen: 'open',
            classChecked: 'checked',
            classClosedBranch: 'tree-collapsed',
            classOpenedBranch: 'tree-expanded',
            classNeedInitialize: 'need-initialize',
            classLoading: 'loading',
            classHidden: 'hidden',
            classTreeLeaf: 'tree-leaf',
            eventNameChange: 'treeSelectChange'
        };

        let $this = $(this);
        $this.options = $.extend({}, defaults, data);
        $this.options.multiple = $this.attr('multiple');
        $this.options.disallow_select_branches = $this.attr('disallowSelectBranches');
        $this.lastClicked = false;

        $this
        // нажатие на выбранное значение
            .on('click', $this.options.selectedValueItem, function () {
                if ($this.options.multiple) {
                    openDropBox();
                } else {
                    toggleDropBox();
                }
                goToValue($(this).data('id'));
            })
            // удаление выбранного значения
            .on('click', $this.options.selectedValueItemRemove, function () {
                removeValue($(this).closest($this.options.selectedValueItem).data('id'));
                return false;
            })
            // нажатие на список выбранных значений
            .on('click', $this.options.selectedBox, function (event) {
                if (!$(event.target).closest($this.options.selectedValueItem).length) {
                    toggleDropBox();
                }
            })
            // открытие/закрытие ветви дерева
            .on('click', $this.options.listItemSublistToggle, function () {
                let row = $(this).closest($this.options.listItem);
                row.toggleClass($this.options.classClosedBranch).toggleClass($this.options.classOpenedBranch);
                if (row.hasClass($this.options.classNeedInitialize)) {
                    loadNodes([row.data('id')]);
                }
            })
            // выбор узла дерева
            .on('click', $this.options.listItemRow, function (event) {
                if (!$(event.target).is($this.options.listItemSublistToggle)) {
                    let row = $(this).closest($this.options.listItem);
                    let id = row.data('id');

                    if (!$this.options.disallow_select_branches || row.hasClass($this.options.classTreeLeaf)) {
                        if ($this.options.multiple) {
                            let ids;
                            let in_closed_selector = '.' + $this.options.classClosedBranch + ' ' + $this.options.listItem + '[data-id="' + $this.lastClicked + '"]';
                            if (event.shiftKey && $this.lastClicked !== 0 && $this.lastClicked !== id && !$(in_closed_selector, $this).length) {
                                ids = getIdsSelectedByShift($this.lastClicked, id);
                            } else {
                                ids = [id];
                            }

                            if (row.hasClass($this.options.classChecked)) {
                                removeValue(ids);
                            } else {
                                if (!$($this.options.selectedValueItem, $this).length) {
                                    removeAllValues();
                                }
                                addValue(ids);
                            }

                            $this.lastClicked = id;
                        } else {
                            if (!row.hasClass($this.options.classChecked)) {
                                replaceValue(id);
                            }
                            closeDropBox();
                        }
                    }
                }
            })
            .on('input', $this.options.searchInput, function () {
                loadAllNodes().then(() => {
                    let search_string = $(this).val().toLowerCase();
                    if (search_string) {
                        hideAllListItems();
                        $($this.options.listItem, $this).each(function () {
                            let item_title = $(this).children($this.options.listItemRow).find($this.options.listItemTitle).text().toLowerCase();
                            if (item_title.indexOf(search_string) >= 0) {
                                showListItem($(this).data('id'));
                            }
                        });
                        $($this.options.listItem, $this).removeClass($this.options.classClosedBranch).addClass($this.options.classOpenedBranch);
                    } else {
                        showAllListItems();
                    }
                });
            });
        $('body').on('click', function (event) {
            if (!$(event.target).closest($this).length) {
                closeDropBox();
            }
        });

        //private
        let checkLongValuePath = () => {
            $($this.options.selectedValueItem).each(function () {
                let path = $(this).find($this.options.selectedValueItemTitlePath);

                $('.' + $this.options.classTitleContractionSeparator, this).remove();
                path.children().removeClass($this.options.classHidden);

                let child_length = 0;
                path.children().each(function () {
                    child_length += $(this).width();
                });

                if (path.width() < child_length) {
                    let html = '<span class="' + $this.options.classTitleContractionSeparator + '">. . . ></span>';
                    path.after(html);
                }

                child_length = 0;
                path.children().each(function () {
                    child_length += $(this).width();
                    if (child_length > path.width()) {
                        $(this).addClass($this.options.classHidden);
                    }
                });
            });
        };

        let replaceValue = function (id) {
            removeAllValues();
            addValue(id);
            $this.trigger($this.options.eventNameChange);
        };

        let addValue = function (ids) {
            if (!Array.isArray(ids)) {
                ids = [ids];
            }

            ids.forEach(function(id) {
                let selected_element = $($this.options.selectedValueItem + '[data-id="' + id + '"]', $this);
                let element = $($this.options.listItem + '[data-id="' + id + '"]', $this);
                if (!selected_element.length && element.length) {
                    let path_element = element;
                    let path_name = [];
                    let path_ids = [];
                    do {
                        path_name.push(path_element.children($this.options.listItemRow).find($this.options.listItemTitle).html());
                        path_ids.push(path_element.data('id'));
                        path_element = path_element.parent().closest($this.options.listItem, $this);
                    } while (path_element.length);
                    path_name.reverse();
                    path_ids.reverse();

                    let element_id = element.data('id');
                    let form_name = $this.data('formName');
                    let html = '<li class="tree-select_selected-value-item" data-id="' + element_id + '" data-path-ids=\'' + JSON.stringify(path_ids) + '\'>';
                    html += '<input type="hidden" name="' + form_name + '" value="' + element_id + '">';
                    html += '<span class="tree-select_selected-value-item_title-path">';
                    $.each(path_name.slice(0, -1), function (index, value) {
                        html += '<span class="tree-select_selected-value-item_title-path-part">' + value + '</span>';
                    });
                    html += '</span>';
                    html += '<span class="tree-select_selected-value-item_title-end-part">' + path_name.slice(-1)[0] + '</span>';
                    html += '<i class="tree-select_selected-value-item_remove zmdi zmdi-close"></i>';
                    html += '</li>';

                    $($this.options.selectedValues, $this).append(html);
                    checkListItem(element.data('id'));
                }
            });

            checkLongValuePath();
            $this.trigger($this.options.eventNameChange);
        };

        let addValueStub = () => {
            let html = '<li class="tree-select_selected-value-stub">' + lang.t('- Ничего не выбрано -') + '</li>';
            $($this.options.selectedValues, $this).append(html);
        };

        let removeValue = function (ids) {
            if (!Array.isArray(ids)) {
                ids = [ids];
            }

            ids.forEach(function(id) {
                $($this.options.selectedValueItem + '[data-id="' + id + '"]', $this).remove();
                if (!$($this.options.selectedValueItem, $this).length) {
                    addValueStub();
                }
                uncheckListItem(id);
            });

            $this.trigger($this.options.eventNameChange);
        };

        let removeAllValues = function () {
            $($this.options.selectedValues, $this).html('');
            uncheckAllListItems();
        };

        let getIdsSelectedByShift = function (id_from, id_to) {
            let result = [];
            let temp;
            let element_from = $($this.options.listItem + '[data-id="' + id_from + '"]', $this);
            let element_to = $($this.options.listItem + '[data-id="' + id_to + '"]', $this);

            if (element_from.offset().top > element_to.offset().top) {
                temp = element_to;
                element_to = element_from;
                element_from = temp;
            }

            temp = element_from;
            let skip_element = false;
            let break_id = element_to.data('id');
            while (true) {
                let skipped = skip_element;
                skip_element = false;
                if (!skipped) {
                    result.push(temp.data('id'));
                }

                if (temp.data('id') == break_id) {
                    break;
                }

                if (temp.is('.' + $this.options.classOpenedBranch) && !skipped) {
                    temp = temp.find($this.options.listItem).first();
                } else if (temp.next().length > 0) {
                    temp = temp.next();
                } else if (temp.parent().closest($this.options.listItem).length) {
                    temp = temp.parent().closest($this.options.listItem);
                    skip_element = true;
                } else {
                    break;
                }
            }

            return result;
        };

        let goToValue = function (id) {
            let element = $($this.options.selectedValueItem + '[data-id="' + id + '"]', $this);
            let path_ids = element.data('pathIds');
            let path_branches = path_ids.slice(0, -1);
            loadNodes(path_branches, true).then(() => {
                let list_element = $($this.options.listItem + '[data-id="' + id + '"]', $this);
                let list = list_element.closest($this.options.list, $this);
                let list_top = list.children().first().offset().top;
                list.scrollTop(list_element.offset().top - list_top);
            });
        };

        let loadAllNodes = () => {
            let ids = [];
            $($this.options.listItem + '.' + $this.options.classNeedInitialize, $this).each(function () {
                ids.push($(this).data('id'));
            });
            return loadNodes(ids, false, true);
        };

        let loadNodes = function (ids, open_loaded_branches = false, load_recursive) {
            return new Promise((resolve, reject) => {
                let load_ids = [];
                $.each(ids, function (index, value) {
                    let branch = $($this.options.listItem + '[data-id="' + value + '"]', $this);
                    if (!branch.length || branch.hasClass($this.options.classNeedInitialize)) {
                        branch.addClass($this.options.classLoading).removeClass($this.options.classNeedInitialize);
                        load_ids.push(value);
                    } else if (open_loaded_branches) {
                        branch.removeClass($this.options.classClosedBranch).addClass($this.options.classOpenedBranch);
                    }
                });

                if (load_ids.length) {
                    let data = {
                        ids: load_ids,
                        recursive: load_recursive
                    };

                    $.ajaxQuery({
                        url: $this.data('treeListUrl'),
                        data: data,
                        success: function (response) {
                            if (response.success) {
                                $.each(response.branches, function (index, value) {
                                    let element = $($this.options.listItem + '[data-id="' + index + '"]', $this);
                                    element.find($this.options.listItemSublist).html(value);
                                    element.removeClass($this.options.classLoading).removeClass($this.options.classNeedInitialize);
                                    if (open_loaded_branches) {
                                        element.removeClass($this.options.classClosedBranch).addClass($this.options.classOpenedBranch);
                                    }
                                    highlightSelectedItems();
                                });
                                resolve();
                            } else {
                                reject(response);
                            }
                        }
                    });
                } else {
                    resolve();
                }
            });
        };

        let showListItem = (id) => {
            let element = getListItemById(id);
            do {
                element.removeClass($this.options.classHidden);
                element = element.parent().closest($this.options.listItem, $this);
            } while (element.length);
        };

        let showAllListItems = () => {
            $($this.options.listItem, $this).removeClass($this.options.classHidden);
        };

        let hideAllListItems = () => {
            $($this.options.listItem, $this).addClass($this.options.classHidden);
        };

        let highlightSelectedItems = () => {
            $($this.options.selectedValueItem, $this).each(function () {
                checkListItem($(this).data('id'));
            });
        };

        let uncheckAllListItems = () => {
            $($this.options.listItem, $this).removeClass($this.options.classChecked);
        };

        let checkListItem = (id) => {
            getListItemById(id).addClass($this.options.classChecked);
        };

        let uncheckListItem = (id) => {
            getListItemById(id).removeClass($this.options.classChecked);
        };

        let toggleDropBox = function () {
            $this.toggleClass($this.options.classOpen);
        };

        let openDropBox = function () {
            $this.addClass($this.options.classOpen);
        };

        let closeDropBox = function () {
            $this.removeClass($this.options.classOpen);
        };

        let getListItemById = (id) => {
            return $($this.options.listItem + '[data-id="' + id + '"]', $this);
        };

        checkLongValuePath();
        highlightSelectedItems();
    };

    $.contentReady(function () {
        $('body').on('new-content', () => {
            $('.tree-select').each(function () {
                $(this).treeSelect($(this).data('treeSelectOptions'));
            });
        });

        $('.tree-select').each(function () {
            $(this).treeSelect($(this).data('treeSelectOptions'));
        });
    });
})(jQuery);