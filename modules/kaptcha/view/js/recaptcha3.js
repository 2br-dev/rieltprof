/**
 * Плагин, который инициализирует невидимую Google Recaptcha V3
 * К форме будет добавлен класс not-initialized, пока инициализируется reCaptcha и отправить форму невозможно
 * К форме будет добавлен класс submiting, пока будет идти запрос на получение токена и повторно отправить форму будет невозможно
 * Данные классы можно использовать для стилизации кнопки Отправки.
 * 
 * @author ReadyScript lab.
 */
(function( $ ) {
    $.fn.reCaptchaV3 = function() {
        return this.each(function() {
            var element = $(this);
            var form = element.closest('form');
            var submitButton = form.find('[type="submit"]:first');
            var canSubmit;

            var setCanSubmit = function(bool) {
                canSubmit = bool;
                submitButton.prop('disabled', !bool);
            };

            if (typeof(grecaptcha) != 'undefined') {
                if (form.length) {
                    setCanSubmit(false);

                    form.addClass('not-initialized');
                    var action = element.data('context') ? element.data('context') : 'default';
                    action = action.replace(/[^A-Za-z/_]/g, '');

                    $(form).on('submit', function (event, reCaptchaSubmit) {
                        if (!reCaptchaSubmit) {

                            event.preventDefault(); //Запрещаем отправлять форму любым естественным образом.
                            // Форму можно будет отправить только после получения токена ReCaptcha.

                            if (canSubmit) {
                                setCanSubmit(false);
                                form.addClass('submiting');
                                grecaptcha.execute(global.reCaptchaV3SiteKey, { action: action })
                                    .then(function (token) {
                                        element.val(token);
                                        form.trigger('submit', [true]); //Выполняем реальный пост
                                        form.removeClass('submiting');
                                        setCanSubmit(true);
                                    }, function (error) {
                                        console.log('ReCaptcha V3 Error' + error);
                                        form.removeClass('submiting');
                                        setCanSubmit(true);
                                    });
                            }
                        }
                    });

                    grecaptcha.ready(function () {
                        //Убираем класс с формы, когда загрузится ReCaptcha
                        form.removeClass('not-initialized');
                        setCanSubmit(true);
                    });
                }
            } else {
                alert('Не подключен Google ReCaptcha API.js скрипт');
            }

        });
    }
}( jQuery ));

