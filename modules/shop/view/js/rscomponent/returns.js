/**
 * Инициализирует выбор товаров на возврат
 * Зависит от wNumb
 */
new class Returns extends RsJsCore.classes.component
{
    /**
     * Рассчитывает общую стоимость возврата, в зависимости от выбранных товаров
     */
    calculateReturnTotal() {
        let total = 0;
        document.querySelectorAll('.rs-return-checkbox:checked').forEach((element) => {
            let context = element.closest('[data-uniq]');
            let price  = context.dataset.price;
            let amount = context.querySelector('.rs-return-amount').value;
            total += (price * amount);
        });

        let returnTotal = document.querySelector(".rs-return-total");
        if (returnTotal) {
            let Format = wNumb({
                thousand: ' '
            });
            returnTotal.innerHTML = Format.to(total);
        }
    }

    /**
     * Увеличивает количество товара
     * @param event
     */
    stepUp(event) {
        let input = event.target.closest('.rs-cart-amount').querySelector('.rs-return-amount');
        input && input.stepUp();
    }

    /**
     * Уменьшает количество товара
     * @param event
     */
    stepDown(event) {
        let input = event.target.closest('.rs-cart-amount').querySelector('.rs-return-amount');
        input && input.stepDown();
    }

    /**
     * Инициализирует подписку на события
     */
    initChangeEvents() {
        document.querySelectorAll('.rs-return-amount').forEach(element => {
            element.addEventListener('change', event => {
                if (parseFloat(event.target.value) > parseFloat(event.target.max)) {
                    event.target.value = event.target.max;
                }

                if (parseFloat(event.target.value) < parseFloat(event.target.min)) {
                    event.target.value = event.target.min;
                }

                this.calculateReturnTotal(event);
            });
        });

        document.querySelectorAll('.rs-return-checkbox').forEach((element) => {
            element.addEventListener('change', (event) => {
                let context = event.target.closest('[data-uniq]');
                context.querySelectorAll('input[type="hidden"], input[type="text"], input[type="number"], select, button')
                    .forEach((element) => {
                        element.disabled = !event.target.checked;
                    });

                this.calculateReturnTotal(event);
            });
        });

        document.querySelectorAll('.rs-step-up').forEach(element => {
            element.addEventListener('click', event => this.stepUp(event));
        });

        document.querySelectorAll('.rs-step-down').forEach(element => {
            element.addEventListener('click', event => this.stepDown(event));
        });
    }

    onDocumentReady() {
        this.initChangeEvents();
    }
};