class FieldPvz {
    constructor(element) {
        this.selector = {
            target: '.rs-field-pvz-select',
            input: '.rs-field-pvz-input',
            label: '.rs-field-pvz-label',
        };
        this.class = {
        };
        this.options = {
            targetSelector: '.rs-field-pvz-select',
            inputSelector: '.rs-field-pvz-input',
            labelSelector: '.rs-field-pvz-label',
            deliveryId: 0,
            cityIdSelector: '',
        };

        this.owner = element;

        if (this.owner.dataset.fieldPvzOptions) {
            this.options = Object.assign(this.options, JSON.parse(this.owner.dataset.fieldPvzOptions));
        }

        this.owner.querySelector(this.options.targetSelector).addEventListener('click', (event) => {
            let url = new URL(this.owner.dataset.pvzSelectUrl);
            let city_element = document.querySelector(this.options.cityIdSelector);
            if (city_element) {
                let cityId;
                if (city_element.dataset.cityId) {
                    cityId = city_element.dataset.cityId;
                } else {
                    cityId = city_element.value;
                }
                url.searchParams.append('city_id', cityId);
                url.searchParams.append('delivery', this.options.deliveryId);

                if ($.openDialog) {
                    $.openDialog({
                        url: url.toString(),
                        dialogOptions: {
                            title: lang.t('Выберите ПВЗ'),
                        },
                        callback: (params) => {
                            let selectPvzElement = document.querySelector('.rs-selectPvz');
                            if (selectPvzElement) {
                                let waiting = setInterval(() => {
                                    if (selectPvzElement.selectPvz) {
                                        clearInterval(waiting);
                                        selectPvzElement.selectPvz.setDispatchEventTarget(this.owner);
                                    }
                                }, 10);
                            }
                        },
                    });
                } else if ($.rs) {
                    $.rs.openDialog({
                        url: url.toString(),
                        dialogOptions: {
                            width: window.innerWidth,
                            height: window.innerHeight,
                            title: lang.t('Выберите ПВЗ'),
                        },
                        afterOpen: (params) => {
                            let selectPvzElement = document.querySelector('.rs-selectPvz');
                            if (selectPvzElement) {
                                let waiting = setInterval(() => {
                                    if (selectPvzElement.selectPvz) {
                                        clearInterval(waiting);
                                        selectPvzElement.selectPvz.setAdminMode(true);
                                        selectPvzElement.selectPvz.setDispatchEventTarget(this.owner);
                                    }
                                }, 10);
                            }
                        },
                    });
                }
            } else {
                $.messenger('show', {
                    'text': lang.t('Не указан город доставки'),
                    'theme': 'error'
                });
            }
        });

        this.owner.addEventListener('pvzSelected', (event) => {
            let pvzInput = this.owner.querySelector(this.options.inputSelector);
            let label = this.owner.querySelector(this.options.labelSelector);

            if (pvzInput.tagName == 'SELECT') {
                pvzInput.value = pvzInput.querySelector('[data-pvz-code="'+event.detail.pvz.code+'"]').value;
            } else {
                pvzInput.value = JSON.stringify(event.detail.pvz);
            }

            if (label) {
                label.textContent = event.detail.pvz.address;
            }
        });
    }

    static init(selector)
    {
        document.querySelectorAll(selector).forEach((element) => {
            if (!element.fieldPvz) {
                element.fieldPvz = new FieldPvz(element);
            }
        });
    }
}

let selector = '.rs-field-pvz';

document.addEventListener('DOMContentLoaded', () => {
    FieldPvz.init(selector);
});
FieldPvz.init(selector);

// todo кусочек jQuery в нативном классе
if ($.contentReady) {
    $.contentReady(() => {
        FieldPvz.init(selector);
    });
} else {
    $(document).on('new-content', () => {
        FieldPvz.init(selector);
    });
}
