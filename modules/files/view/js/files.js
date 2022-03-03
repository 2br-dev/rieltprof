/**
* Plugin, активирующий универсальный блок файлов
*/
(function($){
    
$.fn.filesBlock = function(method) {

    var defaults = {
        filesList:      '.files-list',
        fileLine:       '.files-list .item',
        fileEditLine:   '.edit-form',
        fileDelete:     '.file-delete',
        fileEdit:       '.file-edit, .files-list td.clickable',
        deleteSelected: '.group-toolbar .delete',
        accessFileInput:'.access_file',
        virtualForm:    '.virtual-form',         //Класс виртуальной формы
        virtualSubmit:  '.virtual-form .virtual-submit, .virtual-form button.ok',
        tableInlineEditCancel: '.table-inline-edit .cancel',
        newFileLineHtml: function() {
            return $('\
                    <tr data-id="{$linked_file.id}" class="item nodrop inqueue">\
                        <td class="chk"></td>\
                        <td class="drag drag-handle"></td>\
                        <td class="title"></td>\
                        <td class="description">' + lang.t('нет') + '</td>\
                        <td class="size"></td>\
                        <td class="progress"> \
                            <span class="progress-bar"><i class="bar"></i><span class="percent">' + lang.t('в очереди') + '</span></span>\
                            <span class="error"></span>\
                        </td>\
                        <td class="actions">\
                            <span class="loader"></span>\
                            <a class="cancel delete">' + lang.t('удалить') + '</a>\
                        </td>\
                    </tr>');
        },
        fileFormHtml: function() {
            return $('<tr class="edit-form no-over">'+
                        '<td colspan="7">'+
                            '<div class="bordered"></div>'+
                        '</td>'+
                     '</tr>');
        }
    }, 
    args = arguments;
    
    return this.each(function() {
        var $this = $(this), 
            xhr,
            data = $this.data('filesBlock');
        var methods = {
            init: function(initoptions) {
                if (data) return;
                data         = {}; $this.data('filesBlock', data);
                data.options = $.extend({}, defaults, initoptions);
                data.urls = $this.data('urls');
                initFileUpload();
                
                $this
                    .on('click', data.options.virtualSubmit, submitVirtualForm)
                    .on('click', data.options.fileDelete, function() {
                        var id = $(this).closest('[data-id]').data('id');
                        methods.deleteFile([id]);
                    })
                    .on('click', data.options.deleteSelected, function() {
                        var ids = [];
                        $('input[name="files[]"]:checked', $this).each(function() {
                            ids.push($(this).val());
                        });
                        methods.deleteFile(ids);
                    })
                    .on('change', data.options.accessFileInput, function() {
                        var id = $(this).closest('[data-id]').data('id');
                        var access = $(this).val();
                        methods.changeAccessFile(id, access);
                    })
                    .on('click', data.options.fileEdit, function() {
                        var id = $(this).closest('[data-id]').data('id');
                        methods.editFile(id);
                    })
                    .on('change', data.options.filesList + ' input[type="checkbox"]', function() {
                        checkActiveCheckbox();                        
                    })
                    .on('click', data.options.tableInlineEditCancel, function() {
                        var id = $(this).closest(data.options.fileEditLine).data('id');
                        methods.editFile(id);
                    });
                    
                $this.closest('.dialog-window').on('dialogBeforeDestroy', function() {
                    $this
                        .trigger('enableBottomToolbar', 'file-checked')
                        .trigger('enableBottomToolbar', 'file-edit');
                });                    
            },
            
            deleteFile: function(ids) {
                if (ids.length && confirm(lang.t('Вы действительно хотите удалить выбранные файлы?'))) {
                    $.each(ids, function(i, val) {
                        $('.files-list .item[data-id="'+val+'"]', $this).css('opacity', 0.5);
                    });
                    $.ajaxQuery({
                        type: 'POST',
                        url: data.urls.fileDelete,
                        data: {
                            files: ids
                        },
                        success: function(response) {
                            if (response.success) {
                                $.each(ids, function(i, val) {
                                    $('.files-list .item[data-id="'+val+'"]', $this).remove();
                                });
                                $(data.options.filesList + ' input[type="checkbox"]', $this).prop({'disabled': false, 'checked': false}).change();
                                checkEmpty();
                            }
                        }
                    });
                }
            },
            
            editFile:function(id) {
                var edit_line = $(data.options.fileLine + '[data-id="'+id+'"]', $this);
                var is_opened = edit_line.is('.now-edit');
                
                $(data.options.fileLine, $this).removeClass('now-edit load');
                $(data.options.fileEditLine, $this).remove();
                $(data.options.filesList + ' > tbody > tr', $this).removeClass('nodrag nodrop');
                
                if (is_opened) {
                    $(data.options.filesList + ' input[type="checkbox"]', $this).prop({'disabled': false, 'checked': false}).change();
                } 
                
                if (xhr) xhr.abort();
                
                if (is_opened) { //Закрытие режима редактирования
                    $this.trigger('enableBottomToolbar', 'file-edit');
                    return;
                }
                
                $(data.options.filesList + ' input[type="checkbox"]', $this).prop('disabled', true);
                $this.trigger('disableBottomToolbar', 'file-edit');
                edit_line.addClass('now-edit load');
                
                xhr = $.ajaxQuery({
                    url: data.urls.fileEdit,
                    data: {
                        file: id
                    },
                    success: function(response) {
                        edit_line.removeClass('load');
                        var edit_wrap = data.options.fileFormHtml();
                             
                        edit_wrap.data('id', id).data('editLine', edit_line).find('.bordered').html(response.html);
                        edit_wrap.insertAfter(edit_line).trigger('new-content');
                        
                        //Отключаем сортировку
                        $(data.options.filesList + ' > tbody > tr').addClass('nodrag nodrop');
                    }
                });
            },
            
            changeAccessFile: function(id, access) {
                $.ajaxQuery({
                    type: 'POST',
                    url: data.urls.fileChangeAccess,
                    data: {
                        file: id,
                        access: access
                    }
                });
            }
        }
        
        //private     
        var initFileUpload = function()
        {
            $this.fileupload({
                dataType: 'json',
                dropZone: $('.dragzone', $this),
                pasteZone:null,
                filesContainer: $('.files-list', $this),
                uploadTemplateId:null,
                downloadTemplateId:null,
                sequentialUploads:true,
                url: data.urls.fileUpload,
                type: 'POST',
                formData: function() {
                    return {}
                },
                add: function(e, datafile) {
                    var $file = data.options.newFileLineHtml();
                    $file.find('.title').text(datafile.files[0].name);
                    $file.find('.size').text(bytesToSize(datafile.files[0].size));
                    datafile.li = $file.appendTo( $(this).find(data.options.filesList) );
                                    
                    var jqXHR = datafile.submit();
                    $file.find('.cancel').one('click', function() {
                        jqXHR.abort();
                    });
                    
                    checkEmpty();
                },
                send: function(e, data) {
                    data.li.removeClass('inqueue').addClass('uploading');
                },
                progress: function(e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    data.li.find('.bar').css('width', progress+'%');
                    data.li.find('.percent').text(progress+'%');
                },
                done: function(e, data) {
                    data.li.find('.bar').css('width', '100%');
                    data.li.find('.percent').text('100%');              
                    if (data.result.items[0].success) {
                        data.li.replaceWith(data.result.items[0].html);
                        data.form.trigger('new-content');  
                    } else {
                        data.textStatus = data.result.items[0].error;
                        data.fail(e, data);
                    }
                },
                fail: function(e, data) {
                    data.li.removeClass('uploading').addClass('fail');
                    data.li.find('.progress-bar').remove();
                    data.li.find('.error').text(data.textStatus);
                    data.li.find('.cancel').click(function() {
                        data.li.remove();
                        checkEmpty();
                    });                  
                },
                
                start: function(e) {
                    $('.files-list', $this).addClass('disable-sort');
                },
                
                stop: function(e) {
                    $('.files-list', $this).removeClass('disable-sort');
                }
            });
        },
        
        checkEmpty = function() {
            $('.files-container').toggleClass('hidden', !$(data.options.fileLine).length);
        },
        
        checkActiveCheckbox = function() {
            //Если есть отмеченные элементы, то посылаем событие - Запретить действия над товаром, иначе - разрешить
            if ($(data.options.filesList + ' input[type="checkbox"]:checked', $this).length) {
                $this.trigger('disableBottomToolbar', 'file-checked');
            } else {
                $this.trigger('enableBottomToolbar', 'file-checked');
            }
        },
        
        bytesToSize = function (bytes) {
           var sizes = [lang.t('Байт'), lang.t('Кб'), lang.t('Мб'), lang.t('Гб'), lang.t('Тб')];
           if (bytes == 0) return '0 ' + sizes[0];
           var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
           return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        },
        
        submitVirtualForm = function(e) 
        {
            var edit_form = $(this).closest(data.options.fileEditLine);
            var edit_line = edit_form.data('editLine');
            var form = $(this).closest(data.options.virtualForm);
            var real_form = $('<form />');
            form.find('input, select, textarea').each(function() {
                var element = $(this).clone();
                if (element.is('select, textarea')) {
                    element.val( $(this).val() ); //bugfix select clone
                }
                element.appendTo(real_form);
            });
            
            var params = real_form.serializeArray();
            $.ajaxQuery({
                url: form.data('action'),
                data: params,
                method: 'post',
                success: function(response) {
                    //Если это пост виртуальной формы, то отображаем ошибки формы, если они есть
                    if (response.success) {                        
                        //Закрываем форму редактирования
                        methods.editFile(edit_line.data('id'));
                        edit_line.replaceWith(response.html);
                        $this.trigger('new-content');
                    } else {
                        $('.crud-form-error', form).fillError(response.formdata.errors, form);
                    }
                }
            });     
            
            e.preventDefault();
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
    $('.files-block', this).filesBlock();
});