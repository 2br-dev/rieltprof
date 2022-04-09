/**
 * Плагин, инициализирующий работу механизма выбора комплектации продукта.
 * Позволяет изменять связанные цены на странице при выборе комплектации,
 * а также подменяет ссылку на добавление товара в корзину с учетом комплектации
 */
new class ChangeOffer extends RsJsCore.classes.component {

    constructor() {
        super();
        let defaults = {
            offerInput: '[name="offer"]',
            notExistOffer: '.rs-not-exists-offer', // Скрытое поле, хранит "не существующую" комплектацию
            context: '[data-id]',      // Родительский элемент, ограничивающий поиск элемента с ценой
            dataAttrChangeElements: 'changeElements',
            notAvaliableClass: 'rs-not-avaliable',   // Класс для нет в наличии
            buyOneClick      : '.rs-buy-one-click', //Класс купить в один клик
            reserve          : '.rs-reserve',
            badOfferClass: 'rs-bad-offer',
            badOfferError: '.rs-bad-offer-error',
            offerProperty: '.rs-offer-property',
            unitBlock: '.rs-unit-block',
            unitValue: '.rs-unit',

            // Селекторы цен
            priceSelector: '.rs-price-new',
            oldPriceSelector: '.rs-price-old',
            barcodeSelector: '.rs-product-barcode',

            inDialogUrlDataAttr: 'href',

            mainPicture      : ".rs-main-picture", //Класс главных фото
            previewPicture   : ".rs-gallery-source .rs-item",  //Класс превью фото

            hiddenClass: 'd-none',

            //Паметры для складов
            sticksRow         : '.rs-warehouse-row', //Блок, оборачивающий строку со складом
            stickWrap         : '.rs-stick-wrap',   //Блок, Оборачивающий контейнер с рисками значений заполнености склада
            stick             : '.rs-stick',        //Риски со значениями заполнености склада
            stickRowEmptyClass: 'rs-warehouse-empty', //Класс, располагающийся у sticksRow, означающий что товара нет в наличии на данном складе
            stickFilledClass  : 'availability-indicator__point_act',        //Класс заполненой риски

            stockCountTextContainer: '.rs-stock-count-text-container',
            stockCountTextWrapper: '.rs-stock-count-text-wrapper',

            //Многомерные комплектации
            hiddenOffers        : '.rs-hidden-offers',      // Комплектации с информацией
            multiOfferInput: '[name^="multioffers"]',
            noExistsClass: 'no-exists'
        };

        this.settings = {...defaults, ...this.getExtendsSettings()};
    }

    init(event) {
        event.target.querySelectorAll('.rs-product, .rs-multi-complectations').forEach((element) => {
            if (!element.changeOffer) {
                element.changeOffer = new ProductChangeOffer(element, this.settings);
            }
        });
    }

    onContentReady(event) {
        this.init(event);
    }
};

//=================================================================
/**
 * Класс, отвечает за смену комплектаций у товара
 */
class ProductChangeOffer
{
    constructor(context, settings)
    {
        this.context = context;
        this.settings = settings;

        this.scriptElement = this.context.querySelector('[rel="product-offers"]');
        if (this.scriptElement) {
            this.checkQuantity = parseInt(this.scriptElement.dataset.checkQuantity);
            this.isForcedReserved = this.context.classList.contains(this.settings.forcedReservedClass);
            this.offersData = JSON.parse(this.scriptElement.innerText);

            let selectedOfferId;
            this.context.querySelectorAll(this.settings.offerInput).forEach((it => {
                it.addEventListener('change', event => {
                    let selectedOfferData = this.getOfferById(event.target.value);
                    selectedOfferData && this.onChangeOffer(selectedOfferData, null, event);
                });

                if ((it.getAttribute('type') === 'radio' && it.checked)
                    || (it.getAttribute('type') === 'hidden')) {
                    selectedOfferId = it.value;
                }
            }));

            this.context.querySelectorAll(this.settings.multiOfferInput).forEach((it => {
                it.addEventListener('change', event => this.onChangeMultiOffer(event));
            }));

            this.buildValueMatrix();
            this.setStartOffer(selectedOfferId);
        }
    }

    /**
     * Сторит двумерный массив значений многомерных комплектаций с указателями на соответствующий DOM элемент
     */
    buildValueMatrix() {
        this.matrixValue = {};
        this.context.querySelectorAll('.item-product-choose > li').forEach(it => {
            it.querySelectorAll('[data-property-title]').forEach(element => {
                if (!this.matrixValue[element.dataset.propertyTitle]) {
                    this.matrixValue[element.dataset.propertyTitle] = {};
                }

                this.matrixValue[element.dataset.propertyTitle][element.value] = it;
            });
        });
    }

    /**
     * Выбирает значения многомерных комплектаций по умолчанию
     */
    setStartOffer(selectedOfferId) {
        let selectOfferData = this.getOfferById(selectedOfferId);
        if (selectOfferData && selectOfferData.info.length) {
            //Если есть простая комплектация, то установим по ней многомерные
            this.setOffer(selectedOfferId);
        } else if (this.offersData.levels && !this.offersData.virtual) {
            //Если это многомерные без простых
            this.offersData.levels.forEach(level => {
                if (level.values.length) {
                    this.context.querySelectorAll('input[data-property-title="' + level.title + '"]').forEach(element => {
                        if (element.value === level.values[0].text) {
                            element.checked = true;
                        }
                    });
                }
            });
            this.onChangeMultiOffer();
        }
    }

    /**
     * Возвращает объект одной комплектации по ID
     *
     * @param id
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
     * Устанавливает заданную комплектацию в контексте одного товара
     *
     * @param offerId
     */
    setOffer(offerId) {
        if (!this.offersData) {
            return;
        }
        let selectedOfferData = this.getOfferById(offerId);

        if (selectedOfferData) {
            let offerInput;
            this.context.querySelectorAll(this.settings.offerInput).forEach(element => {
                if (element.getAttribute('type') === 'radio' && element.value == offerId) {
                    offerInput = element;
                    offerInput.checked = true;
                }
                if (element.getAttribute('type') === 'hidden') {
                    //Ожидается, что такой элемент один в рамках контекста
                    offerInput = element;
                    offerInput.value = offerId;
                }
            });

            let formData = new FormData();

            if (this.offersData.levels && !this.offersData.vrtual) {
                this.setMultioffersByOfferData(selectedOfferData, formData);
            }

            this.onChangeOffer(selectedOfferData, formData);
        }
    }

    /**
     * Устанавливает значения многомерных комплектаций по текущему значению offerID
     *
     * @param selectedOfferData
     * @param formData
     */
    setMultioffersByOfferData(selectedOfferData, formData) {
        if (selectedOfferData.info) {
            //Отмечаем значения параметров многомерной комплектации
            for(let pair of selectedOfferData.info) {
                let [title, value] = pair;
                this.context.querySelectorAll('[data-property-title]').forEach(element => {
                    if (element.dataset.propertyTitle === title && element.value === this.decodeEntity(value)) {
                        element.checked = true;
                        formData.append(`multioffers[${element.dataset.propertyId}]`, element.value);
                    }
                });
            }
        }
    }

    /**
     * Преодразовывает entity в текст
     *
     * @param inputString
     * @returns {string}
     */
    decodeEntity(inputString) {
        let textarea = document.createElement("textarea");
        textarea.innerHTML = inputString;
        return textarea.value;
    }

    /**
     * Возвращает массив из выбранных элементов многомерной комплектации
     *
     * @param formData
     */
    getCurrentValueMatrix(formData) {
        let multiofferValues = [];

        this.context.querySelectorAll('[data-property-title]:checked').forEach(element => {
            formData.append(`multioffers[${element.dataset.propertyId}]`, element.value);
            multiofferValues.push([element.dataset.propertyTitle, element.value]);
        });

        return multiofferValues;
    }

    /**
     * В случае, когда выбрано сочетание многомерных для которых нет простой комплектации, то запрещаем покупать такой товар.
     *
     * Делаем исключение для многомерных без простых.
     */
    getUnExistsOffer() {
        let offer = {
            id:0
        };

        if (this.offersData.levels && this.offersData.levels.length
            && this.offersData.offers && this.offersData.offers.length > 1) {
            //Если это обычные многомерные комплектации
            offer.num = 0;
            offer.price = '--';
            offer.oldPrice = '--';
            offer.badOffer = true
        }

        return offer;
    }

    /**
     * Находит корректный offerId, соответствующий
     * всем параметрам многомерной комплектации.
     *
     * В случае с виртуальными многомерными комплектациями, производит
     * перезагрузку страницы на нужный товар
     *
     * @param event
     */
    onChangeMultiOffer() {
        let formData = new FormData();
        let selectedOfferData = this.getUnExistsOffer();
        let multioffersMatrix = this.getCurrentValueMatrix(formData);

        let source = this.offersData.virtual ? this.offersData.virtual : this.offersData.offers;

        for(let offer of source) {
            let count = 0;
            offer.info.forEach(pair => {
                multioffersMatrix.forEach(selectedPair => {
                    if (pair[0] === selectedPair[0] && this.decodeEntity(pair[1]) === selectedPair[1]) {
                        count++;
                    }
                })
            });
            if (count === multioffersMatrix.length) {
                selectedOfferData = offer;
                break;
            }
        }

        if (selectedOfferData.url) {
            location.href = selectedOfferData.url;
        } else {
            this.context.querySelector(this.settings.offerInput).value = selectedOfferData.id;
            this.onChangeOffer(selectedOfferData, formData, event);
        }
    }

    /**
     * Обработчик события изменения комплектации на странице
     *
     * @param selectedOfferData
     * @param formData
     */
    onChangeOffer(selectedOfferData, formData, event) {
        this.changePrice(selectedOfferData);
        this.changeBarcode(selectedOfferData);
        this.changeUnit(selectedOfferData);
        this.showProperties(selectedOfferData);

        this.showPhotos(selectedOfferData);
        this.showAvailability(selectedOfferData);
        this.showStockSticks(selectedOfferData);
        this.updateButtonsLink(formData, selectedOfferData);
        this.checkBadOffer(selectedOfferData);

        this.context.dispatchEvent(new CustomEvent('offer-changed', {bubbles:true}));
    }

    /**
     *
     * @param selectedOfferData
     */
    checkBadOffer(selectedOfferData) {
        let errorContainer = this.context.querySelector(this.settings.badOfferError);

        if (selectedOfferData.badOffer) {
            this.context.classList.add(this.settings.badOfferClass);
            errorContainer && (errorContainer.innerText = lang.t('Товара в такой комплектации не существует'));
        } else {
            this.context.classList.remove(this.settings.badOfferClass);
            errorContainer && (errorContainer.innerText = '');
        }
    }

    /**
     * Изменяет цену и старую цену товара
     *
     * @param selectedOfferData
     */
    changePrice(selectedOfferData) {
        if (selectedOfferData.price) {
            let price = this.context.querySelector(this.settings.priceSelector);
            price && (price.innerText = selectedOfferData.price);
        }

        if (selectedOfferData.oldPrice) {
            let oldPrice = this.context.querySelector(this.settings.oldPriceSelector);
            if (oldPrice) {
                oldPrice.innerText = selectedOfferData.oldPrice;
                if (selectedOfferData.oldPrice == 0 || selectedOfferData.oldPrice == selectedOfferData.price) {
                    oldPrice.parentElement.classList.add(this.settings.hiddenClass);
                } else {
                    oldPrice.parentElement.classList.remove(this.settings.hiddenClass);
                }
            }
        }
    }

    /**
     * Обновляет артикул товара
     *
     * @param selectedOfferData
     */
    changeBarcode(selectedOfferData) {
        if (selectedOfferData.barcode) {
            let barcode = this.context.querySelector(this.settings.barcodeSelector);
            barcode && (barcode.innerText = selectedOfferData.barcode);
        }
    }

    /**
     * Изменяет единицу измерения возле цены
     *
     * @param selectedOfferData
     */
    changeUnit(selectedOfferData) {
        //Сменим единицу измерения, если нужно
        let unitBlock = this.context.querySelector(this.settings.unitBlock);

        if (unitBlock && selectedOfferData.unit && selectedOfferData.unit != "") {
            unitBlock.classList.remove(this.settings.hiddenClass);
            unitBlock.querySelector(this.settings.unitValue).innerText = selectedOfferData.unit;
        } else {
            unitBlock && unitBlock.classList.add(this.settings.hiddenClass);
        }
    }

    /**
     * Отображает характеристики выбранной комплектации
     *
     * @param selectedOfferData
     */
    showProperties(selectedOfferData) {
           this.context.querySelectorAll(this.settings.offerProperty).forEach(element => {
            element.classList.add(this.settings.hiddenClass);
        });
        let propertyBlock = this.context.querySelector(this.settings.offerProperty+'[data-offer="' + selectedOfferData.id + '"]');
        propertyBlock && propertyBlock.classList.remove(this.settings.hiddenClass);
    }

    /**
     * Обновляет ссылки у кнопок Купить в 1 клик и Заказать
     *
     * @param formData
     * @param selectedOfferData
     */
    updateButtonsLink(formData, selectedOfferData) {
        if (!formData) {
            formData = new FormData();
        }

        if (selectedOfferData.id > 0) {
            formData.append('offer_id', selectedOfferData.id);
        }
        let queryParams = new URLSearchParams(formData);

        //Добавим параметр комплектации к ссылке купить в 1 клик
        let replacer = (element) => {
            let clickHref = element.dataset[this.settings.inDialogUrlDataAttr].split('?'); //Получим урл
            element.dataset[this.settings.inDialogUrlDataAttr] = clickHref[0] + "?" + queryParams.toString();
        };
        this.context.querySelectorAll(this.settings.buyOneClick).forEach(replacer);
        this.context.querySelectorAll(this.settings.reserve).forEach(replacer);
    }

    /**
     * Переключает на отображение только связанных с комплектацией фото
     *
     * @param selectedOfferData
     */
    showPhotos(selectedOfferData) {
        if (selectedOfferData.photos) {
            let images = selectedOfferData.photos;
            if (!images || !images.length) images = [];

            //Фото должен скрывать/показывать тот плагин, который инициализирует эти фото
            this.context.dispatchEvent(new CustomEvent('offer-photo-changed', {
                bubbles: true,
                detail: {
                    images: images
                }
            }));
        }
    }

    /**
     * Корректно отображает кнопку Купить/Заказать/Нет в наличии
     *
     * @param selectedOfferData
     */
    showAvailability(selectedOfferData) {
        if (this.checkQuantity) {
            let num = selectedOfferData.num;
            if (typeof (num) != 'undefined') {
                if (num <= 0) { //Если не доступно
                    this.context.classList.add(this.settings.notAvaliableClass);
                } else { //Если  доступно
                    this.context.classList.remove(this.settings.notAvaliableClass);
                }
            }

            this.highlightNotAvailable();
        }
    }

    /**
     * Возвращает порядковый номер элемента среди братьев в DOM, начиная с нуля
     *
     * @param element
     * @returns {number}
     */
    getNodeIndex(element) {
        return [...element.parentNode.children].indexOf(element);
    }

    /**
     * Обновляет информацию об остатках выбранной комплектации на складах
     *
     * @param selectedOfferData
     */
    showStockSticks(selectedOfferData) {
        let sticks = selectedOfferData.sticks ? selectedOfferData.sticks : [];
        //Очистим все риски заполнености склада
        this.context.querySelectorAll(this.settings.stick).forEach((element) => {
            element.classList.remove(this.settings.stickFilledClass);
        });

        //Установим значения рисок заполнености склада
        this.context.querySelectorAll(this.settings.sticksRow).forEach((row) => {
            let warehouseId = row.dataset.warehouseId;
            let num = sticks[warehouseId] ? sticks[warehouseId] : 0; //Количество рисок для складов
            row.querySelectorAll(this.settings.stick).forEach((stick) => {
                if (this.getNodeIndex(stick) < num) {
                    stick.classList.add(this.settings.stickFilledClass);
                }
            });

            if (num > 0) {
                row.classList.remove(this.settings.stickRowEmptyClass);
            } else {
                row.classList.add(this.settings.stickRowEmptyClass);
            }
        });

        //Обновляем фразу о наличии на складах
        let countText = this.context.querySelector(this.settings.stockCountTextContainer);
        if (countText) {

            let availableStockCount = 0;
            for (let warehouseId in sticks) {
                if (sticks[warehouseId] > 0) {
                    availableStockCount++;
                }
            }

            countText.innerText = lang.t('В наличии на %n [plural:%n:складе|складах|складах]', {n: availableStockCount});
            let countTextWrapper = countText.closest(this.settings.stockCountTextWrapper);
            availableStockCount > 0
                ? countTextWrapper.classList.remove(this.settings.hiddenClass)
                : countTextWrapper.classList.add(this.settings.hiddenClass);

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

            this.context.querySelectorAll('input[data-property-title]:checked').forEach(element => {
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
                        if (multioffersMatrix[pair[0]] && multioffersMatrix[pair[0]] == this.decodeEntity(pair[1])) {
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
                                this.matrixValue[pair[0]][this.decodeEntity(pair[1])].classList.remove(this.settings.noExistsClass);
                            });
                        }
                    }
                });
            }
        }
    }
};