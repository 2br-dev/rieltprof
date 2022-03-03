(function($){
    $.fn.templateManager = function( method ) {  
        var defaults = {
            tools: {
                rename: '.tools .rename',
                remove: '.tools .delete'
            },
            makedir: '.makedir',
            fileListContainer: '.file-list-container',
            fileList: '.file-list',
            fileListItems: '.file-list > li',
        }, 
        args = arguments;

        return this.each(function() {
            var $this = $(this), 
                data = $this.data('templateManager');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('templateManager', data);
                    data.options = $.extend({}, defaults, initoptions);

                    $(data.options.makedir).on('click.tm', onMakeDir);
                    $(data.options.tools.rename, $this).on('click.tm', onRename);
                    $(data.options.tools.remove, $this).on('click.tm', onDelete);

                    if ($.fn.lightGallery) {
                        $(data.options.fileList).lightGallery({
                            selector: "a[rel='lightbox-image-tour']",
                            thumbnail: false,
                            autoplay: false,
                            autoplayControls: false
                        });
                    }
                },
                
            }
            
            //private
            var getCurrentFolder = function() {
                return $(data.options.fileListContainer, $this).data('currentFolder') 
            },
            
            onDelete = function() {
                var crudOptions = $(this).data('crudOptions');
                var item = $(this).closest('.item');
                var _this = this;
                if (item.hasClass('file')) {
                    var item_str = lang.t('файл')+' `'+item.data('name')+'`';
                } else {
                    var item_str = lang.t('папку')+' `'+item.data('name')+'`';
                }
                
                if (confirm(lang.t('Вы действительно хотите удалить %what?', {what: item_str}))) {
                    var url = $(this).attr('href');
                    $.ajaxQuery({
                        url: url,
                        success: function() {
                            updateTarget(crudOptions, _this);
                        }
                    });
                }
                return false;
            },
            
            onRename = function() {
                var crudOptions = $(this).data('crudOptions');
                var oldValue = $(this).data('oldValue');
                var url = $(this).data('url');
                var path = $(this).closest('[data-path]').data('path');
                var _this = this;

                var new_filename = prompt(lang.t('Введите новое имя'), oldValue);
                
                if (new_filename !== null) {
                    $.ajaxQuery({
                        url: url,
                        data: {
                            path: path,
                            new_filename: new_filename
                        },
                        success: function() {
                            updateTarget(crudOptions, _this);
                        }
                    });
                }

                return false;
            },
            
            onMakeDir = function() {
                var crudOptions = $(this).data('crudOptions');
                var url = $(this).data('url');
                var name = prompt(lang.t('Введите имя папки'), lang.t('Новая папка'));
                var _this = this;

                if (name !== null) {
                    $.ajaxQuery({
                        url: url,
                        data: {
                            name: name
                        },
                        success: function() {
                            updateTarget(crudOptions, _this);
                        }
                    });
                }

                return false;
            },
            
            updateTarget = function(crudOptions, element) {
                var element = crudOptions && crudOptions.updateElement ? crudOptions.updateElement : element;
                var ajaxParam = (crudOptions && crudOptions.ajaxParam) ? crudOptions.ajaxParam : null;
                $.rs.updatable.updateTarget(element, null, null, ajaxParam);
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
    $('.tmanager').templateManager();
});