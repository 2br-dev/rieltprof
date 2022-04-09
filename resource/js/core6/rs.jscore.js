/**
 * Общий глобальный корневой JS-объект темы оформления ReadyScript.
 * Зависит от corelang.js
 */
window.RsJsCore = {
    /**
     * Здесь можно хранить любые переменные, характеризующие текущее состояние страницы.
     * Это общая глобальная переменная между всеми плагинами и компонентами. Например, здесь можно хранить флаг о том, что
     * сейчас на сайте идет загрузка чего-то и отображен overlay, значит нужно приостановить другие действия,
     * которые могут вызвать конфликт, например повторное выполнение аналогичного действия.
     */
    state: {},

    /**
     * Здесь размещаются любые полезные статические функции общего назначения.
     * Пример вызова: RsJsCore.utils.fetchJSON(...);
     */
    utils: {
        /**
         * Эмитирует логику работы jQuery().on(event, selector, callback).
         * Запускает callback, когда element получает событие eventName, возникшее на элементе selector
         * Возвращает элемент, соответствующий selector в свойстве event.rsTarget
         *
         * @param eventName Имя события
         * @param selector Селектор, на котором ожидается событие
         * @param callback Callback-функция
         * @param element Элемент, на котором ожидается событие. По умолчанию document
         */
        on(eventName, selector, callback, element) {
            element = element ? element : document;
            element.addEventListener(eventName, (event) => {
                event.rsTarget = event.target.closest(selector);
                if (event.rsTarget) {
                    callback(event);
                }
            });
        },
        /**
         * Выполняет запрос к удаленному серверу и ожидает от него ответ в формате JSON. Обертка над системной fetch.
         * Данная функция обрабатывает ошибочные ответы и визуализирует их.
         *
         * @param url
         * @param options
         * @returns {Promise<any>}
         */
        fetchJSON(url, options) {
            let defaults = {
                credentials:'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };

            return fetch(url, {...defaults, ...options})
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(lang.t('Некорректный статус ответа сервера. ' + response.statusText));
                        return;
                    }
                    return response.json()
                        .then((json) => {
                            if (json.reloadPage) {
                                //Перезагрузка страницы всегда методом GET
                                location.replace(window.location.href);
                            }
                            if (json.windowRedirect) {
                                location.href = json.windowRedirect;
                            }

                            return json;
                        });
                }).catch((error) => {
                    if (error.name !== 'AbortError') {
                        if (RsJsCore.plugins.toast) { //Если установлен плагин, для отображения тостов, то отображем ошибку
                            RsJsCore.plugins.toast.show(lang.t('Ошибка'), error.message, {
                                className: 'error'
                            });
                        }
                        throw error;
                    }
                });
        },
        /**
         * Возвращает строку, где первая буква всегда прописная
         *
         * @param name
         * @returns {string}
         */
        getLowerCamelName(name) {
            return name[0].toLowerCase() + name.slice(1);
        }
    },

    plugins: {},
    components: {},
    /**
     * Секция для перегрузки настроек компонентов.
     * Подключайте кастомные настройки до загрузки компонента.
     *
     * Пример использования:
     * RsJsCore.settings.component.listProducts = {...тут кастомные настройки...}
     */
    settings: {
        component: {},
        plugin: {}
    },
    /**
     * Регистрирует объект плагина
     *
     * @param key
     * @param plugin
     */
    addPlugin(plugin, key) {
        if (!key) {
            key = this.utils.getLowerCamelName(plugin.constructor.name);
        }
        this.plugins[key] = plugin;
    },

    /**
     * Регистрирует объект компонента
     *
     * @param component
     * @param key
     */
    addComponent(component, key) {
        if (!key) {
            key = this.utils.getLowerCamelName(component.constructor.name);
        }
        this.components[key] = component;
    },

    /**
     * Здесь размещаются базовые классы для различных сущностей
     */
    classes: {
        /**
         * Базовый класс для любых классов, содержащих логику поведения компонента или целой страницы.
         * Пример: Компонент "Оформление заказа" - обеспечивает корректную работу всей страницы
         * Компонент "Отзывы о товаре", компонент "Блок выбора города",...
         */
        component: class {
            constructor() {
                this.utils = RsJsCore.utils;
                this.plugins = RsJsCore.plugins;
                RsJsCore.addComponent(this);
            }

            getExtendsSettings() {
                let selfName = this.utils.getLowerCamelName(this.constructor.name);
                if (RsJsCore.settings.component[selfName]) {
                    return RsJsCore.settings.component[selfName];
                }
                return {};
            }
        },

        /**
         * Базовый класс для любых классов-плагинов. Плагин - это объект, который используется
         * несколькими компонентами. Пример: модальные окна, работа с cookie, ...
         */
        plugin: class {
            constructor() {
                this.utils = RsJsCore.utils;
                this.plugins = RsJsCore.plugins;
                RsJsCore.addPlugin(this);
            }

            getExtendsSettings() {
                let selfName = this.utils.getLowerCamelName(this.constructor.name);
                if (RsJsCore.settings.plugin[selfName]) {
                    return RsJsCore.settings.plugin[selfName];
                }
                return {};
            }
        }
    },

    /**
     * Инициализирует запуск lifecycle методов в компонентах
     */
    init() {
        document.addEventListener('DOMContentLoaded', (event) => {
            for(let key in this.components) {
                if (typeof(this.components[key].onDocumentReady) == 'function') {
                    this.components[key].onDocumentReady.call(this.components[key], event);
                }
                if (typeof(this.components[key].onContentReady) == 'function') {
                    this.components[key].onContentReady.call(this.components[key], event);
                }
            }

            //Событие выполняется, когда все компоненты уже выполнили onDocumentReady,
            for(let key in this.components) {
                if (typeof (this.components[key].onAfterDocumentReady) == 'function') {
                    this.components[key].onAfterDocumentReady.call(this.components[key], event);
                }
            }
        });

        document.addEventListener('new-content', (event) => {
            for(let key in this.components) {
                if (typeof(this.components[key].onContentReady) == 'function') {
                    this.components[key].onContentReady.call(this.components[key], event);
                }
            }
        });
    }
};

RsJsCore.init();