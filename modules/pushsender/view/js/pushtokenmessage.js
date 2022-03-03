(function($){
    /**
    * Плагин, обеспечивающий работу сообщения в виде Push уведомления
    * Зависит от jQuery.selectProduct
    */
    $.fn.pushTokenMessage = function(method)
    {
        var defaults = {
            tokenMessageType: '#messageType select', //Переключатель типа выбора
            messageType: '.ptmType', //Селектор обёртки типа выбора функционала 
            tinyMce: '.tinymce', //Селектор поля TinyMCE
            selectProduct: '.selectProduct', //Кнопка выбора товара
            selectGroup: '.selectGroup', //Кнопка выбора категории
            defaultSelect: null //Выбранные элемент списка сразу
        },
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('push-token-message');
            
            //public
            var methods = {
                init: function(initoptions) {                    
                    if (data) return;
                    data = {}; $this.data('push-token-message', data);
                    data.options = $.extend({}, defaults, initoptions); 
                    
                    $this
                        .on('change', data.options.tokenMessageType, changeType);
                    
                    bindSelectProducts();
                    bindSelectGroup();
                    if (data.options.defaultSelect){
                        $(data.options.tokenMessageType + "option[value='" + data.options.defaultSelect + "']").prop('selected', true);
                        $(data.options.tokenMessageType).trigger('change');
                    }
                }
            };
            
            //private
            /**
            * Вызывает диалоговое окно с выбором товара и при выборе сохраняет значение в скрытом поле
            * 
            */
            var bindSelectProducts = function() {
                $this.selectProduct({
                    dialog: 'cartProductDialog',
                    startButton: data.options.selectProduct,
                    selectButtonText: false,
                    onSelectProduct:function(params) {
                        var element = params.openDialogEvent.target;
                        $(element).text( params.productTitle + ' (' + params.productBarcode + ')' );
                        var inputHidden = $(element).closest(data.options.messageType).find('input[name="'+ $(element).data('name') +'"]');
                        inputHidden.val(params.productId);
                        params.dialog.dialog('close');
                    }
                });                
            },
            
            /**
            * Вызывает диалоговое окно с выбором категории и при выборе сохраняет значение в скрытом поле
            * 
            */
            bindSelectGroup = function() {
                $this.selectProduct({
                    dialog: 'cartGroupDialog',
                    startButton: data.options.selectGroup,
                    selectButtonText: lang.t('Выбрать группу'),
                    onResult:function(params) {
                        var element = params.openDialogEvent.target;
                        var selectItem = $('#cartGroupDialog .admin-category .act');
                        var catTitle = selectItem.text();
                        var catId = selectItem.closest('[qid]').attr('qid');
                        var inputHidden = $(element).closest(data.options.messageType).find('input[name="'+ $(element).data('name') +'"]');
                        inputHidden.val(catId);
                        $(element).text(catTitle);
                    }
                });
            },
            
            /**
            * Смена типа отправляемого сообщения
            * 
            */
            changeType = function() {
                $(data.options.messageType, $this).removeClass('show');
                var target = "#message" + $(this).val();
                $(target, $this).addClass('show');
                $(target + " " + data.options.tinyMce, $this).trigger('became-visible');
                $this.trigger('contentSizeChanged');
            };
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }
    
})(jQuery);