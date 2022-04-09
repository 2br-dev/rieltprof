/**
 * Класс для работы с окном смены города.
 * Зависит от autoComplete.js
 */
class SelectedAddressChange {

    constructor(element, eventTarget) {
        this.selector = {
            ownerSelector: '.rs-region-change',
            regionInput: '.rs-region-input',
            markedRegion: '.rs-region-marked',
            regionBlock: '.rs-region-block',
            otherRegionForm: 'form'
        };
        this.class = {
            open: 'rs-open',
        };
        this.mode = {
            selectedAddress: 'selectedAddress',
            dispatchEvent: 'dispatchEvent',
        };
        this.options = {
            resultEventTarget: eventTarget,
        };

        this.owner = element.querySelector(this.selector.ownerSelector);

        this.owner.querySelectorAll(this.selector.markedRegion).forEach((element) => {
            element.addEventListener('click', async () => {
                let address = await this.getAddressFieldsByRegionId(element.dataset.regionId);
                if (address) {
                    this.addressSelected(address);
                }
            });
        });

        this.owner.querySelector(this.selector.otherRegionForm).addEventListener('submit', (event) => {
            event.preventDefault();
            event.stopPropagation();
            this.selectOtherAddress(event.target);
        });

        if (this.owner.querySelector('[name="country_id"]')) {
            this.owner.querySelector('[name="country_id"]').addEventListener('change', (event) => {
                this.loadRegionsByCountry(event.target.value);
            });
        }

        this.initSearchAutocomplete();
    }

    /**
     * Инициализирует autocomplete поиска региона по названию
     */
    initSearchAutocomplete()
    {
        let input = this.owner.querySelector(this.selector.regionInput);
        let resultWrapper = input && input.parentNode.querySelector('.rs-autocomplete-result');
        let cancelController;

        let autoCompleteInstance = new autoComplete({
            selector: () => input,
            searchEngine: () => true,
            wrapper: false,
            data: {
                src: async () => {
                    if (cancelController) cancelController.abort();

                    let data;
                    cancelController = new AbortController();
                    data = await RsJsCore.utils.fetchJSON(input.dataset.regionAutocompleteUrl + '&' + new URLSearchParams({
                        term: autoCompleteInstance.input.value
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
                        this.addressSelected(event.detail.selection.value.address_data);
                    }
                }
            }
        });
    }

    /**
     * Загружает регионы, исходя из выбранной страны
     */
    loadRegionsByCountry(countryId) {
        let data = new FormData();
        data.append('Act', 'getRegionsByParent');
        data.append('parent_id', countryId);

        RsJsCore.utils.fetchJSON(this.owner.dataset.url, {
            method: 'post',
            body: data
        }).then((response) => {
            if (response.success) {
                this.owner.querySelector(this.selector.regionBlock).innerHTML = response.regionBlock;
            }
        });
    }

    /**
     * "Выбирает" набранный вручную адрес
     */
    selectOtherAddress(form) {
        let address = {
            country_id: 0,
            region_id: 0,
            city_id: 0,
            country: '',
            region: '',
            city: '',
        };
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

        return RsJsCore.utils.fetchJSON(this.owner.dataset.url, {
            method: 'post',
            body: data,
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
        let target = this.options.resultEventTarget;
        if (typeof(target) == 'function') {
            target(address);
        } else {
            target.dispatchEvent(new CustomEvent('addressSelected', {
                detail: {
                    address: address
                }
            }));
        }

        //Закрываем окно
        RsJsCore.plugins.modal.close();
    }
};