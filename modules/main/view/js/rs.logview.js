class LogView {
    constructor(element) {
        this.selector = {
            clearLog: '.rs-log-view-clear-log',
            datetime: '.rs-log-view-datetime',
            inputDate: '.rs-log-view-input-date',
            inputTime: '.rs-log-view-input-time',
        };
        this.class = {
        };
        this.options = {
        };

        this.owner = element;

        if (this.owner.dataset.LogViewOptions) {
            this.options = Object.assign(this.options, JSON.parse(this.owner.dataset.LogViewOptions));
        }

        this.owner.querySelector(this.selector.clearLog).addEventListener('click', () => {
            if (confirm(lang.t('Вы действительно хотите очистить лог-файл?'))) {
                this.clearLog();
            }
        });
        this.owner.querySelector(this.selector.inputDate).addEventListener('change', (event) => {
            let inputTime = event.target.closest(this.selector.datetime).querySelector(this.selector.inputTime);
            inputTime.disabled = !event.target.value;
        });

        this.owner.querySelectorAll(this.selector.datetime).forEach((element) => {
            element.querySelector(this.selector.inputTime).disabled = !element.querySelector(this.selector.inputDate).value;
        });
    }

    /**
     * Отправляет запрос на изменение заказа и обновляет блоки
     */
    clearLog() {
        let url = this.owner.dataset.urlClearLog;

        fetch(url, {
            method: 'post',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
        }).then((response) => {
            return response.json();
        }).then((response) => {
            if (response.success) {
                location.reload();
            }
        });
    }

    static init(selector)
    {
        document.querySelectorAll(selector).forEach((element) => {
            if (!element.logView) {
                element.logView = new LogView(element);
            }
        });
    }
}

let selector = '.rs-log-view';

document.addEventListener('DOMContentLoaded', () => {
    LogView.init(selector);
});
