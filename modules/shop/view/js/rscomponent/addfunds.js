/**
 * Активирует подсказку суммы пополнения баланса в другой валюте.
 * Актуально, когда текущая валюта на сайте не равна базовой валюте
 */
new class AddFunds extends RsJsCore.classes.component {

    initCurrencyConversation() {
        let input = document.querySelector('.rs-cost-field');
        if (input) {
            let context = input.closest('form');
            input.addEventListener('keyup', (event) => {
                let convertedCost = context.querySelector('.rs-converted-cost');
                if (convertedCost) {
                    let base_cost = parseFloat(event.target.value);
                    if (!base_cost) {
                        base_cost = 0;
                    }
                    let ratio = convertedCost.dataset.ratio;
                    let liter = convertedCost.dataset.liter;

                    let newCost = (base_cost / ratio).toFixed(2);
                    convertedCost.value = newCost.toString() + " " + liter;
                }
            });
        }
    }

    onDocumentReady() {
        this.initCurrencyConversation();
    }
};