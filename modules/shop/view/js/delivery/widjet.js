(function( $ ){
   $.deliveryWidjetCreator = function( method ) {
        //Найдём ближайший input c выбором
        var closestRadio = $('input[name="delivery"]:eq(0)');
        var form         = closestRadio.closest('form'); //найдем форму

        var defaults = {
            additionalHTMLBlock : '.deliveryWidjet',                //Контейнер куда будет вставлятся информация дополнтительного функционала
            deliveryParamsPvz   : '.deliveryParamsPvz',
            deliveryExtra       : '[name="delivery_extra[value]"]', //Скрытое поле с экстра информацией
            deliveryExtraCode   : '[name="delivery_extra[code]"]',  //Скрытое поле с экстра информацией
            select              : '.pickpointsSelect',              //Выпадающий список с забора дополнтительного функционала
            additionalInfo      : '.pickpointsAdditionalInfo',      //Дополнительная информация дополнтительного функционала
            checkout_pvzSelectButton : '.rs-checkout_pvzSelectButton',
            openMapButton       : '.pickpointsOpenMap',             //Кнопка открытия карты
            map                 : '.pickpointsMap',                 //Карта с выбором пукта забора
            mapContainer        : [],                             //Конитейнер для карты
            mapFilters          : '.pickpointsMapFilters',          //Фильтры на карте самовывозов
            yandexReady         : false                             //Флаг того, что яндекс загрузился
        },

        $this = form,
        data  = $this.data('deliveryInfo');

        if (!$this.length) return;
        if (!data) { //Инициализация
            data = { options: defaults };
            $this.data('deliveryInfo', data);
        }

        //public
        var methods = {
            /**
            * Инициализация плагина
            *
            * @param initoptions - введённые пераметра для записи или перезаписи
            */
            init: function(initoptions)
            {
                data.options = $.extend(data.options, initoptions);

                bindEvents();
                changeDelivery();

                //Если контент обновился (для заказа на одной странице)
                $('body').on('new-content', function(){
                    bindEvents();
                    changeDelivery();
                });
            }
        };

        //private

        /**
         * Смена доставки
         */
        var changeDelivery = function (){

            //Отключим поля внутри лишних блоков
            $(data.options.additionalHTMLBlock).each(function(){
                $("select", $(this)).prop('disabled', true);
                $("input", $(this)).prop('disabled', true);
                $("button", $(this)).prop('disabled', true);
                $("textarea", $(this)).prop('disabled', true);
                $(data.options.openMapButton, $(this)).hide();
            });
            /*$(data.options.deliveryParamsPvz).each(function(){
                $("select", $(this)).prop('disabled', true);
                $(data.options.checkout_pvzSelectButton, $(this)).hide();
            });*/

            //Откроем поля только для определённой доставки
            var current = getCurrentAdditionalHTMLBlock();
            $("select", current).prop('disabled', false);
            $("input", current).prop('disabled', false);
            $("button", current).prop('disabled', false);
            $("textarea", current).prop('disabled', false);
            $(data.options.openMapButton, current).show();
            /*if (document.querySelector('input[name="delivery"]:checked')) {
                current = document.querySelector('input[name="delivery"]:checked').closest('.item');
                if (!current) {
                    current = document.querySelector('input[name="delivery"]:checked').closest('.row');
                }
                current = current.querySelector(data.options.deliveryParamsPvz);
                if (current) {
                    current.querySelectorAll('select').forEach((element) => {
                        element.disabled = false;
                    });
                    current.querySelectorAll(data.options.checkout_pvzSelectButton).forEach((element) => {
                        $(element).show();
                    });
                }
            }*/

            $(data.options.select, current).trigger('change');
        },

        /**
         * Возвращает текущий оборачивающий блок
         */
        getCurrentAdditionalHTMLBlock = function (){
            var delivery_id = $('input[name="delivery"]:checked').val();
            return $(data.options.additionalHTMLBlock + "[data-delivery-id='" + delivery_id + "']");
        },

        /**
        * Смена адреса пункта выдачи в выпадающем списке
        */
        changeDeliveryPickpoints = function()
        {
            var context  = $(this).closest(data.options.additionalHTMLBlock);
            var selected = $("option:selected", $(this)); //Тещий выбранный вариант
            if (/^[\],:{}\s]*$/.test(selected.val().replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                var val      = JSON.parse(selected.val());
                if (val.info){
                    $(data.options.additionalInfo, context).html(val.info);
                    $(data.options.deliveryExtraCode, context).val(val.code);
                }
            }

            return false;
        },

        /**
         * Открытие карты с пунктами выдачи товара
         *
         * @param $_this - текущий элемент
         * @param createMap - флаг нужно ли создавать карту
         */
        openMap = function ($_this, createMap)
        {
            var parent     = $_this.closest(data.options.additionalHTMLBlock); //Оборачивающий контейнер
            var mapOptions = $_this.data('mapOptions');          //Дополнительные опции карты, если есть
            var mapDiv     = $(data.options.map, parent);        //Карта
            var mapFilters = $(data.options.mapFilters, parent); //Фильтры карты
            var select     = $(data.options.select, parent);     //Выпадающий список
            var deliveryId = parent.data('deliveryId');          //Текущая доставка

            //Соберём доступные координаты
            var addresses = getAddressCoordinatesBySelectAsArray(select);
            var base      = getPointsForMapFromAddresses(mapFilters, addresses);

            var myCollection;



            //Очистим карту
            ymaps.ready(function(){
                //Менеджер объектов
                myCollection = new ymaps.ObjectManager({
                    // Чтобы метки начали кластеризоваться, выставляем опцию.
                    clusterize: true,
                    // ObjectManager принимает те же опции, что и кластеризатор.
                    gridSize: 32
                });

                if (createMap){
                    mapDiv.empty();
                    //Строим карту
                    var createOptions = {
                        center: [50, 50],
                        zoom: 10,
                        controls: ['zoomControl', 'typeSelector']
                    };
                    if (mapOptions){ //Если есть доп. опции для карты
                        createOptions = $.extend(createOptions, mapOptions)
                    }
                    data.options.mapContainer[deliveryId] = new ymaps.Map(mapDiv.attr('id'), createOptions);
                    mapDiv.show();

                    var top_minus = mapFilters.length ? mapFilters.height() + 10 : 0; //Корректировка при переключении

                    //Прокрутим к карте
                    if (!$_this.hasClass('no-scroll-to-map')){
                        setTimeout(function () {
                            $('html, body').animate({
                                scrollTop: mapDiv.offset().top - top_minus
                            }, 500);
                        }, 1000);
                    }
                }else{
                    //Удалим сначала все точки
                    data.options.mapContainer[deliveryId].geoObjects.removeAll();
                }

                if (base.features.length > 0){
                    myCollection.add(base);

                    /**
                     * Нажатие на открытие карты
                     */
                    myCollection.objects.events.add('click', function (e) {
                        var objectId = e.get('objectId');
                        var option = $("option[value*='"+objectId+"']", select);
                        option.prop('selected', true);
                        select.trigger('change');
                    });

                    data.options.mapContainer[deliveryId].geoObjects.add(myCollection);

                    //Поствим правильно зум и центр
                    data.options.mapContainer[deliveryId].setBounds(data.options.mapContainer[deliveryId].geoObjects.getBounds());
                    data.options.mapContainer[deliveryId].setZoom(11);
                }

                //Убирает скролл на карте
                data.options.mapContainer[deliveryId].behaviors.disable('scrollZoom');
            });

        },

        /**
         * Возвращает список точек на карте отфильтронных или всех
         *
         * @param mapFilters - обертка для фильтров
         * @param addresses - массив адресов
         */
        getPointsForMapFromAddresses = function(mapFilters, addresses)
        {
            //Соберём коллекцию точек
            var base = {
                type: 'FeatureCollection',
                features: []
            };

            var pickpoints_filters = [];
            if (mapFilters.length){ //Пройдем для по фильтрам, чтобы их собрать
                mapFilters.show();
                pickpoints_filters = getCheckedFilters(mapFilters);
                bindFiltersEvents(mapFilters);
            }

            //Расставляем точки
            $(addresses).each(function(i){
                var address = addresses[i];
                if (pickpoints_filters.length){ //Есчли есть включенные фильтры по пройдемся по ним
                    var show_flag = false;
                    for (var j in pickpoints_filters){
                        pickpoints_filter = pickpoints_filters[j];
                        if (address[pickpoints_filter['key']] !== undefined && (address[pickpoints_filter['key']] == pickpoints_filter['val'])){ //Если ключ найден и значение совпало
                            show_flag = true;
                        }
                    }
                    if (!show_flag){
                        return;
                    }
                }

                base.features.push({
                    type: 'Feature',
                    id: address.code,
                    geometry: { //Геометрические признаки
                        type: 'Point',
                        coordinates: [address.coordY, address.coordX]
                    },
                    options: {//Характеристики точки
                        preset: address.preset ? address.preset : 'islands#blueIcon'
                    },
                    properties: {//Содержимое
                        hintContent: address.addressInfo,
                        balloonContentHeader: address.addressInfo,
                        balloonContentBody: getBalloonBodyFromAddress(address)
                    }
                });
            });
            return base;
        },

        /**
         * Возвращает установленные фильтры для карты
         *
         * @param filterWrapper - объертка фильтров карты
         * @return []
         */
        getCheckedFilters = function(filterWrapper)
        {
            var filters = [];
            $('input[type="checkbox"]', filterWrapper).each(function (){
                if ($(this).prop('checked')){
                    filters.push({
                        key: $(this).data('key'),
                        val: $(this).val()
                    });
                }
            });
            $('select', filterWrapper).each(function (){
                if ($(this).val() != '') {
                    filters.push({
                        key: $(this).data('key'),
                        val: $(this).val()
                    });
                }
            });
            return filters;
        },


        /**
         * Возвращает тело содержимого всплывающей подсказки
         *
         * @param address - объект адреса
         */
        getBalloonBodyFromAddress = function(address)
        {
            var placemarkBalloonBody = "<table class='placemarkBalloonBody' border='0'>"; //Тело открываемой информации
            if (address.city && address.city.length){
                placemarkBalloonBody += "\
                    <tr>\
                        <td>" + lang.t('Город') + ":</td>\
                        <td>"+address.city+"</td>\
                    </tr>";
            }
            if (address.code && address.code.length){
                placemarkBalloonBody += "\
                    <tr>\
                        <td>" + lang.t('Код пункта') + ":</td>\
                        <td>"+address.code+"</td>\
                    </tr>";
            }
            if (address.address && address.address.length){
                placemarkBalloonBody += "\
                    <tr>\
                        <td>" + lang.t('Адрес') + ":</td>\
                        <td>"+address.address+"</td>\
                    </tr>";
            }
            if (address.worktime && address.worktime.length){
                placemarkBalloonBody += "\
                    <tr>\
                        <td>" + lang.t('Время работы') + ":</td>\
                        <td>"+address.worktime+"</td>\
                    </tr>";
            }
            if (address.phone && address.phone.length){
                placemarkBalloonBody += "\
                    <tr>\
                        <td>" + lang.t('Телефон') + ":</td>\
                        <td>"+address.phone+"</td>\
                    </tr>";
            }
            if (address.payment_by_cards !== undefined){
                placemarkBalloonBody += "\
                    <tr>\
                        <td>" + lang.t('Оплата картой') + ":</td>\
                        <td>"+((address.payment_by_cards) ? lang.t('Да') : lang.t('Нет'))+"</td>\
                    </tr>";
            }
            if (address.cost && address.cost.length){
                placemarkBalloonBody += "\
                    <tr>\
                        <td>" + lang.t('Цена доставки') + ":</td>\
                        <td>"+address.cost+"</td>\
                    </tr>";
            }
            if (address.datefrom && address.datefrom.length){
                placemarkBalloonBody += "\
                    <tr>\
                        <td>" + lang.t('Доставка с<br/>(примерно)') + ":</td>\
                        <td>"+address.datefrom+"</td>\
                    </tr>";
            }
            if ((address.datefrom && address.datefrom.length) && (address.dateto && address.dateto.length) && (address.datefrom != address.dateto)){
                placemarkBalloonBody += "\
                    <tr>\
                        <td>" + lang.t('Доставка по<br/>(примерно)') + ":</td>\
                        <td>"+address.dateto+"</td>\
                    </tr>";
            }

            placemarkBalloonBody += "</table>";
            return placemarkBalloonBody;
        },

        /**
        * Получает все координаты адресов доставки в виде массива с доп данными.
        * Всё извлекается из выпадающего списка с выбором
        *
        * @param {object} select - объект выпадающего списка
        */
        getAddressCoordinatesBySelectAsArray = function(select)
        {
            var addresses = [];
            $("option", select).each(function(){
                if (/^[\],:{}\s]*$/.test($(this).val().replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                    var info = JSON.parse($(this).val());

                    info.selected = $(this).prop('selected');
                    addresses.push(info);
                }
            });
            return addresses;
        },

        /**
         * Привязываем событие к фильтрам
         */
        bindFiltersEvents = function(filterWrapper){
            $('input[type="checkbox"]', filterWrapper).off('change.oneFilter').on('change.oneFilter', function(){
                openMap($(this), false);
            });
            $('select', filterWrapper).off('change.oneFilter').on('change.oneFilter', function(){
                openMap($(this), false);
            });
        },

        /**
        * Привязываем события
        */
        bindEvents = function(){
            //Смена доставки
            $('input[name="delivery"]', $this).on('change', changeDelivery);

            //Смена адреса доставки в выпадающем списке
            $(data.options.select, $this).on('change', changeDeliveryPickpoints);

            //Открытие карты
            $(data.options.openMapButton, $this).on('click', function(){
                openMap($(this), true);
            });
        };

        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        }
   }
})( jQuery );

$(document).ready(function(){
    $.deliveryWidjetCreator();
});
