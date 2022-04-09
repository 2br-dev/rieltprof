/**
 * Плагин, инициализирующий работу двухфакторной авторизации, регистрации
 */
new class Verification extends RsJsCore.classes.component
{
    constructor()
    {
        super();
        this.settings = {
            verifyBlock:           '.rs-verify-code-block',
            refreshButtonSelector: '.rs-verify-refresh-code',
            resetButtonSelector:   '.rs-verify-reset',

            verifyTimerLine:       '.rs-verify-timer-line',
            verifyTimer:           '.rs-verify-timer .rs-time',
            verifyPhoneInput:      'input[data-phone]',
            verifyTokenInput:      'input[data-token]',
            autoSubmitInput:       'input[data-auto-submit-length]',
            errorSelector:         '.rs-verify-error',
            errorFieldSelector:    '.invalid-feedback',
            waitClass:             'rs-wait'
        };
    }

    onDocumentReady() {
        this.utils.on('click', this.settings.refreshButtonSelector, (event) => this.refreshCode(event));
        this.utils.on('click', this.settings.resetButtonSelector, (event) => this.resetCode(event));
        this.utils.on('keyup', this.settings.autoSubmitInput, (event) => this.onKeyPressAutoSubmit(event));
    }

    onContentReady() {
        document.querySelectorAll(this.settings.verifyBlock).forEach((element) => {
            let timer = element.querySelector('[data-delay-refresh-code-sec]');
            if (timer) {
                this.runTimer(timer);
            }
        });
    }

    /**
     *  Выполняет запрос к серверу, для обновления блока верификации
     *
     * @param url
     * @param data
     * @param context
     */
    refresh(url, data, context) {

        let showError = (text) => {
            let errorElement = context.querySelector(this.settings.errorSelector)
            if (errorElement) {
                errorElement.innerText = text;
                errorElement.classList.remove('hidden');
                errorElement.classList.add('invalid-feedback');
            }
        };

        fetch(url, {
            credentials:'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            method: 'POST',
            body: new URLSearchParams(data)
        }).then(async (data) => {
            if (data.ok) {
                let response = await data.json();

                if (response.success) {
                    //Сбрасываем отображаемую ошибку
                    let input = context.querySelector(this.settings.verifyPhoneInput);
                    if (input) {
                        input.classList.remove('is-invalid');
                        let name = input.getAttribute('name');

                        let error = context.parentNode.querySelector(this.settings.errorFieldSelector + '[data-field="' + name + '"]');
                        error && error.remove();
                    }
                }

                if (response.html) {
                    let parent = context.parentNode;
                    context.insertAdjacentHTML("afterend", response.html);
                    context.remove();
                    parent.dispatchEvent(new CustomEvent('new-content', {bubbles: true}));
                }
            } else {
                if (data.status === 404) {
                    showError(lang.t('Сессия истекла. Обновите страницу'));
                }
            }
        }).catch((response) => {
            showError(lang.t('Произошла ошибка. Попробуйте позже'));
        });
    }

    /**
     * Обработчик ввода клавиш в online-поле с кодом верификации
     */
    onKeyPressAutoSubmit(event) {
        let target = event.rsTarget;
        let context = target.closest(this.settings.verifyBlock);

        if (event.keyCode === 13) {
            event.preventDefault();
        }

        setTimeout(() => {
            let value = target.value;
            let url = context.dataset.checkCodeUrl;

            let data = {
                phone: context.querySelector(this.settings.verifyPhoneInput).value,
                token: context.querySelector(this.settings.verifyTokenInput).value,
                code: value,
            };

            if ( value.length == target.dataset.autoSubmitLength || event.keyCode === 13) {
                this.refresh(url, data, context);
            }
        }, 0);
    }

    /**
     * Обработчик кнопки "Отправить код"
     */
    refreshCode(event) {
        let target = event.rsTarget;
        let context = target.closest(this.settings.verifyBlock);
        let phone = context.querySelector(this.settings.verifyPhoneInput)
        let url = target.dataset.url;
        let data = {
            token: context.dataset.token,
            phone: phone && phone.value
        };

        this.refresh(url, data, context);
    }

    /**
     * Обработчик кнопки "Изменить номер"
     */
    resetCode(event) {
        let target = event.rsTarget;
        let context = target.closest(this.settings.verifyBlock);
        let url = target.dataset.url;
        let data = {
            token: context.dataset.token
        };

        this.refresh(url, data, context);
    }

    /**
     * Возвращает отформатированное количество времени
     *
     * @param second
     * @returns {string}
     */
    formatTime(second)  {
        let hours = Math.floor(second/3600);
        let minutes = Math.floor((second - hours*3600)/60);
        let seconds = Math.floor(second - (minutes * 60 + hours * 3600));

        return (hours > 0 ? ("0" + hours).substr(-2,2) + ':' : '') +
            ("0" + minutes).substr(-2,2) + ':' +
            ("0" + seconds).substr(-2,2);
    }

    /**
     * Запускает обратный отсчет до повтора отправки кода
     */
    runTimer(element) {
        let interval = setInterval(() => {
            let delay = element.dataset.delayRefreshCodeSec ? element.dataset.delayRefreshCodeSec : 0 ;
            if (delay > 0) {
                delay--;

                let timer = element.querySelector(this.settings.verifyTimer);
                if (timer) {
                    timer.innerText = this.formatTime(delay);
                }
                element.dataset.delayRefreshCodeSec = delay;
            }

            if (delay <= 0) {
                clearInterval(interval);
                element.classList.remove(this.settings.waitClass);
            }
        }, 1000);

    }
};