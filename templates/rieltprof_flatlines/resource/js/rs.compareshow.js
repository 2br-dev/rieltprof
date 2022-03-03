(function( $ ){

    /**
    * Активирует функции на странице сравнения товаров
    */
    $.compareShow = function( method ) {
        var defaults = {
            removeButton: '.rs-remove',
            imageContainer: '.rs-active-list',
            printButton: '.rs-print'
        },
        $this = $('.rs-compare-show:first');
        
        var data = $this.data('compareShow');
        if (!data) { //Инициализация
            data = {
                options: defaults
            }; 
            $this.data('compareShow', data);
        }
        
        //public
        var methods = {
            init: function(initoptions) {
                data.options.url = $this.data('compareUrl');
                data.options = $.extend(data.options, initoptions);

                $(data.options.imageContainer+' img', $this).on('click', nextPhoto);
                $(data.options.removeButton, $this).on('click', removeItem);
                $(data.options.printButton, $this).on('click', methods.printPage);
            },
            
            remove: function(product_id) {
                $.post(data.options.url.remove, {id:product_id}, function(response) {
                    $('[data-id="'+product_id+'"]', $this).remove();
                    if ( $('[data-id]', $this).length == 0 ) methods.close();
                    
                    //Обновляем блок у родительского окна
                    try {
                        window.opener.jQuery.compareBlock('removeVisual', product_id, response);
                    } catch(e) {};
                }, 'json');
                return false;
            },
            
            printPage: function() {
                if (typeof(window.print)!='undefined') { 
                    window.print(); 
                }  
            },
            
            close: function() {
                window.close();
            }
        };
        
        //private
        var removeItem = function() {
            var id = $(this).closest('[data-id]').data('id');    
            methods.remove(id);
        },
        
        nextPhoto = function() {
            var next = $(this).next('img');
            if (!next.length) next = $(this).parent().find('img:first');
            $(this).addClass('hidden');
            next.removeClass('hidden');
        }
        
  
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        }       
    }
})( jQuery );


$(function() {
    $.compareShow();
});