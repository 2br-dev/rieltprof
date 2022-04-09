/**
 * Инициализирует работу блока фильтра
 * Зависит от wnumb.js, noUISlider.js
 */
new class SideFilters extends RsJsCore.classes.component
{
    constructor(settings) {
        super();
        let defaults = {
            targetList             : '#products',     //Селектор блока, в котором отображаются товары
            context                : '.rs-filter-section', //Селектор блока фильтров
            form                   : '.rs-filters',      //Селектор формы которая будет отправляться
            submitButton           : '.rs-apply-filter', //Селектор кнопки отправки формы
            cleanFilter            : '.rs-clean-filter',  //Селектор кнопки очистки фильтра
            activeFilterClass      : 'rs-filter-active',

            //Для фильтра множественного выбора
            multiSelectActiveClass : 'rs-active',
            multiSelectRemoveProps : '.rs-clear-one-filter',     //Селектор кнопки, которая убирает все выделенные характеристики в обном блоке
            multiSelectBlock       : '.rs-type-multiselect',      //Селектор обёртки множественного фильтра
            multiSelectInsertBlock : '.rs-selected', //Селектор обёртки всех строк с выбором характеристик отмеченным
            multiSelectRowsBlock   : '.rs-unselected',         //Селектор обёртки всех строк с выбором характестик не отмеченным
            multiSelectRow         : 'li',                    //Селектор обёртки одного фильтра
            sliderInput            : '.rs-type-interval .rs-plugin-input', //Селектор элемента с данными для слайдера
            loadingClass           : 'rs-in-loading',
            disablePropertyClass   : 'rs-disabled-property',
            hiddenClass            : 'd-none'
        };

        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
    }

    /**
     * Инициализирует фильтры
     */
    initFilters() {
        //Функция возврата на предыдущую страницу по ajax через браузер
        window.addEventListener('popstate', event => this.returnPageFilterFromFilter(event));

        let cleanButton = this.context.querySelector(this.settings.cleanFilter);
        cleanButton && cleanButton.addEventListener('click', (event) => this.cleanFilters(event));

        let submitButton = this.context.querySelector(this.settings.submitButton);
        submitButton && submitButton.classList.add(this.settings.hiddenClass);

        this.form = this.context.querySelector(this.settings.form);

        this.context.querySelectorAll('input[type="text"], input[type="hidden"], select')
            .forEach((element) => {
                element.dataset.lastValue = element.value;
            });

        this.bindChanges();

        this.changeEventWithNoApply = new CustomEvent('change', { detail: {
                noApply: true
            }});

        this.checkActiveFilters();
    }

    /**
     * Меняет позиции выбранным элементам в блоках с мультивыбором
     *
     */
    changeMultiSelectCheckedRowsPosition() {
        // Если блоки есть
        this.context.querySelectorAll(this.settings.multiSelectBlock).forEach((it) => {
            let selectedList = it.querySelector(this.settings.multiSelectInsertBlock);
            if (selectedList) {
                let unselectedList = it.querySelector(this.settings.multiSelectRowsBlock);
                let haveChecked = false;
                it.querySelectorAll('input').forEach((input) => {
                    let li = input.closest(this.settings.multiSelectRow);
                    if (input.checked) {
                        haveChecked = true;
                        selectedList.append(li);
                    } else {
                        if (li.closest(this.settings.multiSelectInsertBlock)) {
                            unselectedList.prepend(li);
                        }
                    }
                });

                if (haveChecked) {
                    selectedList.classList.remove(this.settings.hiddenClass);
                } else {
                    selectedList.classList.add(this.settings.hiddenClass);
                }
            }
        });
    }

    /**
     * Подготавливает и применяет фильтры из объекта history
     *
     * @param event
     */
    returnPageFilterFromFilter(event) {
        this.cleanFilters(event, true);
        let params = history.state ? history.state : [];
        let formData = new FormData();

        params.forEach(keyval => {
            this.setFilterParam(keyval);
            formData.append(keyval[0], keyval[1]);
        });

        this.queryFilters(formData, false);
    }

    /**
     * Устанавливает в HTML форме фильтров значения из переданного объекта
     * @param keyval - объект со значниями фильтра
     */
    setFilterParam(keyval) {
        let key = keyval[0];
        let value = keyval[1];

        let filtersInputs = this.context.querySelectorAll("[name='" + key + "']");
        if (filtersInputs.length > 1) { //Если несколько объектов подходящих(checkbox)
            //То выберем нужный
            filtersInputs = filtersInputs.filter((element) => {
                return element.value == value;
            });
        }

        if (filtersInputs.length) {
            let filtersInput = filtersInputs[0];
            let tagName = filtersInput.tagName.toLowerCase();

            switch (tagName) {
                case "input":
                        switch (filtersInput.getAttribute('type').toLowerCase()) {
                            case "checkbox":    //checkbox
                                filtersInput.checked = true;
                                break;

                            default:   //Текстовое поле
                                filtersInput.value = value;
                                break;
                        }
                    break;

                default:
                    filtersInput.value = value;
                    break;
            }

            filtersInput.dispatchEvent(this.changeEventWithNoApply);
        }
    }

    /**
     * Возвращает примененные фильтры в настоящее время
     *
     * @returns {FormData}
     */
    getFiltersFormData()
    {
        let formData = new FormData(this.form);

        //Добавляем поисковую фразу
        let queryValue = this.context.dataset.queryValue;
        if (queryValue != 'undefined' && queryValue.length) {
            formData.append('query', queryValue);
        }

        let forDelete = [];
        //Удаляем не выбранные фильтры
        for(let pair of formData.entries()) {
            let key = pair[0];
            let value = pair[1];
            let field = this.context.querySelector('[name="' +  key + '"][data-start-value]');
            if (field && field.dataset.startValue == value) {
                forDelete.push(key);
            }
        }

        forDelete.forEach((key) => formData.delete(key));
        return formData;
    }

    /**
     * Применяет фильтр, согласно данным в формах
     *
     * @param event
     * @returns {boolean}
     */
    applyFilters(event) {
        if (event.detail && event.detail.noApply) return false;
        let formData = this.getFiltersFormData();

        this.queryFilters(formData);
    }

    /**
     * Устанавливает класс rs-active, если фильтр с чек-боксами активен
     */
    checkActiveFilters() {
        this.context.querySelectorAll(this.settings.multiSelectBlock).forEach((element) => {
            let isSelected = element.querySelectorAll('input[type="checkbox"]:checked').length;
            isSelected ? element.classList.add(this.settings.multiSelectActiveClass)
                : element.classList.remove(this.settings.multiSelectActiveClass);
        });

        //Сменим позиции мульти выбора у выбранных элементов
        clearTimeout(this.timer);
        this.timer = setTimeout(() => {
            this.changeMultiSelectCheckedRowsPosition();
        }, 300);
    }

    /**
     * Запрос результата применения фильтров
     *
     * @param formData - массив объектов запроса
     * @param updateHistoryState - Флаг указывающий на то нужно ли заносить в историю изменение адреса и заносить туда фильтры
     */
    queryFilters(formData, updateHistoryState = true)
    {
        this.context.classList.add(this.settings.loadingClass);
        let filters = Array.from(formData.entries());

        filters.length ? this.context.classList.add(this.settings.activeFilterClass)
            : this.context.classList.remove(this.settings.activeFilterClass);

        let searchParams = new URLSearchParams(formData);
        let url = this.form.getAttribute('action');

        this.utils.fetchJSON( url + (url.indexOf('?') === -1 ? '?' : '&') + searchParams.toString(), {
            method: 'GET'
        }).then((response) => {
            let products = document.querySelector(this.settings.targetList);
            if (products) {
                let parent = products.parentNode;
                products.insertAdjacentHTML('afterend', response.html);
                products.remove();

                var url = decodeURIComponent(response.new_url);

                // заносим ссылку в историю
                if (updateHistoryState) {
                    history.pushState(filters, null, url);
                }

                //> зависимые фильтры
                if (typeof response.filters_allowed_sorted !== "undefined") {
                    var allow_filters = Object.entries(response.filters_allowed_sorted);

                    if (allow_filters !== false) {
                        allow_filters.forEach((filter) => {
                            Object.entries(filter[1]).forEach((filter_val) => {
                                //если есть, то включим
                                let inputFilter = this.context.querySelector('input[name="pf[' + filter[0] + '][]"][value="' + filter_val[0] + '"]');
                                let inputBFilter = this.context.querySelector('input[name="bfilter[' + filter[0] + '][]"][value="' + filter_val[0] + '"]');
                                if (filter_val[1] === false) {
                                    inputBFilter && inputBFilter.parentNode.classList.add(this.settings.disablePropertyClass);
                                    inputFilter && inputFilter.parentNode.classList.add(this.settings.disablePropertyClass);
                                } else {
                                    inputBFilter &&inputBFilter.parentNode.classList.remove(this.settings.disablePropertyClass);
                                    inputFilter && inputFilter.parentNode.classList.remove(this.settings.disablePropertyClass);
                                }
                            });
                        });
                    }
                }
                //< зависимые фильтры

                parent.dispatchEvent(new CustomEvent('new-content', {bubbles: true}));
                this.context.dispatchEvent(new CustomEvent('filters.loaded', {bubbles: true}));
            } else {
                console.error(this.settings.targetList + ' selector not found');
            }
        }).finally(() => {
            this.context.classList.remove(this.settings.loadingClass);
        });

        this.checkActiveFilters();
    }

    /**
     * Снимает все выбранные характеристики в одном блоке
     */
    cleanBlockProps(event) {
        let block = event.target.closest(this.settings.multiSelectBlock);
        block && block.querySelectorAll("input[type='checkbox']").forEach((element) => {
            element.checked = false;
            element.dispatchEvent(this.changeEventWithNoApply);
        });

        this.applyFilters(event);
    };

    /**
     * Фиксирует факт изменения параметров в фильтрах и вызывает метод applyFilters
     */
    bindChanges() {
        this.form.addEventListener('submit', event => this.applyFilters(event));

        this.context.querySelectorAll('select, input[type="radio"], input[type="checkbox"], input[type="hidden"]').forEach((element) => {
            element.addEventListener('change', event => this.applyFilters(event));
        });

        this.context.querySelectorAll(this.settings.multiSelectRemoveProps).forEach((element) => {
            element.addEventListener('click', event => this.cleanBlockProps(event));
        });

        this.context.querySelectorAll('input[type="text"]').forEach((element) => {
            element.addEventListener('keyup', (event) => {
                clearTimeout(this.keyupTimer);
                if (event.keyCode === 13) {
                    return;
                }
                this.keyupTimer = setTimeout(() => {
                    this.applyFilters(event);
                }, 500);
            });
        });
    };

    /**
     * Сбрасывает фильтр
     *
     * @param event
     * @param noApply
     * @returns {boolean}
     */
    cleanFilters(event, noApply) {
        event.preventDefault();

        this.context.querySelectorAll('input[type="text"], input[type="hidden"], input[type="number"], select')
            .forEach((element) => {
                element.value = element.dataset.startValue !== '' ? element.dataset.startValue : "";
                element.dispatchEvent(this.changeEventWithNoApply);
            });

        this.context.querySelectorAll('input[type="radio"][data-start-value]').forEach((element) => {
            element.checked = true;
        });

        this.context.querySelectorAll('input[type="checkbox"]').forEach((element) => {
            element.checked = false;
            element.dispatchEvent(this.changeEventWithNoApply);
        });

        if (!noApply) this.applyFilters(event);

        return false;
    }


    /**
     * Инициализирует слайдеры у числовых полей
     */
    initSliders() {
        this.context.querySelectorAll(this.settings.sliderInput).forEach((pluginInput) => {
            let slider = JSON.parse(pluginInput.dataset.slider);
            let element = document.createElement('div');
            pluginInput.insertAdjacentElement('afterend', element);

            let context = pluginInput.closest('.rs-type-interval');
            let fromField = context.querySelector('.rs-filter-from');
            let toField = context.querySelector('.rs-filter-to');

            noUiSlider.create(element, {
                start: [fromField.value , toField.value],
                step: parseFloat(slider.step),
                connect: false,
                range: {
                    'min': slider.from,
                    'max': slider.to
                },
                format: wNumb({
                    decimals: slider.round,
                    thousand:''
                })
            });

            element.noUiSlider.on('slide', function( values, handle ) {
                fromField.value = values[0];
                toField.value = values[1];
            });

            element.noUiSlider.on('set', function( values, handle ) {
                pluginInput.dispatchEvent(new Event('change'));
            });

            let timeout;
            let onKeyPress = (event) => {
                let code = (event.keyCode || event.which);
                if((code >= 35 && code <= 40) || code == 9 || (code >= 16 && code <= 18)) {
                    return;
                }

                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    element.noUiSlider.set([parseFloat(fromField.value), parseFloat(toField.value)]);
                }, 800);
            };

            fromField.addEventListener('change', onKeyPress);
            fromField.addEventListener('keyup', onKeyPress);
            toField.addEventListener('change', onKeyPress);
            toField.addEventListener('keyup', onKeyPress);
        });
    }

    /**
     * Выполняется, когда документ загружен
     */
    onDocumentReady() {
        this.context = document.querySelector(this.settings.context);
        if (this.context) {
            this.initSliders();
            this.initFilters();
        }
    }
};