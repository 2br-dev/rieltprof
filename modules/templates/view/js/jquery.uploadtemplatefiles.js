/**
* Плагин, отвечающий за диалог загрузки файлов в редакторе шаблонов
* Зависит от jquery.fileupload
*/
$.fn.uploadTemplateFiles = function(method) {
    var defaults = {
        cancelAll: '.cancel-all',
        startUpload: '.start-upload',
        inputUploadFile: '.inputUploadFile',
        selectUploadFile: '.selectUploadFile',
        uploadFilesForm: '.upload-files-dnd',
        filesTable: '.upload-files-table',
        filesContainer: '.upload-files-table tbody'
    }, 
    args = arguments;
    
    return this.each(function() {
        var $this = $(this), 
            data = $this.data('uploadTemplateFiles');
        
        //public
        var methods = {
            init: function(initoptions) {                    
                if (data) return;
                data = {}; $this.data('uploadTemplateFiles', data);
                data.options = $.extend({}, defaults, initoptions);

                initFileUpload();
                
                $this.on('click', data.options.selectUploadFile, browse);
                $('body').on('dialogBeforeDestroy', cancelAllUpload)
            }
        }
        
        //private
        var 
        browse = function() {
            $(data.options.inputUploadFile, $this).click();
        },
        
        /**
        * Отменяем все загрузки
        */
        cancelAllUpload = function() {
            $(data.options.filesContainer, $this).children().each(function() {
                if ($(this).data('jqXHR')) {
                    $(this).data('jqXHR').abort();
                }
            });
        },
        
        bytesToSize = function (bytes) {
           var sizes = [lang.t('Байт'), lang.t('Кб'), lang.t('Мб'), lang.t('Гб'), lang.t('Тб')];
           if (bytes == 0) return '0 ' + sizes[0];
           var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
           return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        },
        
        checkFilesCount = function() {
            $(data.options.filesTable, $this).toggleClass('hidden',  
                $(data.options.filesContainer, $this).children().length==0 );
        },
        
        initFileUpload = function() {
            
            $(data.options.uploadFilesForm, $this).fileupload({
                dataType: 'json',
                dropZone: $('.upload-files-dnd', $this),
                pasteZone:null,
                filesContainer: $('.upload-files-table tbody', $this),
                uploadTemplateId:null,            
                downloadTemplateId:null,
                sequentialUploads:true,
                url: $this.data('uploadUrl'),
                type: 'POST',
                formData: function() {
                    return {}
                },
                add: function(e, filedata) {
                    
                    var file = $(
                        '<tr class="inqueue">\
                            <td class="name"></td>\
                            <td class="size"></td>\
                            <td class="status"></td>\
                            <td class="action"><a class="remove">' + lang.t('отменить') + '</a></td>\
                        </tr>');
                    
                    file.find('.name').text(filedata.files[0].name);
                    file.find('.size').text(bytesToSize(filedata.files[0].size));
                    file.find('.status').text(lang.t('готов к загрузке'));
                    file.appendTo($(data.options.filesContainer, $this));
                    
                    file.submitHandler = function(e) { 
                        if (file.data('jqXHR')) return;

                        var jqXHR = filedata.submit(); 
                        file.data('jqXHR', jqXHR);
                    }
                    
                    $(data.options.startUpload).click(file.submitHandler);
                    
                    file.removeHandler = function() {
                        if (file.data('jqXHR')) {
                            file.data('jqXHR').abort();
                        }
                        file.remove();                                
                        
                        filedata.files.splice(0, 1);
                        checkFilesCount();
                        $(data.options.startUpload).unbind('click', file.submitHandler);
                        $(data.options.cancelAll, $this).unbind('click', file.removeHandler);
                    };
                    
                    file.find('.remove').bind('click', file.removeHandler);
                    $(data.options.cancelAll, $this).bind('click', file.removeHandler);
                    
                    filedata.item = file;                            
                    checkFilesCount();
                },
                send: function(e, filedata) {
                    filedata.item.removeClass('inqueue').addClass('uploading');
                    filedata.item.find('.status').html('<div class="progress-bar"><div class="bar"></div><div class="percent">1%</div></div>');
                    $(data.options.startUpload).unbind('click', filedata.item.submitHandler);
                },
                progress: function(e, filedata) {
                    var progress = parseInt(filedata.loaded / filedata.total * 100, 10);
                    filedata.item.find('.bar').css('width', progress + '%');
                    filedata.item.find('.percent').text(progress + '%');
                },
                done: function(e, filedata) {
                    filedata.item.removeClass('uploading');
                    if (filedata.result.items[0].success) {
                        filedata.item.addClass('success');                                
                        filedata.item.find('.status').text(lang.t('Файл успешно загружен'));
                    } else {
                        filedata.item.addClass('fail');
                        filedata.item.find('.status').html(filedata.result.items[0].error);
                        data.has_error = true;
                    }
                },
                fail: function(e, filedata) {
                    filedata.item.removeClass('uploading').addClass('fail');
                    filedata.item.find('.status').text(lang.t('файл не загружен'));
                    data.has_error = true;
                },
                start: function(e) {
                    data.has_error = false;
                    $(data.options.startUpload).addClass('disabled');
                    $.rs.loading.show();
                },
                stop: function(e) {
                    $(data.options.startUpload).removeClass('disabled');
                    $.rs.loading.hide();
                    
                    //Если действие происходит в окне
                    if (crud) {
                        var dialog = $this.closest('.dialog-window');
                        if (dialog.length) {
                            crud._updateTarget($(data.options.startUpload), null, dialog.dialog('option', 'crudOptions'));
                            if (!data.has_error) dialog.dialog('close');                                    
                        }
                    }
                }
            });
            
        };
        
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, args );
        }
    });
    
};