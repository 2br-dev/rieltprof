/**
 * Инициализирует работу страницы с отображение статуса после оплаты
 */
new class SuccessReceipt extends RsJsCore.classes.component {

    constructor(settings)
    {
        super();
        let defaults = {
            waitReceiptSuccessImg : '.rs-waitReceiptSuccessImg', //Селектор картинки с успешным сообщением
            waitReceiptLoading : '.rs-waitReceiptLoading', //Селектор картинки загрузки
            waitReceiptStatus : '.rs-waitReceiptStatus', //Селектор текста статуса ожидания
            hiddenClass: 'd-none', //Класс скрытого элемента
            timeout: 2000,
            statusParams: '#rs-status-params'
        };
        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
    }

    init() {
        this.paramsElement = document.querySelector(this.settings.statusParams);
        if (this.paramsElement) {
            this.checkTransactionUrl = this.paramsElement.dataset.urlCheckTransaction;
            this.checkReceiptUrl = this.paramsElement.dataset.urlCheckReceipt;
        } else if (global.receipt_check_url) {
            this.checkReceiptUrl = global.receipt_check_url;
        }

        this.initCheckReceiptTimeout();
        this.initCheckTransactionTimeout();
    }

    /**
     * Запускает отсчет до следующей проверки статуса транакции
     */
    initCheckTransactionTimeout() {
        this.timeoutTransaction = setTimeout(() => {
            this.requestStatusTransaction();
        }, this.settings.timeout);
    }

    /**
     * Выполняет запрос на проверку статуса транзакции
     */
    requestStatusTransaction() {
        if (this.checkTransactionUrl) {
            this.utils.fetchJSON(this.checkTransactionUrl)
                .then(response => {
                    clearTimeout(this.timeoutTransaction);
                    if (response.success) { //Если статус успешно получен
                        if (response.status == 'success' || response.status == 'fail' || response.status == 'hold') {
                            this.paramsElement.classList.remove('new', 'fail', 'hold', 'success');
                            this.paramsElement.classList.add(response.status);
                        } else {
                            this.initCheckTransactionTimeout();
                        }
                    } else {
                        this.initCheckTransactionTimeout();
                    }
                });
        }
    }

    /**
     * Запускает отсчет до следующей проверки статуса
     */
    initCheckReceiptTimeout() {
        this.timeout = setTimeout(() => {
            this.requestStatusReceipt();
        }, this.settings.timeout);
    }

    /**
     * Запрашивает статус оплаты
     */
    requestStatusReceipt() {
        if (this.checkReceiptUrl) {
            this.utils.fetchJSON(this.checkReceiptUrl)
                .then(response => {
                    clearTimeout(this.timeout);

                    if (response.success) { //Если статус успешно получен

                        let successImg = document.querySelector(this.settings.waitReceiptSuccessImg);
                        let loadingImg = document.querySelector(this.settings.waitReceiptLoading);
                        let receiptStatus = document.querySelector(this.settings.waitReceiptStatus);

                        successImg.classList.remove(this.settings.hiddenClass);
                        loadingImg.classList.add(this.settings.hiddenClass);

                        if (response.error) {  //Если произошла ошибка
                            receiptStatus.classList.add('error');
                            receiptStatus.innerHTML = " " + response.error;
                        } else {
                            receiptStatus.remove();
                        }
                    } else {
                        this.initCheckReceiptTimeout();
                    }
                });
        }
    }

    onDocumentReady() {
        this.init();
    }
};