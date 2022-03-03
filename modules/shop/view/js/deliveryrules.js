
/**
* Plugin, активирующий вкладку "характеристики" у товаров
*/
(function($){
    
$.fn.deliveryrules = function(method) {

    var defaults = {}
    var args = arguments;
    
    return this.each(function() {
        var $this = $(this),
            data = $this.data('deliveryrules');
            
        var methods = {
            init: function(initoptions) {
                if (data) return;
                data = {}; $this.data('deliveryrules', data);
                data.options = $.extend({}, defaults, initoptions);
                data.index = 1;
                
                bindEvents();
            },
            addRule: function(values) {
                if (!data) alert('data undefined');
                var _values = $.extend({id: data.index++}, values);
                var rule = $(tmpl('rule-line', _values));
                $('select[data-selected]', rule).each(function(){
                    $(this).val($(this).data('selected'));
                });
                $('input[data-checked]', rule).each(function(){
                    if ($(this).data('checked')) {
                        $(this).prop('checked', true);
                    }
                });

                $('.table-header', $this).show();
                $this.append( rule );
                bindEvents();                
            }
        }
        
        //private
        var bindEvents = function(element) {
            $(".add-rule").off('.rules').on('click.rules', function(){
                methods.addRule();
            });
            $(".delete", $this).off('.rules').on('click.rules', function(){
                $(this).closest('.ruleItem').remove();
            });
        }

        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, args );
        }
    });
}
})(jQuery);

