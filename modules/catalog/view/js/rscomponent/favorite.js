/**
 * Активирует работу избранного на всех страницах
 */
new class Favorite extends RsJsCore.classes.component {

    constructor(settings) {
        super();
        let defaults = {
            favorite: '.rs-favorite-page',            //Селектор блока, в котором отображаются товары на странице избранного
            favoriteBlock: '.rs-favorite-block',  //Блок "избранное"
            favoriteLink: '.rs-favorite-link',    //Ссылка на front контроллер
            favoriteCount: '.rs-favorite-items-count',  //Количество избранных товаров
            favoriteButton: '.rs-favorite',       //Кнопка добавить/удалить избранное
            inFavoriteClass: 'rs-in-favorite',    //Класс указывающий, что товар в избранном
            activeClass: 'active',            //Класс, указывающий, что в избранном есть товары
            context: '[data-id]'
        };
        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
    }

    /**
     * Инициализирует подписку на события
     */
    init() {
        this.blocks = document.querySelectorAll(this.settings.favoriteBlock);
        let favUrlElement = document.querySelector('[data-favorite-url]');
        this.addUrl = favUrlElement && favUrlElement.dataset.favoriteUrl;
        if (this.addUrl) {
            this.favoriteCounterElement = document.querySelector(this.settings.favoriteCount);
            this.utils.on('click', this.settings.favoriteButton, event => this.toggleFavorite(event))
            this.utils.on('click', this.settings.favoriteLink, event => {
                location.href = event.rsTarget.dataset.href;
            });

            this.initFirstState();
            this.initUpdateTitle();
        } else {
            console.error(lang.t('Не найден элемент [data-favorite-url], содержащий ссылку на контроллер управления избранным'));
        }
    }

    /**
     * Переключает состояние товара. Помещает/Исключает товар из избранного
     *
     * @param event
     */
    toggleFavorite(event) {
        let productId = event.rsTarget.closest('[data-id]').dataset.id;

        if (event.rsTarget.classList.contains(this.settings.inFavoriteClass)) {
            this.remove(productId);
        } else {
            this.add(productId);
        }

        event.preventDefault();
    }

    /**
     * Добавляет товар в избранное
     *
     * @param productId
     */
    add(productId) {
        this.toggleIcons(productId, true);

        let data = new FormData();
        data.append('Act', 'add');
        data.append('product_id', productId);

        this.utils.fetchJSON(this.addUrl, {
            method:'POST',
            body: data
        }).then((response) => {
            this.checkActive(response.count, productId, true);
        });
    }

    /**
     * Переключает состояние кнопок в избранное
     *
     * @param productId
     * @param active
     */
    toggleIcons(productId, active) {
        let items = document.querySelectorAll('[data-id="' + productId + '"] ' + this.settings.favoriteButton);
        items.forEach((element) => {
            if (active) {
                element.classList.add(this.settings.inFavoriteClass);
            } else {
                element.classList.remove(this.settings.inFavoriteClass);
            }
            this.updateTitle(element);
        });

        return items;
    }

    /**
     * Удаление товара из избранного
     *
     * @param productId
     */
    remove(productId) {
        let isFavoritePage;
        let items = this.toggleIcons(productId, false);
        items.forEach((element) => {
                isFavoritePage = element.closest(this.settings.favorite);
                if (isFavoritePage) {
                    element.closest(this.settings.context).style.opacity = 0.5;
                }
            });

        if (items.length) {
            let data = new FormData();
            data.append('Act', 'remove');
            data.append('product_id', productId);

            this.utils.fetchJSON(this.addUrl, {
                method:'POST',
                body: data
            }).then((response) => {
                if (response.success) {
                    this.checkActive(response.count, productId, false);

                    if (isFavoritePage) {
                        this.updateBody();
                    }
                }

            });
        }
    }

    /**
     * Обновляет страницу с помощью ajax
     */
    updateBody() {
        this.utils.fetchJSON(window.location.href)
            .then((response) => {
                let element = document.querySelector(this.settings.favorite);
                if (element) {
                    let parent = element.parentNode;
                    element.insertAdjacentHTML('afterend', response.html);
                    element.remove();
                    parent.dispatchEvent(new CustomEvent('new-content', {bubbles: true}));
                    this.initUpdateTitle();
                }
            });
    }

    /**
     * Устанавливает нужный title, в зависимости от того, добавлен ли в избранное товар
     *
     * @param element
     */
    updateTitle(element) {
        let title = element.classList.contains(this.settings.inFavoriteClass)
            ? element.dataset.alreadyTitle : element.dataset.title;
        if (typeof(title) != 'undefined') {
            element.title = title;
        }
    }

    /**
     * Инициализирует начальное состояние кнопки В избранное с учетом кэша HTML блока
     */
    initFirstState() {
        if (global.favoriteProducts) {
            document.querySelectorAll(this.settings.favoriteButton).forEach((element) => {
                let productId = element.closest(this.settings.context).dataset.id;
                if (productId) {
                    let isActive = global.favoriteProducts.indexOf(parseInt(productId)) > -1;
                    isActive ? element.classList.add(this.settings.inFavoriteClass)
                        : element.classList.remove(this.settings.inFavoriteClass);
                }
            });
        }
    }

    /**
     * Инициализирует всплывающие подсказки у кнопок В Избранное
     */
    initUpdateTitle() {
        document.querySelectorAll(this.settings.favoriteButton + '[data-title]')
            .forEach((element) => this.updateTitle(element));
    }

    /**
     * Добавляет класс activeClass к блоку, если есть хоть один товар в избранном
     *
     * @param count
     */
    checkActive(count, productId, productState, noFireOpener) {
        this.blocks.forEach((block) => {
            let counter = block.querySelector(this.settings.favoriteCount);
            counter && (counter.innerHTML = count);

            if (count > 0) {
                block.classList.add(this.settings.activeClass);
            } else {
                block.classList.remove(this.settings.activeClass);
            }
        });

        if (!noFireOpener) {
            try {
                //Обновляем количество товаров в избранном в главном окне
                window.opener.RsJsCore.components.Favorite.checkActive(count, null, null, true);
                if (productId) {
                    window.opener.RsJsCore.components.Favorite.toggleIcons(productId, productState);
                }
            } catch(e) {}
        }
    }

    onDocumentReady() {
        this.init();
    }
};