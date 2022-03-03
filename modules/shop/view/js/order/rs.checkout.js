class Checkout {
    constructor(element) {
        this.selector = {
            form: '.rs-checkout_form',
            userBlock: '.rs-checkout_userBlock',
            cityBlock: '.rs-checkout_cityBlock',
            addressBlock: '.rs-checkout_addressBlock',
            deliveryBlock: '.rs-checkout_deliveryBlock',
            paymentBlock: '.rs-checkout_paymentBlock',
            productBlock: '.rs-checkout_productBlock',
            totalBlock: '.rs-checkout_totalBlock',
            captchaBlock: '.rs-checkout_captchaBlock',
            commentBlock: '.rs-checkout_commentBlock',
            lockOnUpdate: '.rs-checkout_lockOnUpdate',
            changeRegionButton: '.rs-checkout_changeRegionButton',
            submitButton: '.rs-checkout_submitButton',
            addressItem: '.rs-checkout_addressItem',
            addressItemDelete: '.rs-checkout_addressItemDelete',
            addressAddressInput: '.rs-checkout_addressAddressInput',
            pvzBlock: '.rs-checkout_pvzBlock',
            pvzInput: '.rs-checkout_pvzInput',
            pvzSelectButton: '.rs-checkout_pvzSelectButton',
            addressAddressInputWrapper: '.rs-checkout_addressAddressInputWrapper',
            registerGeneratePassword: '.rs-checkout_registerGeneratePassword',
            registerGeneratePasswordInput: '.rs-checkout_registerGeneratePasswordInput',
            licenseAgreementCheckbox: '.rs-checkout_licenseAgreementCheckbox',
            registerPassword: '.rs-checkout_registerPassword',
            cartError: '.rs-checkout_cartError',
            objectChangeRegion: '.rs-selectedAddressChange',
            objectsSelectPvz: '.rs-selectPvz',
        };
        this.class = {
            triggerUpdate: 'rs-checkout_triggerUpdate',
            createUserInput: 'rs-checkout_registerUserInput',
            registerGeneratePasswordInput: 'rs-checkout_registerGeneratePasswordInput',
            lock: 'rs-checkout_lock',
            hidden: 'rs-hidden',
            requireSelectAddress: 'rs-checkout_requireSelectAddress',
        };

        this.owner = element;
        this.form = this.owner.querySelector(this.selector.form);
        this.blocks = {
            user: this.owner.querySelector(this.selector.userBlock),
            city: this.owner.querySelector(this.selector.cityBlock),
            address: this.owner.querySelector(this.selector.addressBlock),
            delivery: this.owner.querySelector(this.selector.deliveryBlock),
            payment: this.owner.querySelector(this.selector.paymentBlock),
            product: this.owner.querySelector(this.selector.productBlock),
            total: this.owner.querySelector(this.selector.totalBlock),
            captcha: this.owner.querySelector(this.selector.captchaBlock),
            comment: this.owner.querySelector(this.selector.commentBlock),
        };
        this.requireSelectAddress = false;
        this.addressSelected = false;
        this.noChangesKeycodes = [16, 17, 18, 35, 36, 37, 38, 39, 40];

        //Обязательно используем обертку jQuery, чтобы получать событие, которое генерирует jQuery в других модулях (reCaptcha v3)
        //Необходимо для корректной работы reCaptcha v3
        $(this.form).on('submit',(event) => {
            if (!event.isDefaultPrevented()) {
                let licenseAgreementCheckbox = this.owner.querySelector(this.selector.licenseAgreementCheckbox);
                if (this.owner.querySelector(this.selector.cartError)) {
                    alert(lang.t('В корзине есть ошибки, оформление заказа невозможно'));
                } else if (licenseAgreementCheckbox && !licenseAgreementCheckbox.checked) {
                    alert(lang.t('Необходимо подтвердить согласие с условиями предоставления услуг'));
                } else {
                    this.createOrder();
                }
                event.preventDefault();
            }
        });

        this.owner.addEventListener('click', (event) => {
            if (event.target.closest(this.selector.changeRegionButton)) {
                this.openChangeRegionDialog();
            }
            if (event.target.closest(this.selector.pvzSelectButton)) {
                this.openSelectPvzDialog();
            }
            if (event.target.closest(this.selector.addressItemDelete)) {
                this.deleteAddress(event.target.closest(this.selector.addressItem).dataset.id);
            }
        });

        this.owner.addEventListener('change', (event) => {
            let targetClassList = event.target.classList;
            if (targetClassList.contains(this.class.triggerUpdate)) {
                this.updateBlocks();
            }
            if (targetClassList.contains(this.class.createUserInput)) {
                if (event.target.checked) {
                    this.owner.querySelector(this.selector.registerGeneratePassword).classList.remove(this.class.hidden);
                    this.owner.querySelector(this.selector.registerGeneratePasswordInput).dispatchEvent(new Event('change', {cancelable: true, bubbles: true}));
                } else {
                    this.owner.querySelector(this.selector.registerGeneratePassword).classList.add(this.class.hidden);
                    this.owner.querySelectorAll(this.selector.registerPassword).forEach((element) => {
                        element.classList.add(this.class.hidden);
                    });
                }
            }
            if (targetClassList.contains(this.class.registerGeneratePasswordInput)) {
                if (event.target.checked) {
                    this.owner.querySelectorAll(this.selector.registerPassword).forEach((element) => {
                        element.classList.add(this.class.hidden);
                    });
                } else {
                    this.owner.querySelectorAll(this.selector.registerPassword).forEach((element) => {
                        element.classList.remove(this.class.hidden);
                    });
                }
            }
        });

        this.owner.addEventListener('addressSelected', (event) => {
            let address = event.detail.address;
            this.owner.querySelector('[name="addr_city_id"]').value = address.city_id;
            this.owner.querySelector('[name="addr_city"]').value = address.city;
            this.owner.querySelector('[name="addr_region_id"]').value = address.region_id;
            this.owner.querySelector('[name="addr_region"]').value = address.region;
            this.owner.querySelector('[name="addr_country_id"]').value = address.country_id;
            this.owner.querySelector('[name="addr_country"]').value = address.country;
            this.updateBlocks();
        });

        this.owner.addEventListener('pvzSelected', (event) => {
            let pvzInput = this.owner.querySelector(this.selector.pvzInput);
            if (this.owner.querySelector('[name="delivery"]:checked').value == event.detail.delivery) {
                pvzInput.value = pvzInput.querySelector('[data-pvz-code="'+event.detail.pvz.code+'"]').value;
            }
        });

        document.addEventListener('cart.change', () => {
            this.relinkCart();
        });

        this.afterUpdate();
    }

    /**
     * Навешивает события на элементы обновляемых блоков
     */
    afterUpdate() {
        // todo кусочек jQuery в нативном классе
        $(this.selector.addressAddressInput, this.owner).autocomplete({
            source: this.owner.dataset.url + '&action=addressAutocomplete',
            appendTo: this.owner.querySelector(this.selector.addressAddressInputWrapper),
            minLength: 3,
            select: ( event, ui ) => {
                for (let key in ui.item.data) {
                    let selector = '[name="addr_' + key + '"]';
                    let field = this.owner.querySelector(selector);
                    if (field) {
                        field.value = ui.item.data[key];
                    }
                }

                if (this.requireSelectAddress) {
                    this.addressSelected = true;
                }
            },
        });

        let address_field = this.owner.querySelector(this.selector.addressAddressInput);
        if (address_field && address_field.classList.contains(this.class.requireSelectAddress)) {
            this.requireSelectAddress = true;
            address_field.addEventListener('keyup', (event) => {
                if (!this.noChangesKeycodes.includes(event.keyCode)) {
                    this.addressSelected = false;
                }
            });
        } else {
            this.requireSelectAddress = false;
            this.addressSelected = false;
        }

        this.owner.dispatchEvent(new Event('new-content', {cancelable: true, bubbles: true}));
    }

    /**
     * Открывает диалоговое окно изменения города
     */
    openChangeRegionDialog() {
        // todo кусочек jQuery в нативном классе
        $.openDialog({
            url: this.owner.dataset.regionChangeUrl,
            callback: (params) => {
                let regionChange = document.querySelector(this.selector.objectChangeRegion);
                let waiting = setInterval(() => {
                    if (regionChange.selectedAddressChange) {
                        clearInterval(waiting);
                        regionChange.selectedAddressChange.setMode('dispatchEvent');
                        regionChange.selectedAddressChange.setResultEventTarget(this.owner);
                    }
                }, 10);
            },
        });
    }

    /**
     * Открывает диалоговое окно выбора ПВЗ на карте
     */
    openSelectPvzDialog() {
        let url = new URL(this.owner.dataset.pvzSelectUrl, this.owner.dataset.rootUrl);
        url.searchParams.append('city_id', this.owner.querySelector('[name="addr_city_id"]').value);
        url.searchParams.append('delivery', this.owner.querySelector('[name="delivery"]:checked').value);

        // todo кусочек jQuery в нативном классе
        $.openDialog({
            url: url.toString(),
            callback: (params) => {
                let selectPvzElement = document.querySelector(this.selector.objectsSelectPvz);
                let waiting = setInterval(() => {
                    if (selectPvzElement.selectPvz) {
                        clearInterval(waiting);
                        selectPvzElement.selectPvz.setDispatchEventTarget(this.owner);
                    }
                }, 10);
            },
        });
    }

    /**
     * Отправляет запрос на создание заказа
     */
    createOrder() {
        if (this.requireSelectAddress && !this.addressSelected) {
            alert(lang.t('Вы должны выбрать адрес из выпадающего списка'));
            return;
        }

        let data = new FormData(this.form);
        data.append('action', 'createOrder');
        this.checkoutRequest(data);
    }

    /**
     * Отправляет запрос на удаление адреса и обновляет блоки
     *
     * @param id
     */
    deleteAddress(id) {
        let data = new FormData();
        data.append('action', 'deleteAddress');
        data.append('id', id);
        this.checkoutRequest(data);
    }

    /**
     * Отправляет запрос на перепривязку корзины к заказу и обновляет блоки
     */
    relinkCart() {
        let data = new FormData();
        data.append('action', 'relinkCart');
        this.checkoutRequest(data);
    }

    /**
     * Отправляет запрос на изменение заказа и обновляет блоки
     */
    updateBlocks() {
        let data = new FormData(this.form);
        data.append('action', 'update');
        this.checkoutRequest(data);
    }

    /**
     * Отправляет запрос к контроллеру и обновляет блоки
     *
     * @param {FormData} data - параметры запроса
     */
    checkoutRequest(data) {
        this.lock();
        fetch(this.owner.dataset.url, {
            method: 'post',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: data,
        }).then((response) => {
            return response.json();
        }).then((response) => {
            if (response.redirect) {
                window.location = response.redirect;
            } else {
                this.replaceBlocksHtml(response.blocks);
                this.unlock();
                this.afterUpdate();
            }
        });
    }

    /**
     * Подменяет содержимое блоком новыми данными
     *
     * @param new_blocks - массив с новыми блоками
     */
    replaceBlocksHtml(new_blocks) {
        for (let block in this.blocks) {
            if (this.blocks[block] && new_blocks[block]) {
                this.blocks[block].innerHTML = new_blocks[block];
            }
        }
    }

    /**
     * Блокирует блоки
     */
    lock() {
        for (let block in this.blocks) {
            if (this.blocks[block]) {
                this.blocks[block].classList.add(this.class.lock);
            }
        }
        this.owner.querySelectorAll(this.selector.lockOnUpdate).forEach((element) => {
            element.classList.add(this.class.lock);
        });
    }

    /**
     * Разблокирует блоки
     */
    unlock() {
        for (let block in this.blocks) {
            if (this.blocks[block]) {
                this.blocks[block].classList.remove(this.class.lock);
            }
        }
        this.owner.querySelectorAll(this.selector.lockOnUpdate).forEach((element) => {
            element.classList.remove(this.class.lock);
        });
    }

    static init(selector)
    {
        document.querySelectorAll(selector).forEach((element) => {
            if (!element.checkout) {
                element.checkout = new Checkout(element);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    Checkout.init('.rs-checkout');
});