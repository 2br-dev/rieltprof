/**
 * Инициализирует работу раздела Мои карты в личном кабинете
 */
new class SavedPaymentMethods extends RsJsCore.classes.component {

    constructor(settings)
    {
        super();
        let defaults = {
            makeDefault: '.rs-payment-method-makedefault',
            payment: '.rs-payment',
            paymentTitle: '.rs-payment-method-title',
            paymentMethods: '.rs-payment-methods',
            paymentMethod: '.rs-payment-method',
            paymentMethodDelete: '.rs-payment-method-delete',
        };
        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
    }

    initMakeDefaultCardButton() {
        document.querySelectorAll(this.settings.makeDefault).forEach((element) => {
            element.addEventListener('click', (event) => {
                let saved_method = event.target.closest(this.settings.paymentMethod);
                let url = document.querySelector(this.settings.paymentMethods).dataset.makeDefaultUrl;
                let data = new FormData();
                data.append('saved_method', saved_method.dataset.id);

                this.utils.fetchJSON(url, {
                    method: 'post',
                    body: data
                }).then((response) => {
                    if (response.success) {
                        location.reload();
                    }
                });
            });
        });
    }

    initDeleteCardButton() {
        document.querySelectorAll(this.settings.paymentMethodDelete).forEach((element) => {
            element.addEventListener('click', (event) => {
                let saved_method = event.target.closest(this.settings.paymentMethod);
                let saved_method_title = saved_method.querySelector(this.settings.paymentTitle).textContent;
                if (confirm(lang.t('Вы действительно хотите отвязать способ оплаты "%0"', [saved_method_title]))) {

                    let url = document.querySelector(this.settings.paymentMethods).dataset.deleteUrl;
                    let data = new FormData();
                    data.append('saved_method', saved_method.dataset.id);
                    data.append('payment', saved_method.closest(this.settings.payment).dataset.id);

                    this.utils.fetchJSON(url, {
                        method: 'post',
                        body: data
                    }).then((response) => {
                        if (response.success) {
                            location.reload();
                        }
                    });
                }
            });
        });

    }

    onDocumentReady() {
        this.initMakeDefaultCardButton();
        this.initDeleteCardButton();
    }
};