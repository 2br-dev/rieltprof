{$value = $field->get()}
{if isset($value.address)} {$address = $value.address} {else} {$address = ''} {/if}
{if isset($value.lat)} {$lat = $value.lat} {else} {$lat = 0} {/if}
{if isset($value.lng)} {$lng = $value.lng} {else} {$lng = 0} {/if}
{$name_address = $field->getName()|cat:'[address]'}
{$name_lat = $field->getName()|cat:'[lat]'}
{$name_lng = $field->getName()|cat:'[lng]'}
<div class="yaAddressField">
    <input type="text" name="{$name_address}" class="autocomplete" placeholder="Введите адрес" value="{$address}">
    <input type="hidden" name="{$name_lat}" value="{$lat}">
    <input type="hidden" name="{$name_lng}" value="{$lng}">
</div>

<div class="geoCoorContainer">
    <div id="ardessResults">
        {* Сюда будут вставлены результаты гео-ответа от Yandex *}
    </div>
</div>

{* Подключение карты *}
<div id="map" class="yaMap"></div>
{addJS file="//api-maps.yandex.ru/2.1/?lang=ru_RU" basepath="root"}
{addJS file="jquery.ui/jquery.autocomplete.js" basepath="common"}
<script type="text/javascript">
    var ya_qt,                //Таймер
            ya_queryItemsCnt = 5, //Количество элементов для подгрузки
            ya_queryUrl      = 'https://geocode-maps.yandex.ru/1.x/', //Url куда стучатся за результами поиска
            ya_mapQuery,     //Запрос карты
            ya_time = 700;   //Время ожидания ввода


    $.allReady(function() {
        ymaps.ready(init); //Инициализация Yandex карты
        var myMap;       //Карта
        var myPlacemark; //Метка


        /**
         * Устанавливает координаты в скрытое поле в нужном формате
         * В качестве аргумента передаётся массив со значениями x и y
         *
         * @param Array coords - массив с координатами точки
         */
        function setCoordsToInput(coords){

            $("input[name='{$name_lat}']").val(coords[0]);
            $("input[name='{$name_lng}']").val(coords[1]);
        }


        function init(){
            //Ставим параметры карты
            myMap = new ymaps.Map("map", {
                center: [{$lat}, {$lng}],
                zoom: 12
            });

            //Ставим параметры метке
            myPlacemark = new ymaps.Placemark([{$lat}, {$lng}], {
                balloonContent: '{$address}'
            },{
                draggable: true
            });

            myMap.geoObjects.add(myPlacemark); //Добавляем метку
            setCoordsToInput([{$lat}, {$lng}]);//Выставляем координаты по умолчанию

            //Навесим события
            /**
             * Клик по карте перенос метки.
             */
            myMap.events.add('click',function(e){
                //Текщие координаты щелчка
                var coords = e.get('coords');
                myPlacemark.geometry.setCoordinates(coords);
                setCoordsToInput(coords);
            });

            /**
             * Перетаскивание метки
             */
            myPlacemark.events.add('dragend', function(e) {
                // Получение ссылки на объект, который был передвинут.
                var thisPlacemark = e.get('target');
                // Определение координат метки
                var coords = thisPlacemark.geometry.getCoordinates();
                setCoordsToInput(coords);
            });
        }

        /**
         * Автозаполнение для поля адреса
         */
        $("body .yaAddressField .autocomplete").each(function(i){
            _this = $(this);
            $(this).autocomplete({
                appendTo: "#ardessResults", //Куда будут подгружаться элементы
                minLength: 3, // Минимальная длина текста адреса для запроса
                delay: 500,
                /**
                 * Фокус на пункте выпадающего меню
                 */
                focus: function( event, ui ) {
                    $( "input[name='{$name_address}']" ).val( ui.item.value );
                    return false;
                },
                /**
                 * Нажатие на выбраном пункте выпадающего меню,
                 * установка координат и показ на карте
                 *
                 * @param event event - событие
                 * @param object ui - объект выбраного пункта меню
                 */
                select: function( event, ui ) {
                    var coords = ui.item.coords;
                    myMap.setCenter(coords);                     //Ставим в центр карты
                    myPlacemark.geometry.setCoordinates(coords); //Ставим метку
                    setCoordsToInput(coords);                    //Выставляем в скрытые поля координаты
                    $("input[name='{$name_address}']").val( ui.item.value );
                    return false;
                },
                /**
                 * Подгрузка значение от yandex
                 *
                 * @param string request - url для запроса
                 * @param function response - функция в которую передаютмя значения
                 */
                source: function(request, response){
                    $.ajax({
                        type:'POST',
                        url:ya_queryUrl,
                        data: {
                            format:'json',
                            results: ya_queryItemsCnt,
                            geocode: request.term
                        },
                        success: function(data) {
                            var ya_array = []; //Массив для autocomplete

                            var collection = data.response.GeoObjectCollection;
                            var streetObjs = collection.featureMember; //Найденные элементы

                            if (streetObjs.length>0){
                                $(streetObjs).each(function(i,item){
                                    var coords = item.GeoObject.Point.pos.split(' ');
                                    ya_array.push({
                                        label: item.GeoObject.name,
                                        value: item.GeoObject.name,
                                        desc: item.GeoObject.description,
                                        coords: [coords[1],coords[0]]
                                    });
                                });
                            }else{
                                ya_array.push({
                                    label: lang.t('Ничего не найдено'),
                                    value: '',
                                });
                            }
                            response(ya_array);
                        }
                    });

                }
            });
            /**
             * Обработаем ответа сервера, прорисовка элементов
             */
            $(this).data('ui-autocomplete')._renderItem = function(ul, item){
                var oneItem = $('<li data-coords=""><a href="#" onclick="return false;"><span id="geoTitle" class="title"></span><i id="geoDescription" class="position"></i></a></li>');
                oneItem.data('coords',item.coords[1]+";"+item.coords[0]);
                $('#geoTitle',oneItem).html(item.label);
                $('#geoDescription',oneItem).html(item.desc);

                ul.addClass('ardessResults');

                return oneItem.appendTo(ul);
            }
        });
    });
</script>