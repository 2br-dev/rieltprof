class SelectedAddressChange {
    constructor(element) {
        this.selector = {
            regionInput: '.rs-selectedAddressChange_regionInput',
            markedRegion: '.rs-selectedAddressChange_markedRegion',
            regionInputWrapper: '.rs-selectedAddressChange_regionInputWrapper',
            existAddress: '.rs-selectedAddressChange_existAddress',
            otherAddress: '.rs-selectedAddressChange_otherAddress',
            otherAddressOpenButton: '.rs-selectedAddressChange_otherAddressOpenButton',
            otherAddressCloseButton: '.rs-selectedAddressChange_otherAddressCloseButton',
            otherAddressSelectButton: '.rs-selectedAddressChange_otherAddressSelectButton',
            regionBlock: '.rs-selectedAddressChange_regionBlock',
        };
        this.class = {
            open: 'rs-open',
        };
        this.mode = {
            selectedAddress: 'selectedAddress',
            dispatchEvent: 'dispatchEvent',
        }
        this.options = {
            mode: this.mode.dispatchEvent,
            source: undefined,
            resultEventTarget: undefined,
        };

        this.owner = element;
        let $this = this;

        if (this.owner.dataset.selectedAddressChangeOptions) {
            this.options = Object.assign(this.options, JSON.parse(this.owner.dataset.selectedAddressChangeOptions));
        }

        this.owner.querySelectorAll(this.selector.markedRegion).forEach((element) => {
            element.addEventListener('click', async () => {
                let address = await this.getAddressFieldsByRegionId(element.dataset.regionId);
                if (address) {
                    this.addressSelected(address);
                }
            });
        });
        this.owner.querySelector(this.selector.otherAddressOpenButton).addEventListener('click', () => {
            this.openOtherAddressForm();
        });
        this.owner.querySelector(this.selector.otherAddressCloseButton).addEventListener('click', () => {
            this.closeOtherAddressForm();
        });
        this.owner.querySelector(this.selector.otherAddressSelectButton).addEventListener('click', () => {
            this.selectOtherAddress();
        });
        if (this.owner.querySelector('[name="country_id"]')) {
            this.owner.querySelector('[name="country_id"]').addEventListener('change', () => {
                this.loadRegionsByCountry();
            });
        }

        // todo кусочек jQuery в нативном классе
        $(this.selector.regionInput, this.owner).each(function() {
            $(this).autocomplete({
                source: $this.owner.dataset.regionAutocompleteUrl,
                appendTo: $this.owner.querySelector($this.selector.regionInputWrapper),
                minLength: 3,
                select: function( event, ui ) {
                    $this.addressSelected(ui.item.address_data);
                },
                messages: {
                    noResults: '',
                    results: function() {}
                }
            }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                let li = $('<li></li>');
                li.append('<div>' + item.address_data.city + '</div>');
                li.append('<div class="itemHint">' + item.address_data.region + '</div>');
                return li.appendTo( ul );
            };
        });
    }

    loadRegionsByCountry() {
        let data = new FormData();
        data.append('Act', 'getRegionsByParent');
        data.append('parent_id', this.owner.querySelector('[name="country_id"]').value);

        fetch(this.owner.dataset.url, {
            method: 'post',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: data,
        }).then((response) => {
            return response.json();
        }).then((response) => {
            if (response.success) {
                this.owner.querySelector(this.selector.regionBlock).innerHTML = response.regionBlock;
            }
        });
    }

    openOtherAddressForm() {
        this.owner.querySelector(this.selector.otherAddress).classList.add(this.class.open);
        this.owner.querySelector(this.selector.existAddress).classList.remove(this.class.open);
    }

    closeOtherAddressForm() {
        this.owner.querySelector(this.selector.otherAddress).classList.remove(this.class.open);
        this.owner.querySelector(this.selector.existAddress).classList.add(this.class.open);
    }

    /**
     * "Выбирает" набранный вручную адрес
     */
    selectOtherAddress() {
        let address = {
            country_id: 0,
            region_id: 0,
            city_id: 0,
            country: '',
            region: '',
            city: '',
        };
        let form = this.owner.querySelector(this.selector.otherAddress);
        form.querySelectorAll('[name]').forEach((element) => {
            address[element.name] = element.value;
        });
        this.addressSelected(address);
    }

    /**
     * Получает данные адреса на основе Id региона
     */
    async getAddressFieldsByRegionId(regionId) {
        let data = new FormData();
        data.append('Act', 'getAddressByRegion');
        data.append('region_id', regionId);

        return fetch(this.owner.dataset.url, {
            method: 'post',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: data,
        }).then((response) => {
            return response.json();
        }).then((response) => {
            if (response.success) {
                return response.address;
            }
        });
    }

    /**
     * "Выбирает" переданный адрес, выполняемые действия зависят от режима работы
     *
     * @param address - массив с данными адреса
     */
    addressSelected(address) {
        if (this.options.mode == this.mode.dispatchEvent) {
            let target = document;
            if (this.options.resultEventTarget) {
                target = this.options.resultEventTarget;
            }
            target.dispatchEvent(new CustomEvent('addressSelected', {
                detail: {
                    source: this.options.source,
                    address: address,
                }
            }));

            // todo кусочек jQuery в нативном классе
            $.rsAbstractDialogModule.close();
        }
    }

    /**
     * Устанавливает режим работы
     *
     * @param {string} mode - режим работы
     */
    setMode(mode) {
        this.options.mode = mode;
    }

    /**
     * Устанавливает элемент, в котором будет брошено событие "адрес выбран" (только для режима работы "dispatchEvent")
     *
     * @param {element} element - DOM элемент
     */
    setResultEventTarget(element) {
        this.options.resultEventTarget = element;
    }

    static init(selector)
    {
        document.querySelectorAll(selector).forEach((element) => {
            if (!element.selectedAddressChange) {
                element.selectedAddressChange = new SelectedAddressChange(element);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    SelectedAddressChange.init('.rs-selectedAddressChange');
});
SelectedAddressChange.init('.rs-selectedAddressChange');

// todo кусочек jQuery в нативном классе
$(document).on('new-content', () => {
    SelectedAddressChange.init('.rs-selectedAddressChange');
});