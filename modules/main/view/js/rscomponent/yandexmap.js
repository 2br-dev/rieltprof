class YandexMapItem {
    constructor(element, callbackOnInit = null) {
        this.owner = element;

        if (window.RsYandexMapReady) {
            this.onReady(callbackOnInit);
        } else {
            document.addEventListener('YandexMap.ready', () => {
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

        this.map = new ymaps.Map(this.owner, {
            center: this.options.center,
            zoom: this.options.zoom,
            controls: ['zoomControl', 'typeSelector']
        });

        this.clusterer = new ymaps.Clusterer();
        this.map.geoObjects.add(this.clusterer);

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
     * @return {object}
     */
    createPoint(latitude, longitude) {
        return new ymaps.Placemark([latitude, longitude]);
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
        pointObject.properties.singleSet('balloonContentHeader', header);
        pointObject.properties.singleSet('balloonContentBody', body);
        pointObject.properties.singleSet('balloonContentFooter', footer);
    }

    pointOpenBalloon(pointObject) {
        let geoObjectState = this.clusterer.getObjectState(pointObject);

        if (geoObjectState.isShown) {
            // Если объект попадает в кластер, открываем балун кластера с нужным выбранным объектом.
            if (geoObjectState.isClustered) {
                geoObjectState.cluster.state.set('activeObject', pointObject);
                this.clusterer.balloon.open(geoObjectState.cluster);
            } else {
                // Если объект не попал в кластер, открываем его собственный балун.
                pointObject.balloon.open();
            }
        }
    }

    /**
     * Размещает ранее созданную точку на карте
     *
     * @param {object} pointObject - объект точки
     */
    addPoint(pointObject) {
        this.clusterer.add(pointObject);
    }

    /**
     * Удаляет точку с карты
     *
     * @param {object} pointObject - объект точки
     */
    removePoint(pointObject) {
        this.clusterer.remove(pointObject);
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
        this.map.setBounds([[bottom, left], [top, right]], {
            checkZoomRange: this.options.checkZoomRange,
        });
    }

    /**
     * Устанавливает масштаб карты
     *
     * @param {number} zoom - масштаб
     */
    setZoom(zoom) {
        this.map.setZoom(zoom, {
            checkZoomRange: this.options.checkZoomRange,
        });
    }

    /**
     * Устанавливает центр карты
     *
     * @param {number} latitude - широта
     * @param {number} longitude - долгота
     * @param {number} zoom - масштаб
     */
    setCenter(latitude, longitude, zoom = null) {
        if (zoom === null) {
            zoom = this.map.getZoom();
        }
        this.map.setCenter([latitude, longitude], zoom, {
            checkZoomRange: this.options.checkZoomRange,
        });
    }

    /**
     * Устанавливает возможность изменения масштаба колёсиком мыши
     *
     * @param {bool} value - значение
     */
    setEnableScrollZoom(value) {
        if (value) {
            this.map.behaviors.enable('scrollZoom');
        } else {
            this.map.behaviors.disable('scrollZoom');
        }
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

new class YandexMap extends RsJsCore.classes.plugin {
    constructor() {
        super();
        this.ready = false;

        document.addEventListener('DOMContentLoaded', (event) => {
            this.prepare();
        });
    }

    init(element, callback) {
        if (!element.map) {
            element.map = new YandexMapItem(element, callback);
        }
    }

    prepare() {
        let script = document.querySelector('script[src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"]');
        if (!script) {
            let script = document.createElement('script');
            script.src = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU';
            document.body.appendChild(script);
            script.onload = () => {
                ymaps.ready(() => {
                    this.ready = true;
                    window.RsYandexMapReady = true;
                    document.dispatchEvent(new CustomEvent('YandexMap.ready'));
                });
            };
        }
    }
};
