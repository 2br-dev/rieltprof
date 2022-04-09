/**
 * Скрипт инициализирует работу инструмента для распределения товаров по грузовым местам
 */
class OrderCargo {

    constructor(element) {
        this.selector = {
            addCargo:'.cargo-add',
            cargoFormContainer: '.cargo-form-container',
            cargoListContainer: '.cargo-list-body',
            cargoAmountContainer: '.cargo-amount-container',
            cargoDetail: '.cargo-detail',
            cargoList: '.cargo-list',
            cargoEmpty: '.cargo-empty',
            cargoItemTitle: '.cargo-item-title',
            cargoItemRemove: '.cargo-item-remove',
            cargoProductContainer: '.cargo-product-container',
            cargoEmptyContainer: '.cargo-empty-container',
            cargoProductItem: '.cargo-product-item',
            cargoAmount: '.cargo-amount',
            cargoMaxAmount: '.cargo-max-amount',
            productLine: 'tr',
            cargoPutAll: '.cargo-put-all'
        };

        this.classes = {
            cargoItemActive: 'act',
            cargoAmount: 'cargo-amount'
        };

        this.owner = element;
        this.cargoFormContainer = this.owner.querySelector(this.selector.cargoFormContainer);
        this.cargoListContainer = this.owner.querySelector(this.selector.cargoListContainer);
        this.cargoProductContainer = this.owner.querySelector(this.selector.cargoProductContainer);
        this.cargoEmptyContainer = this.owner.querySelector(this.selector.cargoEmptyContainer);
        this.cargoDetail = this.owner.querySelector(this.selector.cargoDetail);
        this.cargoList = this.owner.querySelector(this.selector.cargoList);
        this.cargoEmpty = this.owner.querySelector(this.selector.cargoEmpty);

        this.owner.querySelectorAll(this.selector.addCargo).forEach((element) => {
            element.addEventListener('click', event => this.onAddCargo(event.target.dataset.presetId));
        });
        this.owner.addEventListener('click', event => {
            if (event.target.closest(this.selector.cargoItemTitle)) {
                let id = event.target.closest('[data-cargo-id]').dataset.cargoId;
                this.onSelectCargo(id);
            }

            if (event.target.closest(this.selector.cargoItemRemove)) {
                let id = event.target.closest('[data-cargo-id]').dataset.cargoId;
                this.onRemoveCargo(id);
            }
        });

        let putAll = this.owner.querySelector(this.selector.cargoPutAll);
        if (putAll) {
            putAll.addEventListener('click', event => this.onPutAll(event));
        }

        this.refreshState();
    }

    /**
     * Помещает в коробку все оставшиеся товары
     *
     * @param event
     */
    onPutAll(event) {
        let activeCargo = this.cargoListContainer.querySelector('.' + this.classes.cargoItemActive);
        if (activeCargo) {
            let id = activeCargo.dataset.cargoId;

            this.cargoProductContainer.querySelectorAll(this.selector.cargoProductItem).forEach((element) => {
                let maxAmount = element.querySelector(this.selector.cargoMaxAmount).innerText;
                let amount = element.querySelector(this.selector.cargoAmount+'[data-cargo-id="' + id + '"] input');
                if (amount) {
                    amount.value = maxAmount;
                }
            });
        }
    }

    /**
     * Выбирает коробку в качестве текущей
     *
     * @param id
     */
    onSelectCargo(id) {
        this.cargoListContainer.childNodes.forEach(element => {
            if (element.classList) {
                element.classList.remove(this.classes.cargoItemActive);
            }
        });
        this.cargoFormContainer.childNodes.forEach(element => {
            element.hidden = true;
        });

        this.cargoListContainer.querySelector('[data-cargo-id="' + id + '"]').classList.add(this.classes.cargoItemActive);
        this.cargoFormContainer.querySelector('[data-cargo-id="' + id + '"]').hidden = false;

        this.refreshState();
    }

    /**
     * Добавляет одну коробку в список
     *
     * @param id
     */
    onAddCargo(id) {
        $.ajaxQuery({
            url: this.owner.dataset.addCargoUrl,
            data: {
                ajax: 1,
                preset_id: id
            },
            success: (response) => {
                //Добавим форму коробки
                this.cargoFormContainer.insertAdjacentHTML('beforeend', response.cargo_form);
                this.cargoListContainer.insertAdjacentHTML('beforeend', response.cargo_item);
                //Добавим инпуты с количеством для каждого товара
                this.cargoProductContainer.querySelectorAll(this.selector.cargoProductItem + ' '+ this.selector.cargoAmountContainer).forEach(element => {
                    this._addAmountInputToProduct(response.cargo_id, element);
                });
                this.onSelectCargo(response.cargo_id);
            }
        });
    }

    /**
     * Рассчитывает для одного товара его максимальный остаток в рамках текущей коробки
     *
     * @param cargoId
     * @param container
     * @private
     */
    _calculateMaxAmount(cargoId, container) {
        let realMaxAmount = container.dataset.maxAmount;
        let usedAmount = 0;

        let currentInput;
        container.querySelectorAll(this.selector.cargoAmount).forEach(element => {
            let input = element.querySelector('input');
            if (element.dataset.cargoId != cargoId) {
                usedAmount += parseFloat(input.value);
            } else {
                currentInput = input;
            }
        });

        let maxAmountElement = container.parentNode.querySelector(this.selector.cargoMaxAmount);
        let maxAmount = realMaxAmount - usedAmount;
        container.closest(this.selector.productLine).hidden = !maxAmount;

        maxAmountElement.innerText = maxAmount;
        currentInput.max = maxAmount;
        return maxAmount;
    }

    /**
     * Добавляет формы ввода количества к товарам для текущей коробки
     *
     * @param cargoId
     * @param container
     * @private
     */
    _addAmountInputToProduct(cargoId, container) {
        let orderItemId = container.dataset.cartitemKey;
        let uitId = (+container.dataset.uitId);
        let step = container.dataset.amountStep;

        let div = document.createElement('div');
        div.className = this.classes.cargoAmount;
        div.dataset.cargoId = cargoId;

        let input = document.createElement('input');
        input.type = "number";
        input.min = "0";
        input.value = "0";
        input.step = step;
        input.name = `cargo[${cargoId}][products][${orderItemId}][${uitId}][amount]`;
        this._checkInputValues(input);

        div.appendChild(input);
        container.appendChild(div);
    }

    /**
     * Навешивает событие на проверку значения в диапазоне Min Max
     *
     * @param input
     * @private
     */
    _checkInputValues(input)
    {
        input.addEventListener('keyup', event => {
            let value = parseFloat(event.target.value),
                min = parseFloat(event.target.min),
                max = parseFloat(event.target.max);

            if(value < min)
                event.target.value = min;
            else if(value > max)
                event.target.value = max;
        });
    }

    /**
     * Удаляет форму ввода количества для конкретной коробки
     *
     * @param cargoId
     * @param container
     * @private
     */
    _removeInputFromProduct(cargoId, container)
    {
        container.querySelector(this.selector.cargoAmount + '[data-cargo-id="' + cargoId + '"]').remove();
    }

    /**
     * Удаляет одну коробку
     *
     * @param id
     */
    onRemoveCargo(id) {
        if (!confirm(lang.t('Вы действительно желаете удалить грузовое место?'))) {
            return;
        }

        let item = this.cargoListContainer.querySelector('[data-cargo-id="' + id + '"]');
        let wasActive = item.classList.contains(this.classes.cargoItemActive);
        item.remove();
        this.cargoFormContainer.querySelector('[data-cargo-id="' + id + '"]').remove();

        let lastChild = this.cargoListContainer.querySelector('li:last-child');
        if (wasActive && lastChild) {
            let id = lastChild.dataset.cargoId;
            this.onSelectCargo(id);
        }

        this.cargoProductContainer.querySelectorAll(this.selector.cargoProductItem + ' '+ this.selector.cargoAmountContainer).forEach(element => {
            this._removeInputFromProduct(id, element);
        });

        this.refreshState();
    }

    /**
     * Обновляет состояние блоков после операций
     */
    refreshState() {
        this.cargoDetail.hidden = !this.cargoFormContainer.children.length;
        this.cargoList.hidden = !this.cargoFormContainer.children.length;
        this.cargoEmpty.hidden = this.cargoFormContainer.children.length;

        let activeCargo = this.cargoListContainer.querySelector('.' + this.classes.cargoItemActive);
        let hasProduct = false;

        if (activeCargo) { //Пересчитаем максимальные остатки для текущей коробки
            let id = activeCargo.dataset.cargoId;
            this.cargoProductContainer.querySelectorAll(this.selector.cargoProductItem + ' ' + this.selector.cargoAmountContainer).forEach(element => {
                element.querySelectorAll(this.selector.cargoAmount).forEach(amount => {
                    amount.hidden = !(amount.dataset.cargoId == id);
                });
                if (this._calculateMaxAmount(id, element) > 0 ) {
                    hasProduct = true;
                }
            });
        }

        this.cargoEmptyContainer.hidden = !!hasProduct;
    }

    /**
     * Инициализирует новое окно с распределением товаров по грузоместам
     *
     * @param selector Идентификатор корневого элемента
     */
    static init(selector)
    {
        document.querySelectorAll(selector).forEach((element) => {
            if (!element.orderCargo) {
                element.orderCargo = new OrderCargo(element);
            }
        });
    }
}

$.contentReady(() => {
    OrderCargo.init('.order-cargo');
});