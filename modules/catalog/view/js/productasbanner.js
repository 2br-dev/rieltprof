/**
* Плагин, инициализирующий показ товаров в виде баннера
*/
(function( $ ){
    $.fn.productsAsBanner = function( method ) {
        var defaults = {
            autoPlayDelay: 5000,
            categorySelector: '.advList a',
            mainImage: '.picture img',
            categorSelectorParent: 'li',
            categoryActClass: 'act',
            wrapperContainer: '.wrapperContainer',
            viewContainer: '.viewContainer',
            newViewContainerClass: 'newViewContainer',
            nextSelector: '.next',
            prevSelector: '.prev',
            startCoorX: 0, //Координа по X для перелистывания
            endCoorX: 0 
        },
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('productsAsBanner');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('productsAsBanner', data);
                    data.options = $.extend({}, defaults, initoptions);
                    
                    $this
                        .on('click', data.options.categorySelector, changeCategory)
                        .on('click', data.options.nextSelector, prevNext)
                        .on('click', data.options.prevSelector, prevNext);
                        
                    //События по нажитию и протягиванию пальцем на экране, только для планшетов и телефонов
                    /*if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
                         $('.viewContainer',$this)
                            .on('touchstart', onTouchStart)
                            .on('touchmove', onTouchMove)
                            .on('touchend', onTouchEnd);
                    }*/
                        
                    //Автопроигрывание
                    data.autoplaySelector = data.options.nextSelector;
                    if (data.options.autoPlayDelay && data.timer !== false) {
                        data.timer = setInterval(function() {
                            var button = $(data.autoplaySelector, $this);
                            if (button.length) {
                                prevNext.call(button.get(0), {}, true);
                            } else {
                                //Перелистываем в другую сторону
                                data.autoplaySelector = data.autoplaySelector == data.options.nextSelector ? data.options.prevSelector : data.options.nextSelector;
                            }
                        }, data.options.autoPlayDelay);
                    }
                },
                
                update: function(params) {
                    $.post($this.data('blockUrl'), params,  function(response) {
                        if (!params.item) params.item = 0;
                        
                        if (typeof(response.total) != 'undefined' ) {
                            $this.data('total', response.total);
                        }
                        
                        var newBanner = $(response.html).addClass(data.options.newViewContainerClass).css('opacity', 0);
                        newBanner.find(data.options.mainImage).load(function() {
                            
                            $(data.options.wrapperContainer, $this).height( newBanner.height() );
                            newBanner.animate({ opacity: 1}, function() {
                                $(data.options.viewContainer+':not(".'+data.options.newViewContainerClass+'")', $this).remove();
                                newBanner.removeClass(data.options.newViewContainerClass);
                                $this.data('item', params.item);
                                $(data.options.wrapperContainer, $this).height( 'auto' );
                            });
                        });
                        $(data.options.wrapperContainer, $this).append( newBanner );

                    }, 'json');
                }
            }
            
            var prevNext = function(e, autoClick) {
                    methods.update($(this).data('params'));
                    if (!autoClick) {
                        clearInterval(data.timer); //Останавливаем проигрывание, если пользователь нажал на стрелку
                    }
                },
                
            changeCategory = function() {
                $('.'+data.options.categoryActClass, $this).removeClass(data.options.categoryActClass);
                $(this).closest(data.options.categorSelectorParent).addClass(data.options.categoryActClass);
                methods.update( $(this).data('params') );
            },
            /**
            * Действие при начале протягивания пальцем
            * @param event - событие
            */
            onTouchStart = function (event){
               if (event.type == "touchstart"){
                   //Получим координату X
                   var touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0]; //объект касания
                   data.options.startCoorX = touch.clientX; 
               } 
            },
            /**
            * Действие при протягивании пальцем
            * @param event - событие
            */
            onTouchMove = function (event){
               if (event.type == "touchmove"){
                   if (defaults.preventDefaultEvents)
                        event.preventDefault();
                   //Получим координату X
                   var touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0]; //объект касания
                   data.options.endCoorX = touch.clientX;
                   
               } 
            },
            /**
            * Действие при окончании протягивания пальцем
            * @param event - событие
            */
            onTouchEnd = function (event){
               if (event.type == "touchend"){
                   delta =  data.options.startCoorX - data.options.endCoorX;
                   if (delta>0 && delta>40){ //Если вправо пролистываем
                      $(data.options.nextSelector).click();   
    
                   }else if(delta<0 && delta<-40){ //Если влево пролистываем
                      $(data.options.prevSelector).click();   
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

})( jQuery );