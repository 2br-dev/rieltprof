/**
 * Плагин, инициализирующий работу двухфакторной авторизации, регистрации
 */
(function($) {
    $.verification = function(method) {
        var defaults = {
            verifyBlock:           '.rs-verify-code-block',
            refreshButtonSelector: '.rs-verify-refresh-code',
            resetButtonSelector:   '.rs-verify-reset',

            verifyTimerLine:       '.rs-verify-timer-line',
            verifyTimer:           '.rs-verify-timer .rs-time',
            verifyPhoneInput:      'input[data-phone]',
            verifyTokenInput:      'input[data-token]',
            autoSubmitInput:       'input[data-auto-submit-length]',
            errorSelector:         '.rs-verify-error',
            waitClass:             'rs-wait'
        };

        //public
        var methods = {
            init: function() {
                $('body')
                    .on('click', defaults.refreshButtonSelector, refreshCode)
                    .on('click', defaults.resetButtonSelector, resetCode)
                    .on('keyup', defaults.autoSubmitInput, onKeyPressAutoSubmit);

                $('body').on('new-content', function() {
                    $(defaults.verifyBlock).each(init);
                });
                $(defaults.verifyBlock).each(init);
            }
        };

        //private
        /**
         *  Выполняет запрос к серверу, для обновления блока верификации
         *
         * @param url
         * @param data
         * @param context
         */
        var refresh = function(url, data, context) {
                $.ajax({
                    method:'POST',
                    dataType:'json',
                    url: url,
                    data: data,
                    success: function(response) {
                        if (response.html) {
                            var html = $(response.html);
                            context.replaceWith(html);
                            html.trigger('new-content');
                        }

                        if (response.success) {
                            //Сбрасываем отображаемую ошибку
                            var name = $(defaults.verifyPhoneInput, html).removeClass('has-error').attr('name');
                            $(html).siblings('.formFieldError[data-field="' + name + '"]').remove();
                        }
                    },
                    error: function(xhr) {
                        var errorText;
                        if (xhr.status == 404) {
                            errorText = lang.t('Сессия истекла. Обновите страницу');
                        } else {
                            errorText = lang.t('Произошла ошибка. Попробуйте позже');
                        }
                        $(defaults.errorSelector, context).text(errorText).removeClass('hidden');
                    }
                });
            },

            /**
             * Обработчик ввода клавиш в online-поле с кодом верификации
             */
            onKeyPressAutoSubmit = function(e) {
                var _this = this;
                var context = $(_this).closest(defaults.verifyBlock);

                if (e.keyCode == 13) {
                    e.preventDefault();
                }

                setTimeout(function() {
                    var value = $(_this).val();
                    var url = context.data('checkCodeUrl');

                    var data = {
                        phone: $(defaults.verifyPhoneInput, context).val(),
                        token: $(defaults.verifyTokenInput, context).val(),
                        code: $(_this).val(),
                    };

                    if ( value.length == $(_this).data('autoSubmitLength') || e.keyCode == 13) {
                        refresh(url, data, context);
                    }
                }, 0);
            },

            /**
             * Обработчик кнопки "Отправить код"
             */
            refreshCode = function() {
                var _this = this;
                var context = $(this).closest(defaults.verifyBlock);
                var url = $(this).data('url');
                var data = {
                    token: context.data('token'),
                    phone: context.find(defaults.verifyPhoneInput).val()
                };

                refresh(url, data, context);
            },

            /**
             * Обработчик кнопки "Изменить номер"
             */
            resetCode = function() {
                var context = $(this).closest(defaults.verifyBlock);
                var url = $(this).data('url');
                var data = {
                    token: context.data('token')
                };

                refresh(url, data, context);
            },

            /**
             * Инициализирует обработчики для новых блоков
             */
            init = function() {
                $('[data-delay-refresh-code-sec]', this).each(runTimer);
                if ($.colorbox) {
                    $.colorbox.resize(); //Для совместимости со старыми темами оформления
                }
            },

            /**
             * Возвращает отформатированное количество времени
             *
             * @param second
             * @returns {string}
             */
            formatTime = function(second) {
                var hours = Math.floor(second/3600);
                var minutes = Math.floor((second - hours*3600)/60);
                var seconds = Math.floor(second - (minutes * 60 + hours * 3600));

                return (hours > 0 ? ("0" + hours).substr(-2,2) + ':' : '') +
                    ("0" + minutes).substr(-2,2) + ':' +
                    ("0" + seconds).substr(-2,2);
            },

            /**
             * Запускает обратный отсчет до повтора отправки кода
             */
            runTimer = function() {
                var _this = this;
                var interval = setInterval(function() {
                    var delay = $(_this).data('delayRefreshCodeSec');
                    delay--;

                    $(defaults.verifyTimer, _this).text(formatTime(delay));
                    $(_this).data('delayRefreshCodeSec', delay);

                    if (delay == 0) {
                        clearInterval(interval);
                        $(_this).removeClass(defaults.waitClass);
                    }
                }, 1000);

            };


        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object') {
            return methods.init.apply( this, args );
        } else {
            methods['init'].apply( this );
        }
    };

    $(function() {
        $.verification();
    });

})(jQuery);