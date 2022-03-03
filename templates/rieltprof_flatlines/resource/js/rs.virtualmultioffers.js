/**
* Плагин, инициализирующий работу виртуальных многоменых комплектаций
*/
(function( $ ){
    $.fn.virtualMultioffers = function( method ) {
        var defaults = {
            target                 : '#updateProduct',                //Селектор блока, в котором заменяются данные о товаре
            virtualMultiOfferName  : '[name^="multioffers["]', //Селектор элементов с выбором значения многомернойй комплектации
            hiddenOffersName       : '[name="hidden_multioffers"]',   // Комплектации с информацией
            context                : '[data-id]',                     //Селектор оборачивающий товар
            disabled               : 'disabled',                      //Селектор означающий, что значение отключено
            notAvaliable           : 'rs-not-avaliable',                  //Селектор означающий, что товар нельзя приобрести
            virtualMultiOffersInfo : [], //Собранная информауция
        },
        args = arguments,
        timer;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('virtualMultioffers');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data                    = {}; $this.data('virtualMultioffers', data);
                    data.options            = $.extend({}, defaults, initoptions);
                    
                    
                    //Соберём информацию для работы
                    $(data.options.hiddenOffersName, $this).each(function(i){
                        data.options.virtualMultiOffersInfo[i]           = {};
                        data.options.virtualMultiOffersInfo[i]['id']     = this;
                        data.options.virtualMultiOffersInfo[i]['url']    = $(this).data('url');
                        data.options.virtualMultiOffersInfo[i]['info']   = $(this).data('info');
                    });
                    
                    $(window).on('popstate', returnToProductPage); //Функция возврата на предыдущую страницу по ajax через браузер
                    hideNotUsedVariants(); //Спрячем не нужное
                                                             
                    bindChanges();                         
                }
            };        
            
            //private 
            /**
            * Смена виртуальной многомерной коплектации
            */
            var changeVirtualMultiOffers = function() {
                
                var selected          = getSelectedValues(); //Получим выбранное
                var virtualMultiOffer = getSelectedVirtualMultioffer(selected);//Получим данные для выбранного
                
                if (virtualMultiOffer){ //Если найдено, то переключим товар
                    refreshProduct(virtualMultiOffer, true);
                }else{
                    hideNotUsedVariants(); //Спрячем не нужное
                    //Отметим значение, что нельзя купить
                    $(data.options.target).addClass(data.options.notAvaliable);
                }
            };
            
            /**
            * Возвращает массив выбранных значений
            * 
            * @returns {Array}
            */
            var getSelectedValues = function()
            {
                var context = $(data.options.target);
                var selected = []; //Массив, что выбрано
                //Соберём информацию, что изменилось
                $("select" + data.options.virtualMultiOfferName, context).each(function(){
                    selected.push({
                        'title': $(this).data('propTitle'),
                        'value': $(this).val()
                    });
                });
                
                $("input" + data.options.virtualMultiOfferName + ":checked", context).each(function(){
                    selected.push({
                        'title': $(this).data('propTitle'),
                        'value': $(this).val()
                    });
                });
                return selected;
            }
            
            /**
            * Прячет неиспользуемые значения виртуальных многомерных комплектаций
            * 
            */
            var hideNotUsedVariants = function ()
            {
                var context    = $(data.options.target); //Контекст
                var input_info = data.options.virtualMultiOffersInfo;
                
                //Разрешим всё по уиолчанию
                $(data.options.virtualMultiOfferName, context).each(function(){
                    $(this).prop('disabled', false).removeClass('disabled');
                });
                
                
                //Поищем что можно запретить
                var selected      = getSelectedValues(); //Получим выбранное
                var check_selected = []; //Массив для проверки
                var count_found    = 0; //Число совпадений которое должно сойтись
                $.each(selected, function (key, values){
                    count_found++;
                    if (key>0){ //Исключим значения для первого параметра
                        var prop_title = values['title'];
                        //Получим варианты тоьлко для нужного ключа
                        item_variants  = $(data.options.virtualMultiOfferName+'[data-prop-title="'+prop_title+'"]', context);
                        
                        //Переберём варианты для поиска
                        $(item_variants).each(function (){
                            check_selected[key]          = {};
                            check_selected[key]['title'] = $(this).data('propTitle');
                            check_selected[key]['value'] = $(this).val();
                            
                            var offer = false; //Cпрятанная комплектация, которую мы выбрали
                
                            for(var j=0;j<input_info.length;j++){
                                var info = input_info[j]['info']; //Группа с информацией
                                var found = 0;                //Флаг, что найдены все совпадения
                                $.each(info, function(k, val){
                                    for(var i=0;i<check_selected.length;i++){
                                       if ((check_selected[i]['title']==k)&&(check_selected[i]['value']==val)){
                                           found++;
                                       } 
                                    }
                                    if (found==count_found){ //Если удалось найди совпадение, то выходим
                                        offer = input_info[j]['id']
                                        return false;
                                    }
                                });  
                            }
                            if (!offer){
                                //Посмотрим какой тип выбора
                                var tagName = $(this).prop("tagName");
                                if (tagName!='SELECT'){
                                   $(this).prop('disabled', true).addClass('disabled'); 
                                }
                            }
                        });
                        
                        
                        check_selected[key]          = {};
                        check_selected[key]['title'] = values['title'];
                        check_selected[key]['value'] = values['value'];
                    }else{
                        check_selected[key]          = {};
                        check_selected[key]['title'] = values['title'];
                        check_selected[key]['value'] = values['value'];
                    }          
                });
                
                
                
                
            }
            
            /**
            * Обновляет страницу с товаром
            * 
            * @param object virtualMultiOffer - выбранная многомерная комплектация
            * @param boolean pushHistory - выбранная многомерная комплектация
            */
            var refreshProduct = function(virtualMultiOffer, pushHistory)
            {
                $(data.options.target).css('opacity', 0.5);
                var url = $(virtualMultiOffer).data('url');
                //Обновим адрес в истории браузера, если нужно
                if (pushHistory){
                    history.pushState($(virtualMultiOffer).data('info'), null, url );    
                }
                
                $.ajax({
                    type     : 'POST',
                    url      : url,
                    dataType : 'json',
                    success: function(response){
                        $(data.options.target).css('opacity', 1);
                        $(data.options.target).replaceWith(response.html);
                        $('body').trigger('new-content').trigger('product.reloaded'); //Событие для навешивания
                        hideNotUsedVariants(); //Спрячем не нужное
                        
                    }
                });
            };
            
            /**
            * Возврат на предыдущую страницу
            * 
            */
            var returnToProductPage = function()
            {
                var context = $(data.options.target); //Контекст
                var params = history.state ? history.state : [];
                //Применим параметры без перезагрузки
                $.each(params, function(prop_title, val){
                    //Если представлено выпадающим списком
                    if ($("select" + data.options.virtualMultiOfferName, context).length){
                        $(data.options.virtualMultiOfferName+'[data-prop-title="'+prop_title+'"]', context).val(val);      
                    }
                    //Если представлено выпадающим списком
                    if ($("input" + data.options.virtualMultiOfferName, context).length){
                        $(data.options.virtualMultiOfferName+'[data-prop-title="'+prop_title+'"][value="'+val+'"]', context).prop('checked', true);   
                    }
                });
                
                //Получим выбранное и подгрузим
                var selected          = getSelectedValues();
                var virtualMultiOffer = getSelectedVirtualMultioffer(selected);
                
                refreshProduct(virtualMultiOffer, false); //Обновим собержимое
            }
            
            /**
            * Возвращает выбранную виртуальную многомерную комплектацию
            * 
            * @param array selected - массив с выбранными значениями многомерных комплектаций
            */
            var getSelectedVirtualMultioffer = function (selected){
                var input_info = data.options.virtualMultiOffersInfo;
                
                var offer = false; //Cпрятанная комплектация, которую мы выбрали
                
                for(var j=0;j<input_info.length;j++){
                    var info = input_info[j]['info']; //Группа с информацией
                    var found = 0;                //Флаг, что найдены все совпадения
                    $.each(info, function(key, val){
                        for(var i=0;i<selected.length;i++){
                           if ((selected[i]['title']==key)&&(selected[i]['value']==val)){
                               found++;
                           } 
                        }
                        if (found==selected.length){ //Если удалось найди совпадение, то выходим
                            offer = input_info[j]['id']
                            return false;
                        }
                    });
                        
                }
                return offer;
            }
            
            
            
            /**
            * Инициализирует необходимые события
            */
            var bindChanges = function() {
                //Смена значений виртуальных многомерных комплектаций
                $('body').on('change', data.options.virtualMultiOfferName, changeVirtualMultiOffers);
            };
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }

})( jQuery );


$(function() {
    $('body').virtualMultioffers(); //Инициализируем работу плагина
});