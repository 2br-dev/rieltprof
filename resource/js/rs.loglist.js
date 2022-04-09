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