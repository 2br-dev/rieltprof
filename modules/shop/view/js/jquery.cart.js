(function( $ ){
    /**
    * Инициализирует функцию добавления товаров в корзину, отображение общего числа товаров в корзине
    * Зависит от common.js
    */
    $.cart = function( method ) {
        var defaults = {
            addToCart: '.addToCart',
            context: '[data-id]',
            offerFormName: 'offer'
        },
        $this = $('#cart');
        
        if (!$this.length) {
            console.log('element #cart not found');
            return;    
        }
        
        var data = $this.data('cart');
        if (!data) { //Инициализация
            data = { options: defaults };
            $this.data('cart', data);
        }
        
        //public
        var methods = {
            init: function(initoptions) {
                data.options.url = $this.data('compareUrl');
                data.options = $.extend(data.options, initoptions);
                $(data.options.addToCart).on('click.cart', addToCart);
            },
            
            /**
            * Добавляет товар в корзину
            * 
            * @param url - ссылка на добавление товара в корзину
            * @param offer - номер комплектации 0,1,2...
            */
            add: function(url, offer) {
                $.openDialog()
                
            }
        };
        
        //private
        var addToCart = function() {
            var context = $(this).closest(data.options.context);
            var offer = $('[name="'+data.options.offerFormName+'"]:checked', context).val();
            
            methods.add($(this).attr('href'), (offer) ? offer : 0);
            return false;
        }
  
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        }       
    }
})( jQuery );


$(function() {
    $.cart();
});