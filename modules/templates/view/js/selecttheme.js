(function($){
    $.fn.selectTheme = function( method ) {  
        var defaults = {
            justSelect: false,
            themeBlock: '#theme-block',
            errorBlock: '.form-error',
            theme: '.theme',
            theme_selector: '.set-this',
            uploadForm: '.uploadTheme',
            uploadButton: '.upload-theme-file',
            mpContainer:'.theme-container.mp',
            fileinput: '#theme-file',
            img: '.image',
            colorContainer: '.colors',
            colorItem: '.colors .item',
            handler: '#selectTheme'
        }, 
        args = arguments;

        return this.each(function() {
            var $this = $(this), 
                context,
                data = $this.data('selectTheme');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('selectTheme', data);
                    data.options = $.extend({}, defaults, initoptions);
                   $(data.options.handler).click(showDialog);
                },
                
            }
            
            //private
            var initEvents = function() {
                $('#templateManager .canselect').click(selectFile);
            },

            showDialog = function() {
                $(data.options.errorBlock, context).empty();
                $.rs.openDialog({
                    dialogOptions: {
                        dialogClass: 'themeManager',
                        width:900,
                        height:800
                    },
                    dialogId: 'themeManager',
                    url: data.options.dialogUrl,
                    afterOpen: initContent
                });
            },
            
            initContent = function(dialog) {
                context = dialog;
                if (!data.isInitialized) {
                    context
                        .on('click', data.options.colorItem, changeShade)
                        .on('change', data.options.fileinput, selectFile)
                        .on('click', data.options.theme_selector, selectTheme);

                    var initMarketplaceThemes = function() {
                        //Загружаем модули из Marketplace
                        var loadMpThemesUrl = $(data.options.mpContainer, context).data('url');
                        $.ajaxQuery({
                            url: loadMpThemesUrl,
                            success: function (response) {
                                var mpContainer = $(data.options.mpContainer, context)
                                    .html(response.html)
                                    .find(data.options.colorItem)
                                    .click(changeShade);
                            }
                        });
                    };

                    initMarketplaceThemes();
                    context.on('new-content', function() {
                        initMarketplaceThemes(); //Инициализируем повторно, если произошла загрузка новой темы
                    });

                    data.isInitialized = true;
                }
            },
            
            changeShade = function() {
                var $theme = $(this).closest(data.options.theme);
                var $image = $(data.options.img, $theme);
                var src = $(this).data('previewUrl');
                $(data.options.colorItem, $theme).removeClass('act');
                $(this).addClass('act');
                $image.attr('src', src);
                return false;
            },
            
            disableUploadButton = function() {
                $(data.options.uploadButton, context).addClass('disabled');
                $(data.options.fileinput, context).hide();                
            },
            
            enableUploadButton = function() {
                $(data.options.uploadButton, context).removeClass('disabled');
                $(data.options.fileinput, context).show();                
            },
            
            selectFile = function() {
                //Делаем кнопку неактивной
                disableUploadButton();
                $(data.options.errorBlock, context).empty();
                $.rs.loading.show();
                
                $(data.options.uploadForm, context).ajaxSubmit({
                    dataType:'json',
                    data: {
                        ajax:1
                    },
                    success: function(response, status, hxr, $form) {
                        $.rs.loading.hide();
                        enableUploadButton();
                        if (response.success) {
                            $.rs.updatable.updateTarget($form, null, null, {noUpdateHash: true});
                        } else {
                            $(data.options.errorBlock, context).fillError(response.formdata.errors);
                        }
                    },
                    error: function() {
                        loading.error();
                    }
                    
                });
            },
            
            selectTheme = function() {
                var theme = $(this).closest('.theme');

                var theme_id = theme.data('themeId');
                var shade_id = $('.act[data-shade-id]', theme).data('shadeId');
                if (typeof(shade_id) != 'undefined') {
                    var str = theme_id+'('+shade_id+')';
                } else {
                    var str = theme_id;
                }                

                var success = function(str) {
                    $this.val(str);
                    context.dialog('close');
                }                
                                
                    
                if (data.options.justSelect) {
                    success(str);
                } else {                
                    if (confirm(lang.t('Вместе с темой будет загружена информация о блоках. Вы действительно хотите сменить тему?'))) {
                        $(data.options.errorBlock, context).empty();                    
                            $.ajaxQuery({
                                url: data.options.setThemeUrl,
                                data: {
                                    theme: str
                                },
                                success: function(response) {
                                    if (response.success) {
                                        success(str);
                                    } else {
                                        $(data.options.errorBlock, context).fillError(response.formdata.errors);
                                    }
                                }
                            });
                        

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
})(jQuery);