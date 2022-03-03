/**
* @author ReadyScript lab.
* Плагин позволяет работат со страницей оформления заказа в один шаг
* 
*/
(function( $ ){

    $.fn.onePageCheckout = function( method ) {
        var defaults = {
            form            : "#order-form", //Селектор текущей формы заказа
            method          : 'POST',      //каким методом делать запрос на сервер
            oneTab          : '.tabList a', //Селектор вкладки
            changeUser      : "#changeuser", //Селектор ссылки смена пользователя
            orderLogin      : "#order_login", //Селектор ссылки с авторизацией (вход)
           
            deliveryToggler     : '[name="only_pickup_points"]', //Селектор переключения общего выбора доставки или самовывоза
            deliveryTogglerWrap : '#formAddressSectionWrapper', //Селектор блока оборачивающего общий тип доставки
            userType            : 'input[name="user_type"]', //Селектор поля с выбранным пользователем
            useAddress          : 'input[name="use_addr"]', //Селектор переключателя выбора адреса
            regAutologin        : 'input[name="reg_autologin"]', //Селектор флажка получения пароля на E-mail
            selectCountry       : 'select[name="addr_country_id"]', //Селектор - Выпадающий список стран
            selectRegion        : 'select[name="addr_region_id"]', //Селектор - Выпадающий список региона страны
            inputCity           : 'input[name="addr_city"], input[name="addr_zipcode"]', //Селектор - Указание города
            inputOnlyCity       : 'input[name="addr_city"]', //Селектор - Указание наименования города
            inputZipCode        : 'input[name="addr_zipcode"]', //Селектор - Указание индекса города
            inputTimeoutTime    : 2000, //Сколько секунд ждать ввода текста перед отправкой 
            inputTimeout        : false, //Таймаут на вводимый текст
           
            radioDelivery   : 'input[name="delivery"]', //Селектор - переключателя типа доставки
            radioPayment    : 'input[name="payment"]', //Селектор - переключателя типа оплаты
            newAdressBlock  : '.new-address', //Селектор - блока с вводом адреса
            adressListBlock : '#address-list', //Селектор - блока готовыми адресами пользователя
            submitButton    : '.formSave', //Селектор - кнопка подтвердить
            errorBlock      : '.errorBlock',
            deleteAddress   : '.deleteAddress', //Кнопка удаления адреса
            addressItem     : '.addressItem',
            searchCityItemsClass : 'searchCityItems',
            alwaysUpdate    : [], //блоки, которые нужн ообновлять даже, если выбрана вкладка user [delivery, payment, products]

            hidden          : 'rs-hidden',
            loading         : 'rs-loading', //Класс который будет добавлятся блоку во время загрузки
            blocks          : false, //Информация о блоках которые будут обновлены
            delay           : 300, //Задерка между запросами
            inQuery         : false //Флаг, что сейчас делается запрос
        },
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('onePageCheckout');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('onePageCheckout', data);
                    data.options = $.extend({}, defaults, initoptions, $this.data('paginationOptions'));
                    
                    //Получаем обновляемые блоки
                    data.options.blocks = $this.data('blocks');

                    //Назначаем события
                    $this
                        .on('change', data.options.deliveryToggler, deliveryToggle) //Смена общего способа получения товара
                        .on('click', data.options.useAddress, changeAddress) //Смена выбора адреса
                        .on('change', data.options.regAutologin, changeRegAutologin) //Переключение флажка получать пароль на E-mail
                        .on('change', data.options.selectCountry, countryChange)  //Смена страны
                        .on('keyup', data.options.inputCity, keyUpInput) //Ввод города
                        .on('click', data.options.deleteAddress, deleteAddress);

                       
                    $('body')
                        .on('change', data.options.selectRegion, methods.ajaxGetBlocks)  //Смена региона страны
                        .on('change', data.options.radioDelivery, methods.ajaxGetBlocks) //Смена типа доставки
                        .on('change', data.options.radioPayment, methods.ajaxGetBlocks)
                        .on('click', data.options.submitButton, methods.createOrder) //Смена типа оплаты
                        .on('cartChange', methods.reloadCheckout);
                        
                                                    
                    firstInit();//Первичная инициализация    
                },
                createOrder: function() {
                    $(data.options.errorBlock).html('');
                    $(data.options.errorBlock).addClass(data.options.hidden);

                    $this.ajaxSubmit({
                        method: data.options.method,
                        url: $this.data('createOrderUrl'),
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                if (response.redirect) {
                                    window.location = response.redirect;
                                }
                            } else {
                                if (response.errors) {
                                    response.errors.forEach(element => {
                                        let line = document.createElement('div');
                                        line.innerHTML = element;
                                        $(data.options.errorBlock).append(line);
                                    });
                                    $(data.options.errorBlock).removeClass(data.options.hidden);
                                }
                            }
                        }
                    });
                },
                reloadCheckout: function() {

                    $this.ajaxSubmit({
                        url: $this.data('reload-checkout-url'),
                        dataType: 'json',
                        success: function (response) {
                            let html = $(response.html);
                            $this.html(html.find('#order-form').html());
                            firstInit();
                            $this.trigger('new-content');
                        }
                    });
                },
                /**
                * Посылает ajax запрос с данными возвращает json с готовыми блоками
                */
                ajaxGetBlocks: function() {
                   //Посмотрим какой тип пользователя выбран
                   var userType = $(data.options.userType, $this).val();

                   if (userType == 'user'){ //Если это смена пользователя, то слать запрос не будем и скроем лишние блоки
                       return false;
                   }
                    
                   //Подсветим ошибки на необходимые данные у формы
                   if (typeof($this.data('update-url'))=='undefined'){
                       alert(lang.t('У формы отсутcтвует атрибут data-update-url'));
                       return false;
                   } 
                   
                   var blocks = $this.data('blocks'); //Селекторы блоков на странице
                   if (typeof($this.data('blocks'))=='undefined'){
                       alert(lang.t('У формы отсутcтвует атрибут data-blocks'));
                       return false;
                   } 

                   if (!data.options.inQuery){
                       //Покажем загрузку в блоках
                       $(data.options.blocks['user']).addClass(data.options.loading);
                       $(data.options.blocks['address']).addClass(data.options.loading);
                       $(data.options.blocks['delivery']).addClass(data.options.loading);
                       $(data.options.blocks['payment']).addClass(data.options.loading);
                       $(data.options.blocks['products']).addClass(data.options.loading);
                       $(data.options.blocks['total']).addClass(data.options.loading);

                       var options = { //Опции для отправки формы
                           method: data.options.method,
                           url: $this.data('update-url'),
                           dataType: 'json',
                           success: function (response) {
                               data.options.inQuery = false;
                               if (response.success) { //Всё успешно
                                   var is_user_tab = $(data.options.userType, $this).val() == "user";

                                   //Страхуемся на случай быстрого переключения вкладок
                                   //Обновляем блок пользователя
                                   if ((!is_user_tab || $.inArray('user', data.options.alwaysUpdate) > -1)) {
                                       $(data.options.blocks['user']).replaceWith(response.blocks.user);
                                   }
                                   //Обновляем блок адреса
                                   if ((!is_user_tab || $.inArray('address', data.options.alwaysUpdate) > -1)) {
                                       $(data.options.blocks['address']).replaceWith(response.blocks.address);
                                   }

                                   //Обновляем блок доставки
                                   if ((!is_user_tab || $.inArray('delivery', data.options.alwaysUpdate) > -1)) {
                                       $(data.options.blocks['delivery']).replaceWith(response.blocks.delivery);
                                   }

                                   //Обновляем блок оплаты
                                   if ((!is_user_tab || $.inArray('payment', data.options.alwaysUpdate) > -1)) {
                                       $(data.options.blocks['payment']).replaceWith(response.blocks.payment);
                                   }

                                   //Обновляем блок с товарами
                                   if ((!is_user_tab || $.inArray('products', data.options.alwaysUpdate) > -1)) {
                                       $(data.options.blocks['products']).replaceWith(response.blocks.products);
                                   }

                                   //Обновляем блок с итогом
                                   if ((!is_user_tab || $.inArray('total', data.options.alwaysUpdate) > -1)) {
                                       $(data.options.blocks['total']).replaceWith(response.blocks.total);
                                   }

                                   firstInit();

                               } else { //Если нужен редирект
                                   document.location(response.redirect);
                               }

                               $this.trigger('new-content');

                           }

                       };

                       //Отправляем форму
                       $this.ajaxSubmit(options); 
                   }
                }
            };
            
            //private

            /**
            * Первичная инициализация плагина
            */
            var deleteAddress = function() {
                var parent = $(this).closest(data.options.addressItem).css('opacity', '0.5');
                $.get($(this).attr('href') ? $(this).attr('href') : $(this).data('href'), function( response ) {
                    parent.css('opacity', '1');
                    if (response.success){
                       parent.remove(); 
                       $(data.options.addressItem + ":eq(0) input[name='use_addr']").click();
                    }
                }, "json");
                return false;
            },
            
            /**
            * Получает адрес для получения подсказок для города
            */
            getCityAutocompleteUrl = function(){
                var form   = $( data.options.inputOnlyCity ).closest('form'); //Объект формы
                var url    = form.data('city-autocomplete-url'); //Адрес для запросов
                var params = []; //Дополнительные параметры

                //Если есть селектор страны, то сузим поиск до этой страны
                if ($( data.options.selectCountry ).length){
                    params.push({
                        name   : 'country_id',
                        value : $( "[name='addr_country_id']" ).val()
                    });
                }
                //Если есть селектор региона, то сузим поиск до этого региона
                if ($( data.options.selectRegion ).length){
                    params.push({
                        name : 'region_id',
                        value : $( "[name='addr_region_id']" ).val()
                    });
                }
                
                var str_params = $.param(params);
                if (str_params){
                    url += "?"+str_params; 
                }
                return url;
            },
            
            firstInit = function(){

                //Смена выбора адреса
                if ($(data.options.adressListBlock).length>0 && $(data.options.useAddress+':checked').val()!='0') {
                    $(data.options.newAdressBlock).hide();
                } 

                //Смена вкладки (типа пользователя)
                if (data.options.oneTab) {

                    $(data.options.oneTab).on('click', function () {
                        var userType = $(data.options.userType, $this).val();
                        if (userType == 'user') { //Если это смена пользователя, то слать запрос не будем и скроем лишние блоки
                            hideUpdateBlock();
                        } else {
                            showUpdateBlock();
                        }
                        setTimeout(methods.ajaxGetBlocks(), data.options.delay);
                    });
                }
                
                //Ссылка сменить пользователя
                if (data.options.changeUser) {
                    $(data.options.changeUser).click(function () {
                        $(data.options.form).append('<input type="hidden" name="logout" value="1">');
                        $(data.options.form).submit();
                        return false;
                    });
                }
                
                //Ссылка войти в систему
                $(data.options.orderLogin).click(function() {
                    $(data.options.form).append('<input type="hidden" name="ologin" value="1">');
                    $(data.options.form).submit();
                    return false;
                }); 
                
                //Автозаполнение в строке с вводом города
                $( data.options.inputOnlyCity ).each(function() {
                    var url = getCityAutocompleteUrl(); //Установка адреса
                    
                    $(this).autocomplete({
                        source: url,
                        minLength: 3,
                        select: function( event, ui ) {
                            var region_id  = ui.item.region_id; //Выбранный регион
                            var country_id = ui.item.country_id; //Выбранная страна
                            var zipcode    = ui.item.zipcode; //Индекс
                            
                            //Установка индекса
                            if ($(data.options.inputZipCode).length){
                                $(data.options.inputZipCode).val(zipcode);
                            }
                        },
                        messages: {
                            noResults: '',
                            results: function() {}
                        }
                    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                        ul.addClass(data.options.searchCityItemsClass);
                        
                        return $( "<li />" )
                            .append( '<a>' + item.label + '</a>' )
                            .appendTo( ul );
                    };
                }); 
                
                /**
                * Если меняется регион или страна в выпадающем списке
                */
                $(data.options.selectCountry + ", " + data.options.selectRegion).on('change', function(){
                    var url = getCityAutocompleteUrl(); //Установка адреса
                    $( data.options.inputOnlyCity ).autocomplete('option', 'source', url);
                });
            },
            
            /**
            * Переключение общего типа способа доставки
            * 
            */
            deliveryToggle = function(event){
                $(data.options.deliveryTogglerWrap).toggleClass('hidden', parseInt($(data.options.deliveryToggler+":checked").val())>0);
                methods.ajaxGetBlocks(event); //Обновим блоки
            },
            
            /**
            * Срабатывание отправки данных при вводе текста
            * 
            */
            keyUpInput = function (event){
               var val = $(this).val(); 
               if(val.length < 3){ //Если длинна значения меньше 3-х символов
                  return false; 
               } 
               if ($(this).data('val') != val) { //Если значение у поля уже другое
                  clearTimeout(data.options.inputTimeout);  
                  data.options.inputTimeout = setTimeout(methods.ajaxGetBlocks(event), data.options.inputTimeoutTime);
                  $(this).data('val',val); 
               }
            },
            
            /**
            * Прячет блоки которые нужно обновлять
            * 
            */
            hideUpdateBlock = function (){

               if (typeof(data.options.blocks)=='object'){

                   $(data.options.blocks['address']).hide();
                   $(data.options.blocks['delivery']).hide();
                   $(data.options.blocks['payment']).hide();
                   $(data.options.blocks['products']).hide();
                   $(data.options.submitButton).hide();
                   $(".authUserHideSection").hide();
               } 
            },
            
            /**
            * Показывает блоки, которые нужно обновлять
            * 
            */
            showUpdateBlock = function (){
               if (typeof(data.options.blocks)=='object'){
                   $(data.options.blocks['address']).show();
                   $(data.options.blocks['delivery']).show();
                   $(data.options.blocks['payment']).show();
                   $(data.options.blocks['products']).show();
                   $(data.options.submitButton).show();
                   $(".authUserHideSection").show();
               }  
            },
            
            /**
            * Смена выбора адреса
            * 
            */
            changeAddress = function(event){
               if (this.value == '0'){
                  $(data.options.newAdressBlock).show(); 
               }else{
                  $(data.options.newAdressBlock).hide();
               }
               methods.ajaxGetBlocks(event);
            },
            
            /**
            * Переключение флажка получать пароль на E-mail
            * 
            */
            changeRegAutologin = function(){
               $('.rs-manual-login').toggle(!this.checked);
            },
              
            /**
            * Смена страны
            *    
            */
            countryChange = function(event){
                var regions = $(data.options.selectRegion).attr('disabled','disabled');
        
                $.getJSON($(this).data('regionUrl'), {
                    parent: $(this).val()
                }, 
                function(response) {
                    if (response.list.length>0) {
                        regions.html('');
                        for(var i=0; i< response.list.length; i++) {
                            var item = $('<option value="'+response.list[i].key+'">'+response.list[i].value+'</option>');
                            regions.append(item);
                        }
                        regions.removeAttr('disabled');
                        $('#region-input').val('').hide();
                        $('#region-select').show();
                    } else {
                        $('#region-input').show();
                        $('#region-select').hide();
                    }
                    methods.ajaxGetBlocks(event);
                });
            };
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }
})( jQuery );