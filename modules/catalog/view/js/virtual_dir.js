/**
* Plugin, активирующий вкладку "подбор товаров" у категории
*/
(function($){
    $.fn.virtualDir = function(method) {
        var defaults = {
            isVirtualSwitcher: '.is-virtual',
            addProp: '.vt-add-prop',
            removeProp: '.vt-remove-prop',
            addPropIdField: '.vt-add-prop-id',
            fields: '.vt-fields',
            valueContainer: '.vt-props',
            emptyProperty: '.vt-empty',
            propItem: '.vt-props-item'
        }, 
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('virtualDir');
            
            var methods = {
                init: function(initoptions) {                    
                    if (data) return;
                    data = {}; $this.data('virtualDir', data);
                    data.options = $.extend({}, defaults, initoptions); 
                    
                    $this
                        .on('change', data.options.isVirtualSwitcher, toggleVirtual)
                        .on('click', data.options.addProp, addProperty)
                        .on('click', data.options.removeProp, removeProperty);
                        
                }
              
            }
            
            //private 
            var 
                toggleVirtual = function() {
                    $(data.options.fields).toggle($(this).is(':checked'));
                },
                
                addProperty = function() {
                    var prop_id = $(data.options.addPropIdField).val();
                    if ($(data.options.propItem+'[data-id="' + prop_id + '"]').length) {
                        $.messenger('show', {
                            theme: 'error',
                            text: lang.t('Фильтр по такой характеристике уже добавлен')
                        });
                        return false;
                    }
                    
                    $.ajaxQuery({
                        url: $this.data('urls').addPropertyUrl,
                        data: {
                            prop_id: prop_id
                        },
                        success: function(response) {
                            if (response.success) {
                                $(data.options.valueContainer).append( response.html );
                                checkEmpty();
                            }
                        }
                    });
                },
                
                removeProperty = function() {
                    if (confirm(lang.t('Вы действительно хотите удалить фильтр по характеристике?'))) {
                        $(this).closest(data.options.propItem).remove();
                        checkEmpty();
                    }
                },
                
                checkEmpty = function() {
                    var is_empty = $(data.options.valueContainer).children().length == 1;
                    $(data.options.emptyProperty).toggleClass('hidden', !is_empty);
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
    $('#virtualDir').virtualDir();
});