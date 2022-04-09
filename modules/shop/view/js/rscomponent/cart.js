/**
 * Компонент инициализирует работу кнопок "В корзину", а также самой корзины
 */
new class Cart extends RsJsCore.classes.component {

    constructor(settings)
    {
        super();
        let defaults = {
            cartBlockMain: '#rs-cart',
            cartBlock:'.rs-cart-block',
            context: '[data-id]',

            addToCart: '.rs-to-cart',
            reserve: '.rs-reserve',
            alreadyInCartClass: 'added',
            alreadyInCartClassTimeout:5,

            noShowModalCartClass: 'rs-no-modal-cart',

            offerFormName: '[name="offer"]',
            amountFormName: '[name="amount"]',
            multiOfferFormName: '[name^="multioffers"]',
            concomitantFormName: '[name^="concomitant"]',

            cartTotalPrice: '.rs-cart-items-price',
            cartTotalItems: '.rs-cart-items-count',
            cartActiveClass: 'active',

            checkoutButton: '.rs-go-checkout',
            checkoutButtonActiveClass: 'active',

            cartAmountField: '.rs-amount',
            cartPage: '#rs-cart-page',
            cartForm: '#rs-cart-form',
            cartItem: '.rs-cart-item',
            cartItemRemove: '.rs-remove',
            cartItemRemoveConcomitant: '.rs-remove-concomitant',
            cartItemOffer: '.rs-offer',
            cartItemMultiOffer: '.rs-multioffer',
            cartGoBackButton: '.rs-go-back',
            cartItemHiddenOffers: '.rs-hidden-multioffer',
            cartClean: '.rs-clean',
            cartApplyCoupon: '.rs-apply-coupon',
            productWrapper: '.rs-product-item',
            inLoadingClass: 'in-loading',
            cartConcomitantCheckbox: '.rs-concomitant-checkbox',

            //Умные кнопки, превращающиеся в количество

        };

        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
    }

    init() {
        this.utils.on('click', this.settings.addToCart, event => this.addToCart(event));
        this.utils.on('click', this.settings.reserve, event => this.reserve(event));
        this.utils.on('click', this.settings.checkoutButton, event => this.checkout(event));

        this.cartBlockMain = document.querySelector(this.settings.cartBlockMain);

        let cartPage = document.querySelector(this.settings.cartPage);
        if (cartPage) {
            this.initCart(cartPage.parentNode);
        }

        //Умные кнопки В корзину с количеством
        this.utils.on('click', this.settings.smartAmountBuyButton, event => this.smartAmountAdd(event));
    }

    /**
     * Добавляет товар в корзину и открывает окно упрощенной корзины
     *
     * @param event
     */
    addToCart(event) {
        let button = event.rsTarget;
        let context = button.closest(this.settings.context);
        this.changeStateToAdded(button);
        let formData = this.getProductParams(context);

        if (this.needShowSelectMultiofferDialog(button)) {
            let url = button.dataset.selectMultiofferHref;
            this.plugins.openDialog.show({
                url: url
            });
        } else {
            let url = button.dataset.href ? button.dataset.href : button.getAttribute('href');
            let noShowModalCart = this.cartBlockMain.classList.contains(this.settings.noShowModalCartClass)
                || button.classList.contains(this.settings.noShowModalCartClass);

            this.requestToCart(url, formData, noShowModalCart);
        }
    }

    /**
     * Вызывает окно выбора комплектации или окно резервирования товара
     *
     * @param event
     */
    reserve(event) {
        let button = event.rsTarget;
        let url;

        if (this.needShowSelectMultiofferDialog(button)) {
            url = button.dataset.selectMultiofferHref;
        } else {
            url = button.dataset.href;
        }

        if (url) {
            this.plugins.openDialog.show({
                url: url
            });
        }
    }

    /**
     * Возвращает true, если по нажатию на данную кнопку в
     * текущей ситуации нужно отобразить диалог выбора комплектаций
     *
     * @param button
     */
    needShowSelectMultiofferDialog(button)
    {
        if (!button.dataset.selectMultiofferHref) return false;
        let showOffersInListThemeOption = button.closest('[data-sol]');
        return !showOffersInListThemeOption || !window.matchMedia('(min-width: 992px)').matches;
    }

    /**
     * Выполняет запрос за сервер
     * @param url
     * @param formData
     * @param noShowModalCartClass
     */
    requestToCart(url, formData, noShowModalCart) {
        this.utils.fetchJSON(url, {
            method:'POST',
            body: formData
        }).then((response) => {
            this.updateCartBlock(response);
            if (!noShowModalCart) {
                this.plugins.modal.open(response.html, (event) => {
                    this.initCart(event.target);
                });
            }
        });
    }

    /**
     * Инициализирует события внутри корзины
     *
     * @param context
     */
    initCart(context) {
        this.cartPage = context.querySelector(this.settings.cartPage);

        if (this.cartPage) {
            this.utils.on('change', this.settings.cartAmountField, event => this.refresh(), this.cartPage);
            this.utils.on('click', this.settings.cartItemRemove, event => this.removeProduct(event), this.cartPage);
            this.utils.on('click', this.settings.cartItemRemoveConcomitant, event => this.removeConcomitant(event), this.cartPage);
            this.utils.on('change', this.settings.cartConcomitantCheckbox, () => this.refresh(), this.cartPage);
            this.utils.on('click', this.settings.cartGoBackButton, () => this.goBack(), this.cartPage);
            this.utils.on('change', this.settings.cartItemOffer, () => this.refresh(), this.cartPage);
            this.utils.on('change', this.settings.cartItemMultiOffer, (event) => this.changeMultiOffer(event), this.cartPage);
            this.utils.on('click', this.settings.cartClean, event => this.cleanCart(event), this.cartPage);
            this.utils.on('click', this.settings.cartApplyCoupon, () => this.refresh(), this.cartPage);

            let form = this.cartPage.querySelector(this.settings.cartForm);
            if (form) {
                form.addEventListener('submit', (event) => {
                    clearTimeout(this.cartPage.changeTimer);
                    this.refresh();
                    event.preventDefault();
                });
            }
        }
    }

    /**
     *
     * @param context
     */
    getCurrentValueMatrix(context) {
        let multiofferValues = [];

        context.querySelectorAll('[data-prop-title]').forEach(element => {
            multiofferValues.push([element.dataset.propTitle, element.value]);
        });

        return multiofferValues;
    }

    changeMultiOffer(event) {
        let context = event.target.closest(this.settings.cartItem);
        let values = this.getCurrentValueMatrix(context);

        let hiddenOffers = context.querySelectorAll(this.settings.cartItemHiddenOffers);
        let offerInput = context.querySelector(this.settings.cartItemOffer);
        console.log(offerInput);

        let foundOfferInput;
        hiddenOffers.forEach(element => {
            if (element.dataset.info) {
                let info = JSON.parse(element.dataset.info);
                let counter = 0;
                info.forEach(inputPair => {
                    values.forEach(valuesPair => {
                        if (inputPair[0] === valuesPair[0]
                            && inputPair[1] === valuesPair[1]) counter++;
                    });
                });

                if (counter === values.length) {
                    foundOfferInput = element;
                }
            }
        });

        if (foundOfferInput){ //Если комплектация найдена
            offerInput.value = foundOfferInput.value;
        } else { //Если не найдена комплектация, выберем нулевую
            offerInput.value = 0;
        }

        this.refresh();
    }

    /**
     * Удаляет один товар из корзины
     *
     * @param event
     */
    removeProduct(event) {
        event.preventDefault();
        if (!this.isLoading()) {
            let removeButton = event.rsTarget;
            let cartItem = removeButton.closest(this.settings.cartItem);
            if (cartItem) {
                cartItem.style.opacity = 0.5;
                let other = cartItem.parentNode.querySelectorAll('[data-id="' + cartItem.dataset.productId + '"]');

                if (!other.length) {
                    document.querySelectorAll(
                        this.settings.productWrapper + '[data-id="' + cartItem.dataset.productId + '"] ' + this.settings.addToCart)
                        .forEach(element => {
                            element.classList.remove(this.settings.alreadyInCartClass);
                        });
                }
            }

            this.refresh(removeButton.getAttribute('href'));
        }
    }

    /**
     * Обновляет корзину
     *
     * @param url
     * @param callback
     */
    refresh(url, callback) {

        let cartForm = this.cartPage.querySelector(this.settings.cartForm);
        let formData = new FormData(cartForm);

        if (!url) {
            url = cartForm.getAttribute('action');
        }

        cartForm.querySelectorAll('input, select, button').forEach(element => {
            element.disabled = true;
        });

        this.showLoading();
        this.utils.fetchJSON(url, {
            method:'POST',
            body: formData
        }).then(response => {
            if (response.redirect) {
                location.href = response.redirect;
            }

            if (response.cart.items_count === 0 && this.plugins.modal.isOpen()) {
                this.plugins.modal.close();
            } else {
                this.cartPage.insertAdjacentHTML('afterend', response.html);
                let parent = this.cartPage.parentNode;
                this.cartPage.remove();
                this.initCart(parent);
                parent.dispatchEvent(new CustomEvent('new-content', {bubbles: true}));
                parent.dispatchEvent(new CustomEvent('cart.change', {"bubbles": true, "cancelable": true}));
            }
            this.updateCartBlock(response);
            if (callback) callback(response);
        });
    }

    /**
     * Обновляет параметры
     *
     * @param response
     */
    updateCartBlock(response) {
        if (response) {
            global.cartProducts = response.cart.session_cart_products;
            document.querySelectorAll(this.settings.cartBlock).forEach((cartBlock) => {

                let totalItems = cartBlock.querySelector(this.settings.cartTotalItems);
                let totalPrice = cartBlock.querySelector(this.settings.cartTotalPrice);
                let checkoutButton = cartBlock.querySelector(this.settings.checkoutButton);

                totalItems && (totalItems.innerText = response.cart.items_count);
                totalPrice && (totalPrice.innerText = response.cart.total_price);

                if (checkoutButton) {
                    if (response.cart.can_checkout && parseFloat(response.cart.items_count) > 0) {
                        checkoutButton.classList.add(this.settings.checkoutButtonActiveClass);
                    } else {
                        checkoutButton.classList.remove(this.settings.checkoutButtonActiveClass);
                    }
                }

                if (parseFloat(response.cart.items_count) > 0) {
                    cartBlock.classList.add(this.settings.cartActiveClass);
                } else {
                    cartBlock.classList.remove(this.settings.cartActiveClass);
                }
            });
        }
    }

    /**
     * Показ загрузки
     */
    showLoading() {
        this.cartPage.classList.add(this.settings.inLoadingClass);
    }

    /**
     * Возвращает true, если в настоящее время идет загрузка
     *
     * @returns {boolean}
     */
    isLoading() {
        return this.cartPage.classList.contains(this.settings.inLoadingClass);
    }

    /**
     * Собирает параметры товара, который будет добавлен в корзину
     *
     * @param context
     */
    getProductParams(context) {
        let data = new FormData();
        let radioInput = context.querySelector(this.settings.offerFormName + ':checked');
        let hiddenInput = context.querySelector(this.settings.offerFormName);
        let offerId = radioInput ? radioInput.value : (hiddenInput ? hiddenInput.value : 0);

        //Комплектация
        if (offerId) {
            data.append('offer', offerId);
        }

        //Количество
        let amountElement = context.querySelector(this.settings.amountFormName);
        let amount = amountElement && amountElement.value;
        if (amount) {
            data.append('amount', amount);
        }

        //Многомерные комплектации
        context.querySelectorAll(this.settings.multiOfferFormName + ':checked').forEach(element => {
            data.append(element.getAttribute('name'), element.value);
        });

        //Сопутствующие товары
        context.querySelectorAll(this.settings.concomitantFormName + ':checked').forEach(element => {
            data.append(element.getAttribute('name'), element.value);
        });

        data.append('floatCart', 1); //Сообщаем, что возвращать надо упрощенную корзину

        return data;
    }

    /**
     * Изменяет надпись на кнопке, при добавлении в корзину
     *
     * @param button
     */
    changeStateToAdded(button) {
        if (button.timeoutText) {
            clearTimeout(button.timeoutText);
        }

        let buttonSpan = button.querySelector('span');

        button.classList.add(this.settings.alreadyInCartClass);
        if (!button.dataset.storedText && button.dataset.addText) {
            button.dataset.storedText = buttonSpan.innerHTML;
            buttonSpan.innerHTML = button.dataset.addText;
        }

        if (this.settings.alreadyInCartClassTimeout) {
            button.timeoutText = setTimeout(() => {
                button.classList.remove(this.settings.alreadyInCartClass);
                if (button.dataset.storedText) {
                    buttonSpan.innerHTML = button.dataset.storedText;
                    delete button.dataset.storedText;
                }
            }, this.settings.alreadyInCartClassTimeout * 1000);
        }
    }

    cleanCart(event) {
        event.preventDefault();
        document.querySelectorAll(this.settings.productWrapper + '[data-id] ' + this.settings.addToCart)
            .forEach((element) => {
                element.classList.remove(this.settings.alreadyInCartClass)
            });

        this.refresh(event.rsTarget.getAttribute('href'));
    }

    /**
     * Возвращает пользователя назад к покупкам
     */
    goBack() {
        history.back();
    }

    /**
     * Проверяет корзину на ошибки и если их нет, то перенаправляет
     * пользователя на оформление заказа
     * @param event
     */
    checkout(event) {
        if (this.cartPage) {
            let cartForm = this.cartPage.querySelector(this.settings.cartForm);
            let url = cartForm.getAttribute('action');
            let checkoutParam = (url.indexOf('?') > -1 ? '&' : '?') + 'checkout=1';
            this.refresh(url + checkoutParam);
            event.preventDefault();
        }
    }

    onDocumentReady() {
        this.init();
    }

    onContentReady() {
        //Инициализируем умные кнопки добавления товара в корзину
        SmartAmount.init();
    }
};



/**
 * ====================================================
 * Класс описывает работу одной умной кнопки В корзину
 */
class SmartAmount
{
    constructor(element, settings) {

        let defaults = {
            smartAmount: '.rs-sa',
            smartAmountActiveClass: 'item-product-cart-action_amount',
            smartAmountBuyButton: '.rs-to-cart',
            smartAmountIncButton: '.rs-sa-inc',
            smartAmountDecButton: '.rs-sa-dec',
            smartAmountInput: '.rs-sa-input',
            productContext: '[data-id]'
        };

        this.settings = {...settings, ...defaults};
        this.smartButton = element;

        this.smartButton.querySelector(this.settings.smartAmountBuyButton)
            .addEventListener('click', event => this.addProduct(event));

        this.smartButton.querySelector(this.settings.smartAmountIncButton)
            .addEventListener('click', event => this.incButton(event));

        this.smartButton.querySelector(this.settings.smartAmountDecButton)
            .addEventListener('click', event => this.decButton(event));

        this.amountInput = this.smartButton.querySelector(this.settings.smartAmountInput);
        this.amountInput.addEventListener('keyup', event => this.changeAmount(event));
        this.amountInput.addEventListener('blur', event => this.blur(event));
        this.amountParams = JSON.parse(this.smartButton.dataset.amountParams);

        document.addEventListener('cart.removeProduct', event => this.onRemoveProduct(event));

        this.restoreFromCache();
    }

    restoreFromCache() {
        let productId = this.smartButton.closest(this.settings.productContext).dataset.id;
        let numList = global.cartProducts[productId];
        if (numList) {
            let total = 0;
            for (let num in numList) {
                total = total + numList[num];
            }
            this.amountInput.value = total;
            this.smartButton.classList.add(this.settings.smartAmountActiveClass);
        } else {
            this.amountInput.value = 0;
            this.smartButton.classList.remove(this.settings.smartAmountActiveClass);
        }
    }

    onRemoveProduct(event) {
        let productId = this.smartButton.closest(this.settings.productContext).dataset.id;
        if (event.detail.productId === productId) {
            this.amountInput.value = 0;
            this.amountInput.dispatchEvent(new CustomEvent('keyup', {
                bubbles: true,
                detail: {
                    noRefreshCart: true
                }
            }));
        }
    }

    /**
     * Добавляет товар в корзину, переключает состояние кнопки на количество
     */
    addProduct() {
        this.smartButton.classList.add(this.settings.smartAmountActiveClass);
        this.smartButton.dispatchEvent(new CustomEvent('add-product', {bubbles: true}));
        this.amountInput.value = this.amountParams.amountAddToCart;
    }

    /**
     * Увеличивает количество товара в корзине
     */
    incButton() {
        let oldValue = parseFloat(this.amountInput.value);
        let newValue = Math.round((oldValue + this.amountParams.amountStep) * 1000) / 1000;
        let breakpoint = parseFloat(this.amountParams.amountBreakPoint);

        if (newValue < this.amountParams.minAmount) {
            newValue = this.amountParams.minAmount;
        }
        if (oldValue < breakpoint && newValue > breakpoint) {
            newValue = breakpoint;
        }
        if (this.amountParams.maxAmount !== null && newValue > this.amountParams.maxAmount) {
            newValue = this.amountParams.maxAmount;
            this.smartButton.dispatchEvent(new CustomEvent('max-limit', {bubbles:true}));
        } else {
            this.smartButton.dispatchEvent(new CustomEvent('increase-amount', {bubbles:true}));
        }
        this.amountInput.value = newValue;
        this.amountInput.dispatchEvent(new Event('keyup', {bubbles: true}));
    }

    /**
     * Уменьшает количество товара в корзине
     */
    decButton() {

        let oldValue = parseFloat(this.amountInput.value);
        let newValue = Math.round((oldValue - this.amountParams.amountStep) * 1000) / 1000;
        let breakpoint = parseFloat(this.amountParams.amountBreakPoint);

        if (newValue < this.amountParams.minAmount) {
            newValue = 0;
        }

        if (oldValue > breakpoint && newValue < breakpoint) {
            newValue = breakpoint;
        }

        if (newValue != 0 || !this.amountParams.forbidRemoveProducts) {
            this.smartButton.dispatchEvent(new CustomEvent('decrease-amount', {bubbles:true}));
            this.amountInput.value = newValue;
            this.amountInput.dispatchEvent(new Event('keyup', {bubbles: true}));
        }

        return false;
    }

    /**
     * Изменяет количество товара на введенное вручную
     */
    changeAmount(event) {
        let noChangesKeycodes = [16, 17, 18, 35, 36, 37, 39];
        if (noChangesKeycodes.includes(event.keyCode)) {
            return false;
        }

        let amount = this.amountInput.value;

        if (amount === '') {
            return false;
        }

        if (this.amountParams.maxAmount !== null && amount > parseFloat(this.amountParams.maxAmount)) {
            amount = this.amountParams.maxAmount;
            this.amountInput.value = amount;
            this.smartButton.dispatchEvent(new CustomEvent('max-limit', {bubbles:true}));
        }

        if (amount == 0) {
            this.smartButton.classList.remove(this.settings.smartAmountActiveClass);
            this.smartButton.dispatchEvent(new CustomEvent('remove-product', {bubbles:true}));
        }

        if (!event.detail || !event.detail.noRefreshCart) {
            let formData = new FormData();
            formData.append('id', this.smartButton.closest(this.settings.productContext).dataset.id);
            formData.append('amount', amount);

            RsJsCore.utils.fetchJSON(this.smartButton.dataset.url, {
                method: 'post',
                body: formData
            }).then(response => {
                if (response.success) {
                    // Обновляем корзину на экране
                    RsJsCore.components.cart.updateCartBlock(response);
                }
            });
        }
    }

    blur() {
        if (this.amountInput.value === '') {
            this.amountInput.value = 0;
            this.amountInput.dispatchEvent(new Event('keyup', {bubbles: true}));
        }
    }

    static init(selector) {
        document.querySelectorAll(selector ? selector : '.rs-sa').forEach(element => {
            if (!element.smartAmount) {
                element.smartAmount = new SmartAmount(element);
            }
        });
    }
};