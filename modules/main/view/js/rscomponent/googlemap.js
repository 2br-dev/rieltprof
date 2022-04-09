class GoogleMapItem {
    constructor(element, callbackOnInit = null) {
        this.owner = element;

        if (RsJsCore.plugins.googleMap.ready) {
            this.infowindow = new google.maps.InfoWindow({});
            this.onReady(callbackOnInit);
        } else {
            document.addEventListener('GoogleMap.ready', () => {
                this.infowindow = new google.maps.InfoWindow({});
                this.onReady(callbackOnInit);
            });
        }
    }

    onReady(callbackOnInit) {
        this.selector = {
            button: '.button',
        };
        this.class = {
            button: 'button',
        };
        this.options = {
            center: [55.76, 37.64], // центр карты
            zoom: 10, // масштаб карты
            checkZoomRange: true, // автоматическая корректировка масштаба
            callbackOnInit: false,
            dispatchEventTarget: undefined,
        };

        if (this.owner.dataset.mapOptions) {
            this.options = Object.assign(this.options, JSON.parse(this.owner.dataset.mapOptions));
        }

        this.map = new google.maps.Map(this.owner, {
            center: {
                lat: this.options.center[0],
                lng: this.options.center[1],
            },
            zoom: this.options.zoom,
        });

        this.pointCluster = new MarkerClusterer(this.map, {}, {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});

        this.owner.addEventListener('click', (event) => {
            let target = event.target;
            if (target.closest(this.selector.button)) {
                let value = (JSON.parse(target.closest(this.selector.button).dataset.value));
                let eventProperties = {
                    detail: value,
                    cancelable: true,
                };
                if (!this.options.dispatchEventTarget) {
                    eventProperties['bubbles'] = true;
                }
                let event = new CustomEvent('map.buttonClick', eventProperties);

                if (this.options.dispatchEventTarget) {
                    this.options.dispatchEventTarget.dispatchEvent(event);
                } else {
                    this.owner.dispatchEvent(event);
                }
            }
        });

        if (callbackOnInit) {
            callbackOnInit(this);
        }
        this.callback('OnInit');
    }

    /**
     * Создаёт точку и возвращает её
     *
     * @param {number} latitude - широта
     * @param {number} longitude - долгота
     * @param {object} properties - данные метки
     * @param {object} options - опции метки
     * @return {object}
     */
    createPoint(latitude, longitude, properties = {}, options = {}) {
        latitude = parseFloat(latitude);
        longitude = parseFloat(longitude);
        let newObject = new google.maps.Marker({
            'position': {lat: latitude, lng: longitude},
        });
        return newObject;
    }

    /**
     * Устанавливает у созданной точки содержимое балуна
     *
     * @param {object} pointObject - объект точки
     * @param {string} header - заголовок
     * @param {string} body - содержимое
     * @param {string} footer - подвал
     * @return {void}
     */
    pointSetBalloon(pointObject, header, body, footer) {
        pointObject.balloonContent = '<h4>' + header + '</h4><p>' + body + '</p><div>' + footer + '</div>';

        pointObject.addListener('click', () => {
            this.infowindow.setContent(pointObject.balloonContent);
            this.infowindow.open({
                anchor: pointObject,
                map: this.map,
                shouldFocus: false,
            });
        });
    }

    pointOpenBalloon(pointObject) {
        this.infowindow.setContent(pointObject.balloonContent);
        this.infowindow.open({
            anchor: pointObject,
            map: this.map,
            shouldFocus: false,
        });
    }

    /**
     * Размещает ранее созданную точку на карте
     *
     * @param {object} pointObject - объект точки
     */
    addPoint(pointObject) {
        this.pointCluster.addMarker(pointObject);
    }

    /**
     * Удаляет точку с карты
     *
     * @param {object} pointObject - объект точки
     */
    removePoint(pointObject) {
        this.pointCluster.removeMarker(pointObject);
    }

    /**
     * Устанавливает границы карты
     *
     * @param {float} top - верхняя граница
     * @param {float} bottom - нижняя граница
     * @param {float} left - левая граница
     * @param {float} right - правая граница
     */
    setBounds(top, bottom, left, right) {
        this.map.fitBounds({north: top, south: bottom, west: left, east: right});
    }

    /**
     * Устанавливает масштаб карты
     *
     * @param {number} zoom - масштаб
     */
    setZoom(zoom) {
        zoom = parseInt(zoom);
        this.map.setZoom(zoom);
    }

    /**
     * Устанавливает центр карты
     *
     * @param {number} latitude - широта
     * @param {number} longitude - долгота
     * @param {number} zoom - масштаб
     */
    setCenter(latitude, longitude, zoom = null) {
        latitude = parseFloat(latitude);
        longitude = parseFloat(longitude);
        this.map.setCenter({lat: latitude, lng: longitude});
        if (zoom) {
            this.setZoom(zoom);
        }
    }

    /**
     * Устанавливает возможность изменения масштаба колёсиком мыши
     *
     * @param {bool} value - значение
     */
    setEnableScrollZoom(value) {
        // todo реализовать метод
    }

    /**
     * Возвращает HTML для кнопки
     *
     * @param {string} value - передаваемое значение
     * @param {string} title - отображаемая надпись
     * @param {string} css_class - css класс
     * @return {string}
     */
    htmlButton(value, title, css_class = '') {
        return "<button data-value='" + JSON.stringify(value) + "' class='" + this.class.button + " " + css_class + "'>" + title + "</button>";
    }

    /**
     * Вызывает пользовательский callback
     *
     * @param {string} name - имя функции
     */
    callback(name) {
        let option_name = 'callback' + name;
        if (this.options[option_name]) {
            window[this.options[option_name]](this);
        }
    }

    /**
     * Устанавливает цель событий карты
     *
     * @param {element} element - цель событий
     */
    setDispatchEventTarget(element) {
        this.options.dispatchEventTarget = element;
    }
};

initGoogleMap = () => {};

new class GoogleMap extends RsJsCore.classes.plugin {
    constructor() {
        super();
        this.ready = false;

        document.addEventListener('DOMContentLoaded', (event) => {
            this.prepare();
        });
    }

    init(element, callback) {
        if (!element.map) {
            element.map = new GoogleMapItem(element, callback);
        }
    }

    prepare() {
        let promiseCluster = new Promise((resolve) => {
            let js_source_cluster ='https://unpkg.com/@googlemaps/markerclustererplus/dist/index.min.js';
            let script_cluster = document.querySelector('script[src="' + js_source_cluster + '"]');
            if (!script_cluster) {
                let script_cluster = document.createElement('script');
                script_cluster.src = js_source_cluster;
                document.body.appendChild(script_cluster);

                script_cluster.onload = () => {
                    resolve();
                }
            } else {
                resolve();
            }
        });

        let promiseMap = new Promise((resolve) => {
            let js_source ='https://maps.googleapis.com/maps/api/js?callback=initGoogleMap';
            if (global.mapParams.googleApiKeyMap) {
                js_source = js_source + '&key=' + global.mapParams.googleApiKeyMap;
            }
            let script = document.querySelector('script[src="' + js_source + '"]');
            if (!script) {
                let script = document.createElement('script');
                script.src = js_source;
                document.body.appendChild(script);

                initGoogleMap = resolve;
            } else {
                resolve();
            }
        });

        Promise.all([promiseCluster, promiseMap]).then(() => {
            RsJsCore.plugins.googleMap.ready = true;
            document.dispatchEvent(new CustomEvent('GoogleMap.ready'));
        });
    }
};

