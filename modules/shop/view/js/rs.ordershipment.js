class orderShipment {

    constructor(element) {
        this.selector = {
            form: '.order-shipment-form',
            errorFloatHead: '.order-shipment-error-float-head',
            itemsTable: '.order-shipment-table',
            itemsTableHead: '.order-shipment-table-head',
            itemsTableInfo: '.order-shipment-table-info',
            item: '.order-shipment-item',
            itemUIT: '.order-shipment-item-uit',
            itemUITList: '.order-shipment-item-uit-list',
            itemUITInput: '.order-shipment-item-uit-input',
            itemButtonAddUIT: '.order-shipment-item-btn-add-uit',
            blankUIT: '.blank-uit-list-item',
            UITListItem: '.uit-list-item',
            dialogWindow: '.dialog-window',
            buttonExecuteShipment: '.execute-shipment',
        };
        this.class = {
            empty: 'empty',
            loading: 'rs-loading-spin',
            highlight: 'order-shipment-highlight',
            highlightFading: 'order-shipment-highlight-fading',
            highlightError: 'order-shipment-highlight-error',
            showError: 'show-error',
            itemSelected: 'item-selected',
            UITListItem: 'uit-list-item',
            UITListItemField: 'uit-list-item-field',
            UITListItemFieldTitle: 'uit-list-item-field-title',
            UITListItemRemove: 'uit-list-item-remove',
            buttonExecuteShipmentDisable: 'btn-default',
        };
        this.dataKeys = {
            gtin: '01',
            serial: '21',
        };

        this.owner = element;
        this.form = this.owner.querySelector(this.selector.form);

        this.options = JSON.parse(this.owner.dataset.orderShipmentOptions);
        this.checkConformityUitToBarcode = (this.options.checkConformityUitToBarcode == 1);

        this.selectedItem = null;
        this.canFullShipment = false;
        this.canShipment = false;

        this.owner.querySelectorAll(this.selector.item).forEach((element) => {
            element.addEventListener('click', () => {
                this.selectItem(element);
            });
        });
        this.owner.querySelectorAll(this.selector.itemButtonAddUIT).forEach((element) => {
            element.addEventListener('click', () => {
                let productItem = element.closest(this.selector.item);
                this.itemAddUITFromInput(productItem);
            });
        });
        this.owner.querySelectorAll(this.selector.itemUITInput).forEach((element) => {
            element.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    event.stopPropagation();

                    this.itemAddUITFromInput(event.target.closest(this.selector.item));
                } else {
                    let new_key = this.getKeyFromKeydownEvent(event);
                    if (event.key != new_key) {
                        event.preventDefault();
                        event.stopPropagation();

                        event.target.value = event.target.value + new_key;
                    }
                }
            });
        });
        this.owner.addEventListener('click', (event) => {
            if (event.target.classList.contains(this.class.UITListItemRemove)) {
                this.itemRemoveUIT(event.target);
            }
        });
        this.owner.addEventListener('barcode-scanned', (event) => {
            if (event.detail.codeType === 'twoDimensional') {
                this.itemAddUITFromScanner(event.detail.code);
            }
            if (event.detail.codeType === 'oneDimensional') {
                this.selectItemByBarcode(event.detail.code);
            }
        });

        // todo кусочек jQuery в нативном плагине
        $(this.owner).on('crudSaveFail', (event, response) => {
            this.showErrorFromResponse(response);
        });

        this.checkShipment();

        this.owner.closest(this.selector.dialogWindow).querySelector(this.selector.buttonExecuteShipment).addEventListener('click', (event) => {
            event.preventDefault();

            this.runShipment();
        });
    }

    /**
     * Проверяет возможна ли отгрузка и запускает её
     *
     * @returns {boolean|void}
     */
    runShipment() {
        let execute = false;

        if (!this.canShipment) {
            this.showErrorFloatHead(lang.t('Отгрузка невозможна'));
            return false;
        } else if (this.canFullShipment) {
            execute = true;
        } else if (confirm("Просканированы не все товары, вы действительно хотите произвести частичную отгрузку?")) {
            execute = true;
        }

        if (execute) {
            // todo кусочек jQuery в нативном плагине
            $.rs.loading.show();

            let formData = new FormData(this.form);
            formData.append('ajax', 1);

            fetch(this.form.action, {
                method: 'post',
                body: formData,
            }).then((response) => {
                return response.json();
            }).then((response) => {
                if (response.success) {
                    this.executeShipment();
                } else {
                    // todo кусочек jQuery в нативном плагине
                    $.rs.loading.hide();
                    this.showErrorFromResponse(response);
                }
            });
        }
    }

    /**
     * Отправляет запрос на отгрузку
     */
    executeShipment() {
        let formData = new FormData(this.form);
        formData.append('ajax', 1);

        fetch(this.owner.dataset.urlMakeShipment, {
            method: 'post',
            body: formData,
        }).then((response) => {
            return response.json();
        }).then((response) => {
            // todo кусочек jQuery в нативном плагине
            $.rs.checkMessages(response);
            $.rs.loading.hide();
            if (response.success) {
                $(this.owner.closest('.dialog-window')).dialog('close');
            }
        });
    }

    /**
     * Добавляет УИТ из поля ввода
     *
     * @param productItem - строка товарной позиции
     */
    itemAddUITFromInput(productItem) {
        let button = productItem.querySelector(this.selector.itemButtonAddUIT);
        let input = productItem.querySelector(this.selector.itemUITInput);
        let url = this.owner.dataset.urlParseCode;
        let productId = productItem.dataset.productId;
        let code = input.value;

        if (productItem.querySelectorAll(this.selector.UITListItem).length >= productItem.dataset.totalAmount) {
            this.showErrorFloatHead(lang.t('Для данной позиции все УИТ уже просканированы'));
            return;
        }
        if (!code) {
            this.showErrorFloatHead(lang.t('Код не указан'));
            return;
        }

        let formData = new FormData();
        formData.append('product_id', productId);
        formData.append('code', code);

        button.classList.add(this.class.loading);
        input.value = '';

        fetch(url, {
            method: 'post',
            body: formData,
        }).then((response) => {
            return response.json();
        }).then((response) => {
            button.classList.remove(this.class.loading);
            if (response.success) {
                this.itemAddUIT(productItem, response.result);
            } else {
                this.showErrorFromResponse(response);
            }
        });
    }

    /**
     * Добавляет УИТ со сканера
     *
     * @param code - введённый код
     */
    itemAddUITFromScanner(code) {
        let probablyBarcode = code.substr(2, 14).replace(new RegExp('^0+'), '');

        if (this.checkConformityUitToBarcode) {
            if (!this.selectItemByBarcode(probablyBarcode)) {
                return;
            }
        } else if (!this.selectedItem) {
            this.showErrorFloatHead(lang.t('Не выбрана товарная позиция'));
            return;
        }

        let productItem = this.selectedItem;

        if (!productItem.dataset.isMarked) {
            this.showErrorFloatHead(lang.t('Выбранный товар не подлежит маркировке'));
        }

        if (productItem.querySelectorAll(this.selector.UITListItem).length >= productItem.dataset.totalAmount) {
            this.showErrorFloatHead(lang.t('Для данной позиции все УИТ уже просканированы'));
            return;
        }

        let formData = new FormData();
        formData.append('product_id', productItem.dataset.productId);
        formData.append('code', code);

        fetch(this.owner.dataset.urlParseCode, {
            method: 'post',
            body: formData,
        }).then((response) => {
            return response.json();
        }).then((response) => {
            if (response.success) {
                let gtin = response.result[this.dataKeys.gtin];
                if (!this.checkConformityUitToBarcode || gtin === productItem.dataset.barcode) {
                    this.itemAddUIT(productItem, response.result);
                } else {
                    this.showErrorFloatHead(lang.t('Код не соответствует штрихкоду товарной позиции'));
                }
            } else {
                this.showErrorFromResponse(response);
            }
        });
    }

    /**
     * Добавляет УИТ к товарной позиции
     *
     * @param item - строка товарной позиции
     * @param data - данные УИТ
     */
    itemAddUIT(item, data) {
        let gtin = data[this.dataKeys.gtin];
        let serial = data[this.dataKeys.serial];
        let list = item.querySelector(this.selector.itemUITList);

        let probablyUIT = list.querySelector(this.selector.UITListItem + '[data-id="' + gtin + serial + '"]');
        if (probablyUIT) {
            this.highlightItem(probablyUIT);
            this.showErrorFloatHead(lang.t('Данный код уже добавлен'));
            return;
        }

        let html = this.owner.querySelector(this.selector.blankUIT).innerHTML;
        html = html.replace(new RegExp('%cart_item_uniq', 'g'), item.dataset.id);
        html = html.replace(new RegExp('%gtin', 'g'), gtin);
        html = html.replace(new RegExp('%serial', 'g'), serial);

        list.insertAdjacentHTML('beforeend', html);
        this.checkShipment();
    }

    /**
     * Удаляет УИТ из товарной позиции
     *
     * @param element - элемент внутри УИТ
     */
    itemRemoveUIT(element) {
        element.closest(this.selector.UITListItem).remove();
        this.checkShipment();
    }

    /**
     * Проверяет возможность отгрузки и заполняет информирующую надпись
     */
    checkShipment() {
        let already_full_shipment = true;
        let already_part_shipment = false;
        let can_full_shipment = true;
        let can_shipment = false;
        let has_marked_items = this.owner.querySelector(this.selector.item + '[data-is-marked="1"]');

        this.owner.querySelectorAll(this.selector.item).forEach((item) => {
            if (item.dataset.alreadyShippedAmount > 0) {
                already_part_shipment = true;
            }
            if (item.dataset.alreadyShippedAmount < item.dataset.totalAmount) {
                already_full_shipment = false;
            }
            if (item.dataset.isMarked == 1) {
                let checked_amount = item.querySelectorAll(this.selector.UITListItem).length;
                if (checked_amount > item.dataset.alreadyShippedAmount) {
                    can_shipment = true;
                }
                if (checked_amount < item.dataset.totalAmount) {
                    can_full_shipment = false;
                }
            } else {
                if (item.dataset.totalAmount > item.dataset.alreadyShippedAmount) {
                    can_shipment = true;
                }
            }
        });

        if (already_full_shipment) {
            this.setTableInfo(lang.t('Все товары уже отгружены.'));
            this.owner.closest(this.selector.dialogWindow).querySelector(this.selector.buttonExecuteShipment).classList.add(this.class.buttonExecuteShipmentDisable);
            this.canShipment = false;
        } else if (can_full_shipment) {
            let info = (has_marked_items) ? lang.t('Все товары просканированы. ') : '';
            info = info + lang.t('Можно совершить полную отгрузку.');

            this.setTableInfo(info);
            this.owner.closest(this.selector.dialogWindow).querySelector(this.selector.buttonExecuteShipment).classList.remove(this.class.buttonExecuteShipmentDisable);
            this.canShipment = true;
            this.canFullShipment = true;
        } else if (can_shipment) {
            let info = (has_marked_items) ? lang.t('Часть товаров просканирована. ') : '';
            info = info + lang.t('Можно совершить частичную отгрузку.');

            this.setTableInfo(info);
            this.owner.closest(this.selector.dialogWindow).querySelector(this.selector.buttonExecuteShipment).classList.remove(this.class.buttonExecuteShipmentDisable);
            this.canShipment = true;
            this.canFullShipment = false;
        } else {
            this.setTableInfo(lang.t('Для совершения отгрузки просканируйте товары.'));
            this.owner.closest(this.selector.dialogWindow).querySelector(this.selector.buttonExecuteShipment).classList.add(this.class.buttonExecuteShipmentDisable);
            this.canShipment = false;
        }
    }

    /**
     * Устанавливает информационный текст
     *
     * @param html - текст
     */
    setTableInfo(html) {
        this.owner.querySelector(this.selector.itemsTableInfo).innerHTML = html;
    }

    /**
     * Выбирает товарную позицию с указанным штрихкодом, возвращает false в случае шибки
     *
     * @param code
     */
    selectItemByBarcode(code) {
        let productItem = this.owner.querySelector(this.selector.item + '[data-barcode="' + code + '"]');
        if (productItem) {
            this.selectItem(productItem);
            return true;
        } else {
            this.showErrorFloatHead(lang.t('Заказ не содержит позицию с таким штрихкодом') + ' (' + code + ')');
            return false;
        }
    }

    /**
     * Помечает товарную позицию как выбранную
     *
     * @param element
     */
    selectItem(element) {
        if (this.selectedItem !== element) {
            this.selectedItem = element;
            document.querySelectorAll(this.selector.item).forEach((element) => {
                element.classList.remove(this.class.itemSelected);
                this.selectedItem.classList.add(this.class.itemSelected);
            });

            this.scrollToItem(this.selectedItem);
        }
    }

    /**
     * Прокручавает список товарных позиций к указанной
     *
     * @param element
     */
    scrollToItem(element) {
        let tableElement = element.closest(this.selector.itemsTable);
        let headHeight = tableElement.querySelector(this.selector.itemsTableHead).offsetHeight;
        let tableScrollBottom = tableElement.scrollTop + tableElement.clientHeight;
        let scrollTo = element.offsetTop - headHeight;
        if (scrollTo < tableElement.scrollTop || scrollTo > tableScrollBottom) {
            tableElement.scrollTop = scrollTo;
            this.highlightItem(element);
        }
    }

    highlightItem(element) {
        element.classList.add(this.class.highlight);
        setTimeout(() => {
            element.classList.add(this.class.highlightFading);
            element.classList.remove(this.class.highlight);
            setTimeout(() => {
                element.classList.remove(this.class.highlightFading);
            }, 5000);
        }, 4);
    }

    /**
     * Обработчик шибок
     *
     * @param response
     */
    showErrorFromResponse(response) {
        if (response.error_type === 'float_head') {
            this.showErrorFloatHead(response.error);
        }
        if (response.error_type === 'uit_highlight') {
            this.showErrorUitHighlight(response.uit_list);
            this.showErrorFloatHead(response.error);
        }
    }

    /**
     * Показывает ошибу в верхней части окна
     *
     * @param html - html ошибки
     */
    showErrorFloatHead(html) {
        let error = this.owner.querySelector(this.selector.errorFloatHead);
        error.innerHTML = html;
        error.classList.remove(this.class.empty);
        error.classList.add(this.class.showError);
        setTimeout(() => {
            error.classList.remove(this.class.showError);
        }, 500);
    }

    /**
     * Подсвечивает ошибочные УИТ
     *
     * @param uit_list - список УИТ
     */
    showErrorUitHighlight(uit_list) {
        uit_list.forEach(uit_id => {
            let uit = this.owner.querySelector(this.selector.UITListItem + '[data-id="' + uit_id + '"]');
            uit.classList.add(this.class.highlightError);
        });

        let first_uit_with_error = this.owner.querySelector(this.selector.UITListItem + '.' + this.class.highlightError).closest(this.selector.item);

        if (this.selectedItem === first_uit_with_error) {
            this.scrollToItem(first_uit_with_error);
        } else {
            this.selectItem(first_uit_with_error);
        }
    }

    /**
     * Корректирует введённый символ
     *
     * @param event - событие нажатия клавиши
     * @returns {string}
     */
    getKeyFromKeydownEvent(event) {
        let key;
        if (event.code.search('^Key') !== -1) {
            key = String.fromCharCode(event.keyCode);
            if (!event.shiftKey) {
                key = key.toLowerCase();
            }
        } else if (event.code.search('^Slash') !== -1) {
            key = (event.shiftKey) ? '?' : '/';
        } else {
            key = event.key;
        }

        return key;
    }

    static init(selector)
    {
        document.querySelectorAll(selector).forEach((element) => {
            if (!element.orderShipment) {
                element.orderShipment = new orderShipment(element);
            }
        });
    }
}

// todo кусочек jQuery в нативном плагине
$(document).on('new-content', () => {
    orderShipment.init('.rs-order-shipment');
});
