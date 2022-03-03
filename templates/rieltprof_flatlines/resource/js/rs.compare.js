/**
* Плагин, инициализирующий работу функции сравнения товаров
* @author ReadyScript lab.
*/
(function( $ ){
    /**
    * Инициализирует блок сравнения товаров
    */
    $.compareBlock = function( method ) {
        var defaults = {
            compareBlock: '.rs-compare-block',
            compareButton:'a.rs-compare',
            compareItemsCount: '.rs-compare-items-count',
            activeCompareClass: 'rs-in-compare',
            ulCompare:'.rs-compare-products',
            doCompare:'.rs-do-compare',
            removeItem:'.rs-remove',
            removeAll: '.rs-remove-all'
        },
        $this = $('.rs-compare-block:first');
        
        if (!$this.length) {
            console.log('element .rs-compare-block not found');
            return;    
        }
        
        var data = $this.data('compareBlock');
        if (!data) { //Инициализация
            data = {
                options: defaults
            }; 
            $this.data('compareBlock', data);
        }
        
        //public
        var methods = {
            init: function(initoptions) {
                data.options.url = $this.data('compareUrl');
                data.options = $.extend(data.options, initoptions);
                data.context = $(data.options.compareBlock);
                $('body')
                    .on('click.compare', data.options.compareButton, toggleCompare)
                    .on('click.compare', data.options.doCompare, methods.compare)
                
                data.context
                    .on('click', data.options.removeItem, removeItem)
                    .on('click.compare', data.options.removeAll, methods.removeAll);
                
                initUpdateTitle();
            },
            
            add: function(product_id) {
                $('[data-id="'+product_id+'"] '+data.options.compareButton).addClass(data.options.activeCompareClass).each(updateTitle);
                $.post(data.options.url.add, {id: product_id}, function(response) {
                   $(data.options.ulCompare).html(response.html);

                   data.context.each(function() {
                       if (!$(this).is(':visible')) {
                            $(this).slideDown();
                       }
                   });
                   $(data.options.compareItemsCount).text(response.total);
                   $this.toggleClass('active', response.total>0);
                   
                }, 'json');
            },
            
            remove: function(product_id) {
                $('[data-id="'+product_id+'"] '+data.options.compareButton).removeClass(data.options.activeCompareClass).each(updateTitle);
                var item = $('[data-compare-id="'+product_id+'"]', data.context).css('opacity', 0.5);
                $.post(data.options.url.remove, {id: product_id}, function(response) {
                    if (response.success) {
                        methods.removeVisual(product_id, response);
                    }
                }, 'json');
            },
            
            removeVisual: function(product_id, response) {
                $('[data-id="'+product_id+'"] '+data.options.compareButton).removeClass(data.options.activeCompareClass).each(updateTitle);
                var item = $('[data-compare-id="'+product_id+'"]', data.context).css('opacity', 0.5);
                $(data.options.compareItemsCount).text(response.total);
                $this.toggleClass('active', response.total>0);
                if (response.total) {
                    item.remove();    
                }
            },
            removeAll: function() {
                $.post(data.options.url.removeAll, function(response) {
                    if (response.success) {
                        $(data.options.compareButton).removeClass(data.options.activeCompareClass).each(updateTitle);
                        data.context.fadeOut(function() {
                            $(data.options.compareItemsCount).text(0);
                            $this.toggleClass('active', false);
                        });
                    }
                }, 'json');
                
                return false;
            },
            compare: function() {
                if ($(this).closest(data.options.compareBlock).is('.active')) {
                    window.open(data.options.url.compare, 'compare', 'top=170, left=100, scrollbars=yes, menubar=yes, resizable=yes');
                }
                return false;
            }
        };
        
        //private
        var toggleCompare = function() {
            var id = $(this).closest("[data-id]").data('id');
            
            if ($(this).hasClass(data.options.activeCompareClass)) {
                methods.remove( id );
            } else {
                methods.add( id );
            }
            return false;
        },
        
        removeItem = function() {
            var id = $(this).closest('[data-compare-id]').data('compareId');
            methods.remove(id);
        };
        
        /*
         * Обновляет всплывающую подсказку у кнопки
         */
        updateTitle = function() {
            var title = $(this).hasClass(data.options.activeCompareClass) ? $(this).data('alreadyTitle') : $(this).data('title');
            if (typeof(title) != 'undefined') {
                $(this).attr('title', title);
            }
        },
        /**
         * Инициализируем title у значков "сравнить"
         */
        initUpdateTitle = function() {
            $(data.options.compareButton+'[data-title]').each(updateTitle);
        }
  
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        }
    }


})( jQuery );


$(function() {
    $.compareBlock();
});