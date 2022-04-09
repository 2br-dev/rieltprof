/**
 * Плагин, который инициализирует невидимую Google Recaptcha V3
 * К форме будет добавлен класс not-initialized, пока инициализируется reCaptcha и отправить форму невозможно
 * К форме будет добавлен класс submiting, пока будет идти запрос на получение токена и повторно отправить форму будет невозможно
 * Данные классы можно использовать для стилизации кнопки Отправки.
 * 
 * @author ReadyScript lab.
 */

(function() {
    /**
     * Класс обеспечивает работу одной формы с ReCaptchaV3
     */
    class reCaptchaV3 {

        constructor(form) {
            this.form = form;
            this.submitButton = this.form.querySelector('[type="submit"]');
            this.canSubmit = false;

            this.bindForm();
        }

        setCanSubmit(bool) {
            this.canSubmit = bool;
            this.submitButton.disabled = !bool;
        }

        bindForm() {
            if (typeof(grecaptcha) != 'undefined') {
                if (this.form) {
                    this.setCanSubmit(false);

                    this.form.classList.add('not-initialized');

                    //Обрабатываем событие отправки формы. Этот обработчик будет всегда первый
                    this.form.addEventListener('submit', (event) => {

                        if (!event.detail || !event.detail.reCaptchaSubmit) {
                            let captchaInput = this.form.querySelector('.recaptcha-v3');
                            if (captchaInput) {
                                let action = captchaInput.dataset.context ? captchaInput.dataset.context : 'default';
                                action = action.replace(/[^A-Za-z/_]/g, '');

                                event.preventDefault(); //Запрещаем отправлять форму любым естественным образом.
                                event.stopPropagation();
                                // Форму можно будет отправить только после получения токена ReCaptcha.

                                if (this.canSubmit) {
                                    this.setCanSubmit(false);
                                    this.form.classList.add('submiting');

                                    //Добавляем токен reCaptcha, а затем повторяем попытку отправки формы
                                    grecaptcha.execute(global.reCaptchaV3SiteKey, {action: action})
                                        .then((token) => {
                                            captchaInput.value = token;
                                            console.log('call');
                                            this.form.dispatchEvent(new CustomEvent('submit', {
                                                cancelable: true,
                                                bubbles: true,
                                                detail: {
                                                    reCaptchaSubmit: true
                                                }
                                            }));
                                            this.form.classList.remove('submiting');
                                            this.setCanSubmit(true);
                                        }, (error) => {
                                            console.error('ReCaptcha V3 Error' + error);
                                            this.form.classList.remove('submiting');
                                            this.setCanSubmit(true);
                                        });
                                }
                            }
                        }
                    }, true); //Ловим событие в режиме capture

                    grecaptcha.ready(() => {
                        //Убираем класс с формы, когда загрузится ReCaptcha
                        this.form.classList.remove('not-initialized');
                        this.setCanSubmit(true);
                    });
                }
            } else {
                alert('Не подключен Google ReCaptcha API.js скрипт');
            }
        }

        static init(context) {
            context.querySelectorAll('form .recaptcha-v3').forEach(it => {
                let form = it.closest('form');
                if (form) {
                    if (!form.reCaptchaV3) {
                        form.reCaptchaV3 = new reCaptchaV3(form);
                    }
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', (event) => {
        reCaptchaV3.init(event.target);
    });

    document.addEventListener('new-content', (event) => {
        reCaptchaV3.init(event.target);
    });
})();
