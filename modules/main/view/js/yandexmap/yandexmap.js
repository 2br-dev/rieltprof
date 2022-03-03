/**
 * Плагин, инициализирующий работу Яндекс карты
 */
(function ($) {
    $.fn.initYandexMap = function (method) {
        var defaults = {
                zoom: 13,                //Масштаб карты
                block_mouse_zoom: true,              //Блокировать изменение колесом мышки масштаба
                mapType: 'yandex#map',      //Тип карты
                yMap: {},                //Объект Яндекс Карты
                points: {},                //Объект Яндекс Карты
            },
            args = arguments;

        return this.each(function () {
            var $this = $(this),
                data = $this.data('initYandexMap');

            var methods = {
                init: function (initoptions) {
                    if (data) return;
                    data = {};
                    $this.data('initYandexMap', data);
                    data.options = $.extend({}, defaults, initoptions);

                    methods.initMap(); //Инициализируем карту
                },

                /**
                 * Инициализируем Яндекс карту
                 */
                initMap: function () {
                    $(document).ready(function () {
                        data.options.points = $this.data('points'); //Точки
                        if (!data.options.points.length) {
                            console.log('Яндекс карту с идентификатором ' + $this.attr('id') + ' невозможно показать. Нет точек.');
                            return false;
                        }
                        ymaps.ready(function () { //После готовности яндекс карты

                            data.options.yMap = new ymaps.Map($this.attr('id'), {
                                center: [data.options.points[0].lat, data.options.points[0].lon], //Центр по умолчанию в первой точке
                                type: data.options.mapType,
                                zoom: data.options.zoom
                            });

                            if (data.options.block_mouse_zoom) { //Если нужно блокировать зум при прокрутке
                                data.options.yMap.behaviors.disable("scrollZoom");
                            }
                            if (data.options.points.length == 1) {
                                var myPlacemark = new ymaps.GeoObject({
                                    geometry: {
                                        type: "Point",
                                        coordinates: [data.options.points[0].lat, data.options.points[0].lon]
                                    },
                                    properties: {
                                        balloonContent: data.options.points[0].balloonContent
                                    }
                                });

                                data.options.yMap.geoObjects.add(myPlacemark);
                            } else { //Если несколько точек
                                addPlacemarks(); //Добавляет точки на карту   
                            }
                        });
                    });

                }
            };

            //private 
            /**
             * Добавляет точки на карту
             *
             */
            var addPlacemarks = function () {
                //Менеджер объектов
                var objectManager = new ymaps.ObjectManager({
                    // Чтобы метки начали кластеризоваться, выставляем опцию.
                    clusterize: true,
                    // ObjectManager принимает те же опции, что и кластеризатор.
                    gridSize: 32
                });

                //Соберём коллекцию точек
                var base = {
                    type: 'FeatureCollection',
                    features: []
                };


                $.each(data.options.points, function (key, point) {

                    base.features.push({
                        type: 'Feature',
                        id: 'point' + key,
                        geometry: {
                            type: 'Point',
                            coordinates: [point.lat, point.lon]
                        },
                        properties: {
                            balloonContent: point.balloonContent
                        }
                    });
                });

                objectManager.add(base);
                data.options.yMap.geoObjects.add(objectManager);
                data.options.yMap.setBounds(objectManager.getBounds(), {
                    checkZoomRange: true,
                    useMapMargin: true,
                    zoomMargin: [100]
                });
            };

            if (methods[method]) {
                methods[method].apply(this, Array.prototype.slice.call(args, 1));
            } else if (typeof method === 'object' || !method) {
                return methods.init.apply(this, args);
            }
        });
    }

})(jQuery);
