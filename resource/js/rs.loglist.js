class LogSettings {
    constructor(element) {
        this.selector = {
            logItem: '.rs-log-item',
            logItemToggle: '.rs-log-item-toggle',
        };
        this.class = {
            open: 'rs-open',
        };
        this.options = {
        };

        this.owner = element;

        if (this.owner.dataset.ClassNameOptions) {
            this.options = Object.assign(this.options, JSON.parse(this.owner.dataset.ClassNameOptions));
        }

        this.owner.querySelectorAll(this.selector.logItemToggle).forEach((element) => {
            element.addEventListener('click', (event) => {
                element.closest(this.selector.logItem).classList.toggle(this.class.open);
            });
        });

    }

    /**
     * Отправляет запрос на изменение заказа и обновляет блоки
     */
    method() {
        let data = new FormData();
        data.append('key', 'value');

        fetch(url, {
            method: 'post',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: data,
        }).then((response) => {
            return response.json();
        }).then((response) => {
            if (response.success) {
                console.log(response);
            }
        });
    }

    static init(selector)
    {
        document.querySelectorAll(selector).forEach((element) => {
            if (!element.logSettings) {
                element.logSettings = new LogSettings(element);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    LogSettings.init('.rs-log-list');
});
//LogSettings.init('.rs-log-settings');

// todo кусочек jQuery в нативном классе
/*
$(document).on('new-content', () => {
    LogSettings.init('.rs-');
});*/

