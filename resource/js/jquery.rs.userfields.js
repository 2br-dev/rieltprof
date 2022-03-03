/**
* Jquery плагин, отвечающий за пользовательские поля форм
*
* @author ReadyScript lab.
*/
(function( $ ){

    $.fn.userFields = function( method ) {
        var defaults = {
            'addButton': '.add',
            'delButton': '.del',
            'editButton': '.edit',
            'dialog': '#userfield-dialog',
            dialogFields: {
                values: '.p-values',
                type: '.p-type'
            }
        },
        args = arguments;

        return this.each(function() {
            var $this = $(this),
                currentEdit,
                data = $this.data('userfields');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('userfields', data);
                    data.opt = $.extend({}, defaults, initoptions);

                    data.dialog = $(data.opt.dialog).dialog({
                        title: lang.t('Параметры поля'),
                        width: 600,
                        height:600,
                        autoOpen:false,
                        modal:true,
                        buttons: [{
                            "text": lang.t("Сохранить"),
                            "class":'btn btn-success',
                            "click": save
                        }]
                    });
                    data.dialog
                        .on('change', data.opt.dialogFields.values, onValuesChange)
                        .on('change', data.opt.dialogFields.type, onTypeChange);

                    $this
                        .on('click', data.opt.addButton, add)
                        .on('click', data.opt.delButton, remove)
                        .on('click', data.opt.editButton, edit);

                    $.contentReady(function() {
                        $('table:has(".p-dndsort")').tableDnD({
                            dragHandle: ".p-drag-handle",
                            onDragClass: "in-drag"
                        });
                    })
                }
            }

            //private
            var
                add = function() {
                    var values = {
                       'alias': '',
                       'title': '',
                       'type' :'string',
                       'values': '',
                       'val' : '',
                       'maxlength' : '',
                       'necessary': '',
                       'result': ''
                    }
                    setDialogValues(values);

                    data.dialog.dialog('option','action','add');
                    data.dialog.dialog('open');
                },

                edit = function() {
                    currentEdit = $(this).closest('.property-item');
                    var values = {
                       'alias': '',
                       'title': '',
                       'type' : '',
                       'values': '',
                       'val' : '',
                       'maxlength': '',
                       'necessary': '',
                       'result': ''
                    }
                    for(var i in values) {
                        values[i] = $('.h-'+i, currentEdit).length ? $('.h-'+i, currentEdit).val() : '';
                    }
                    setDialogValues(values);
                    data.dialog.dialog('option','action','edit'); //Передаем сообщение: для какого действия открыто окно
                    data.dialog.dialog('open');
                },

                remove = function() {
                    if ($("input[name='chk[]']:checked", $this).length && confirm(lang.t('Вы действительно хотите удалить выделенные свойства?'))) {
                        $("input[name='chk[]']:checked", $this).each(function() {
                            var item = $(this).closest('tr');
                            item.remove();
                        });
                        checkEmpty();
                    }
                },

                save = function() {
                    var nowedit = (currentEdit) ? $('.h-alias', currentEdit).val() : 0;

                    var values = {
                        key: $this.data('key'),
                        alias: $('.p-alias', data.dialog).val(),
                        title: $('.p-title', data.dialog).val(),
                        type: $('.p-type', data.dialog).val(),
                        values: $('.p-values', data.dialog).val(),
                        val: $('.p-val', data.dialog).val(),
                        val_str: $('.p-val', data.dialog).val(),
                        maxlength: $('.p-maxlength', data.dialog).val(),
                        necessary: (+$('.p-necessary', data.dialog).get(0).checked)
                    };

                    var result = $('.p-result');
                    var type_str = {
                        "int":    lang.t('Число'),
                        "string": lang.t('Строка'),
                        "text":   lang.t('Текст'),
                        "list":   lang.t('Список'),
                        "bool":   lang.t('Да/Нет')
                    };

                    values.type_str = type_str[values.type];

                    if (trim(values.alias) == '') return result.html(lang.t('Идентификатор поля не может быть пустым'));
                    if (trim(values.title) == '') return result.html(lang.t('Название поля не может быть пустым'));
                    else if (nowedit != values.alias && $(".h-alias[value='"+values.alias+"']", $this).length>0 ) return result.html(lang.t('Поле с таким названием уже присутствует'));


                    if (values.type == 'bool') {
                        var checked = $('.p-val:checked', data.dialog).length > 0;
                        values.val_str = (checked) ? lang.t('Да') : lang.t('Нет');
                        values.val = (+checked);
                    }

                    values.necessary_str = ($('.p-necessary:checked', data.dialog).length > 0) ? lang.t('Да') : lang.t('Нет');
                    var new_item = $(tmpl('userfield-line', values));

                    if (data.dialog.dialog('option','action') == 'edit')
                    {
                        currentEdit.before(new_item);
                        currentEdit.remove();
                    } else {
                        $('.property-container', $this).append(new_item).trigger('new-content');
                    }

                    checkEmpty();
                    data.dialog.dialog('close');
                    $this.trigger('new-content');
                },

                trim = function( str, charlist )
                {
                    charlist = !charlist ? ' \\s\\xA0' : charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
                    var re = new RegExp('^[' + charlist + ']+|[' + charlist + ']+$', 'g');
                    return str.replace(re, '');
                }

                checkEmpty = function()
                {
                    $('.norecord', $this).toggle( !$('.property-container .property-item', $this).length );
                },

                onTypeChange = function()
                {
                    var el = $('.p-type', data.dialog);
                    if (el.val() == 'list') {
                        $('.p-values-block', data.dialog).show();
                    } else {
                        $('.p-values-block', data.dialog).hide();
                        $('.p-values', data.dialog).val('');
                    }

                    var pval = $('.p-val', data.dialog);

                    var new_input = getInputByType(el.val());
                    if (new_input) {
                        pval.after(new_input);
                        pval.remove();
                        if (el.val() == 'list') onValuesChange();
                    }
                },

                getInputByType = function(type)
                {
                    if (type == 'bool')
                        var val_input = '<input type="checkbox" value="1" class="p-val" style="width:13px">';
                    else
                    if (type == 'list')
                        var val_input = '<select class="p-val"></select>';
                    else
                        var val_input = '<input class="p-val" type="text">';

                    return val_input;
                },

                onValuesChange = function()
                {
                    var values = $('.p-values', data.dialog).val();
                    var values_arr = values.split(',');
                    var val_input = '';
                    var objSel = $('.p-val', data.dialog).get(0);
                    objSel.options.length = 0;

                    if (values != '') {
                        objSel.options[objSel.options.length] = new Option(lang.t('- Не выбрано -'), '');
                    }
                    for(var key in values_arr) {
                        objSel.options[objSel.options.length] = new Option(trim(values_arr[key]), trim(values_arr[key]));
                    }
                },

                setDialogValues = function(values) {
                    for(var key in values) {
                        switch (key) {
                            case 'result': {
                                $('.p-'+key, data.dialog).html( values[key] );
                                break;
                            }
                            case 'val': {
                                if (values.type == 'bool') {
                                    $('.p-val', data.dialog).get(0).checked = (values.val==1);
                                } else {
                                    $('.p-val', data.dialog).val( values.val );
                                }
                                break;
                            }
                            case 'necessary': {
                                $('.p-necessary', data.dialog).get(0).checked = (values.necessary==1);
                                break;
                            }
                            default: {
                                $('.p-'+key, data.dialog).val( values[key] );
                                if (key == 'type') {
                                    onTypeChange();
                                }
                                if (key == 'values' && values.type == 'list') {
                                    onValuesChange();
                                }
                            }
                        }
                    }
                };


            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }

    $(function() {
        $('.userfields-container').userFields();
    });

})( jQuery );