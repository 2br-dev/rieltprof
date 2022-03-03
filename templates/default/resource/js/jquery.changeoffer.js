/**
* Плагин, инициализирующий работу механизма выбора комплектации продукта.
* Позволяет изменять связанные цены на странице при выборе комплектации, 
* а также подменяет ссылку на добавление товара в корзину с учетом комплектации
*/
(function( $ ){
    $.fn.changeOffer = function( method ) {
        var defaults = {
            buyOneClick      : '.buyOneClick', //Класс купить в один клик
            reserve          : '.reserve',
            unitBlock        : '.unitBlock',
            dataAttribute    : 'changeCost',
            offerProperty    : '.offerProperty',
            notAvaliable     : 'notAvaliable',   // Класс для нет в наличии
            hiddenClass      : 'hidden',
            offerParam       : 'offer',
            context          : '[data-id]',      // Родительский элемент, ограничивающий поиск элемента с ценой
            inDialogUrlDataAttr: 'href',
            
            //Параметры для многомерных комплектаций
            multiOffers             : false,                    // Флаг использования мн. комплектаций
            virtual_multiOffers     : false,                    // Флаг использования виртуальных мн. комплектаций
            multiOffersInfo         : {},                       // Массив с информацией о комплектациях
            multiOfferName          : '[name^="multioffers["]', // Списки многомерных комплектаций
            multiOfferPhotoBlock    : '.multiOfferValueBlock',  // Блок многомерной комплектации представленный как фото
            multiOfferPhotoWrap     : '.multiOfferValues',      // Оборачивающий блок с многомерными комплектации представленными как фото
            multiOfferPhotoSel      : 'sel',                    // Класс выбранной многомерных комплектаций в виде фото 
            multiOfferDialogWrapper : '.multiComplectations',   // Класс оборачивающего элемента диалогового окна выбора комплектаций
            multiOfferDialogPhoto   : '.multiComplectations .image img', // Картинка в диалоговом окне многомерных комплектаций
            hiddenOffersName        : '[name="hidden_offers"]',          // Комплектации с информацией
            hiddenVirtualOffersName : '[name="hidden_multioffers"]',     // Комплектации с информацией о виртуальным мн. комплектациях
            theOffer                : '[name="offer"]',         // Скрытое поле откуда забирается комплектация
            notExistOffer           : '[data-type-offer="notExist"]', // Скрытое поле, хранит "не существующую" комплектацию
            quotReplacer            : '*`*',                    // Конструкция, подменяющая ковычки
            
            //Паметры для складов
            sticksRow        : '.warehouseRow', //Оборачивающий контейнер с рисками значений заполнености склада
            stick            : '.stick',        //Риски со значениями заполнености склада
            stickFilledClass : 'filled',        //Класс заполненой риски
            
            //Пораметры для фото
            jcarouselBlock   : ".productGalleryWrap .gallery", //Блок карусели
            mainPicture      : ".mainPicture", //Класс главных фото
            previewPicture   : ".gallery li",  //Класс превью фото 
            pictureDisable   : "hidden",        //Класс скрытия фото
            onShowOfferPhotos: null
        },
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('changeOffer');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('changeOffer', data);
                    data.options = $.extend({}, defaults, initoptions);
                    $this.change(changeOffer);
                    
                    var context = $(this).closest(data.options.context);                                      
             
                    //Виртуальные многомерные комплектации
                    if ($(data.options.hiddenVirtualOffersName, context).length){
                        data.options.virtual_multiOffers = true;
                    }
             
                    //Многомерные комплектации
                    if ($(data.options.hiddenOffersName, context).length>0 && !data.options.virtual_multiOffers){
                        data.options.multiOffers = true;
                        
                        //Соберём информацию для работы
                        $(data.options.hiddenOffersName,context).each(function(i){
                            var offerId = $(this).data('offerId');
                            data.options.multiOffersInfo[offerId]           = {};
                            data.options.multiOffersInfo[offerId]['input']  = this;
                            data.options.multiOffersInfo[offerId]['id']     = offerId;
                            data.options.multiOffersInfo[offerId]['info']   = $(this).data('info');
                            data.options.multiOffersInfo[offerId]['num']    = $(this).data('num');
                            data.options.multiOffersInfo[offerId]['sticks'] = $(this).data('sticks');
                        });

                        //Навесим событие
                        context
                             .on('change',data.options.multiOfferName,changeMultiOffer)
                             .on('click',data.options.multiOfferPhotoBlock,changeMultiOfferPhotoBlock);
                    }

                    //Устанавливаем текущую комплектацию
                    var photoEx = new RegExp('#(\\d+)');
                    var res = photoEx.exec(location.hash);
                    res = (res != null) ? res[1] : $(data.options.theOffer).val();

                    $('select[name="offer"]', context).val(res);
                    $('select[name="offer"] [value="'+res+'"]', context).click().change();
                    $('input[name="offer"][value="'+res+'"]', context).click().change();
                    
                    //Если используются многомерные комплектации
                    var multioffer = $('input#offer_'+res);
                    if (multioffer.length) {
                        var multioffer_values = multioffer.data('info');        
                        if (multioffer_values) {
                            for(var j in multioffer_values) {
                                $('[data-prop-title="'+multioffer_values[j][0]+'"]', context).each(function() {
                                    var item = $(this);
                                    if (item.is('input[type="radio"]')) {
                                        //Radio кнопки                                    
                                        if (item.val() == multioffer_values[j][1].replace(data.options.quotReplacer, '"')) {
                                            item.prop('checked', true).change();
                                        }
                                    } else {
                                        //Select
                                        item.val(multioffer_values[j][1].replace(data.options.quotReplacer, '"')).change();
                                    }
                                });
                                //Проверим, а многомерная комплектация эта представлена как фото, тогда выберем правильное фото при переключении
                                chooseRightOfferPhotoSelected($('[data-prop-title="'+multioffer_values[j][0]+'"]', context));
                            }
                        }
                    }
                    //Выбираем правильный offer, в случае, если у нулевой комплектации не прописаны свойства
                    $('[data-prop-title]:first', context).change();
                }
            };
            
            
            //private 
            //Комплектации
            /**
            * Смена комплектации
            * 
            */
            var changeOffer = function() { 
                var $selected = $this;
                
                
                if ($this.get(0).tagName.toLowerCase() == 'select') {
                    $selected = $('option:selected', $this);
                }
                
                var list    = $selected.data(data.options.dataAttribute);
                var context = $selected.closest(data.options.context);
                var offer   = $selected.val();

                
                if (list){
                    //Сменим артикул и цену
                    $.each(list, function(selector, cost) {
                        $(selector, context).text(cost);
                    });     
                }
                               
                
                //Сменим единицу измерения, если нужно
                if ((typeof($selected.data('unit'))!='undefined') && ($selected.data('unit')!="")){
                   $(data.options.unitBlock,context).show();
                   $(data.options.unitBlock+" .unit",context).text($selected.data('unit'));
                }else{
                   $(data.options.unitBlock,context).hide(); 
                }
                
                $(data.options.offerProperty).addClass(data.options.hiddenClass);
                $(data.options.offerProperty+'[data-offer="'+offer+'"]').removeClass('hidden');
                
                //Добавим параметр комплектации к ссылке купить в 1 клик
                if ($(data.options.buyOneClick, context).length>0){
                   var clickHref = $(data.options.buyOneClick, context).data(data.options.inDialogUrlDataAttr).split('?'); //Получим урл
                   $(data.options.buyOneClick, context).data(data.options.inDialogUrlDataAttr, clickHref[0]+"?offer_id="+$selected.data('offerId'));
                }
                //Добавим параметр комплектации к ссылке заказать
                if ($(data.options.reserve, context).length>0){
                   var clickHref = $(data.options.reserve, context).data(data.options.inDialogUrlDataAttr).split('?'); //Получим урл
                   $(data.options.reserve, context).data(data.options.inDialogUrlDataAttr, clickHref[0]+"?offer_id="+$selected.data('offerId'));
                }
                
                //Показываем фото комплектаций
                showOfferPhotos($selected);
                //Показывает доступность комплектации
                showAvailability(offer);
            },
            
            /**
            * Показывает наличие на складе
            * @param Array stock_arr - массив с наличием "палочек наличия" для отображения 
            */
            showStockSticks = function(stock_arr, context) {
               //Сбросим наличие
               $(data.options.sticksRow+" "+data.options.stick, context).removeClass(data.options.stickFilledClass);
               //Установим значения рисок заполнености склада
               $(data.options.sticksRow, context).each(function() {
                   var warehouse_id = $(this).data('warehouseId');
                   var num = stock_arr[warehouse_id]; //Количество рисок для складов
                   for(var j=0; j<num; j++) {
                       $(data.options.stick+":eq("+j+")", $(this)).addClass(data.options.stickFilledClass);
                   }
               });
            },
            
            /**
            * Показывает наличие товара, приписывает или убирает класс
            * notAvaliable
            * 
            */
            showAvailability = function(offerValue) {
               var context = $this.closest(data.options.context); 
               
               if (data.options.multiOffers){ //Если используются многомерные комплектации
                  var num = data.options.multiOffersInfo[offerValue]['num'];
                  //Покажем наличие на складах
                  showStockSticks(data.options.multiOffersInfo[offerValue]['sticks'], context);
               }else{ 
                  var offer = $(data.options.theOffer+"[value='"+offerValue+"']",context); //Если радиокнопками
                  if (!offer.length){ //Если выпадающее меню
                     var offer = $(data.options.theOffer+" option:selected",context);  
                  } 
                  var num = offer.data('num');
                  //Покажем наличие на складах
                  showStockSticks(offer.data('sticks'), context);
               }
                                                   
               if (typeof(num) != 'undefined') {
                   if (num<=0){ //Если не доступно
                      $(context).addClass(data.options.notAvaliable); 
                   }else{ //Если  доступно
                      $(context).removeClass(data.options.notAvaliable);   
                   }
               }
            },
            
            
            //Многомерные комплектации
            /**
            * Смена/Выбор многомерной комплектации
            * 
            */
            changeMultiOffer = function (){
                var context = $this.closest(data.options.context);

                var selected = []; //Массив, что выбрано
                //Соберём информацию, что изменилось
                $(data.options.multiOfferName, context).each(function(i) {
                    if ($(this).is(':not(input[type="radio"])') || $(this).is(':checked')) {
                        selected.push({
                            title:$(this).data('propTitle'),
                            value:$(this).val(),
                            id:$(this).data('propId')
                        });
                    }
                });

                //Найдём инпут с комплектацией
                var input_info = data.options.multiOffersInfo;
                var offerId    = 0; //Cпрятанная комплектация, которую мы выбрали

                for(var key in input_info) {
                    element = input_info[key];
                    if (element.id == 0) continue;

                    var info = element['info']; //Группа с информацией
                    var found = 0;                //Флаг, что найдены все совпадения

                    for (var m = 0; m < info.length; m++) {
                        var titleOffers = info[m][0];
                        var valueOffers = info[m][1];

                        for (var i = 0; i < selected.length; i++) {
                            var valueSelected = selected[i]['value'].replace('"', data.options.quotReplacer);
                            var titleSelected = selected[i]['title'].replace('"', data.options.quotReplacer);

                            if ((titleSelected == titleOffers) && (valueOffers.toLowerCase() == valueSelected.toLowerCase())) {
                                found++;
                            }
                        }

                        if (found == selected.length) { //Если удалось найди совпадение, то выходим
                            offerId = element['id'];
                            offerInput = element['input'];
                            break;
                        }
                    }
                };

                //Отметим выбранную комплектацию
                if (!offerId) {
                    offerInput = $(data.options.notExistOffer, context);
                    offerId = $(offerInput).val();
                }

                $(data.options.theOffer, context).val(offerId);


                //Соберём информацию что выбрано из многомерных
                var multiSelected = [];
                $(selected).each(function(i){
                    multiSelected.push('multioffers['+selected[i]['id']+']='+encodeURIComponent(selected[i]['value']));
                });
                multiSelected = multiSelected.join('&');


                //Добавим параметр комплектаций к ссылке купить в 1 клик, если купить в 1 клик присутствует
                if ($(data.options.buyOneClick, context).length>0) {
                    var clickHref = $(data.options.buyOneClick, context).data(data.options.inDialogUrlDataAttr).split('?'); //Получим урл
                    $(data.options.buyOneClick, context).data(data.options.inDialogUrlDataAttr, clickHref[0]+'?offer_id='+offerId+'&'+multiSelected); //Запишем урл
                }

                //Добавим параметр комплектаций к ссылке заказать
                if ($(data.options.reserve, context).length>0) {
                    var clickHref = $(data.options.reserve, context).data(data.options.inDialogUrlDataAttr).split('?'); //Получим урл
                    $(data.options.reserve, context).data(data.options.inDialogUrlDataAttr, clickHref[0]+'?offer_id='+offerId+'&'+multiSelected); //Запишем урл
                }

                $(data.options.offerProperty).addClass(data.options.hiddenClass);
                $(data.options.offerProperty+'[data-offer="'+offerId+'"]').removeClass('hidden');

                //Поменяем цену за комплектацию
                var dataCost = $(offerInput).data('changeCost');
                for(var i in dataCost){
                    $(i, context).html(dataCost[i]);
                }

                //Сменим единицу измерения, если нужно
                if ((typeof($(offerInput).data('unit'))!='undefined') && ($(offerInput).data('unit')!="")){
                    $(data.options.unitBlock,context).show();
                    $(data.options.unitBlock+" .unit",context).text($(offerInput).data('unit'));
                }else{
                    $(data.options.unitBlock,context).hide();
                }

                //Показываем фото комплектаций
                if ($(data.options.hiddenOffersName+"[value='"+offerId+"']",context).length>0){
                    $selected = $(data.options.hiddenOffersName+"[value='"+offerId+"']",context);
                    showOfferPhotos($selected);
                }

                //Покажем наличие товара после выбора комплектации
                showAvailability(offerId);
            },
            
            /**
            * Смена многомерной комплектации, если она представлена как фото
            */
            changeMultiOfferPhotoBlock = function (){
                var context = $(this).closest(data.options.multiOfferPhotoWrap);
                $(data.options.multiOfferPhotoBlock, context).removeClass(data.options.multiOfferPhotoSel);
                $(this).addClass(data.options.multiOfferPhotoSel);
                $('input',context).val($(this).data('value'));
                $('input',context).change();
                
                //Если указано data-image то поменяем фото(для окна с многомерными комплектациями, не для карточки товара)
                if ($(this).closest(data.options.multiOfferDialogWrapper).length) {
                    var bigPhoto = $(this).data('image');
                    //Сохраним основное фото, чтобы можно было переключится на него, если фото для многомерной комплектации не существует
                    var pagePhoto = $(data.options.multiOfferDialogPhoto);
                    if (typeof(pagePhoto.data('mainPhoto'))=='undefined'){
                        pagePhoto.data('mainPhoto', pagePhoto.attr('src'));
                    }
                    
                    //Если есть заданные фото у фото значения комплектации, то переключимся на него
                    if (typeof(bigPhoto)!='undefined'){
                        pagePhoto.attr('src',bigPhoto);
                    }else{
                        pagePhoto.attr('src',pagePhoto.data('mainPhoto'));
                    }
                }
                
                return false;
            },
            
            /**
            * Проверяет фото ли это в виде многомерной комплектации и выбирает правильное фото, если это так
            */  
            chooseRightOfferPhotoSelected = function(offer) {
               
               var photoWrap = offer.closest(data.options.multiOfferPhotoWrap);
               if (photoWrap.length){
                    $(data.options.multiOfferPhotoBlock,photoWrap).removeClass(data.options.multiOfferPhotoSel);
                    $(data.options.multiOfferPhotoBlock+"[data-value='"+offer.val()+"']",photoWrap).addClass(data.options.multiOfferPhotoSel);
               } 
            },
            
            //Фото комплектаций
            
            /**
            * Показывает фото выбранной комплектаций 
            * 
            */ 
            showOfferPhotos = function(offer) {
                var context = $this.closest(data.options.context);                  
                //Получим массив фото из комплектаций  
                var images = offer.data('images');
                if (!images || !images.length) images = [];
                
                //Покажем, только те, которые принадлежат комплектации и переключимся на первое фото иначе, 
                //покажем все фото и перключимся на первое фото
                if (images.length>0){
                    $(data.options.mainPicture, context).addClass(data.options.pictureDisable).removeAttr('rel');
                    
                    //Скроем все превью фото
                    $(data.options.previewPicture,context).addClass(data.options.pictureDisable);
                    
                    //Пройдёмся по главным фото
                    var mainFound = false; //Флаг главного фото
                    $(data.options.mainPicture,context).each(function(i){
                        var id = $(this).data('id'); //id картинки 
                        if (!mainFound && in_array(id,images)){
                           $(this).removeClass(data.options.pictureDisable).attr('rel','bigphotos'); 
                           mainFound = true; //Найдено первое главное фото
                        }else if (in_array(id,images)){
                           $(this).attr('rel','bigphotos');  
                        }
                    }); 
                    
                    //Пройдёмся по превью фото
                    $(data.options.previewPicture,context).each(function(i){
                        var id = $(this).data('id'); //id картинки 
                        if (in_array(id,images)){
                           $(this).removeClass(data.options.pictureDisable); 
                        }
                    }); 
                }else{
                    //Покажем все фото
                    $(data.options.mainPicture, context).attr('rel','bigphotos');
                    $(data.options.previewPicture, context).removeClass(data.options.pictureDisable);
                }
                
                if (typeof(data.options.onShowOfferPhotos) == 'function') data.options.onShowOfferPhotos.call(this, offer, context, data);
                
            },
            
            /**
            * Проверяет есть ли в массиве нужный элемента
            * 
            * @param mixed needle      - то что ищем
            * @param array haystack    - массив в котором нужно искать
            * 
            * @returns {Boolean}
            */
            in_array = function(needle, haystack) 
            {
               var key = '';
               for (key in haystack) {
                  if (haystack[key] == needle) {
                    return true;
                  }
               }
               return false;
            } 
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }

})( jQuery );


$(function() {
    $('[name="offer"]').changeOffer({
        
        onShowOfferPhotos: function(offer, context, data) //Индивидуально для темы default обновим карусель
        {
            if (offer.closest('.multiComplectations').length == 0) { //Если мы находимся в карточке товара
                //Обновим привязки карусели и показа фото
                if (typeof($(data.options.jcarouselBlock, context).data('jcarousel')) != 'undefined'){
                    $(data.options.jcarouselBlock, context).jcarousel('reload',{
                        items: "li:visible"
                    });
                    //Пролистнём к первому пункту карусели
                    $(data.options.jcarouselBlock,context).jcarousel('scroll', 0);
                }
            
                $.colorbox.remove();
                $('.no-touch .product .viewbox[rel="bigphotos"]').colorbox({
                   rel:'bigphotos',
                   className: 'titleMargin',
                   opacity:0.2
                });
            }
        }
    });
});