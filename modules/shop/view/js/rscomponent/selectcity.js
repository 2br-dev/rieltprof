/**
 * Компонент - выбор города.
 * Может использоваться в сторонних блоках, например в блоке стоимость доставки в карточке товара
 */
new class SelectCity extends RsJsCore.classes.component
{
    constructor()
    {
        super();
        let defaults = {
            changeCity: '.rs-change-city' //Элемент, клик на который будет открывать окно выбора города
        };

        this.settings = {...defaults, ...this.getExtendsSettings()};
    }

    /**
     * Инициализирует компонент
     */
    init() {
        this.utils.on('click', this.settings.changeCity, event => this.changeCityDialog(event));
    }

    /**
     *
     * @param event
     */
    changeCityDialog(event) {
        let setAddressUrl = event.rsTarget.dataset.setAddressUrl;
        let dialogUrl = event.rsTarget.dataset.selectAddressUrl;

        this.plugins.openDialog.show({
            url: dialogUrl,
            bindSubmit:false,
            callback: (response, element) => {
                //Инициализируем диалог выбора города
                new SelectedAddressChange(element, (address) => {
                    let formData = new FormData();
                    for (let key in address) {
                        formData.append(key, address[key]);
                    }

                    this.utils.fetchJSON(setAddressUrl, {
                        method:'POST',
                        body: formData
                    }).then(response => {
                        if (response.success) {
                            location.replace(location.href.replace(location.hash,""));
                        }
                    });
                });
            }
        });
    }

    onDocumentReady() {
        this.init();
    }
};