class RsBarcodeScanner {
    constructor() {
        this.const = {
            keydownMaxInterval: 20,
            eventNameBarcodeScanned: 'barcode-scanned',
            keyCode: {
                enter: 13,
                shift: 16,
            },
            barcodeType: {
                oneDimensional: 'oneDimensional',
                twoDimensional: 'twoDimensional',
            },
        };

        this.buffer = '';
        this.lastTimeStamp = null;

        document.addEventListener('keydown', (event) => {
            this.keydown(event);
        });
    }

    /**
     * Обработчик нажатия клавиши
     *
     * @param event
     */
    keydown(event) {
        if (event.isTrusted && event.keyCode !== this.const.keyCode.shift) {
            if (event.keyCode === this.const.keyCode.enter) {
                this.getFormElement().dispatchEvent(this.getCodeScanEvent());

                this.buffer = '';
                this.lastTimeStamp = null;
            } else {
                let key = this.getKeyFromKeydownEvent(event);

                this.buffer = (event.timeStamp - this.lastTimeStamp < this.const.keydownMaxInterval) ? this.buffer + key : key;
                this.lastTimeStamp = event.timeStamp;
            }
        }
    }

    /**
     * Возвращает объект события сканирования штрихкода на основе текущего буфера
     *
     * @returns {Event}
     */
    getCodeScanEvent() {
        let event = new Event(this.const.eventNameBarcodeScanned, {
            bubbles: true,
            cancelable: true,
        });

        event.detail = {
            codeType: (this.buffer.length > 14) ? this.const.barcodeType.twoDimensional : this.const.barcodeType.oneDimensional,
            code: this.buffer,
        };

        return event;
    }

    /**
     * Возвращает элемент формы у котого будет брошено событие сканирования штрихкода
     *
     * @returns {Element}
     */
    getFormElement() {
        let formElement;
        document.querySelectorAll('.ui-dialog').forEach((element) => {
            if (element.style.display !== 'none') {
                formElement = element.querySelector('form');
            }
        });
        if (!formElement) {
            let mainForm = document.querySelector('#content .crud-form');
            formElement = (mainForm) ? mainForm : document;
        }

        return formElement;
    }

    /**
     * Корректирует введённый символ
     *
     * @param event - событие нажатия клавиши
     * @returns {string}
     */
    getKeyFromKeydownEvent(event) {
        let key;
        if (event.code.search('^Key') !== -1) {
            key = String.fromCharCode(event.keyCode);
            if (!event.shiftKey) {
                key = key.toLowerCase();
            }
        } else if (event.code.search('^Slash') !== -1) {
            key = (event.shiftKey) ? '?' : '/';
        } else {
            key = event.key;
        }

        return key;
    }

    static init() {
        if (!window.rsBarcodeScanner) {
            window.rsBarcodeScanner = new RsBarcodeScanner();
        }
    }
}

RsBarcodeScanner.init();