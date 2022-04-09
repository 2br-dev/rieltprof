/**
 * Плагин инициализирует работу Ajax-пагинаторов.
 */
new class AjaxPaginator extends RsJsCore.classes.plugin {
    init(selector, settings, context) {
        (context ? context : document).querySelectorAll(selector).forEach((element) => {
            if (!element.ajaxPaginator) {
                element.ajaxPaginator = new ElementAjaxPaginator(element, settings);
            }
        });
    }
};

/**
 * Класс одного паинатора
 */
class ElementAjaxPaginator {

    constructor(startElement, settings) {
        let defaults = {
            method: 'GET',
            findElement: '',
            loaderButton: '.rs-ajax-paginator',
            loaderBlock: '', // селектор блока пагинации (loaderButton должен находиться в нём)
            loadingClass: 'rs-in-loading',
            appendElement: '',
            contextElement: null,
            clickOnScroll: false,
            scrollDistance: 100,
        };

        if (startElement.dataset.paginationOptions) {
            //Получим все настройки у элемента
            defaults = {...defaults, ...JSON.parse(startElement.dataset.paginationOptions)};
        }
        this.settings = {...defaults, ...settings};

        if (!this.settings.findElement) {
            this.settings.findElement = this.settings.appendElement;
        }

        this.element = startElement;

        this.bindEvents();
    }

    /**
     * Навешивает обработчики событий
     */
    bindEvents() {
        if (this.element) {
            this.element.addEventListener('click', event => this.load(event));
        }
    }

    /**
     * Загружает новый контент
     *
     * @param event
     * @returns {boolean}
     */
    load(event) {
        let target = event.target.closest(this.settings.loaderButton);
        if (target.classList.contains(this.settings.loadingClass)) return false;

        let url = target.hasAttribute('href')
            ? target.getAttribute('href') : target.dataset.url;

        target.classList.add(this.settings.loadingClass);

        RsJsCore.utils.fetchJSON(url, {
            method: this.settings.method
        }).then(response => this.onResponse(url, response));

    }

    /**
     * Обрабатывает загруженный контент перед вставкой
     *
     * @param url
     * @param response
     */
    onResponse(url, response) {

        let parsed = document.createElement('div');
        parsed.innerHTML = response.html;
        let appendElement = parsed.querySelector(this.settings.findElement);

        let destination = (this.settings.contextElement ? this.settings.contextElement : document)
            .querySelector(this.settings.appendElement);

        while (appendElement.children.length > 0) {
            destination.append(appendElement.children[0]);
        }

        let newLoader = parsed.querySelector(this.settings.loaderButton);
        let replaceElement = this.element;

        if (this.settings.loaderBlock) {
            newLoader = parsed.querySelector(this.settings.loaderBlock);
            replaceElement = (this.settings.contextElement ? this.settings.contextElement : document)
                .querySelector(this.settings.loaderBlock);
        }

        this.element = parsed.querySelector(this.settings.loaderButton);

        if (newLoader) {
            replaceElement.parentNode.replaceChild(newLoader, replaceElement);
            this.bindEvents();
        } else {
            replaceElement.remove();
        }

        if (this.settings.replaceBrowserUrl) {
            history.replaceState(null, null, url);
        }

        document.body.dispatchEvent(new CustomEvent('new-content', {bubbles: true}));
    }
}