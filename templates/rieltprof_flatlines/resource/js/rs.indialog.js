/* jQuery-плагин, позволяющий открывать во всплывающих окнах любой контент.
Если будет загружен контент с формой, то данная форма автоматически станет AJAX формой,
результат отправки такой формы будет отображен в том же окне */

/*
* Абстрактный объект диалогового окна. По умолчанию используем плагин magnificPopup.
* Здесь можно объявить любой jQuery плагин для модальных окон
*/
$.rsAbstractDialogModule = {
    /**
     * Открывает модальное окно
     * @param html Содержимое окна
     * @param onOpen callback, вызываемый после открытия окна
     * @param options объект, любые дополнительные свойства
     */
    open: function(html, onOpen, options) {
        $.magnificPopup.close();
        $.magnificPopup.open($.extend({
            items: {
                src: html,
                type: 'inline'
            },
            callbacks: {
                open:onOpen
            },
            mainClass: 'rs-dialog'
        }, options));

    },

    /**
     * Обновляет содержимое уже открытого окна
     *
     * @param html Содержимое окна
     * @param onOpen callback, вызываемый после открытия окна
     * @param options объект, любые дополнительные свойства
     */
    reopen: function(html, onOpen, width, options) {
        $.rsAbstractDialogModule.open(html, onOpen, width, options);
    },

    /**
     * Закрывает модальное окно
     */
    close: function() {
        $.magnificPopup.close();
    },

    /**
     * Возвращает класс корневого блока окна
     * @returns {string}
     */
    getDialogRootClass: function() {
        return '.mfp-wrap.rs-dialog'
    },

    /**
     * Отображает индикатор загрузки внутри окна
     */
    showLoader: function() {
        $('.mfp-content').append('<div class="rs-client-loading"></div>');
    },

    /**
     * Скрывает идентификатор загрузки внутри окна
     */
    hideLoader: function() {
        $('.mfp-content .rs-client-loading').remove();
    }
};


$.extend({
    /**
     * Загружает контент в диалоговом окне. Зависит от jquery.form
     */
    openDialog: function (options) {

        options = $.extend({
            url: '',
            data: null,
            callback: null,
            colorboxOptions: {},
            bindSubmit: true
        }, options);

        var xhr,
            colorBoxParams = $.extend(
            { //Отображаем диалог в режиме ожидания данных
                callbacks: {
                    close: function () {
                        if (xhr)
                            xhr.abort();
                    }
                }
            }, options.colorboxOptions);

        var showOverlay = function() {
            var overlay = $('<div id="rs-overlay" class="mfp-bg"/>').appendTo('body');
            $(window).on('keypress.inDialog', function(e) {
                if (e.keyCode == 27) {
                    if (xhr) {
                        xhr.abort();
                    }
                    hideOverlay();
                }
            });
        },

        hideOverlay = function() {
            $('#rs-overlay').remove();
            $(window).off('.inDialog');
        };

        var onComplete = function (response) {
            if (options.bindSubmit) {

                $('.mfp-content').each(function () {
                    var content = this;
                    $(content).trigger('new-content');

                    $('form', this).each(function () {

                        var new_action = $(this).attr('data-ajax-action');
                        if (typeof(new_action) != 'undefined') {
                            $(this).attr('action', new_action);
                        }

                        $(this).ajaxForm({
                            dataType: 'json',
                            data: {dialogWrap: 1},
                            beforeSubmit: function (arr, form, options) {
                                $('button[type="submit"], input[type="submit"]', form).attr('disabled', 'disabled'); //Защита от двойного клика
                                $.rsAbstractDialogModule.showLoader();
                            },
                            success: function (response) {

                                if (response.closeDialog) {
                                    $.rsAbstractDialogModule.close();
                                }
                                if (response.reloadPage) {
                                    //Перезагрузка страницы всегда методом GET
                                    location.replace(window.location.href);
                                    return;
                                }
                                if (response.redirect) {
                                    $.rsAbstractDialogModule.close();
                                    $.openDialog({
                                        url: response.redirect
                                    });
                                    return;
                                }

                                if (response.windowRedirect) {
                                    location.href = response.windowRedirect;
                                    return;
                                }

                                if (!response.closeDialog) {
                                    $(content).empty();
                                    var html = $('<div>').hide().appendTo('body').append(response.html);
                                    var data = html.children();

                                    $.rsAbstractDialogModule.reopen(data, function() {
                                        onComplete(response);
                                    });

                                }
                            },
                            complete: function() {
                                $.rsAbstractDialogModule.hideLoader();
                            }
                        });

                    });
                });
            }
            if (options.callback) options.callback(response);
        };

        var param = options.url.indexOf('?') == -1 ? '?dialogWrap=1' : '&dialogWrap=1';

        //Выполняем GET запрос
        showOverlay();
        $.rsAbstractDialogModule.close();

        xhr = $.get(options.url + param, options.data, function (response) {
            //Вставляем HTML в DOM для выполнения JavaScript и получения параметров окна
            var html = $('<div>').hide()
                                 .appendTo('body')
                                 .append(response.html);

            var dialogData = html.find('[data-dialog-options]').data('dialogOptions');
            if (dialogData) {
                colorBoxParams = $.extend(colorBoxParams, dialogData);
            }

            var data = html.children()

            $.rsAbstractDialogModule.open(data, function() {
                onComplete(response);
            });

        }, 'json').always(function() {
            hideOverlay();
        });

        return false;
    }
});

$(function() {

    //Открываем любую ссылку с классом rs-in-dialog во всплывающем окне (inDialog - старый класс для совместимости)
    $('body').on('click', '.rs-in-dialog, .inDialog', function(e) {
        if (!$(this).is('.disabled')) {
            var url = $(this).data('url') ? $(this).data('url') : $(this).attr('href');
            if ($(this).data('href')){ //Для совместимости
                url = $(this).data('href');
            }
            $.openDialog({
                url: url
            });
        }
        e.preventDefault();
    });

});