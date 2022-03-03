/**
    * Plugin, активирующий вкладку "характеристики" у товаров
    */
(function($){
    $.fn.propertyBlock = function(method) {
        var defaults = {
            addButton: '.add-property',
            someAddButton: '.add-some-property',
            collapseButton: '.close',
            saveButton: '.property-table .add',
            someSaveButton: '.some-property-table .add-some',
            propertyForm: '.property-form',
            somePropertyForm: '.some-property-form',
            addNewListValue: '.p-add-new-value',
            removeListValue: '.p-remove-val',
            propertyActions: '.property-actions',
            propertyList: '.property-container',
            propertyLoading: '.ploading',
            propertyItem: '.property-item',
            successText: '.success-text',
            setSelfVal: '.set-self-val',
            errors: {
                title: '.p-title-block .field-error'
            },
            blocks: {
                title: '.p-title-block',
                proplist: '.p-proplist-block',
                values: '.p-values-block',
                val: '.p-val-block',
                value: '.p-value-block',
                group: '.p-group-block',
                listValues: '.p-list-values'
                
            },
            formFields: {
                propertyList: '.p-proplist',
                parent: '.p-parent_id',
                title: '.p-title',
                type: '.p-type',
                unit: '.p-unit',
                values: '.p-values',
                val: '.p-val',
                step: '.p-step',
                newGroup: '.p-new-group',
                useVal: '.h-useval',
                uValue: '.h-val, .h-val-linked',
                hPublic: '.h-public',
                hExpanded: '.h-expanded',
                someProps: '.some-props',
                pNewValue: '.p-new-value'
            },
            tools: {
                edit: '.p-edit',
                del: '.p-del'
            },
            getPropertyUrl: '' //Инициализируется из аттрибута data-get-property-url
            
        }, 
        fullPropertyList,
        whenListLoad = $.Deferred(),
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), options;
            
            var methods = {
                init: function(initoptions) {                    
                    if ($this.data('propertyBlock')) return false;
                    $this.data('propertyBlock', {});
                    options = $.extend({}, defaults, initoptions);
                    options.getPropertyUrl = $this.data('getPropertyUrl');
                    options.getPropertyValueUrl = $this.data('getPropertyValueUrl');
                    options.createPropertyValueUrl = $this.data('createPropertyValueUrl');
                    options.removePropertyValueUrl = $this.data('removePropertyValueUrl');
                    $this
                        .on('click', options.addButton, methods.addProperty)
                        .on('click', options.collapseButton, methods.collapseForm)
                        .on('change', options.formFields.propertyList, onPropertySelect)
                        .on('change', options.formFields.type, onTypeChange)
                        //.on('change', options.formFields.values, onValuesChange)
                        .on('keyup', options.formFields.newGroup, onNewGroupChange)
                        .on('click', options.saveButton, onSave)
                        .on('change', options.formFields.useVal, onUseValChange)
                        .on('click', options.tools.edit, editProperty)
                        .on('click', options.tools.del, deleteProperty)
                        .on('click', options.someAddButton, methods.addSomeProperty)
                        .on('click', options.someSaveButton, onInsertSome)
                        .on('click', options.setSelfVal, onSetSelfVal)
                        .on('click', options.addNewListValue, onAddNewListValue)
                        .on('keypress', options.formFields.pNewValue, function(e) {
                            if ( e.keyCode == 13 ) onAddNewListValue();
                        })
                        .on('click', options.removeListValue, onRemoveListValue);
                        
                    $(options.formFields.useVal, $this).each(onUseValChange);
                    updateTmpId();
                },
                
                addProperty: function() {
                    var hasAct = $(this).hasClass('act');
                    methods.collapseForm();                    
                    if (!hasAct) {
                        $(this).addClass('act');                        
                        cancelEditProperty();
                        $(options.formFields.propertyList).val('new').trigger('change');
                        methods.expandForm();
                    }
                    $this.trigger('contentSizeChanged');
                    return false;
                },
                
                addSomeProperty: function() {
                    var hasAct = $(this).hasClass('act');
                    methods.collapseForm();
                    if (!hasAct) {
                        $(this).addClass('act');
                        cancelEditProperty();
                        loadPropertyList();
                        
                        $(options.somePropertyForm, $this).show();
                        $this.trigger('contentSizeChanged');
                    }                    
                    
                },
                
                expandForm: function() {
                    $form = $(options.propertyForm, $this);
                    loadPropertyList();
                    $form.show();
                    $this.trigger('disableBottomToolbar', 'property-edit');
                },
                
                collapseForm: function() {
                    cancelEditProperty();
                    $(options.propertyActions+' .act').removeClass('act');
                    $(options.somePropertyForm, $this).hide();
                    $this.trigger('enableBottomToolbar', 'property-edit');
                    return false;
                }
            }
            
            //private 
            
            /**
            * Загружаем список свойств
            */
            var loadPropertyList = function()
            {
                if (whenListLoad.state() != 'resolved' || !fullPropertyList) {
                    $(options.propertyLoading).show();
                    $(options.formFields.propertyList).prop('disabled', true);
                    $(options.formFields.parent).prop('disabled', true);
                    $(options.formFields.newGroup).prop('disabled', true);
                    
                    initList(function() {
                        $(options.propertyLoading).hide();
                        $(options.formFields.propertyList).prop('disabled', false);
                        $(options.formFields.parent).prop('disabled', false);
                        $(options.formFields.newGroup).prop('disabled', false);                        
                        
                        fillSelect();
                        whenListLoad.resolve();
                    });
                }
            },

            initList = function(callback)
            {
                $.ajaxQuery({
                    url: options.getPropertyUrl,
                    success: function(response) {                        
                        fullPropertyList = response;
                        callback.call();                        
                    }
                });
            },
            
            fillSelect = function()
            {
                //Загружаем значения в список
                var select = $(options.formFields.propertyList, $this).empty();
                var group_select = $(options.formFields.parent, $this).empty();
                var cur_group;
                var optgroup;
                
                select.append('<option value="new">' + lang.t('Новая характеристика') + '</option>');
                group_select.append('<option value="0">' + lang.t('Без группы') + '</option>');
                
                //Генерируем корректно отсортированный список характеристик
                fullPropertyList.properties = {};
                for(var i in fullPropertyList.properties_sorted) 
                {
                    var item = fullPropertyList.properties_sorted[i];
                    fullPropertyList.properties[item.id] = item;
                    if (cur_group === undefined || parseInt(item.parent_id) != cur_group) {
                        cur_group = parseInt(item.parent_id);
                        optgroup = $('<optgroup></optgroup>').attr({
                                        label: fullPropertyList.groups[cur_group].title
                                    }).appendTo(select);
                    }
                    $('<option></option>').attr('value', item.id).text(item.title).appendTo(optgroup);
                }
                
                group_select.html('');
                for (i in fullPropertyList.groups) {
                    var item = fullPropertyList.groups[i];
                    $('<option></option>').attr('value', i).text(item.title).appendTo(group_select);
                }
                
                //Если присутствует множественная вставка свойств, то заполняем и её
                var $someSelect = $(options.formFields.someProps, $this);
                if ($someSelect.length) {
                    select.children().clone().appendTo($someSelect.empty());
                    $('option[value="new"]:first', $someSelect).remove();
                    $someSelect.removeAttr('disabled');
                }
                
                $(options.saveButton, $this).removeClass('disabled');
                $(options.someSaveButton, $this).removeClass('disabled');
            },
            
            onNewGroupChange = function()
            {
                $(options.formFields.parent).prop('disabled', $(this).val() != '');
                
            },
            
                
            decode = function(encodedStr) {
                return $("<div/>").html(encodedStr).text();
            },             
            
            onPropertySelect = function()
            {
                var index = $(this).val();
                if (index == 'new' || typeof(fullPropertyList) == 'undefined' ) {
                    var params = {
                        title: '',
                        type: 'string',
                        values: '',
                        unit: '',
                    }
                } else {
                    var params = fullPropertyList.properties[index];
                };
                
                //Устанавливаем значения в диалоговом окне
                $(options.formFields.title, $this).val( decode(params.title) );
                $(options.formFields.type, $this).val( params.type );
                $(options.formFields.values, $this).val( decode(params.values) );
                $(options.formFields.unit, $this).val( decode(params.unit) );
                $(options.formFields.step, $this).val( params.step );
                $(options.formFields.parent, $this).val( params.parent_id );
                
                if (index == 'new') {
                    //Режим создания нового свойства
                    $(options.blocks.value+','+options.blocks.title+','+options.blocks.group, $this).show();
                    //$(options.formFields.type +','+ options.formFields.parent +','+ options.formFields.title, $this).removeAttr('disabled');
                    $(options.blocks.proplist+','+options.blocks.group , $this).show();                    
                    $('.p-type-block', $this).show();
                    $(options.saveButton, $this).text( $(options.saveButton, $this).data('addText'));
                } else {
                    if ($(options.propertyForm, $this).data('propertyEditMode')) {
                        //Режим редактирования
                        $(options.blocks.title).show();
                        $(options.blocks.value+','+options.blocks.proplist+','+options.blocks.group , $this).hide();
                        $(options.formFields.type, $this).removeAttr('disabled');
                        $(options.saveButton, $this).text( $(options.saveButton, $this).data('editText'));
                    } else {
                        //Режим добавления
                        $(options.blocks.title+','+options.blocks.group, $this).hide();
                        $(options.saveButton, $this).text( $(options.saveButton, $this).data('addText'));
                        //$(options.formFields.type+','+options.formFields.parent, $this).attr('disabled','disabled');
                    }
                    $('.p-type-block', $this).hide();
                }
                
                onTypeChange();
            },
            
            onTypeChange = function()
            {
                var prop_id = $(options.formFields.propertyList, $this).val();
                var el = $(options.formFields.type, $this);
                var is_list = $('option:selected', el).data('isList');
                var edit_mode = $(options.propertyForm, $this).data('propertyEditMode');
                
                //Определяем контейнер для формы со значениями
                var value_container = edit_mode ? $('.now-edit .item-val', $this) : $('.p-val-block', $this);
                
                if (is_list) {
                    $('.p-new-value-block', $this).show();
                    $('.p-value-block', $this).toggle(!edit_mode);
                } else {
                    $('.p-new-value-block', $this).hide();
                    if (!edit_mode) {
                        $('.p-value-block', $this).show();
                    }
                }
                
                //В режиме редактирования тип не изменяется, поэтому не обновляем поля со значениями
                if (edit_mode) return;
                
                //Получаем соответствующую типу характеристики форму
                var new_input = getInputByType(el.val(), is_list, '', edit_mode);
                value_container.empty().append(new_input);                
                
                if (is_list) {                                    
                    //Подгружаем значения характеристики
                    if (prop_id != 'new' && fullPropertyList) {
                        var loader = $('<div class="loading-p-values">' + lang.t('Загрузка...') + '</div>').appendTo(new_input);
                                            
                        var prop_params = fullPropertyList.properties[prop_id];                    
                        //Если характеристика списковая, то загрузим значения
                        if (fullPropertyList.types[prop_params.type].is_list) {
                            if (typeof(prop_params.list_values) == 'undefined') {
                                $.ajaxQuery({
                                    url:options.getPropertyValueUrl,
                                    data:{
                                        prop_id: prop_id
                                    },
                                    success: function(response) {
                                        loader.remove();                                        
                                        prop_params.list_values = response.property_values;
                                        showListValues(prop_params.list_values, value_container);
                                    }
                                });
                            } else {
                                loader.remove();                                
                                showListValues(prop_params.list_values, value_container);
                            }
                        }
                    }
                    checkEmptyListValues();
                }
            },
            
            onAddNewListValue = function() {
                var prop_id = $(options.formFields.propertyList, $this).val();
                var new_value = $(options.formFields.pNewValue).val();
                
                //Если характеристика новая, то используем временный ID характеристики
                if (prop_id == 'new') prop_id = $this.data('tmpId');
                
                $.ajaxQuery({
                    method:'POST',
                    url:options.createPropertyValueUrl,
                    data: {
                        prop_id: prop_id,
                        value:new_value
                    },
                    success: function(response) {
                        if (response.success) {
                            response.item_value.is_checked = true;
                            var edit_mode = $(options.propertyForm, $this).data('propertyEditMode');
                            var item = getOneListValue(response.item_value, edit_mode);
                            
                            if (edit_mode) {
                                // большие списки сами обрабатывают добавление новых значений
                                let big_list = $('.property-item.now-edit .item-val .property-type-big-list');
                                if (big_list.length) {
                                    big_list.propertyTypeBigList('addNewValue', response.item_value.id, response.item_value.value);
                                } else {
                                    $('.property-item.now-edit .item-val').append(item);
                                }
                            } else {
                                $(options.blocks.listValues).append(item);
                            }
                            
                            $(options.formFields.pNewValue).val('');
                            checkEmptyListValues();
                        }
                    }
                });
                
            },
            
            showListValues = function(values, value_container) {
                let ul = $(options.blocks.listValues, value_container).empty();

                if (values.length > 20) {
                    ul.append('<span class="inline-item">' + lang.t('Указать значения можно будет после добавления') + '</span>');
                } else {
                    $.each(values, function(i, data) {
                        let li = getOneListValue(data, false);
                        ul.append(li);
                    });
                }

                checkEmptyListValues();
            },
            
            getOneListValue = function(data, edit_mode) {
                var item = $('<span class="inline-item property-type-list">\
                    <input type="checkbox">\
                    <label></label>\
                    <a class="p-remove-val">&times;</a>\
                </span>');
                
                var input = item.find('input').attr({
                    value: data.id,
                    id:'ch_' + data.id
                })
                .addClass(edit_mode ? 'h-val' : 'p-val')
                .prop('checked', data.is_checked);
                
                if (edit_mode) {
                    input.attr('name', 'prop[' + data.prop_id + '][value][]');
                }
                
                item.find('.p-remove-val').attr('title', lang.t('Удалить значение из характеристики'));
                item.find('label').attr('for', 'ch_' + data.id).text(data.value);
                return item;
            },
            
            onRemoveListValue = function() {
                if (confirm(lang.t('Вы действительно хотите удалить значение списковой характеристики? Связь с этим значением будет удалена у всех товаров.'))) {
                    var value_id = $(this).closest('.inline-item').find('input').val();
                    var _this = this;
                    
                    $.ajaxQuery({
                        method:'POST',
                        url:options.removePropertyValueUrl,
                        data: {
                            id:value_id
                        },
                        success: function(response) {
                            if (response.success) {
                                $(_this).closest('.inline-item').remove();
                                checkEmptyListValues();                                
                            }
                        }
                    });
                    
                }
            },

            getInputByType = function(type, is_list, value, edit_mode)
            {
                var class_name = edit_mode ? 'h-val' : 'p-val';
                if (is_list) {
                    var val_input = $('<div class="p-list-values"></div>');
                } else if (type == 'bool') {
                    var val_input = $('<input type="checkbox" value="1">').addClass(class_name).prop('checked', value != '');
                } else {
                    var val_input = $('<input type="text">').addClass(class_name).val(value);
                }
                
                return val_input;
            },
            
            checkEmptyListValues = function() {
                            
                if (!$('.p-list-values').children().length) {
                    $('<div class="p-list-empty">' + lang.t('нет значений') + '</div>').appendTo($('.p-list-values', $this));
                } else {
                    $('.p-list-empty', $this).remove();
                }
            },
             
            onInsertSome = function()
            {
                if ($(this).hasClass('disabled')) return false;
                
                var ids = [];
                $(options.formFields.someProps+' option:selected', $this).each(function() {
                    ids.push({
                        name: 'ids[]',
                        value: $(this).val()
                    });
                });
                
                $.ajaxQuery({
                    url: $this.data('getSomeProperties'),
                    type:'POST',
                    data: ids,
                    success: function(response) {
                        for(var i in response.result) {
                            if (!$(options.propertyItem+'[data-property-id="'+response.result[i].prop.id+'"]', $this).length) {
                                if (response.result[i].group.length == 0) {
                                    response.result[i].group.id = 0;
                                }
                                
                                var target_group = $('tbody[data-group-id="'+response.result[i].group.id+'"]', $this);
                                
                                if ( target_group.length ) {
                                    target_group.append(response.result[i].property_html);
                                } else {
                                    $(options.propertyList, $this).append( response.result[i].group_html );
                                    $('tbody[data-group-id="'+response.result[i].group.id+'"]', $this)
                                        .append(response.result[i].property_html)
                                        .trigger('new-content');
                                }
                                
                            }
                        }
                    }
                });
            },
            
            /**
            * Устанавливает tmp_id, которое будет использоваться при создании новой характеристики
            */
            updateTmpId = function() {
                var timestamp = new Date().getTime();
                $this.data('tmpId', -Math.floor(timestamp/1000));
            },
            
            onSave = function()
            {
                if ($(this).hasClass('disabled')) return false;
                
                var $form = $(options.propertyForm, $this);
                var $item = $form.data('propertyItem'), 
                    $context,
                    edit_mode = $form.data('propertyEditMode');
                
                if (edit_mode) {
                    val_class = '.h-val'; $context = $item;
                } else {
                    val_class = '.p-val'; $context = $form;
                }
                
                var item = {
                    title:          $(options.formFields.title, $form).val(),
                    type:           $(options.formFields.type, $form).val(),
                    values:         $(options.formFields.values, $form).val(),
                    value:          $(val_class, $context).val(),
                    unit:           $(options.formFields.unit, $form).val(),
                    step:           $(options.formFields.step, $form).val(),
                    parent_id:      $(options.formFields.parent, $form).val(),
                };
                if (!edit_mode) {
                    item['new_group_title'] = $(options.formFields.newGroup, $form).val();
                }
                var val_class;
                
                var id = $(options.formFields.propertyList, $form).val();
                var is_list = $(options.formFields.type+' option:selected', $form).data('isList');
                item.id = (id == 'new') ? $this.data('tmpId') : id;
                item.is_my = 1;
                item.owner_type = $this.data('ownerType');
                
                if (edit_mode) {
                    item['public'] = $(options.formFields.hPublic+':checked', $item).length>0 ? 1 : 0;
                    item['is_expanded'] = $(options.formFields.hExpanded+':checked', $item).length>0 ? 1 : 0;
                    item['useval'] = $(options.formFields.useVal+':checked', $item).length>0 ? 1 : 0;
                }
                
                if (id > 0) {
                    var exists = $(options.propertyItem+'[data-property-id="'+id+'"]', $this);
                    if (exists.length) {
                        item.is_my = exists.data('isMy'); //Определяем какой шаблон нам должен вернуть сервер
                    }
                }
                
                $(options.formFields.title, $form).removeClass('has-error');
                $(options.errors.title, $form).hide();

                
                if (item.type == 'bool') {
                    var checked = $(val_class+':checked', $context).length > 0;
                    item.value = (+checked);
                }

                if (is_list) {
                    var val_list = [];
                    $(val_class+':checked', $context).each(function() {
                        val_list.push($(this).val());
                    });
                    item.value = val_list;
                }
                
                $.ajaxQuery({
                    url: $this.data('savePropertyUrl'),
                    type:'POST',
                    data: {
                        item: item
                    },
                    success: function(response) {
                        if (response.success) {
                            updateTmpId();
                            methods.collapseForm();                            
                            //Обновляем характеристику в списке
                            if (response.group.length == 0) {
                                response.group.id = 0;
                            }
                            
                            if ($('.property-item[data-property-id="'+response.prop.id+'"]', $this).length) {
                                $('.property-item[data-property-id="'+response.prop.id+'"]', $this).replaceWith(response.property_html);
                            } else {
                                var target_group = $('tbody[data-group-id="'+response.group.id+'"]', $this);
                                if ( target_group.length ) {
                                    target_group.append(response.property_html);
                                } else {
                                    $(options.propertyList, $this).append( response.group_html );
                                    $('tbody[data-group-id="'+response.group.id+'"]', $this)
                                        .append(response.property_html)
                                        .trigger('new-content');
                                }
                            }
                            
                            if (!edit_mode) {
                                $(options.successText, $this).fadeIn();
                                setTimeout(function() {
                                    $(options.successText, $this).fadeOut();
                                }, 7000);
                                
                            }

                            let list_updated = false;
                            for(var j in fullPropertyList.properties_sorted) {
                                if (fullPropertyList.properties[response.prop.id]) {
                                    if (fullPropertyList.properties_sorted[j].id == response.prop.id) {
                                        //Обновляем запись
                                        fullPropertyList.properties_sorted[j] = response.prop;
                                        list_updated = true;
                                    }
                                } else {
                                    if (fullPropertyList.properties_sorted[j].parent_id == response.prop.parent_id) {
                                        //Добавляем запись
                                        fullPropertyList.properties_sorted.splice(j, 0, response.prop);
                                        list_updated = true;
                                        break;
                                    }
                                }
                            }

                            if (!list_updated) {
                                if (response.group.id > 0) {
                                    fullPropertyList.groups[response.group.id] = response.group;
                                }
                                fullPropertyList.properties[response.prop.id] = response.prop;
                                fullPropertyList.properties_sorted.push(response.prop);
                            }
                           
                            fillSelect(); //Обновляем значения в сисках

                            $(options.formFields.useVal, $('.property-item[data-property-id="'+response.prop.id+'"]', $this)).each(onUseValChange);
                        } else {
                            for (var field in response.formdata.errors) {
                                
                                var errors_str = response.formdata.errors[field].errors.join('<br>');
                                if (field == '@system') field = 'title';
                                
                                $('.p-'+field, $this).addClass('has-error');
                                $('.field-error[data-field="'+field+'"]', $this).html(
                                    '<span class="text"><i class="cor"></i>'+errors_str+'</span>'
                                ).show();
                            }
                        }
                        $form.trigger('new-content');
                    }
                })
                
            },
            
            onUseValChange = function()
            {
                var item = $(this).closest('.property-item');
                if ($(this).prop('checked')) {
                    $(options.formFields.uValue, item).removeAttr('disabled');
                } else {
                    $(options.formFields.uValue, item).attr('disabled','disabled');
                }
            },
            
            deleteProperty = function()
            {
                if (confirm(lang.t('Вы действительно хотите удалить характеристику?')))
                {
                    cancelEditProperty();
                    var div = $(this).closest(options.propertyItem);
                    var my_group = div.closest('tbody');
                    div.remove();
                    
                    if (!my_group.children().length) {
                        var group_head = my_group.prev('.group-body[data-gid]').remove();
                        my_group.remove();
                    }                   
                }
            },
            
            cancelEditProperty = function()
            {
                var inEditNow = $('.now-edit', $this);
                $(options.propertyForm, $this).data('propertyEditMode', false).hide();
                $('.has-error', $this).removeClass('has-error');
                $('.field-error', $this).empty().hide();
                
                if (inEditNow.removeClass('now-edit noover').length) {
                    $(options.propertyForm, $this).insertAfter($(options.propertyActions, $this));
                    inEditNow.next('.edit-form').remove();
                }
            },
            
            editProperty = function()
            {
                var item = $(this).closest(options.propertyItem);
                var needOpen = !item.hasClass('now-edit');
                
                methods.collapseForm();
                
                if (needOpen) {
                    var editform = $('<tr class="edit-form noover"><td colspan="6"><div class="bordered"></div></td></tr>');
                    item.addClass('now-edit noover').removeClass('over');
                    var $form = $(options.propertyForm, $this).data({
                        'propertyEditMode': true,
                        'propertyItem': item
                    });
                    editform.insertAfter(item).find('.bordered').append($form);
                    methods.expandForm();
                    
                    whenListLoad.done(function() {
                        $(options.formFields.propertyList).val(item.data('propertyId')).trigger('change');
                    });
                }
                
            },
            trim = function ( str, charlist ) 
            {
                charlist = !charlist ? ' \\s\\xA0' : charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
                var re = new RegExp('^[' + charlist + ']+|[' + charlist + ']+$', 'g');
                return str.replace(re, '');
            },
            onSetSelfVal = function()
            {
                $(options.formFields.useVal, $this).prop('checked', true).trigger('change');
                return false;
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
    $('#propertyblock').propertyBlock();
});