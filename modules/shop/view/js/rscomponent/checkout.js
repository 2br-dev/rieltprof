/**
 * Скрипт инициализирует работу нового оформления заказа на одной странице,
 * а также оформления заказа на странице с корзиной
 */
new class Checkout extends RsJsCore.classes.component {

    constructor() {
        super();
        this.selector = {
            pageContext: '.rs-checkout',
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
            numberBlock: '.checkout-block__num',

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
            hidden: 'd-none',
            requireSelectAddress: 'rs-checkout_requireSelectAddress',
        };
    }

    init(element) {
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

        //Необходимо для корректной работы reCaptcha v3
        this.form.addEventListener('submit', (event) => {
            if (!event.defaultPrevented) {
                let licenseAgreementCheckbox = this.owner.querySelector(this.selector.licenseAgreementCheckbox);
                if (this.owner.querySelector(this.selector.cartError)) {
                    alert(lang.t('В корзине есть ошибки, оформление заказа невозможно'));
                    event.stopPropagation();
                } else if (licenseAgreementCheckbox && !licenseAgreementCheckbox.checked) {
                    alert(lang.t('Необходимо подтвердить согласие с условиями предоставления услуг'));
                    event.stopPropagation();
                } else {
                    this.createOrder();
                }
                event.preventDefault();
            }
        });

        this.owner.addEventListener('click', (event) => {
            if (event.target.closest(this.selector.changeRegionButton)) {
                this.openChangeRegionDialog();
                event.preventDefault();
            }
            if (event.target.closest(this.selector.pvzSelectButton)) {
                this.openSelectPvzDialog();
                event.preventDefault();
            }
            if (event.target.closest(this.selector.addressItemDelete)) {
                this.deleteAddress(event.target.closest(this.selector.addressItem).dataset.id);
                event.preventDefault();
            }
        });

        this.owner.addEventListener('change', (event) => {
            let targetClassList = event.target.classList;
            if (targetClassList.contains(this.class.triggerUpdate)
                && event.target.getAttribute('aria-expanded') != 'true') {
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
                pvzInput.dispatchEvent(new Event('change', {bubbles: true}));
            }
        });

        document.addEventListener('cart.change', () => {
            this.relinkCart();
        });

        window.addEventListener('scroll', event => this.onScroll(event));

        this.afterUpdate();
    }

    /**
     * Навешивает события на элементы обновляемых блоков
     */
    afterUpdate() {
        let addressInput = this.owner.querySelector(this.selector.addressAddressInput);
        if (addressInput) {
            let resultWrapper = document.createElement('div');
            resultWrapper.className = 'head-search__dropdown';
            addressInput.insertAdjacentElement('afterend', resultWrapper);
            let cancelController;

            let autoCompleteInstance = new autoComplete({
                selector: () => addressInput,
                searchEngine: () => true,
                wrapper: false,
                data: {
                    src: async () => {
                        if (cancelController) cancelController.abort();
                        cancelController = new AbortController();

                        let data = await RsJsCore.utils.fetchJSON(this.owner.dataset.url + '&' + new URLSearchParams({
                            action: 'addressAutocomplete',
                            term:autoCompleteInstance.input.value
                        }), {
                            signal: cancelController.signal
                        });

                        return data ? data : [];
                    },
                    keys:['label']
                },
                resultsList: {
                    maxResults:20,
                    class: '',
                    position:'beforeend',
                    destination:() => resultWrapper,
                },
                resultItem: {
                    element: (element, data) => {
                        let tpl;
                        tpl = `<a class="dropdown-item">
                                <div class="col">${data.value.label}</div>
                            </a>`;
                        element.innerHTML = tpl;
                    },
                    selected: 'selected'
                },
                events: {
                    input: {
                        selection: (event) => {
                            const selection = event.detail.selection.value;
                            autoCompleteInstance.input.value = selection.label;

                            for (let key in selection.data) {
                                let selector = '[name="addr_' + key + '"]';
                                let field = this.owner.querySelector(selector);
                                if (field) {
                                    field.value = selection.data[key];
                                }
                            }

                            if (this.requireSelectAddress) {
                                this.addressSelected = true;
                            }
                        }
                    }
                }
            });

            if (addressInput.classList.contains(this.class.requireSelectAddress)) {
                this.requireSelectAddress = true;
                addressInput.addEventListener('keyup', (event) => {
                    if (!this.noChangesKeycodes.includes(event.keyCode)) {
                        this.addressSelected = false;
                    }
                });
            } else {
                this.requireSelectAddress = false;
                this.addressSelected = false;
            }
        }

        this.owner.dispatchEvent(new Event('new-content', {cancelable: true, bubbles: true}));
        this.updateBlockNumbers();
        this.updateSticky();
    }

    /**
     * Обновляет порядковый номер блока
     */
    updateBlockNumbers() {
        this.owner.querySelectorAll(this.selector.numberBlock).forEach((element, i) => {
            element.innerText = i + 1;
        });
    }

    /**
     * Обновляет информацию
     */
    updateSticky() {
        this.checkoutTotalFix = document.querySelector('.checkout-total-fixed');
        let checkoutTotalFixLimit = document.querySelector('#checkout-total-fixed-limit');
        this.stickyOffset = checkoutTotalFixLimit && checkoutTotalFixLimit.offsetTop;
        this.onScroll();
    }

    /**
     * Обработчик смещения Scroll, отображает прибитую панель с суммой
     */
    onScroll() {
        if (this.checkoutTotalFix && this.stickyOffset) {
            if (window.pageYOffset + document.documentElement.clientHeight < this.stickyOffset) {
                this.checkoutTotalFix.classList.add("checkout-total-fixed_act");
            } else {
                this.checkoutTotalFix.classList.remove("checkout-total-fixed_act");
            }
        }
    }

    /**
     * Открывает диалоговое окно изменения города
     */
    openChangeRegionDialog() {
        this.plugins.openDialog.show({
            url: this.owner.dataset.regionChangeUrl,
            bindSubmit:false,
            callback: (response, element) => {
                //Инициализируем диалог выбора города
                new SelectedAddressChange(element, this.owner);
            }
        });
    }

    /**
     * Открывает диалоговое окно выбора ПВЗ на карте
     */
    openSelectPvzDialog() {
        let url = new URL(this.owner.dataset.pvzSelectUrl, location.origin);
        url.searchParams.append('city_id', this.owner.querySelector('[name="addr_city_id"]').value);
        url.searchParams.append('city', this.owner.querySelector('[name="addr_city"]').value);
        url.searchParams.append('region_id', this.owner.querySelector('[name="addr_region_id"]').value);
        url.searchParams.append('region', this.owner.querySelector('[name="addr_region"]').value);
        url.searchParams.append('country_id', this.owner.querySelector('[name="addr_country_id"]').value);
        url.searchParams.append('country', this.owner.querySelector('[name="addr_country"]').value);
        url.searchParams.append('delivery', this.owner.querySelector('[name="delivery"]:checked').value);

        this.plugins.openDialog.show({
            url: url.toString(),
            callback: (params) => {
                let selectPvzElement = document.querySelector(this.selector.objectsSelectPvz);
                if (selectPvzElement) {
                    new SelectPvz(selectPvzElement, () => {
                        selectPvzElement.selectPvz.setDispatchEventTarget(this.owner);
                    });
                }
            }
        });
    }

    /**
     * Отправляет запрос на создание заказа
     */
    createOrder() {
        if (this.requireSelectAddress && !this.addressSelected) {
            alert(lang.t('Выберите адрес из выпадающего списка'));
            return;
        }

        this.form.querySelectorAll('[type="submit"]').forEach(it => {
            it.disabled = true
        });

        let data = new FormData(this.form);
        data.append('action', 'createOrder');
        this.checkoutRequest(data, () => {
            this.goToFirstError();
        });
    }

    /**
     * Перемещает скролл к первой ошибке
     */
    goToFirstError() {
        let errorBlock = this.form.querySelector('.invalid-feedback');
        if (errorBlock) {
            errorBlock.scrollIntoView({block: "center", behavior: "smooth"});
        }
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
    checkoutRequest(data, callback) {
        this.lock();
        this.utils.fetchJSON(this.owner.dataset.url, {
            method: 'post',
            body: data
        }).then((response) => {
            if (response.redirect) {
                window.location = response.redirect;
            } else {
                this.replaceBlocksHtml(response.blocks);
                this.unlock();
                this.afterUpdate();
                if (callback) callback();
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

    onDocumentReady() {
        let pageContext = document.querySelector(this.selector.pageContext);
        if (pageContext) {
            this.init(pageContext);
        }
    }
};

//Окно выбора ПВЗ

class SelectPvz {
    constructor(element, callback = false) {
        if (element.selectPvz) {
            return false;
        }
        element.selectPvz = this;

        this.selector = {
            map: '.rs-selectPvz_map',
            pvzListItem: '.rs-selectPvz_pvzListItem',
            pvzSearchInput: '.rs-selectPvz_pvzSearchInput',
        };
        this.class = {
            pvzMapButton: 'btn btn-primary btn-sm',
        };
        this.options = {
            adminMode: false,
            dispatchEventTarget: undefined,
        };
        this.pvzPoints = {};

        this.owner = element;

        if (this.owner.dataset.SelectPvzOptions) {
            this.options = Object.assign(this.options, JSON.parse(this.owner.dataset.SelectPvzOptions));
        }

        this.owner.addEventListener('map.buttonClick', (event) => {
            this.pvzSelected(event.detail);
        });

        this.owner.querySelector(this.selector.pvzSearchInput).addEventListener('input', (event) => {
            this.pvzSearch(event.target.value);
        });

        let mapElement = this.owner.querySelector(this.selector.map);
        RsJsCore.plugins.mapManager.map.init(mapElement, (map) => {
            this.map = map;
            map.setDispatchEventTarget(this.owner);

            let pvz_list = JSON.parse(this.owner.dataset.pvzJson);
            let coords_x = [];
            let coords_y = [];

            for (let deliveryId in pvz_list) {
                pvz_list[deliveryId].forEach((item) => {
                    if (this.pvzPoints[deliveryId] == undefined) {
                        this.pvzPoints[deliveryId] = {};
                    }

                    let balloonBody = lang.t('Адрес: ') + item.address;
                    if (item.phone) {
                        balloonBody = balloonBody + '<br>' + lang.t('Телефон: ') + item.phone;
                    }
                    if (item.worktime) {
                        balloonBody = balloonBody + '<br>' + lang.t('Время работы: ') + item.worktime;
                    }
                    if (item.note) {
                        balloonBody = balloonBody + '<br>' + lang.t('Заметки: ') + item.note;
                    }

                    let button_data = {
                        delivery: deliveryId,
                        pvz: item,
                    };

                    let newPoint = map.createPoint(parseFloat(item.coord_y), parseFloat(item.coord_x));
                    map.pointSetBalloon(newPoint, item.title, balloonBody, map.htmlButton(button_data, lang.t('Выбрать этот ПВЗ'), this.class.pvzMapButton));
                    map.addPoint(newPoint);
                    this.pvzPoints[deliveryId][item.code] = newPoint;

                    coords_x.push(parseFloat(item.coord_x));
                    coords_y.push(parseFloat(item.coord_y));
                });
            }

            map.setBounds(Math.max.apply(Math, coords_y), Math.min.apply(Math, coords_y), Math.min.apply(Math, coords_x), Math.max.apply(Math, coords_x));

            this.owner.querySelectorAll(this.selector.pvzListItem).forEach((element) => {
                element.addEventListener('click', () => {
                    this.map.pointOpenBalloon(this.pvzPoints[element.dataset.deliveryId][element.dataset.pvzCode]);
                });
            });

            if (callback) {
                callback();
            }
        });
    }

    /**
     * Поиск ПВЗ
     *
     * @param {string} searchQuery - поисковый запрос
     */
    pvzSearch(searchQuery) {
        let search = searchQuery.toLowerCase();
        this.owner.querySelectorAll(this.selector.pvzListItem).forEach((element) => {
            if (search != '' && element.dataset.searchString.toLowerCase().indexOf(search) == -1) {
                if (!element.hidden) {
                    element.hidden = true;
                    this.map.removePoint(this.pvzPoints[element.dataset.deliveryId][element.dataset.pvzCode]);
                }
            } else {
                if (element.hidden) {
                    element.hidden = false;
                    this.map.addPoint(this.pvzPoints[element.dataset.deliveryId][element.dataset.pvzCode]);
                }
            }
        });
    }

    /**
     * Обрабатывает событие выбора ПВЗ на карте
     *
     * @param {array} eventDetail - данные выбранного ПВЗ
     */
    pvzSelected(eventDetail) {
        // todo это приватный метод, но js такого пока не умеет
        let eventProperties = {
            detail: eventDetail,
            cancelable: true,
        };
        if (!this.options.dispatchEventTarget) {
            eventProperties['bubbles'] = true;
        }
        let newEvent = new CustomEvent('pvzSelected', eventProperties);

        if (this.options.dispatchEventTarget) {
            this.options.dispatchEventTarget.dispatchEvent(newEvent);
        } else {
            document.dispatchEvent(newEvent);
        }

        RsJsCore.plugins.modal.close();
    }

    /**
     * Устанавливает цель событий выбора ПВЗ
     *
     * @param {element} element - цель событий
     */
    setDispatchEventTarget(element) {
        this.options.dispatchEventTarget = element;
    }

    /**
     * Устанавливает контекст запуска "в админ. панели"
     *
     * @param {bool} value
     */
    setAdminMode(value) {
        this.options.adminMode = value;
    }
};