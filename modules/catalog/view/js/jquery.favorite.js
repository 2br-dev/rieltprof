/**
* Плагин, инициализирующий работу функции избранных товаров
*/
(function( $ ){
    /**
    * Инициализирует блок избранное
    */
    $.favorite = function( method ) {
        var defaults = {
            favorite: '#favorite',            //Селектор блока, в котором отображаются товары на странице избранного
            favoriteBlock: '#favoriteBlock',  //Блок "избранное"
            favoriteLink: '.favoriteLink',    //Ссылка на front контроллер
            favoriteCount: '.countFavorite',  //Количество избранных товаров
            favoriteButton: '.favorite',       //Кнопка добавить/удалить избранное
            inFavoriteClass: 'inFavorite',    //Класс указывающий, что товар в избранном
            activeClass: 'active',            //Класс, указывающий, что в избранном есть товары
            context: '[data-id]'
        },
        $this = $('#favoriteBlock');
                
        var data = $this.data('favoriteBlock');
        if (!data) { //Инициализация
            data = {
                options: defaults
            }; 
            $this.data('favoriteBlock', data);
        }
        
        //public
        var methods = {
            /**
            * Иницилизация плагина, отработка событий
            * 
            * @param initoptions - массив с параметрами
            */
            init: function(initoptions) {
                data.options     = $.extend(data.options, initoptions);
                data.options.url = $(data.options.favoriteLink).data('favorite-url');

                $('body').on('click'+data.options.favoriteButton, data.options.favoriteButton, toggleFavorite);
                initFirstState();
                initUpdateTitle();
                $(data.options.favoriteLink).on('click', function() {
                    location.href = $(this).data('href');
                });
            },
            
            /**
            * Добавление товара в избранное
            * 
            * @param integer product_id - id товара
            */
            add: function(product_id) {
                var item = $('[data-id="'+product_id+'"] '+data.options.favoriteButton);
                item.addClass(data.options.inFavoriteClass).each(updateTitle);
                
                var url = data.options.url ? data.options.url : item.closest('[data-favorite-url]').data('favoriteUrl');
                
                $.post(url, {Act: 'add', product_id: product_id}, function(response) {           
                    $(data.options.favoriteCount).html(response.count);
                    checkActive(response.count);
                }, 'json');
                
            },
            
            /**
            * Удаление товара из избранного
            * 
            * @param integer product_id - id товара
            */
            remove: function(product_id) {
                var item = $('[data-id="'+product_id+'"] '+data.options.favoriteButton);
                item.removeClass(data.options.inFavoriteClass).each(updateTitle);                
                
                var is_favorite_page = item.closest(data.options.favorite).length;
                var url = data.options.url ? data.options.url : item.closest('[data-favorite-url]').data('favoriteUrl');
                
                $.post(url, {Act: 'remove', product_id: product_id}, function(response) {                  
                    $(data.options.favoriteCount).html(response.count);
                    checkActive(response.count);                                    
                    
                    $.post(window.location.href, function(response){
                        $(data.options.favorite).replaceWith(response.html);
                        initUpdateTitle();
                    }, 'json');
                    
                }, 'json');
                // если находимся на странице избранного, обновим список
                if (is_favorite_page) {
                    item.closest(data.options.context).css('opacity', 0.5);                    
                }
            }
        };
        
        //private
        /**
        * Добавление/удаление товара в избранное
        */
        var toggleFavorite = function() {
            var product_id = $(this).closest("[data-id]").data('id'),
                guest_id = $.cookie('guest');
                                                                
            if ($(this).hasClass(data.options.inFavoriteClass)) {
                methods.remove(product_id, guest_id);
            } else {
                methods.add(product_id, guest_id);
            }
            return false;
        },
        
        /**
        * Добавляет класс active к блоку избранного, если в нем есть элементы
        */
        checkActive = function(count) {
            $this.toggleClass(data.options.activeClass, count > 0);
        },
        
        /* 
        * Обновляет всплывающую подсказку у кнопки 
        */
        updateTitle = function() {
            var title = $(this).hasClass(data.options.inFavoriteClass) ? $(this).data('alreadyTitle') : $(this).data('title');
            if (typeof(title) != 'undefined') {
                $(this).attr('title', title);
            }            
        },
        /**
        * Инициализируем title у значков в избранное
        */
        initUpdateTitle = function() {
            $(data.options.favoriteButton+'[data-title]').each(updateTitle);
        },

        /**
         * Обновляет состояние кнопки В избранное, при включенном кэшировании
         */
        initFirstState = function() {
            $(data.options.favoriteButton).each(function() {
                if (global.favoriteProducts) {
                    //Устанавливаем значения по умолчанию кнопкам "В избранное" из global.favoriteProducts
                    //Это необходимо при включенном кэшировании блоков
                    var productId = $(this).closest(data.options.context).data('id');
                    if (productId) {
                        var isActive = global.favoriteProducts.indexOf(parseInt(productId)) > -1;
                        $(this).toggleClass(data.options.inFavoriteClass, isActive);
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
    $.favorite();
});