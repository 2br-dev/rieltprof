/**
* Plugin, активирующий вкладку значений характеристики (PropertyValueList)
*/
(function($){
    $.fn.pvlBlock = function(method) {
        var defaults = {
            addValue              : '.add-item',
            addSomeValues         : '.add-list',
            naturalSortValues     : '.natural-sort-list',
            editValue             : '.edit-button, .values-list td.clickable',
            tableInlineEdit       : '.table-inline-edit', //Таблица, оборачивающая форму редактирования
            tableInlineEditCancel : '.cancel',            //Кнопка "Отменить" в форме редактирования
            removeValue           : '.remove-button',
            multiRemoveValue      : '.group-toolbar .delete',
            valuesList            : '.values-list',
            valueEditLine         : '.edit-form',
            valueLine             : '.item',
            valueEmptyLine        : '.empty-row',
            newItem               : '.new-item',
            nowEdit               : '.now-edit',
            virtualForm           : '.virtual-form',
            virtualSubmit         : '.virtual-form .virtual-submit, .virtual-form button[type="submit"]',
            contextForm           : '.crud-form',
            propertyTypeInput     : '[name="type"]'
        },
        xhr,
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                options,
                data = $this.data('pvlBlock');
            
            var methods = {
                init: function(initoptions) {                    
                    if (data) return;
                    data = {}; $this.data('pvlBlock', data);
                    data.options = $.extend({}, defaults, initoptions); 

                    //Навешивание события
                    $this
                        .on('click', data.options.virtualSubmit, submitVirtualForm)
                        //Перехватываем нажатие Enter в форме фильтрации
                        .on('keydown', data.options.virtualForm + ' input', function(e) {
                            if (e.keyCode == 13) {
                                e.preventDefault();
                                e.stopPropagation();
                                submitVirtualForm.call(this, e);
                            }
                        })                    
                        .on('click', data.options.virtualForm + ' a[data-href]', function(e) {
                            methods.refresh($(this).data('href'));
                            e.preventDefault();
                        })
                        .on('click', data.options.addValue, function() {
                            methods.add();
                        })
                        .on('click', data.options.editValue, function() {
                            var id = $(this).closest(data.options.valueLine).data('id');
                            methods.edit(id);
                        })
                        .on('click', data.options.addSomeValues, methods.addSomeValues)
                        .on('click', data.options.naturalSortValues, methods.naturalSortValues)
                        .on('click', data.options.tableInlineEdit + ' ' + data.options.tableInlineEditCancel, function() {
                            var id = $(this).closest(data.options.valueEditLine).data('id');
                            methods.edit(id);
                        })                        
                        .on('click', data.options.removeValue, function(e) {
                            var id = $(this).closest(data.options.valueLine).data('id');
                            methods.remove(id);
                        })
                        .on('click', data.options.multiRemoveValue, function(e) {
                            var ids = getSelectedOffers();
                            methods.remove(ids);
                        });
                        
                    $this.closest(data.options.contextForm)
                         .find(data.options.propertyTypeInput)
                         .change(onChangePropertyType).trigger('change', true);
                        
                },

                /**
                 * Открывает окно добавления нескольких значений
                 *
                 * @return {*}
                 */
                addSomeValues: function() {
                    return methods.add(true);
                },

                /**
                 * Отпавляет запрос на натуральную сортировку значений характеристик
                 *
                 * @return {*}
                 */
                naturalSortValues: function(e) {
                    $.rs.loading.show();
                    $.ajax({
                        url: $this.data('urls').naturalSort,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response){
                            $.rs.loading.hide();
                            if ($.rs.checkAuthorization(response) &&
                                $.rs.checkWindowRedirect(response) &&
                                $.rs.checkMessages(response) &&
                                response.success){
                                methods.refresh();
                            }
                        },
                        error: function() {
                            $.rs.loading.error();
                        }
                    });
                    e.preventDefault();
                },
                
                /**
                * Обновляет список
                */
                refresh: function(url, post_params) {
                    
                    if (!url) url = $this.data('urls').refresh;
                    if (!post_params) post_params = [];
                    
                    post_params.push({
                        name: 'prop_type',
                        value: getPropertyType()
                    });
                    
                    post_params.push({
                        name: 'pvl_page',
                        value: getCurrentPage()
                    });
                    
                    $.ajaxQuery({
                        url: url,
                        data: post_params,
                        method: 'post',
                        success: function(response) {
                            if (response.success != false) {
                                $this.html($(response.html).children()).trigger('new-content');
                                $this.trigger('enableBottomToolbar', 'pvl-edit');
                                $this.trigger('enableBottomToolbar', 'pvl-checked');
                            }
                        }
                    });
                    
                },

                /**
                 * Отправка формы
                 *
                 * @param form - объект существующей формы
                 * @param real_form - объект формы которую будем отправлять
                 * @param post_params - параметры передаваемые в POST запрос
                 */
                postForm: function(form, real_form, post_params) {
                    $.rs.loading.show();
                    real_form.ajaxSubmit({
                        url      : form.data('action'),
                        type     : 'post',
                        dataType : 'json',
                        success  : function(response) {
                            $.rs.loading.hide();
                            //Если это пост виртуальной формы, то отображаем ошибки формы, если они есть
                            if (response.success) {   
                                methods.refresh();
                            } else {
                                $('.crud-form-error', form).fillError(response.formdata.errors, form);
                            }
                        },
                        error: function() {
                            $.rs.loading.error();
                        }
                    });
                },                
                
                /**
                * Добавление значения
                */
                add: function(add_some_values) {
                    //Добавляем новую строку в таблицу комплектаций
                    var tr = $('<tr class="item new-item" data-id="0">\
                                <td class="chk"></td>\
                                <td class="drag drag-handle"></td>\
                                <td class="title">'+(add_some_values ? lang.t('Экспресс добавление списка значений характеристики') : lang.t('Новое значение'))+'</td>\
                                <td></td>\
                                <td class="actions">\
                                    <span class="loader"></span>\
                                    <a class="offer-del">' + lang.t('удалить') + '</a>\
                                </td>\
                            </tr>');
                    
                    $(data.options.valuesList, $this).prepend(tr);
                    $(data.options.valuesList + ' ' + data.options.valueEmptyLine, $this).hide();
                    methods.edit(0, add_some_values);
                },

                /**
                 * Редактирование значения
                 *
                 * @param value_id - идентификатор значения характеристики
                 * @param add_some_values
                 */
                edit: function(value_id, add_some_values) {
                    //Закрываем форму, если она была открыта раннее.
                    var edit_line = $(data.options.valueLine + '[data-id="'+value_id+'"]', $this);
                    var is_opened = edit_line.is(data.options.nowEdit);
                    
                    //Удаляем строку несозданной комплектации, если таковая была
                    if (value_id>0 || is_opened) $(data.options.valuesList + ' ' + data.options.newItem, $this).remove();
                    
                    $(data.options.valueLine, $this).removeClass('now-edit load');
                    $(data.options.valueEditLine, $this).remove();
                    $(data.options.valuesList + ' > tbody > tr', $this).removeClass('nodrag nodrop');

                    if (is_opened) {
                        $(data.options.valuesList + ' input[type="checkbox"]', $this).prop({'disabled': false, 'checked': false}).change();
                    } 
                    
                    if (xhr) xhr.abort();
                    
                    if (is_opened) { //Закрытие режима редактирования
                        $this.trigger('enableBottomToolbar', 'pvl-edit');
                        $(data.options.valueEmptyLine, $this).show();
                        return;
                    }
                    
                    $(data.options.valuesList + ' input[type="checkbox"]', $this).prop('disabled', true);
                    $this.trigger('disableBottomToolbar', 'pvl-edit');
                    
                    edit_line.addClass('now-edit load');
                    
                    var url = add_some_values ? $this.data('urls').addSomeValues : $this.data('urls').edit;
                    var post_data = {
                        value_id: value_id,
                        prop_type: getPropertyType()
                    };
                                 
                    xhr = $.ajaxQuery({
                        url: url,
                        data: post_data,
                        success: function(response) {
                            edit_line.removeClass('load');
                            var edit_wrap = $('<tr class="edit-form no-over">'+
                                                '<td colspan="5">'+
                                                    '<div class="bordered"></div>'+
                                                '</td>'+
                                             '</tr>');
                            
                            edit_wrap.data('id', value_id).find('.bordered').html(response.html);
                            edit_wrap.insertAfter(edit_line);
                            edit_wrap.find('.virtual-form').data('isAdd', value_id == 0); //Запишем флаг создание это или редактирование
                            
                            $.allReady(function() {
                                edit_wrap.trigger('new-content');
                            });
                            
                            //Отключаем сортировку
                            $(data.options.valuesList + ' > tbody > tr').addClass('nodrag nodrop');
                        }
                    });
                    
                },

                /**
                 * Удаление значения характеристики
                 *
                 * @param value_id - идентификатор значения характеристики
                 * @return {boolean}
                 */
                remove: function(value_id)
                {
                    if ($.isArray(value_id)) {
                        var items = value_id;
                        var count = $(data.options.valuesList + ' .select-all:checked', $this).length ? $('.total_value', $this).text() : items.length;
                        
                        if (!items.length || !confirm(lang.t('Вы действительно хотите удалить выбранные значения(%count)', {count:count}))) {
                            return false;
                        }
                    } else {
                        var edit_line = $(data.options.valueLine + '[data-id="'+value_id+'"]', $this),
                            value_title = edit_line.find('.title').text(),
                            items = {value_items: [value_id]};
                        
                        if (!confirm(lang.t('Вы действительно хотите удалить значение "%title"?', {title: value_title}))) {
                            return false;
                        }
                    }
                    
                    $.ajaxQuery({
                        url: $this.data('urls').remove,
                        method:'POST',
                        data: items,
                        success: function(response) {
                            if (response.success) methods.refresh();
                        }
                    });
                }
                
            };
            
            //private
            /**
            * Постит данные из виртуальной формы
            */
            var submitVirtualForm = function(e) 
            {
                var form = $(this).closest(data.options.virtualForm);
                var real_form = $('<form enctype="multipart/form-data"/>');
                form.find('input, select, textarea').each(function() {
                    var element = $(this).clone(true);
                    if ($(this).attr('type') == 'file' && element.val() != $(this).val()) {
                        $(this).after(element);
                        element = $(this);
                    }
                    
                    if (element.is('select,textarea')) {
                        element.val( $(this).val() ); //bugfix select clone
                    }
                    element.appendTo(real_form);
                });
                
                var params = real_form.serializeArray();
                
                if (form.data('hasValidation')) {
                    methods.postForm(form, real_form, params);
                } else {
                    methods.refresh(form.data('action'), params);
                }
                
                e.preventDefault();
            },
            /**
            * Возвращает отмеченные строки
            */
            getSelectedOffers = function() 
            {
                var items = [];
                $(data.options.valuesList + ' input[name][type="checkbox"]:checked').each(function() {
                    items.push({
                         name: $(this).attr('name'),
                         value: $(this).val()
                    });
                });
                return items;
            },
            
            /**
            * Возвращает действующий тип характеристики
            */
            getPropertyType = function() {
                return $this.closest(data.options.contextForm).find(data.options.propertyTypeInput).val();
            },
            
            /**
            * Вовращает текущую страницу
            */
            getCurrentPage = function() {
                return $this.find('input.page').val();
            },

            /**
             * Действия при смене типа характеристики
             *
             * @param e - объект события
             * @param no_refresh - обновлять ли контент страницы
             */
            onChangePropertyType = function(e, no_refresh) {
                var selected = $('option:selected', this);
                var tab = $('.tab-nav a:contains('+lang.t('Значения')+')', $this.closest('.formbox')).closest('li');
                if (selected.data('isList')) {
                    tab.show();
                    if (!no_refresh) {
                        methods.refresh();
                    }
                } else {
                    tab.hide();
                    $this.trigger('enableBottomToolbar', 'pvl-edit');
                    $this.trigger('enableBottomToolbar', 'pvl-checked');                 
                }
            };
            
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }
})(jQuery);    

$.contentReady(function() {
    $('#pvl-block').pvlBlock();
});