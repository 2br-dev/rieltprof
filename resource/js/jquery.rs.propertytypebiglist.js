/**
 * Плагин, активирует древовидный выпадающий список
 *
 * @author ReadyScript lab.
 */
(function ($) {
    $.fn.propertyTypeBigList = function (method) {
        let $this = $(this).data('propertyTypeBigList');
        let search_timeout;

        let methods = {
            addNewValue: (id, value) => {
                addSelectedValue(id, value);
                searchValues();
            },
            init: (data) => {
                if ($this) return false;
                $this = $(this);
                $(this).data('propertyTypeBigList', $this);

                let defaults = {
                    selected: '.property-type-big-list_selected',
                    selectedItem: '.property-type-big-list_selected-item',
                    selectedItemStub: '.property-type-big-list_selected-item-stub',
                    selectedItemRemove: '.property-type-big-list_selected-item-remove',
                    dropBox: '.property-type-big-list_drop-box',
                    searchInput: '.property-type-big-list_search-input',
                    listBox: '.property-type-big-list_list-box',
                    listItem: '.property-type-big-list_list-item',
                    listItemCheckbox: '.property-type-big-list_list-item-checkbox',
                    listItemValue: '.property-type-big-list_list-item-value',
                    listPaginatorPage: '.property-type-big-list_list-paginator-page',
                    listPaginatorPrev: '.property-type-big-list_list-paginator-prev',
                    listPaginatorNext: '.property-type-big-list_list-paginator-next',
                    dropBoxToggle: '.property-type-big-list_drop-box-toggle',
                    classClosed: 'closed',
                };

                $this.options = $.extend({}, defaults, data);
                $this.options.disabled = $this.data('disabled');

                $this
                // удаление выбранного значения
                    .on('click', $this.options.selectedItemRemove, function () {
                        let id = $(this).closest($this.options.selectedItem).data('id');
                        removeSelectedValue(id);
                    })
                    // нажатие на списковое значение
                    .on('change', $this.options.listItemCheckbox, function () {
                        let id = $(this).data('id');
                        let checked = $(this).prop('checked');

                        if (!$($this.options.selectedItem + '[data-id="' + id + '"]', $this).length && checked) {
                            let value = $(this).closest($this.options.listItem).find($this.options.listItemValue).html();
                            addSelectedValue(id, value);
                        } else {
                            removeSelectedValue(id);
                        }
                    })
                    // строка поиска
                    .on('input', $this.options.searchInput, function () {
                        clearTimeout(search_timeout);
                        search_timeout = setTimeout(() => {
                            $($this.options.listPaginatorPage, $this).val(1);
                            searchValues();
                        }, 500);
                    })
                    // номер страницы
                    .on('change', $this.options.listPaginatorPage, function () {
                        searchValues();
                    })
                    // на страницу назад
                    .on('click', $this.options.listPaginatorPrev, function () {
                        let page = Number($($this.options.listPaginatorPage, $this).val());
                        if (page > 1) {
                            $($this.options.listPaginatorPage, $this).val(page - 1).change();
                        }
                    })
                    // на страницу вперёд
                    .on('click', $this.options.listPaginatorNext, function () {
                        let page = Number($($this.options.listPaginatorPage, $this).val());
                        if (page < $($this.options.listPaginatorPage, $this).data('max')) {
                            $($this.options.listPaginatorPage, $this).val(page + 1).change();
                        }
                    })
                    // открытие/закрытие блока значений
                    .on('click', $this.options.dropBoxToggle, function () {
                        $($this.options.dropBox, $this).toggleClass($this.options.classClosed);
                    });

                checkListValues();
            }
        };



        //private
        let searchValues = () => {
            let query = $($this.options.searchInput, $this).val();
            let page = $($this.options.listPaginatorPage, $this).val();
            let data = {
                query: query,
                page: page,
                disabled: $this.options.disabled
            };

            $.ajaxQuery({
                url: $this.data('searchUrl'),
                data: data,
                success: function (response) {
                    if (response.success) {
                        $($this.options.listBox, $this).html(response.html);
                        checkListValues();
                    }
                }
            });
        };

        let checkListValues = () => {
            $($this.options.selectedItem, $this).each(function () {
                $($this.options.listItemCheckbox + '[data-id="' + $(this).data('id') + '"]', $this).prop('checked', true);
            });
        };

        let removeSelectedValue = (id) => {
            $($this.options.selectedItem + '[data-id="' + id + '"]', $this).remove();
            $($this.options.listItemCheckbox + '[data-id="' + id + '"]', $this).prop('checked', false);

            if (!$($this.options.selectedItem, $this).length) {
                let html = '<span class="property-type-big-list_selected-item-stub">- ' + lang.t('Значения не указаны') + ' -</span>';
                $($this.options.selected, $this).append(html);
            }
        };

        let addSelectedValue = (id, value) => {
            let prop_id = $this.data('propId');
            let disabled = '';
            if ($this.data('disabled')) {
                disabled = 'disabled';
            }

            $($this.options.selectedItemStub, $this).remove();

            let html = '<span class="property-type-big-list_selected-item" data-id="' + id + '"><label>';
            html += '<input type="hidden" name="prop[' + prop_id + '][value][]" class="property-type-big-list_selected-item-checkbox" data-id="' + id + '" ' + disabled + ' value="' + id + '" checked>';
            html += '<span >' + value + '</span>';
            html += '<i class="property-type-big-list_selected-item-remove zmdi zmdi-close"></i>';
            html += '</label></span>';

            $($this.options.selected, $this).append(html);
        };

        //checkListValues();
        if (!method) {
            method = 'init';
        }
        if (methods[method]) {
            methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else {
            console.log('propertyTypeBigList: ' + lang.t('вызов несуществующего метода') + ' "' + method + '"');
        }
    };

    $.contentReady(function () {
        $('.property-type-big-list').each(function () {
            $(this).propertyTypeBigList('init', $(this).data('propertyTypeBigListOptions'));
        });
        $('body').on('new-content',() => {
            $('.property-type-big-list').each(function () {
                $(this).propertyTypeBigList('init', $(this).data('propertyTypeBigListOptions'));
            });
        });
    });
})(jQuery);