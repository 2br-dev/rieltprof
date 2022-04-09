/**
 * Плагин, позволяющий открывать диалоговые окна с предварительной загрузкой данных через AJAX.
 * Переводит любую отправку формы внутри окна в режим AJAX.
 *
 * Зависит от RsJsCore.plugins.modal
 */
new class OpenDialog extends RsJsCore.classes.plugin {

    constructor() {
        super();
        let defaults = {
            inDialogSelector: '.rs-in-dialog',
            disableClass: 'disabled'
        };
        this.settings = {...defaults, ...this.getExtendsSettings()};

        if (RsJsCore.plugins.modal) {
            this.modal = RsJsCore.plugins.modal;
        } else {
            console.error(lang.t('Плагин для модальных окон RsJsCore.plugins.modal не установлен'));
        }

        this._bindEvents();
    }

    /**
     * Устанавливает необходимые обработчики событий
     *
     * @private
     */
    _bindEvents() {
        document.addEventListener('click', (e) => {
            let element = e.target.closest(this.settings.inDialogSelector);
            if (element) {
                let href = element.dataset.href ? element.dataset.href : element.href;
                if (href) {
                    if (!element.classList.contains(this.settings.disableClass)) {
                        this.show({
                            url: href
                        });
                    }

                    e.preventDefault();
                }
            }
        });
    }

    /**
     * Загружает данные по URL, открывает диалоговое окно с загруженными данными
     * @param options
     * @param requestOptions
     */
    show(options, requestOptions) {
        let defaults = {
            url:'',
            data: {},
            callback: null,
            bindSubmit: true
        };
        this.options = {...defaults, ...options};

        let url = new URL(this.options.url, window.location.origin);
        url.searchParams.append('dialogWrap', 1);
        Object.keys(this.options.data).forEach(key => url.searchParams.append(key, this.options.data[key]));

        this._requestContent(url, requestOptions);
    }

    /**
     * Выполняет запрос к серверу на получение контента и открывает его в модальном окне
     *
     * @param url
     * @param options
     * @private
     */
    _requestContent(url, options) {
        this.showOverlay();

        return RsJsCore.utils.fetchJSON(url, options).then(response => {
            this._prepareResponse(response);
            if (response.html) {
                this.modal.open(response.html, (event) => {
                    this._prepareHtml(response, event.target);
                });
            }

        }).finally(() => {
            this.hideOverlay();
        });
    }

    /**
     * Обрабатывает специфические json ключи, которые отвечают за действия.
     *
     * @param response
     * @private
     */
    _prepareResponse(response) {
        if (response.closeDialog) {
            if (this.modal && this.modal.isOpen()) {
                this.modal.close();
                response.html = null;
            }
        }

        if (response.redirect) {
            this.show({
                url: response.redirect
            });
            response.html = null;
        }

        return true;
    }

    /**
     * Выполняет обработку полученного с сервера HTML-кода.
     * Навешивает необходимые события
     *
     * @param response
     * @param element
     * @private
     */
    _prepareHtml(response, element)
    {
        if (this.options.bindSubmit) {
            element.querySelectorAll('form').forEach((form) => {
                this._ajaxForm(form);
            });
        }

        if (this.options.callback) {
            this.options.callback.call(this, response, element);
        }
    }

    /**
     * Отправляет все формы в диалоговом окне на сервер с помощью AJAX
     *
     * @param form
     * @private
     */
    _ajaxForm(form) {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            let data = new FormData(form);
            let formAction = form.dataset.ajaxAction ? form.dataset.ajaxAction : form.getAttribute('action');
            if (!formAction) formAction = '';
            formAction += formAction.indexOf('?') === -1 ? '?dialogWrap=1' : '&dialogWrap=1';

            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((element) => {
                element.disabled = true;
            });

            this._requestContent(formAction, {
                method: 'POST',
                body: data
            })
            .finally(() => {
                form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((element) => {
                    element.disabled = false;
                });
            });
        });
    }

    /**
     * Отображает прелоадер, в момент, когда происходит загрузка
     * данных с сервера, до открытия окна
     */
    showOverlay() {
        if (this.modal.isOpen()) {
            this.modal.showLoader();
        }
    }

    /**
     * Скрывает прелоадер предшествующий отображению окна
     */
    hideOverlay() {
        if (this.modal.isOpen()) {
            this.modal.hideLoader();
        }
    }
};