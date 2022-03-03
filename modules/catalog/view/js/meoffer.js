
/**
* Plugin, активирующий вкладку "Комплектации" у товаров при мультиредактировании товара
*/
(function($){
    
$.fn.offerme = function(method) {

    var defaults = {  
        
        //Параметры для многомерных комплектаций
        useMultiOffer  : '#use-multiofferME',      //Галка включающая многомерные компл. 
        multiCheckWrap : '#multi-check-wrapME',     //Обораживающий контейнер с галкой включения мн. компл.
        multiOfferName : '_offers_[levels]',      //Атрибут name у уровня многомерной комплектации
        multiOfferWrap : '.multioffer-wrapME',     //Оборачивающий общий котейнер
        offersBody     : '.offers-bodyME',         //Контейнер со всеми комплектациями
        addLevel       : '.add-levelME',           //Кнопка добавить уровень мн. компл. 
        deleteLevel    : '.delete-levelME',        //Кнопка удалить уровень комплектации
        isPhotoBlock   : '.is_photo',             //Блок с радиокнопкой "С фото"
        rowMO          : '.lineME'                 //Строка уровнем компл.
    },
    num  = 1, 
    args = arguments;
    
    return this.each(function() {
        var $this = $(this),
            data = $this.data('offerme');
        
        var methods = {
            /**
            * Инициализация, назначение действий
            * 
            * @param initoptions
            */
            init: function(initoptions) {
                if (data) return;
                data = {}; $this.data('offerme', data);
                data.options = $.extend({}, defaults, initoptions); 
                
                
                //Многомерные комплектации
                $(data.options.multiOfferWrap,$this)
                                    .on('click',data.options.addLevel,methods.addMultiOfferLevel)                            //Добавить уровень комплектаций
                                    .on('click',data.options.deleteLevel,methods.delMultiOfferLevel)                         //Добавить уровень комплектаций
                                    .on('change','select[name^="'+data.options.multiOfferName+'"]',onMultiOfferLevelChange); //Событие на изменение уровня многомерной комплектации
                                     
                $(data.options.useMultiOffer,$this).on('click',showMOffers);
                $(data.options.multiOfferWrap,$this).hide(); 
                checkMODelete();
                $(data.options.addLevel,$this).show();
            },
            
            
            //Многомерные комплектации
           
            /**
            * Добавляет уровень многомерной комплектации
            * 
            */
            addMultiOfferLevel: function (){
               //Клонируем строку уровня 
               var offerLevel = $(tmpl('multioffer-lineme', {}));
               $(data.options.multiOfferWrap+" "+data.options.offersBody,$this).append(offerLevel);
               
               remakeMONames();               //Переформирование имен в нужном порядке 
               checkMODelete();
               var i, useIds = {}; //Массив с уже выбранными характеристиками
               //Если у нас окажется, что нет выбранных элементов списке, прямо укажем их
               $(data.options.multiOfferWrap+" "+data.options.rowMO+" select").each(function(){
                   i = 0;
                  var value = $('option:selected',$(this)).val();
                  if (!useIds[value]){
                     useIds[value] = true;   
                  } else {
                     sel = false; 
                     while(!sel){
                         value = $('option:eq('+i+')',$(this)).val();  
                         if (typeof(value) == 'undefined' ) break;
                         
                         if (!useIds[value]){ //Если у нас такой не выбран ещё
                            useIds[value] = true; 
                            $('option:eq('+i+')',$(this)).prop('selected',true);  
                            sel=true;
                         }
                         i++; 
                     }  
                  }
                      
                   $(this).change();
               });
               return false;
            },
            
            /**
            * Удаляет уровень многомерной комплектации
            * 
            */
            delMultiOfferLevel: function (){ 
               var wrap = $(this).closest(data.options.rowMO);
               $(wrap).remove();
               $.messenger('hideAll');
               
               remakeMONames();
               checkMODelete();
               return false;
            }
            
            
        }
        
        //private
        //Многомерные комплектации
        /**
        * Переформировывает атрибут name у уровней комплектаций
        * Проставляет name по по порядку
        */
        var remakeMONames = function(){    
           var cnt       = $(data.options.multiOfferWrap+" "+data.options.rowMO,$this).length; 

           //Флаг с фото
           $(data.options.multiOfferWrap+" "+data.options.rowMO+" input[type='radio']",$this).each(function(i){
               $(this).val(i+1);
           });
           //Название многомерной комплектации
           $(data.options.multiOfferWrap+" "+data.options.rowMO+" input[type='text']",$this).each(function(i){
               $(this).attr('name',data.options.multiOfferName + '['+i+'][title]');
           });
           //Выпадающий список многомерной комплектации
           $(data.options.multiOfferWrap+" "+data.options.rowMO+" select",$this).each(function(i){
               $(this).attr('name',data.options.multiOfferName + '['+i+'][prop]');
           });
           
           $(data.options.multiOfferWrap+" "+data.options.rowMO,$this).removeClass('error');
        },
        
        /**
        * Показывает окно многомерных комплектаций
        * Клик на галочке мн. компл.
        */
        showMOffers = function(){
            openMOffers();
            var cnt       = $(data.options.multiOfferWrap+" "+data.options.rowMO,$this).length; 
            if (cnt==0){
                methods.addMultiOfferLevel(); //Добавим нулевой уровень если требуется
            }
            if ($(this).prop('checked')){
               $(data.options.multiOfferWrap,$this).show(); 
            }else{
               $(data.options.multiOfferWrap,$this).hide();  
            }
            
            $(data.options.addLevel,$this).show();
        },
        
        /**
        * Проверяет право на удаление многомерных комплектаций
        */
        checkMODelete = function(){
            var cnt       = $(data.options.multiOfferWrap+" "+data.options.rowMO,$this).length; 
            if (cnt>1){
                $(data.options.multiOfferWrap+" "+data.options.deleteLevel,$this).show();
            }else{
                $(data.options.multiOfferWrap+" "+data.options.deleteLevel,$this).hide();
            }
            return false;
        },
        
        /**
        * Событие при изменении выбора в селекторе уровня многомерных компл.
        * 
        */
        onMultiOfferLevelChange = function (){
            $(this).data('prop-id',$(this).val()); //Выставляем доп информацию
            var parent = $(this).closest(data.options.rowMO);
            $(data.options.isPhotoBlock+" input",parent).attr('value',$(this).val());
        },
        
        /**
        * Открывает окно многомерных комплектаций
        */
        openMOffers = function(){
            $(data.options.multiOfferWrap,$this).show();
            $(this).hide();
            $(data.options.addLevel,$this).show();
            return false;
        },
        /**
        * Сворачивает окно многомерных комплектаций
        */
        closeMOffers = function(){
            $(data.options.multiOfferWrap,$this).hide();
            return false;
        }
          
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, args );
        }
    });
}
})(jQuery);