/**
 * Инициализирует работу функции добавления/удаления товаров к сравнению
 */
new class Compare extends RsJsCore.classes.component {

    constructor(settings) {
        super();
        let defaults = {
            comparePage: '.rs-compare-page',
            compareBlock: '.rs-compare-block',
            compareButton:'.rs-compare',
            compareItemsCount: '.rs-compare-items-count',
            activeCompareClass: 'rs-in-compare',
            activeClass: 'active',
            doCompare:'.rs-do-compare',
            removeItem:'.rs-remove',
            removeAll: '.rs-remove-all',
            context:'[data-id]',
            doCompareWindowTarget: '_blank',
            doCompareWindowParams: '' //'top=170, left=100, scrollbars=yes, menubar=yes, resizable=yes'

        };
        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
    }

    /**
     * Инициализирует подписку на события
     */
    init() {
        this.blocks = document.querySelectorAll(this.settings.compareBlock);

        this.urls = JSON.parse(document.querySelector('[data-compare-url]').dataset.compareUrl);

        this.utils.on('click', this.settings.compareButton, event => this.toggleCompare(event))
        this.utils.on('click', this.settings.doCompare, event => this.compare(event));

        this.comparePage = document.querySelector(this.settings.comparePage);
        if (this.comparePage) {
            this.utils.on('click', this.settings.removeItem, event => this.removeItem(event), this.comparePage);
            this.utils.on('click', this.settings.removeAll, event => this.removeAll(event), this.comparePage);
        }

        this.initFirstState();
        this.initUpdateTitle();
    }

    add(productId) {
        this.toggleIcons(productId, true);

        let data = new FormData();
        data.append('id', productId);

        this.utils.fetchJSON(this.urls.add, {
            method:'POST',
            body: data
        }).then(response => {
            this.checkActive(response.total);
        });
    }

    remove(productId) {
        this.toggleIcons(productId, false);

        let data = new FormData();
        data.append('id', productId);

        this.utils.fetchJSON(this.urls.remove, {
            method:'POST',
            body: data
        }).then((response) => {
            if (response.success) {
                this.checkActive(response.total);
            }
        });
    }

    /**
     * Переключает состояние кнопок в сравнение
     *
     * @param productId
     * @param active
     */
    toggleIcons(productId, active) {
        let items = document.querySelectorAll('[data-id="' + productId + '"] ' + this.settings.compareButton);
        items.forEach((element) => {
            if (active) {
                element.classList.add(this.settings.activeCompareClass);
            } else {
                element.classList.remove(this.settings.activeCompareClass);
            }
            this.updateTitle(element);
        });

        return items;
    }

    toggleCompare(event) {
        let id = event.rsTarget.closest(this.settings.context).dataset.id;

        if (event.rsTarget.classList.contains(this.settings.activeCompareClass)) {
            this.remove( id );
        } else {
            this.add( id );
        }
        return false;
    }

    removeItem(event) {
        let id = event.rsTarget.closest('[data-compare-id]').dataset.compareId;
        this.remove(id);
    }

    removeAll(event) {

    }


    /**
     * Открывает окно сравнения товаров
     *
     * @param event
     * @returns {boolean}
     */
    compare(event) {
        if (this.blocks[0].classList.contains(this.settings.activeClass)) {
            window.open(this.urls.compare, this.settings.doCompareWindowTarget, this.settings.doCompareWindowParams);
        }
        return false;
    }

    /**
     * Добавляет класс activeClass к блоку, если есть хоть один товар в сравнении
     *
     * @param count
     */
    checkActive(count, productId, productState) {

        this.blocks.forEach((block) => {
            let counter = block.querySelector(this.settings.compareItemsCount);
            counter && (counter.innerHTML = count);

            if (count > 0) {
                block.classList.add(this.settings.activeClass);
            } else {
                block.classList.remove(this.settings.activeClass);
            }
        });

        if (productId) {
            this.toggleIcons(productId, productState)
        }
    }


    /**
     * Обновляет всплывающую подсказку у кнопки
     */
    updateTitle(element) {
        let title = element.classList.contains(this.settings.activeCompareClass)
            ? element.dataset.alreadyTitle : element.dataset.title;
        if (typeof(title) != 'undefined') {
            element.title = title;
        }
    }

    /**
     * Инициализируем title у значков "сравнить"
     */
    initUpdateTitle() {
        document.querySelectorAll(this.settings.compareButton + '[data-title]')
            .forEach((element) => this.updateTitle(element));
    }

    /**
     * Обновляет состояние кнопки В избранное, при включенном кэшировании
     */
    initFirstState() {
        if (global.compareProducts) {
            document.querySelectorAll(this.settings.compareButton).forEach((element) => {
                let productId = element.closest(this.settings.context).dataset.id;
                if (productId) {
                    let isActive = global.compareProducts.indexOf(parseInt(productId)) > -1;
                    isActive ? element.classList.add(this.settings.activeCompareClass)
                        : element.classList.remove(this.settings.activeCompareClass);
                }
            });
        }
    }

    onDocumentReady() {
        this.init();
    }
};