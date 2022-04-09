/**
 * Инициализирует отображение комплектаций в списке товаров.
 * Динамически создает разметку для комплектаций, исходя из JSON-данных, которые есть у каждой карточки товара
 */
new class OffersPreview extends RsJsCore.classes.component {

    constructor(settings) {
        super();
        let defaults = {
            productContext: '[data-id]',
            offersPreviewContainer: '.rs-offers-preview',
            previewBlockClass: 'item-card__wrapper',
            previewTableClass: 'item-list__bar',
            price: '.rs-price-new',
            oldPrice: '.rs-price-old',
            toProductLink: '.rs-to-product',
            toCartButton: '.rs-to-cart',
            reserveButton: '.rs-reserve',
            oneClickButton: '.rs-buy-one-click',
            mainImage: '.rs-image',
            initOnHover: false,
            hiddenClass: 'd-none',
            notAvaliableClass: 'rs-not-avaliable',
            forcedReservedClass: 'rs-forced-reserve',
            noExistsClass: 'no-exists',
            badOfferClass: 'rs-bad-offer',
            badOfferError: '.rs-bad-offer-error',
        };

        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
        this.counter = 1;
    }

    onContentReady(event) {
        event.target.querySelectorAll('script[rel="offers"]').forEach((scriptElement) => {
            if (!scriptElement.offersPreview) {
                scriptElement.offersPreview = new OffersPreviewCard(scriptElement, this.settings, this.counter++);
            }
        });
    }
};

/**
 * Класс отвечает за предварительный просмотр комплектаций у одной карточки товара
 */
class OffersPreviewCard
{
    constructor(scriptElement, settings, index) {
        this.settings = settings;
        this.scriptElement = scriptElement;
        this.context = this.scriptElement.closest(this.settings.productContext);
        this.isForcedReserved = this.context.classList.contains(this.settings.forcedReservedClass);
        this.productId = this.context.dataset.id;
        this.offersData = JSON.parse(this.scriptElement.innerText);

        this.unique = index;
        this.checkQuantity = parseInt(this.scriptElement.dataset.checkQuantity);

        this.toCartButton = this.context.querySelector(this.settings.toCartButton);
        this.reserveButton = this.context.querySelector(this.settings.reserveButton);
        this.toProductLink = this.context.querySelectorAll(this.settings.toProductLink);
        this.oneClickButton = this.context.querySelector(this.settings.oneClickButton);

        this.toCartButton && (this.toCartButton.originalHref = this.toCartButton.dataset.href);
        this.reserveButton && (this.reserveButton.originalHref = this.reserveButton.dataset.href);
        this.oneClickButton && (this.oneClickButton.originalHref = this.oneClickButton.dataset.href)

        this.matrixValue = {};

        this.toProductLink.forEach((element) => {
            element.originalHref = element.getAttribute('href');
        });

        if (!this.settings.initOnHover) {
            this.init();
        } else {
            this.context.addEventListener('mouseenter', () => this.init(), {
                once: true
            });
        }
    }

    init() {
        let container = this.context.querySelector(this.settings.offersPreviewContainer);

        if ((this.offersData.levels && this.offersData.levels.length)
            || (this.offersData.offers && this.offersData.offers.length > 1)) {

            this.wrapper0 = document.createElement('div');
            this.wrapper0.className = container.classList.contains('item-card__wrapper') ? 'item-card__complete' : 'd-none d-sm-block mb-4';

            this.wrapper = document.createElement('div');
            this.wrapper.className = 'row g-3 row-cols-auto';

            this.wrapper0.appendChild(this.wrapper);
            container.prepend(this.wrapper0);

            if (this.offersData.levels && this.offersData.levels.length > 0) {
                this.makeByLevels(); //Многомерные комплектации
            } else {
                this.makeByOffers(); //Простые комплектации
            }

        }
    }

    /**
     * Создает HTML разметку по многомерным комплектациям
     */
    makeByLevels() {
        this.offersData.levels.forEach((level) => {
            let choose = this.makeChoose(level.title);

            level.values.forEach((value, i) => {
                let className = 'radio-' + (level.isPhoto ? 'image' : level.type);
                let name = `${this.unique}-${this.productId}-${level.id}`;
                let id = `${this.unique}-${name}-${i}`;
                let {content, title} = this.getContentByLevel(level, value);

                let tpl = `<div class="${className}">
                                <input type="radio" id="${id}" name="${name}">
                                <label for="${id}" title="${title}">
                                    ${content}
                                </label>
                            </div>`;

                let li = document.createElement('li');
                li.innerHTML = tpl;
                let input = li.querySelector('input');
                input.dataset.propertyTitle = level.title;
                input.dataset.propertyId = level.id;
                input.value = value.text;
                input.setAttribute('autocomplete', 'off');
                input.addEventListener('change', event => this.onChangeMultioffers());

                if (!this.matrixValue[level.title]) {
                    this.matrixValue[level.title] = {};
                }
                this.matrixValue[level.title][value.text] = li;

                choose.append(li);
            });
        });

        let mainOfferId = this.offersData.mainOfferId;
        let mainOffer = this.getOfferById(mainOfferId);

        if (this.checkQuantity && mainOffer && mainOffer.num <= 0) {
            //Находим первую комплектацию, что есть в наличии
            for (let offer of this.offersData.offers) {
                if (offer.num > 0) {
                    mainOfferId = offer.id;
                    break;
                }
            }
        }

        this.setMultiofferByOfferId(mainOfferId);
    }

    /**
     * Создает оборачивающий HTML для выбора комплектаций
     *
     * @param title
     * @returns {Element}
     */
    makeChoose(title) {
        let levelWrapper = document.createElement('div');
        levelWrapper.innerHTML =
            `<div class="fs-5 text-gray">${title}:</div>
                <ul class="item-product-choose"></ul>`;
        this.wrapper.appendChild(levelWrapper);
        return levelWrapper.querySelector('.item-product-choose');
    }

    /**
     * Возвращает содержимое для
     *
     * @param level
     * @param value
     * @returns {{title: *, content: *}}
     */
    getContentByLevel(level, value) {
        let content = '';
        let title = '';

        if (!level.isPhoto) {
            if (level.type === 'color') {
                if (value.image) {
                    content = `<img src="${value.image.url}" alt="" loading="lazy">`;
                } else {
                    content = `<div class="radio-bg-color" style="background-color:${value.color}"></div>`;
                }
            }
            else if (level.type === 'image' && value.image) {
                content = `<img src="${value.image.url}" alt="" loading="lazy">`;
                title = value.text;
            }
            else {
                content = value.text;
            }
        } else {
            if (value.image) {
                content = `<img src="${value.image.url}" alt="" loading="lazy">`;
                title = value.text;
            } else {
                content = value.text;
            }
        }

        return {content: content, title: title};
    }

    /**
     * Возвращает объект одной комплектации по ID
     *
     * @param id
     * @returns {*|string}
     */
    getOfferById(id) {
        if (this.offersData.offers) {
            for (const offer of this.offersData.offers) {
                if (offer.id == id) {
                    return offer;
                }
            }
        }
    }

    /**
     * Устанавливаем выбранные значения многомерных комплектаций по умолчанию
     *
     * @param offerId
     */
    setMultiofferByOfferId(offerId) {
        let offer = this.getOfferById(offerId);

        if (offer && offer.info.length) {
            offer.info.forEach((pair) => {
                this.wrapper.querySelectorAll('input[data-property-title]').forEach(element => {
                    if (element.dataset.propertyTitle === pair[0] && element.value === pair[1]) {
                        element.checked = true;
                    }
                });
            });

            this.onChangeMultioffers();
        } else {
            //Выбираем первые значения каждого параметра многомерной комплектации,
            //если нет простых комплектаций.
            this.offersData.levels.forEach(level => {
                if (level.values.length) {
                    this.wrapper.querySelectorAll('input[data-property-title="' + level.title + '"]').forEach(element => {
                        if (element.value === level.values[0].text) {
                            element.checked = true;
                        }
                    });
                }
            });
            this.onChangeMultioffers();
        }
    }

    /**
     * Обработчик изменения многомерной комплектации
     */
    onChangeMultioffers() {
        let formData = new FormData();
        let multioffersMatrix = [];
        let offerId = 0;

        this.wrapper.querySelectorAll('input[data-property-title]:checked').forEach(element => {
            formData.append(`multioffers[${element.dataset.propertyId}]`, element.value);
            multioffersMatrix.push([element.dataset.propertyTitle, element.value]);
        });

        for(let offer of this.offersData.offers) {
            let count = 0;
            offer.info.forEach(pair => {
                multioffersMatrix.forEach(selectedPair => {
                    if (pair[0] === selectedPair[0] && pair[1] === selectedPair[1]) {
                        count++;
                    }
                })
            });
            if (count === multioffersMatrix.length) {
                offerId = offer.id;
                break;
            }
        }

        this.onChangeOffer(offerId, formData);
    }

    /**
     * Создает HTML разметку по простым комплектациям
     */
    makeByOffers() {
        if (this.offersData.offers && this.offersData.offers.length > 1) {
            let choose = this.makeChoose(this.offersData.offersCaption);
            let activeOffer;
            let firstExistsInput;
            for(let offer of this.offersData.offers) {
                    let className = 'radio-list';
                    let id = `${this.unique}-${this.productId}--${offer.id}`;
                    let name = `${this.unique}-${this.productId}--`;

                    let tpl = `<div class="${className}">
                                <input type="radio" id="${id}" name="${name}">
                                <label for="${id}">
                                    ${offer.title}
                                </label>
                            </div>`;

                    let li = document.createElement('li');
                    li.className = (this.checkQuantity && offer.num <= 0) ? this.settings.noExistsClass : '';
                    li.innerHTML = tpl;

                    let input = li.querySelector('input');
                    input.value = offer.id;
                    input.setAttribute('autocomplete', 'off');
                    input.checked = (offer.id == this.offersData.mainOfferId);

                    input.addEventListener('change', event => {
                        let offerId = event.target.value;
                        this.onChangeOffer(offerId);
                    });

                    choose.append(li);

                    if (this.checkQuantity && offer.num > 0 && !firstExistsInput) {
                        firstExistsInput = input;
                    }

                    if (input.checked) {
                        activeOffer = offer;
                    }
            }

            if (activeOffer && activeOffer.num <= 0 && firstExistsInput) {
                //Привключенном контроле остатков, если первой комплектации нет в наличии,
                //то автоматически выбираем первую, что в наличии есть, чтобы люди в списках не видели надпись "Нет в наличии"
                firstExistsInput.checked = true;
                firstExistsInput.dispatchEvent(new Event('change', {bubbles: true}));
            } else if (activeOffer) {
                this.addOfferToLink(null, activeOffer);
            }
        }
    }

    /**
     * Обработчик изменения простой комплектации
     *
     * @param offerId
     * @param formData
     */
    onChangeOffer(offerId, formData) {
        let offer;

        if (offerId > 0) {
            offer = this.getOfferById(offerId);
            this.changePrice(offer);
            this.changePhoto(offer);
            this.changeAvailability(offer);
        } else {
            offer = null;
        }
        this.addOfferToLink(formData, offer);
        this.checkBadOffer(offer);
    }

    /**
     * Показывает, когда выбрана несуществующее сочетание параметров
     * @param offer
     */
    checkBadOffer(offer) {
        if (this.offersData.levels && this.offersData.levels.length
            && this.offersData.offers && this.offersData.offers.length > 1) {

            let errorContainer = this.context.querySelector(this.settings.badOfferError);

            if (offer === null) {
                this.context.classList.add(this.settings.badOfferClass);
                errorContainer && (errorContainer.innerText = lang.t('Нет комплектации'));
            } else {
                this.context.classList.remove(this.settings.badOfferClass);
                errorContainer && (errorContainer.innerText = '');
            }
        }


    }

    /**
     * Изменяет цену
     *
     * @param offer
     */
    changePrice(offer) {
        let price = this.context.querySelector(this.settings.price);
        let oldPrice = this.context.querySelector(this.settings.oldPrice);

        price && (price.innerText = offer.price);

        if (oldPrice) {
            oldPrice.innerText = offer.oldPrice;
            if (offer.oldPrice == 0 || offer.oldPrice == offer.price) {
                oldPrice.parentElement.classList.add(this.settings.hiddenClass);
            } else {
                oldPrice.parentElement.classList.remove(this.settings.hiddenClass);
            }
        }
    }

    /**
     * Изменяет фото
     *
     * @param offer
     */
    changePhoto(offer) {
        let imageElement = this.context.querySelector(this.settings.mainImage);
        if (imageElement) {
            let image;
            if (offer.photos && offer.photos.length) {
                if (this.offersData.images[offer.photos[0]]) {
                    image = this.offersData.images[offer.photos[0]];
                }
            } else {
                let mainImageId = this.offersData.mainImageId ?
                    this.offersData.mainImageId : Object.keys(this.offersData.images)[0];
                image = this.offersData.images[ mainImageId ];
            }
            if (image) {
                imageElement.src = image.url;
                imageElement.srcset = image.url2x + ' 2x';
            }
        }
    }

    /**
     * Изменяет ссылки
     *
     * @param offer
     * @param formData
     */
    addOfferToLink(formData, offer) {
        if (!formData) {
            formData = new FormData();
        }
        if (offer) {
            formData.append('offer_id', offer.id);
        }

        let queryParams = new URLSearchParams(formData);
        if (this.toCartButton) {
            this.toCartButton.dataset.href = this.toCartButton.originalHref
                + (this.toCartButton.originalHref.indexOf('?') === -1 ? '?' : '&')
                + queryParams.toString();
        }
        if (this.reserveButton) {
            this.reserveButton.dataset.href = this.reserveButton.originalHref
                + (this.reserveButton.originalHref.indexOf('?') === -1 ? '?' : '&')
                + queryParams.toString();
        }
        if (this.oneClickButton) {
            this.oneClickButton.dataset.href = this.oneClickButton.originalHref
                + (this.oneClickButton.originalHref.indexOf('?') === -1 ? '?' : '&')
                + queryParams.toString();
        }

        this.toProductLink.forEach((element) => {
            let newHref = element.originalHref + ((offer && offer.id != this.offersData.mainOfferId) ? '#' + offer.id : '');
            element.setAttribute('href', newHref);
        });
    }

    /**
     * Изменяет наличие
     *
     * @param offer
     */
    changeAvailability(offer) {
        if (this.checkQuantity) {
            if (offer.num <= 0) {
                this.context.classList.add(this.settings.notAvaliableClass);
            } else {
                this.context.classList.remove(this.settings.notAvaliableClass);
            }
            this.highlightNotAvailable();
        }
    }

    /**
     * Подсвечивает, каких сочетаний многомерных комплектаций - нет
     */
    highlightNotAvailable() {
        if (this.offersData.levels && this.offersData.levels.length
            && this.offersData.offers && this.offersData.offers.length > 1
            && !this.offersData.virtual) {
            let multioffersMatrix = {};
            let levelCount = 0;
            this.wrapper.querySelectorAll('input[data-property-title]:checked').forEach(element => {
                multioffersMatrix[element.dataset.propertyTitle] = element.value;
                levelCount++;
            });

            //Изначально отмечаем, что всего нет в наличии
            for(let i in this.matrixValue) {
                for(let j in this.matrixValue[i]) {
                    this.matrixValue[i][j].classList.add(this.settings.noExistsClass);
                }
            }

            if (!this.isForcedReserved) { //Если только предзаказ, то всегда всего нет в наличии
                this.offersData.offers.forEach(offer => {
                    let identity = 0;
                    let updatePair = [];
                    offer.info.forEach(pair => {
                        if (multioffersMatrix[pair[0]] && multioffersMatrix[pair[0]] == pair[1]) {
                            identity++;
                        } else {
                            updatePair.push(pair); //Сохраняем, какую пару ключ => значение будем обновлять
                        }
                    });

                    if (identity === levelCount) {
                        updatePair = offer.info;
                    }

                    if (identity >= levelCount - 1) { //Устанвливаем нужным элементам, что они есть в наличии
                        if (offer.num > 0) {
                            updatePair.forEach((pair) => {
                                let value;
                                if (this.matrixValue[pair[0]]) {
                                    value = this.matrixValue[pair[0]][pair[1]];
                                }
                                if (value) {
                                    value.classList.remove(this.settings.noExistsClass);
                                } else {
                                    console.log('Ошибка в товаре ' + this.productId, 'Отсутстует комплектация', pair);
                                }
                            });
                        }
                    }
                });
            }
        }
    }
};