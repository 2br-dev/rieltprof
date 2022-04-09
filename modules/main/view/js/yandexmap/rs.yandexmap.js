class YandexMap {
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
        this.selector = {};
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
            zoom: this.options.zoom
        });

        this.clusterer = new ymaps.Clusterer();
        this.map.geoObjects.add(this.clusterer);

        this.owner.addEventListener('click', (event) => {
            let target = event.target;
            if (target.closest(this.class.button)) {
                let value = (JSON.parse(target.closest(this.class.button).dataset.value));
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
     * Создаёт точку и размещает её на карте, возвращает созданную точку
     *
     * @param {number} latitude - широта
     * @param {number} longitude - долгота
     * @param {object} properties - данные метки
     * @param {object} options - опции метки
     * @return {object}
     */
    addPoint(latitude, longitude, properties = {}, options = {}) {
        let newObject = new ymaps.Placemark([latitude, longitude], properties, options);
        this.clusterer.add(newObject);
        return newObject;
    }

    /**
     * Размещает ранее созданную точку на карте
     *
     * @param {object} pointObject - объект точки
     */
    addPointObject(pointObject) {
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

    static init(selector, callbackOnInit) {
        document.querySelectorAll(selector).forEach((element) => {
            if (!element.yandexMap) {
                element.yandexMap = new YandexMap(element, callbackOnInit);
            }
        });
    }

    static initListeners() {
        let elementSelector = '.rs-yandexMap';

        document.addEventListener('DOMContentLoaded', () => {
            YandexMap.init(elementSelector);
        });

        // todo кусочек jQuery в нативном классе
        if ($.contentReady) {
            $.contentReady(() => {
                YandexMap.init(elementSelector);
            });
        } else {
            $(document).on('new-content', () => {
                YandexMap.init(elementSelector);
            });
        }

        YandexMap.init(elementSelector);
    }

    static prepare() {
        let script = document.querySelector('script[src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"]');
        if (!script) {
            let script = document.createElement('script');
            script.src = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU';
            document.body.appendChild(script);
            script.onload = () => {
                ymaps.ready(() => {
                    window.RsYandexMapReady = true;
                    document.dispatchEvent(new CustomEvent('YandexMap.ready'));
                });
            };
        } else {
            if ((typeof ymaps)  == 'undefined' || (typeof ymaps.Map) == 'undefined') {
                let interval = setInterval(() => {
                    if ((typeof ymaps) != 'undefined' && (typeof ymaps.Map) != 'undefined') {
                        window.RsYandexMapReady = true;
                        document.dispatchEvent(new CustomEvent('YandexMap.ready'));
                        clearInterval(interval);
                    }
                }, 10);
            } else {
                window.RsYandexMapReady = true;
                document.dispatchEvent(new CustomEvent('YandexMap.ready'));
            }
        }
    }
}

YandexMap.prepare();
YandexMap.initListeners();