/**
* Плагин, инициализирующий работу функции сравнения товаров
*/
(function( $ ){
    /**
    * Инициализирует блок сравнения товаров
    */
    $.compareBlock = function( method ) {
        var defaults = {
            compareBlock: '#compareBlock',           //Блок с отображением товаров для сравнения  
            compareButton:'a.compare',               //Кнопка сравнить
            compareItemsCountWrapper: '.compareTopBlock', //Элемент, оборачивающий информацию о количестве товаров в сравнении
            compareItemsCount: '.compareItemsCount', //Элемент с количеством товаров в сравнении
            activeCompareClass: 'inCompare',         //Класс указывающий, что товар в сравнении
            ulCompare:'.compareProducts',            //Список товаров в сравнении
            doCompare:'.doCompare',                  //Кнопка открытия отдельного окна сравнения товаров
            removeItem:'.remove',                    //Кнопка удаления товара из сравнения
            removeAll: '.removeAll'                  //Кнопка удаления всех товаров из сравнения
        },
        $this = $('#compareBlock');
        
        if (!$this.length) {
            console.log('element #compareBlock not found');
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

                initFirstState();
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
                   $this.add(data.options.compareItemsCountWrapper).toggleClass('active', response.total>0);
                   
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
                $this.add(data.options.compareItemsCountWrapper).toggleClass('active', response.total>0);
                if (response.total) {
                    item.remove();    
                } else {
                    data.context.slideUp(function() {
                        item.remove();
                    });
                }                                
            },
            removeAll: function() {
                $.post(data.options.url.removeAll, function(response) {
                    if (response.success) {
                        $(data.options.compareButton).removeClass(data.options.activeCompareClass);
                        data.context.fadeOut(function() {
                            $(data.options.compareItemsCount).text(0);
                            $this.add(data.options.compareItemsCountWrapper).toggleClass('active', false);
                        });
                    }                         
                }, 'json');
                
                return false;
            },
            compare: function() {
                var url = $(this).attr('href');
                if ($(this).closest(data.options.compareBlock).is('.active') || $(this).is('.active') || url) {
                    window.open(url ? url : data.options.url.compare, 'compare', 'top=170, left=100, scrollbars=yes, menubar=yes, resizable=yes');
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
        
        checkEmpty = function() {
            if (!$(data.options.ulCompare).children().length) {
                $this.slideUp();
            } else {
                if (!$this.is(':visible')) {
                    $this.slideDown();
                }
            }            
        },
        
        removeItem = function() {
            var id = $(this).closest('[data-compare-id]').data('compareId');
            methods.remove(id);
        },
        
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
        * Инициализируем title у значков сравнить
        */
        initUpdateTitle = function() {
            $(data.options.compareButton + '[data-title]').each(updateTitle);
        },

        /**
         * Обновляет состояние кнопки В избранное, при включенном кэшировании
         */
        initFirstState = function() {
            $(data.options.compareButton).each(function() {
                if (global.compareProducts) {
                    //Устанавливаем значения по умолчанию кнопкам "В избранное" из global.favoriteProducts
                    //Это необходимо при включенном кэшировании блоков
                    var productId = $(this).closest("[data-id]").data('id');
                    if (productId) {
                        var isActive = global.compareProducts.indexOf(parseInt(productId)) > -1;
                        $(this).toggleClass(data.options.activeCompareClass, isActive);
                    }
                }
            });
        };
        
  
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