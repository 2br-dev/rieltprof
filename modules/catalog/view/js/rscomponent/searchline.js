/**
 * Компонент обеспечивает работу "живого поиска" на странице
 * Зависит от autoComplete.js
 */
new class SearchLine extends RsJsCore.classes.component
{
    onDocumentReady()
    {
        let searchLine = document.querySelector('.rs-search-line');

        let input = searchLine && searchLine.querySelector('.rs-autocomplete');
        let resultWrapper = searchLine && searchLine.querySelector('.rs-autocomplete-result');
        let clearButton = searchLine && searchLine.querySelector('.rs-autocomplete-clear');
        let cancelController;

        if (input && input.dataset.sourceUrl) {
            if (clearButton) {
                input.addEventListener('keyup', (event) => {
                    clearButton.classList.toggle('d-none', event.target.value === '');
                });

                clearButton.addEventListener('click', (event) => {
                    input.value = '';
                    input.dispatchEvent(new Event('keyup'));
                    input.dispatchEvent(new Event('keydown'));
                });
            }

            let onEnter = (event) => {
                if (event.key === 'Enter') {
                    event.target.closest('form').submit();
                }
            };

            this.autoComplete = new autoComplete({
                selector: () => input,
                searchEngine: () => true,
                wrapper:false,
                data: {
                    src: async () => {
                        if (cancelController) cancelController.abort();
                        cancelController = new AbortController();

                        let data = await this.utils.fetchJSON(input.dataset.sourceUrl + '&' + new URLSearchParams({
                            term:this.autoComplete.input.value
                        }), {
                            signal: cancelController.signal
                        });

                        return data ? data : [];
                    },
                    keys:['value']
                },
                resultsList: {
                    class: '',
                    maxResults:20,
                    destination:() => resultWrapper,
                    position:'beforeend',
                    noResults: true,
                    element: (list, data) => {
                        if (!data.results.length) {
                            const message = document.createElement("li");
                            message.setAttribute("class", "no_result");
                            message.innerHTML = lang.t('Ничего не найдено по вашему запросу');
                            list.appendChild(message);
                        }
                    },
                },
                resultItem: {
                    element: (element, data) => {
                        let tpl;
                        if (data.value.type === 'product') {
                            tpl = `<a class="dropdown-item" href="${data.value.url}">
                                        <div class="col">${data.value.label}</div>
                                        <div class="ms-4 text-nowrap">${data.value.price}</div>
                                    </a>`;
                        }
                        else {
                            let types = {
                                'category': lang.t('Категория: '),
                                'brand': lang.t('Бренд: ')
                            };
                            let typeAsString = types[data.value.type] ? types[data.value.type] : '';
                            tpl = `<a class="dropdown-item" href="${data.value.url}">
                                        <div class="col">${typeAsString}${data.value.label}</div>
                                    </a>`;
                        }

                        element.innerHTML = tpl;
                    },
                    selected: 'selected'
                },
                events: {
                    input: {
                        selection: (event) => {
                            if (event.detail.selection.value) {
                                input.removeEventListener('keyup', onEnter);
                                location.href = event.detail.selection.value.url;
                            }
                        }
                    }
                }
            });

            input.addEventListener('keyup', onEnter);

        }
    }
};