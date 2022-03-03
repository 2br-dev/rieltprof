/* Данный файл содержит JavaScript плагины ReadyScript, необходимые для работы функций административной панели.
 * Также данные функции используются в режиме отладки в клиентской части сайта
 *
 * @author ReadyScript lab.
 */
(function($) {
    /**
     * Содержит touchstart или click в зависимости от типа устройства
     */
    $.rs = {};
    $.rs.hasTouch = ('ontouchstart' in document.documentElement && navigator.userAgent.match(/Mobi/));
    $.rs.clickEventName = $.rs.hasTouch ? 'touchstart' : 'click';

    $.extend({
        /**
         * Вызывает callback, когда весь DOM загружен и когда DOM обновлен.
         */
        contentReady: function(callback) {
            if (callback) {
                $(function() {
                    if (typeof($LAB) == 'undefined' || !$LAB.loading) {
                        callback.call($('body').get(0));
                    }
                });
                $(window).bind('new-content', function(e) {
                    var _this = e.target;
                    if ($LAB.loading) {
                        $(window).one('LAB-loading-complete', function(e) {
                            callback.call(_this);
                        });
                    } else {
                        callback.call(_this);
                    }
                });
            }
        },
        /**
         * Вызывается, когда загружен весь DOM и динамически загружены все скрипты
         * Расширяет стандартный $(document).ready
         */
        allReady: function(callback) {
            $(function() {
                if ($LAB.loading) {
                    var _this = this;
                    $(window).one('LAB-loading-complete', function(e) {
                        callback.call(_this);
                    });
                } else {
                    callback.call(this);
                }
            });
        },

        /**
         * jQuery.ajax с настроенными обработчиками по-умолчанию
         */
        ajaxQuery: function(options) {
            options = $.extend({
                loadingProgress: true,
                checkAuthorization: true,
                checkMessages: true,
                checkWindowRedirect:true,
                checkMeters:true,
                dataType: 'json'
            }, options);

            var clone_options = jQuery.extend({}, options);
            return $.ajax( $.extend(clone_options,
                {
                    beforeSend: function(jqXHR, settings) {
                        if (options.loadingProgress) $.rs.loading.show();
                        if (options.beforeSend) options.beforeSend.call(this, jqXHR, settings);
                    },
                    success: function(data, textStatus, jqXHR) {
                        if (options.loadingProgress) $.rs.loading.hide();
                        
                        if (options.dataType == 'json') {
                            if (options.checkAuthorization) 
                                $.rs.checkAuthorization(data);
                            
                            if (options.checkWindowRedirect)    
                                $.rs.checkWindowRedirect(data);
                                
                            if (options.checkMessages) 
                                $.rs.checkMessages(data);
                            
                            if (options.checkMeters)    
                                $.rs.checkMeters(data);
                        }
                        if (options.success) options.success.call(this, data, textStatus, jqXHR);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        if (options.loadingProgress) $.rs.loading.error();
                        if (options.error) options.error.call(this, jqXHR, textStatus, errorThrown);
                    },
                    complete: function(jqXHR, textStatus) {
                        if (options.complete) options.complete.call(this, jqXHR, textStatus);
                    }
                }));
        }

    });


    /**
     * Инициализирует обновляемые блоки на странице в административной панели
     */
    $.rs.updatable = {
        initialized: false,
        dom: {
            defaultContainer: '.default-updatable:first', //Контейер, который будет обновлен по умолчанию
            container: '.updatable',
            callUpdate: '.call-update',
            formCallUpdate: 'form.form-call-update'
        },

        init: function() {
            $('body')
                .on('rs-update.updatable', $.rs.updatable.dom.container, $.rs.updatable.updateContainer)
                .on('click.updatable', $.rs.updatable.dom.callUpdate, $.rs.updatable.callUpdate)
                .on('submit.updatable', $.rs.updatable.dom.formCallUpdate, $.rs.updatable.formCallUpdate);

            $.rs.updatable.initState();
            $.rs.updatable.initialized = true;

            $(window).on('popstate', function(event) {
                //Инициализируем обновление при нажатии кнопки "назад"
                var state = event.originalEvent.state;
                if (state && state.rsUpdatableUrl) {
                    $($.rs.updatable.dom.defaultContainer).trigger('rs-update', [state.rsUpdatableUrl, null, {noUpdateHash: true}]);
                }
            });
        },

        initState: function() {
            if ($.rs.updatable.initialized) return;
            var blocksUrl = $.rs.updatable.getParsedHash();

            $.each(blocksUrl, function(block, url) {
                var div = '.updatable[data-update-block-id="'+block+'"]';
                $(div).trigger('rs-update', [decodeURIComponent(url), null, {noUpdateHash:true}]);
            });
        },

        getParsedHash: function() {
            var result = {};
            var hash = decodeURIComponent(location.hash.slice(1));

            if (hash.match(/([\w-]+='.*?')/g)) {
                var parts = hash.match(/([\w-]+='.*?')/g);
                $.each(parts, function(i, value) {
                    var item = value.match(/([\w-]+)='(.*?)'/);
                    result[item[1]] = item[2];
                });
            }
            return result;
        },

        /**
         * Устанавливает хэш страницы или меняет её URL
         *
         * @param blocksUrl - блок с параметрами для адреса
         */
        setHash: function(blocksUrl) {
            var hash = [];
            var newUrl;

            $.each(blocksUrl, function(block, url) {
                if (block == 'u') {
                    newUrl = url;
                } else {
                    //Остальное идет в хэш
                    hash.push( block+"='"+encodeURIComponent(url)+"'");
                }
            });

            if (newUrl) {
                var newHash = hash.length ? '#'.hash.join(';') : '';

                history.pushState({
                    rsUpdatableUrl: newUrl,
                    rsUpdatableHash: newHash
                }, '', newUrl + newHash);

            } else {
                location.hash = hash.join(';');
            }
        },

        /**
         * Обновление контейнера
         *
         * @param e - объект события
         * @param {string} url - Адрес для запроса
         * @param data - Передаваемые данные
         * @param ajaxParams - параметры для передачи ajax
         * @return {boolean}
         */
        updateContainer: function(e, url, data, ajaxParams) {
            if (!ajaxParams) ajaxParams = {};

            if (!url) {
                url = $(this).data('url');
            }
            if (url == '') return false;

            var $frame = $(this);
            var url_get = url;
            if (data) {
                url_get = url+(url.indexOf('?') > -1 ? '&'+$.param(data) : '?'+$.param(data));
            }
            $frame.data('url', url_get); //Устанавливаем последний

            $.ajaxQuery($.extend({
                url: url,
                dataType: 'json',
                data: data,
                success: function(response) {
                    if (response.close_dialog) { //Закрываем внешнее окно, если оно присутствует.
                        $frame.closest('.dialog-window').dialog('close');
                    } else {
                        //Изменяем URL
                        if (!ajaxParams.noUpdateHash && !$frame.is('[data-no-update-hash]')) {
                            var blockId = $frame.data('updateBlockId') ? $frame.data('updateBlockId') : 'u';
                            var blocks = $.rs.updatable.getParsedHash();
                            blocks[blockId] = url;
                            $.rs.updatable.setHash(blocks);
                        }

                        if ($frame.data('updateReplace')) {
                            var newFrame = $(response.html);
                            $frame.replaceWith(newFrame);
                            $frame = newFrame;
                        } else {
                            $frame.html(response.html);
                        }
                        $frame.trigger('new-content');
                    }
                }
            }, ajaxParams));

            e.stopPropagation();
        },

        /**
         * Вызов обновления формы
         *
         * @param e
         * @return {boolean}
         */
        callUpdate: function(e) {
            var href = $(this).data('updateUrl') ? $(this).data('updateUrl') : $(this).attr('href'),
                ajaxParams = {};

            if ($(this).hasClass('no-update-hash')) ajaxParams.noUpdateHash = true;
            $.rs.updatable.updateTarget(e.target, href, null, ajaxParams);
            return false;
        },

        /**
         * Вызов обновления формы из самой формы
         * @param e
         * @return {boolean}
         */
        formCallUpdate: function(e) {
            var data = $(this).serializeArray();
            var url  = $(this).attr('action') ? $(this).attr('action') : location.pathname;


            if ($(this).attr('method').toUpperCase() == 'GET') {
                data = data.filter(function(value) {
                    //Исключаем параметр, который
                    return value['name'] != 'mod_controller' && value['value'] != '';
                });
                url = '?' + $.param(data);
                data = {};
            }

            var ajaxParams = {};
            if ($(this).hasClass('no-update-hash')) ajaxParams.noUpdateHash = true;
            $.rs.updatable.updateTarget(e.target, url, data, ajaxParams);
            return false;
        },

        /**
         * Обновляет контейнер, на который указывает элемент или контейнер по умолчанию
         */
        updateTarget: function(element, url, data, ajaxParams) {
            var $el = $(element), targetContainer;

            if ($el.data('updateContainer')) {
                targetContainer = $el.data('updateContainer');
            } else if ($el.closest($.rs.updatable.dom.container).length) {
                targetContainer = $el.closest($.rs.updatable.dom.container);
            } else {
                targetContainer = $.rs.updatable.dom.defaultContainer;
            }

            var target = $(targetContainer);

            if (target.length && (url || target.data('url'))) {
                target.trigger('rs-update', [url, data, ajaxParams]);
                return true;
            }
            return false;
        }
    };


    $.fn.extend({
        /**
         * Вызывает callback, когда был произведен клик вне зоны элемента или по нажатию Esc.
         * @param function callback - функция, которую необходимо вызвать при возникновении события
         * @param except - элемент исключения, при нажатии на который не будет происходить вызов callback
         */
        rsCheckOutClick: function( callback, except ) {
            return $(this).each(function() {
                var _this = this;
                $(this).on('click.checkout', function(e){e.stopPropagation();});
                $('html').on('click.checkout', function(e) {
                    if (e.target != except) {
                        callback.call(_this, e)
                    }
                });
                $('html').on('keypress.checkout', function(e) {if (e.keyCode == 27 ) callback.call(_this, e)} );
            });
        },
        /**
         * Активирует кнопки сортировки в диалоге настройки таблицы
         */
        tableOptions: function() {
            var $context = this;
            $('.ch-sort', $context).off('click').on('click', function() {
                var set_sortn;
                $('.current-sort', $context).remove();
                if ($(this).hasClass('asc') && $(this).data('canBe') != 'ASC') {
                    set_sortn = 'desc';
                }
                if ($(this).hasClass('desc') && $(this).data('canBe') != 'DESC') {
                    set_sortn = 'asc';
                }
                if ($(this).hasClass('no')) {
                    if ($(this).data('canBe') != 'BOTH') {
                        set_sortn = $(this).data('canBe').toLowerCase();
                    } else {
                        set_sortn = 'asc';
                    }
                }
                if (set_sortn) {
                    $('.ch-sort', $context).removeClass('asc desc').addClass('no');
                    $(this).removeClass('no').addClass(set_sortn);
                }
            });
        },

        /**
         * Добавляет HTML ошибок формы
         *
         * @param errors - объект ошибок
         * @param $form - jquery Объект формы, в которой нахзоятся input
         * @param _options
         */
        fillError: function(errors, $form, _options) {
            var defaults = {
                    containerClass: 'error-list',
                    fieldError: '.field-error'
                },
                options = $.extend(defaults, _options);

            var ul = $('<ul></ul>').addClass(options.containerClass);

            for(var key in errors) {
                var marker = $('<div></div>')
                    .addClass(errors[key]['class'])
                    .append(errors[key]['fieldname']+'<i class="cor"></i>')

                var err_msg = '';
                for(var n in errors[key]['errors']) {
                    err_msg = err_msg + errors[key]['errors'][n]+'<br>';
                }
                ul.append( $('<li></li>').append(marker, $('<div class="text"></div>').append(err_msg)) );

                if (key[0] != '@' && $form) {
                    $('[name="'+key+'"]', $form)
                        .addClass('has-error')
                        .on({
                            'focus.formerror': function() {
                                $(options.fieldError+'[data-field="'+$(this).attr('name')+'"]', $form).show();
                            },
                            'blur.formerror': function() {
                                $(options.fieldError+'[data-field="'+$(this).attr('name')+'"]', $form).hide();
                            }
                        });

                    $(options.fieldError+'[data-field="'+key+'"]', $form).html(
                        $('<span class="text"></span>').append('<i class="cor"></i>'+err_msg)
                    );

                    if ($('[name="'+key+'"]', $form).is(':focus')) {
                        $('[name="'+key+'"]', $form).focus(); //Посылаем событие
                    }
                }
            }
            $(this).html(ul);
        }

    });

    /**
     * Обрабатывает информацию о счетчиках, если таковая
     * присутствует в ответе от сервера
     */
    $.rs.checkMeters = function(response) {
        if (response.meters) {
            $.rsMeters('update', response.meters);
        }

        if (response.markViewed) {
            for(var meterId in response.markViewed) {

                if (response.markViewed[meterId] == 'all') {
                    var markViewedSelector = '';
                } else {
                    var markViewedSelector = '[data-id="'+response.markViewed[meterId]+'"]';
                }

                $('.item-is-viewed[data-meter-id="'+meterId+'"]' + markViewedSelector).each(function() {
                    $(this)
                        .removeClass('new')
                        .attr('data-original-title', $(this).data('viewedText'));
                });

            }
        }
        return true;
    };

    /**
     * Возвращает true, если требуется выполнить повторный запрос на сервер
     *
     * @param response
     * @returns {boolean}
     */
    $.rs.checkRepeat = function(response) {
        if (response.repeat && !response.needAuthorization) {
            return true;
        }
        return false;
    };

    /**
     * Возвращает true, если не требуется выполнять редирект на другую страницу
     *
     * @param response
     * @returns {boolean}
     */
    $.rs.checkWindowRedirect = function(response) {
        if (typeof(response) == 'object' && response.windowRedirect) {
            location.href = response.windowRedirect;
            return false;
        }
        return true;
    };

    /**
     * Отключает scroll у основного контента в body
     */
    $.rs.lockBody = function() {
        var $wrap = $('#content');
        if ($wrap.length) {
            if (window.pageYOffset) {
                $wrap[0].oldScrollTop = window.pageYOffset;
                $wrap.css({
                    top: -($wrap[0].oldScrollTop)
                });
            }

            $('html, body').css({
                height: "100%",
                overflow: "hidden"
            });
        }
    };

    /**
     * Возвращает scroll основному контенту в body
     */
    $.rs.unlockBody = function() {
        var $wrap = $('#content');
        if ($wrap.length) {
            $('html, body').css({
                height: "",
                overflow: ""
            });

            $wrap.css({
                top: ''
            });

            window.scrollTo(0, $wrap[0].oldScrollTop);
            window.setTimeout(function () {
                $wrap[0].oldScrollTop = null;
            }, 0);
        }
    };

    /**
     * Отображает сообщения, если данная инструкция была направлена с сервера
     *
     * @param response
     * @returns {boolean}
     */
    $.rs.checkMessages = function(response) {
        try {
            if (typeof(response) == 'object' && response.messages) {
                for(var i in response.messages) {
                    messageObject = {
                        text: response.messages[i].text
                    }
                    if (typeof(response.messages[i].options) == 'object') {
                        messageObject = $.extend(response.messages[i].options, messageObject);
                    }
                    $.messenger('show', messageObject);
                }
            }
        } catch(err) {};
        return true;
    };

    /**
     * Выполняет редирект на страницу авторизации, в случае необходимости.
     * Возвращает false, если произошел редирект
     *
     * @param response ответ сервера от ajax запроса
     * @returns {boolean}
     */
    $.rs.checkAuthorization = function(response)
    {
        if (typeof(response) == 'object' && response.needAuthorization) {
            var param = (global.authUrl.indexOf('?') == -1) ? '?referrer='+location.href : '&referer='+location.href;
            location.href = global.authUrl + param;
            return false;
        }
        return true;
    };

    /**
     * Отвечает за отображение полосы загрузки, ошибок во время Ajax операций
     */
    $.rs.loading = {
        stackLength: 0,
        element: null,
        inProgress: false,
        /**
         * Отображает индикатор загрузки
         */
        show: function() {
            this.hide(true);
            this.inProgress = true;
            if (!this.element) {
                this.element = $('<div class="rs-loading"></div>');

                var currentWindowTitle = $('.ui-dialog-titlebar:last:visible');
                var targetZone = (currentWindowTitle.length) ? currentWindowTitle : '.header-panel';

                this.element.appendTo(targetZone);
            }
            this.stackLength++;
        },

        /**
         * Скрывает индикатор загрузки
         */
        hide: function(noDec) {
            if (this.stackLength>0 && !noDec) {
                this.stackLength--;
            }

            if (this.element && this.stackLength == 0) {
                this.inProgress = false;
                this.element.remove();
                this.element = null;
            }
        },

        /**
         * Отображает текст ошибки загрузки отправки данных
         */
        error: function(errtext) {
            this.stackLength = 0;
            this.hide();
            if (!errtext) {
                errtext = lang.t('Ошибка передачи данных. Повторите попытку еще раз');
            }

            var currentWindowTitle = $('.ui-dialog-titlebar:last:visible');
            var targetZone = (currentWindowTitle.length) ? currentWindowTitle : '.header-panel';

            this.element = $('<div class="rs-loading-error"></div>')
                .append( $('<span></span>').html(errtext) )
                .append('&nbsp;')
                .append( $('<a class="zmdi zmdi-close"></a>').on('click', function() {
                    $(this).parent().remove();
                }))
                .appendTo(targetZone);
        }
    };

    /**
     * Открывает определенный url в диалоговом окне
     *
     * @param {Object} options - опции диалогового окна
     */
    $.rs.openDialog = function(options)
    {
        options = $.extend({
            url: '',
            ajaxOptions: {},
            dialogOptions: {},
            close: function() {},
            afterOpen: function() {},
            dialogId: null,
            extraParams: {}
        }, options);

        if (options.ajaxOptions && options.ajaxOptions.data) {
            if (typeof(options.ajaxOptions.data) == 'object' && options.ajaxOptions.data instanceof Array) {
                options.ajaxOptions.data.push({name: 'ajax', value: '1'}, {name: 'ajaxDialog', value: '1'});
            }
            if (typeof(options.ajaxOptions.data) == 'object') {
                options.ajaxOptions.data = $.extend(options.ajaxOptions.data, {ajax:1, dialogMode:1});
            }
        }

        if (options.dialogId) {
            var dialog = $('#'+options.dialogId);
            if (!dialog.length) {
                var dialog = $('<div>').attr('id', options.dialogId);
            }
        } else {
            var dialog = $('<div>');
        }

        var initDialog = function(response) {
            if (!response || !response.needAuthorization) {

                if (response) {
                    try{
                        if (options.dialogOptions.crudOptions.onLoadTrigger) {
                            $(window).trigger(options.dialogOptions.crudOptions.onLoadTrigger, response);
                        }
                    } catch(e) {}
                    if (response.close_dialog) return;
                }

                $(dialog)
                    .addClass('dialog-window')
                    .dialog($.extend({
                        modal: true,
                        resizable:false,
                        draggable:false,
                        responsive:true,
                        clickOut:false,
                        closeText:false,
                        closeX:false,

                        width:'auto',
                        create: function() {
                            //Оборачиваем диалог, чтобы сохранить его стили
                            var wrapper = $('.admin-dialog-wrapper:first');
                            if (!wrapper.length) {
                                wrapper = $('<div class="admin-style admin-dialog-wrapper" />').appendTo('body');
                            }

                            $(this).closest('.ui-dialog').appendTo(wrapper);
                            $(this).data('dialogWrapper', wrapper);

                            var initContent = function() {
                                //Устанавливаем заголовок окна
                                var titleElement = $('.titlebox:first', dialog).hide();
                                if (titleElement.length) {
                                    dialog.dialog('option', 'title', titleElement.length ? titleElement.html() : '');
                                }
                                var dataLocalOptions = {};

                                var localOptions = $('[data-dialog-options]', dialog);
                                if (localOptions.length) {
                                    dataLocalOptions = $.extend(dataLocalOptions, localOptions.data('dialogOptions'));
                                }

                                for (var property in dataLocalOptions) {
                                    dialog.dialog('option', property, dataLocalOptions[property]);
                                    //Обновляем данные для корректной адаптации Диалогового окна
                                    if (property == 'width') {
                                        dialog.dialog('option', 'originalWidth', dataLocalOptions[property]);
                                    }
                                    if (property == 'height') {
                                        dialog.dialog('option', 'originalHeight', dataLocalOptions[property]);
                                    }
                                }
                            };

                            if (response) {
                                $(this).html(response.html);
                                $(this).bind('initContent', initContent);
                                initContent.call(this);
                            }
                        },
                        open: function() {
                            options.afterOpen($(this));

                            $('.ui-widget-overlay:last').appendTo( $(this).data('dialogWrapper') );

                            if (!$(this).data('dialogAlreadyLoaded')) {
                                $(this).data('dialogAlreadyLoaded', true);

                                if (typeof($LAB) != 'undefined' && $LAB.loading) {
                                    $(window).one('LAB-loading-complete', function() {
                                        //Если идет загрузка скриптов, то откладываем событие,
                                        //чтобы код с криптах мог подписаться на события
                                        $(dialog).trigger('new-content');
                                    });
                                } else {
                                    $(dialog).trigger('new-content');
                                }
                            }
                        },
                        close: function(e, ui) {
                            options.close.call(dialog, dialog);
                            if (!options.dialogId) {
                                $(this).dialog('destroy');
                            }
                        },
                        beforeDestroy: function(e, ui) {
                            $(dialog).trigger('dialogBeforeDestroy');
                        },
                        afterDestroy: function(e, ui) {
                            if ($(this).data('dialogWrapper') && $(this).data('dialogWrapper').is(':empty')) {
                                $(this).data('dialogWrapper').remove();
                            }
                        }
                    }, options.dialogOptions))
                    .on('close-dialog', function(event, originalEvent) {
                        $(this).dialog('close');
                        if (originalEvent) {
                            originalEvent.preventDefault();
                        }
                    });
            }
        };

        try{
            if (options.dialogOptions.crudOptions.beforeCallback) {
                var result = eval(options.dialogOptions.crudOptions.beforeCallback+'(options)');
                if (result) options = result;
            }
        } catch(e) {}

        if (!dialog.data('dialogAlreadyLoaded')) {
            $.ajaxQuery($.extend({
                url: options.url,
                dataType: 'json',
                data: $.extend({ajax:1, dialogMode:1}, options.extraParams),
                success: initDialog
            }, options.ajaxOptions));
        } else {
            initDialog(null);
        }

        return false;
    };


    /**
     * Plugin инициализирует отображение форм редактирования во весь экран
     */
    $.widget('rs.rsFullScreenDialog', {
        options: {
            onShow:function(event, data) {},
            onDestroy:function(event, data) {}
        },
        _create: function() {
            $(' > :visible',this.document[ 0 ].body).addClass('hide-important');

            this._createWrapper();
            this.element.appendTo(this.fsDialog);
            this.fsDialog.appendTo(this.document[ 0 ].body);
            this._trigger('onShow', {dialog: this.fsDialog});

            this._on('body', {
                keyup: function(e) {
                    if (e.keyCode == 27) this.destroy();
                }
            });

            this._on(this.fsDialog, {
                'closeFsDialog': function(event, sourceEvent) {
                    if (sourceEvent) {
                        sourceEvent.preventDefault();
                    }
                    this.destroy();
                }
            });
        },
        _createWrapper: function() {
            this.fsDialog = $('<div>').addClass('rs-fs-dlg');
            return this.fsDialog;
        },
        _destroy:function() {
            this._trigger('onDestroy', {dialog: this.fsDialog});
            $(' > .hide-important', this.document[ 0 ].body).removeClass('hide-important');

            this.element.detach();
            this.fsDialog.remove();
        },
        widget:function() {
            return this.fsDialog;
        }
    });

    /* Plugin инициализирует микроразметку административных функций ReadyScript */
    $.widget('rs.rsAdminFunctions', {
        options: {

        },
        _create:function() {
            var self = this;
            this.bottomDisableStack = [];
            this.element
                .on('click', '.crud-add', function(e) { return self._runManager(e, 'add') })
                .on('click', '.crud-edit', function(e) { return  self._runManager(e, 'edit') })
                .on('click', '.crud-remove', function(e) { return  self._runManager(e, 'remove')})
                .on('submit','.crud-form', function(e) { return self._runManager(e, 'formsave')})
                .on('click', '.crud-form-save', function(e) { return self._runManager(e, 'formsave') })
                .on('click', '.crud-form-apply', function(e) { return self._runManager(e, 'formsave') })
                .on('click', '.crud-form-cancel', function(e) { return self._runManager(e, 'cancel') })
                .on('click', '.crud-list-save', function(e) { return self._runManager(e, 'listsave') })
                .on('click', '.crud-post, .crud-post-selected', function(e) { return self._runManager(e, 'post') })
                .on('click', '.crud-remove-one', function(e) { return self._runManager(e, 'removeone') })
                .on('click', '.crud-multiedit', function(e) { return self._runManager(e, 'multiedit') })
                .on('click', '.crud-multiaction', function(e) { return self._runManager(e, 'multiaction') })
                .on('click', '.crud-dialog', function(e) { return self._runManager(e, 'dialog') })
                .on('click', '.crud-switch', function(e) { return self._runManager(e, 'itemswitch') })
                .on('click', '.crud-get', function(e) { return self._runManager(e, 'get')})
                .on({
                    'disableBottomToolbar.crud': function(e, key) {
                        $('.crud-form-save, .crud-form-apply, .btn.delete', $('.bottom-toolbar')).addClass('disabled');
                        if (self.bottomDisableStack.indexOf(key) == -1) {
                            self.bottomDisableStack.push(key);
                        }
                    },
                    'enableBottomToolbar.crud': function(e, key) {
                        var index = self.bottomDisableStack.indexOf(key);
                        if (index != -1) {
                            self.bottomDisableStack.splice(index, 1);
                        }
                        //Разблокируем кнопки, только если не осталось ни одного блокирующего элемента
                        if (!self.bottomDisableStack.length) {
                            $('.crud-form-save, .crud-form-apply, .btn.delete', $('.bottom-toolbar')).removeClass('disabled');
                        }
                    }
                });
        },

        /**
         *
         */
        _runManager: function(event, action) {
            if (event.ctrlKey)
                return true;

            if ($.rs.loading.inProgress)
                return false;

            if ($(event.currentTarget).is('.disabled'))
                return false;

            return this['_action_' + action](event);
        },

        /**
         * Открывает окно редактирования объекта
         */
        _action_add: function(e, checked) {
            var self = this,
                button = e.currentTarget,
                url = $(button).data('url') ? $(button).data('url') : $(button).attr('href');

            var crudOptions = $(button).data('crudOptions');

            if (typeof(crudOptions) != 'object') crudOptions = {};
            crudOptions.openerElement = $(button).closest('.updatable');

            var extendOptions = {};
            try {
                if (crudOptions.updateBlockId) {
                    crudOptions.updateElement = $('[data-update-block-id="'+crudOptions.updateBlockId+'"]').get(0);
                }
                if (crudOptions.updateThis) {
                    crudOptions.updateElement = button;
                }
                if (crudOptions.dialogId) {
                    extendOptions.dialogId = crudOptions.dialogId;
                }
            } catch(err) {}

            //Маленькое окно по умолчанию
            var widthParam = {
                width:600,
                height:500
            };

            //Большое окно
            if (!$(button).hasClass('crud-sm-dialog')) {
                widthParam = {
                    width:'95%', //  0.95 * $(window).width(),
                    height:0.95 * $(window).height()
                };
            }
            // Свои размеры
            if ($(button).data('crud-dialog-width')) {
                widthParam.width = $(button).data('crud-dialog-width');
            }
            if ($(button).data('crud-dialog-height')) {
                widthParam.height = $(button).data('crud-dialog-height');
            }

            $.rs.openDialog($.extend({
                dialogOptions: $.extend({//Передаем в диалоговое окно дополнительные данные инициатора действия
                    crudOptions: crudOptions,
                }, widthParam),
                url: url,
                button: button,
                ajaxOptions: {
                    data: checked
                },
                afterOpen: function(dialog) {
                    if ($(button).hasClass('crud-replace-dialog')) {
                        $(button).trigger('close-dialog');
                    }

                    dialog.on('dialogclose', function() {
                         //Очищаем стек запретов, если окно закроется
                         self.bottomDisableStack = [];
                    });
                }
            }, extendOptions));

            return false;
        },

        /**
         * Открывает диалог редактирования записи
         */
        _action_edit: function(e, checked) {
            return this._action_add.call(this, e, checked);
        },

        _action_cancel: function(e) {
            $(e.currentTarget).trigger('close-dialog', e);
        },

        _action_formsave: function(e) {
            e.preventDefault();
            var self = this;
            if (self.bottomDisableStack.length) return false;

            var button = e.currentTarget;
            var is_apply = $(button).is('.crud-form-apply');

            //Если Submit был по нажатию Enter, эмитируем нажатие кнопки "Сохранить"
            if ($(button).is('form')) {
                button = $('.crud-form-save:first', self._getGroup(button));
                if (!button.length) return false;
            }

            var $group = self._getGroup(button);
            var $form = self._getForm(button);
            var dialog = self._getMyDialog(button);

            var afterSubmit = function() {
                $('button[type="submit"], input[type="submit"]', $form)
                    .removeAttr('disabled'); //Защита от двойного клика
            };

            /**
             * Отображает всплывающую подсказку возле кнопки "Сохранить"
             *
             * @param text
             * @param className
             */
            var showPopOver = function(text, className) {
                var content = $('<span>').addClass(className).html(text);
                content.find('.scroll-to-top').click(function() {
                    if (self._getMyDialog(button) !== false) {
                        self._getGroup(button).find('.contentbox').scrollTop(0);
                    } else {
                        $(window).scrollTop(0);
                    }
                });

                $(button).popover({
                    content: content,
                    viewport:self._getGroup(button),
                    trigger:'manual',
                    placement:'top',
                    html:true
                }).popover('show');

                var timeout = setTimeout(function() {
                    $(button).popover('destroy');
                }, 7000);

                content.on('click', function() {
                    clearTimeout(timeout);
                    $(button).popover('destroy');
                });
            };

            /**
             * Финальная обработка пришедшего запроса
             *
             * @param object dialog   - объект диалогового окна
             * @param object responce - объект ответа с сервера
             */
            var finalProcess = function (dialog, response){
                if ($.rs.checkAuthorization(response)
                    && $.rs.checkWindowRedirect(response)
                    && $.rs.checkMessages(response)                    
                    && $.rs.checkMeters(response)) {
                    
                    var crudOptions = dialog ? dialog.dialog('option', 'crudOptions') : {};
                    
                    if (response.success) {
                        $form.trigger('crudSaveSuccess', [response, crudOptions]);
                        var redirect = response.redirect ? response.redirect : null;
                        //Сохранение прошло успешно
                        if (dialog && !is_apply) { //Если всё было в диалоговом окне
                            if (response.html && response.html != '') {
                                //Обновляем контент в диалоге
                                dialog.html(response.html)
                                    .trigger('new-content')
                                    .trigger('initContent')
                                    .trigger('contentSizeChanged');
                                return;
                            }

                            //Редактирование в диалоге
                            if (response.callCrudAdd) {
                                var a = $('<a />').data('url', response.callCrudAdd);
                                if (response.crudDialogWidth) {a.data('crud-dialog-width', response.crudDialogWidth)}
                                if (response.crudDialogHeight) {a.data('crud-dialog-height', response.crudDialogHeight)}
                                self._action_add({currentTarget: a});
                            } else {
                                if (!response.noUpdateTarget){ //Если нет флага о том, что нужно перезагрузить область
                                    if (crudOptions && crudOptions.openerElement) {
                                        self._updateTarget(crudOptions.openerElement, redirect, crudOptions); //обновляем область с опциями
                                    }
                                }
                            }
                            dialog.dialog('close');

                        } else {
                            //Редактирование на отдельной странице
                            if (!is_apply && !dialog) {
                                //Если нажали "сохранить и закрыть" на отдельной странице
                                //то нажимаем после сохранения кнопку Закрыть
                                if (response.callCrudAdd) {
                                    location.href = response.callCrudAdd;
                                } else {
                                    var cancel = $('.crud-form-cancel', $group);
                                    if (cancel.length) {
                                        cancel[0].click();
                                    }
                                }
                            } else {
                                //Нажали "сохранить" в диалоговом окне
                                $('.crud-form-success', $group).html(response.formdata.success_text).show();
                                if (response.success_text_timeout) {
                                    setTimeout(function () {
                                        $('.crud-form-success', $group).slideUp();
                                    }, response.success_text_timeout ? response.success_text_timeout : 7000);
                                }

                                showPopOver(lang.t('Изменения успешно сохранены'), 'c-green text-nowrap');

                                if (!response.noUpdateTarget){ //Если нет флага о том, что нужно перезагрузить область

                                    //Перезагрузим область, из которой произошел переход
                                    if (crudOptions && crudOptions.openerElement) {
                                        self._updateTarget(crudOptions.openerElement, redirect, crudOptions); //обновляем область с опциями
                                    }
                                }
                            }
                        }

                    } else {
                        $form.trigger('crudSaveFail', [response, crudOptions]);
                        //Во время сохранения возникли ошибки
                        if (response.formdata) {

                            showPopOver(lang.t('<a class="scroll-to-top c-red">Во время сохранения возникла ошибка</a>'), 'c-red');

                            $('.crud-form-error', $group).fillError(response.formdata.errors, $form);
                            if (dialog) {
                                dialog.trigger('contentSizeChanged');
                            }
                        }
                    }
                }
            };

            var ajaxOptions = {
                data: {ajax: 1},
                dataType:'json',
                beforeSerialize: function(form, options) {
                    $('select.selectAllBeforeSubmit option', form).prop('selected', true);
                    form.trigger('beforeAjaxSubmit', form, options);
                },
                beforeSubmit: function(arr, form, options) {
                    $('button[type="submit"], input[type="submit"]',$form).attr('disabled','disabled'); //Защита от двойного клика
                    $.rs.loading.show();
                },
                success: function(response) {
                    $('.crud-form-error, .crud-form-success', $group).html('');
                    $('.has-error', $form).removeClass('has-error');
                    $('.field-error', $form).hide();
                    $('[name]', $form).off('.formerror');

                    //Данные успешно отправлены

                    if ($.rs.checkRepeat(response)){
                        var params = $.extend({
                            type:'POST',
                            dataType:'json',
                            data: {},
                            url:'',
                            success: ajaxOptions.success,
                            error: ajaxOptions.error,
                            checkAuthorization: false,
                            checkMessages: false,
                            checkWindowRedirect: false,
                            checkMeters: false,
                        }, response.queryParams);

                        $.ajaxQuery(params);

                    }else{
                        $.rs.loading.hide();
                        finalProcess(dialog, response);
                        afterSubmit();
                    }
                },
                error: function() {
                    //Ошибка отправки формы
                    $.rs.loading.error();
                    afterSubmit();
                }
            };

            if ($(button).data('url')) {
                ajaxOptions.url = $(button).data('url');
            }

            $form.ajaxSubmit(ajaxOptions);

        },

        _action_remove: function(e)
        {
            var target = $(e.currentTarget);
            if (!target.data('confirmText')) {
                target.data('confirmText', lang.t('Вы действительно хотите удалить выбранные элементы (%count)?'));
            }
            return this._action_post(e);
        },

        /**
         * Выполняет Ajax'ом POST запрос и передает все сведения, представленные внутри формы
         */
        _action_listsave: function(e) {
            var button = e.currentTarget;
            var $form = this._getListForm(button);
            var url = $(button).data('url') ? $(button).data('url') : $(button).attr('href');
            var confirmText = $(button).data('confirmText');

            if (confirmText && !confirm( confirmText )) {
                return false;
            }

            $.rs.loading.show();
            $form.ajaxSubmit({
                dataType: 'json',
                data: {
                    ajax: 1
                },
                url: url,
                success: function(response) {
                    $.rs.loading.hide();
                    $.rs.checkAuthorization(response);
                    $.rs.checkWindowRedirect(response);
                    $.rs.checkMessages(response);
                    $.rs.checkMeters(response);

                    if (response.success) {
                        $.rs.updatable.updateTarget(e.currentTarget);
                    }
                },
                error: function() {
                    $.rs.loading.error();
                }
            });

            return false;
        },

        /**
         * Выполняет Ajax'ом POST запрос для выбранных элементов
         */
        _action_post: function(e) {
            var button = e.currentTarget;
            var $form = this._getListForm(button);
            var url = $(button).data('url') ? $(button).data('url') : $(button).attr('href');
            var confirmText = $(button).data('confirmText');

            if ($('.select-all:checked', $form).length && $('.total_value', $form.parent()).length) {
                var total = $('.total_value', $form.parent()).text();
            } else {
                var total = $('input[name="chk[]"]:checked', $form).length;
            }
            
            if (confirmText) {
                confirmText = confirmText.replace('%count', total);
            }
            if (!total || (confirmText && !confirm( confirmText )) ) {
                return false;
            }

            $.rs.loading.show();
            $form.ajaxSubmit({
                dataType: 'json',
                data: {
                    ajax: 1
                },
                url: url,
                success: function(response) {
                    $.rs.loading.hide();
                    $.rs.checkAuthorization(response);
                    $.rs.checkWindowRedirect(response);
                    $.rs.checkMessages(response);
                    $.rs.checkMeters(response);

                    if (response.success) {
                        $.rs.updatable.updateTarget(e.currentTarget);
                    }
                },
                error: function() {
                    $.rs.loading.error();
                }
            });

            return false;
        },

        /**
         * Удаляет один элемент
         */
        _action_removeone: function(e) {
            var self = this;
            if (!confirm(lang.t('Вы действительно хотите удалить выбранный элемент?'))) {
                return false;
            }

            var button = $(e.currentTarget),
                crudOptions = button.data('crudOptions'),
                url = button.data('url') ? button.data('url') : button.attr('href');

            $.ajaxQuery({
                url : url,
                data: {
                    ajax: 1
                },
                success: function() {
                    self._updateTarget(button, null, crudOptions ? crudOptions : null);
                }
            });

            return false;
        },

        _action_multiedit: function(e) {
            var $form = this._getListForm(e.currentTarget);
            var data = $form.serializeArray();

            var checked = $('input[name="chk[]"]:checked', $form);

            if (!checked.length) {
                return false;
            }

            if (checked.length == 1) { //Перебрасываем на обычное редактирование
                var edit_one = $('.crud-edit[data-id="'+checked[0]['value']+'"]', $form);
                if (edit_one.length) {
                    edit_one.click();
                } else {
                    alert(lang.t('Выберите как минимум 2 элемента для мультиредактирования'));
                }
                return false;
            } else {
                return this._action_edit(e, data);
            }
        },

        _action_multiaction: function(e) {
            var $form = this._getListForm(e.currentTarget);
            var data = $form.serializeArray();

            var checked = $('input[name="chk[]"]:checked', $form);

            if (!checked.length) {
                return false;
            }
            return this._action_edit(e, data);

        },

        /**
         * Открывает диалог и загружает в него контент, при закрытии диалога обновляет содержимое основного поля
         */
        _action_dialog: function(e)
        {
            var button = $(e.currentTarget);
            $.rs.openDialog({
                url: button.data('url') ? button.data('url') : button.attr('href'),
                button: e.currentTarget,
                dialogOptions: {
                    title: button.attr('title'),
                    close: function() {
                        $.rs.updatable.updateTarget(e.currentTarget);
                    }
                }
            });
            return false;
        },

        /**
         * Отправляет запрос на сервер, после действия выключателя
         */
        _action_itemswitch: function(e) {
            var url = $(e.currentTarget).data('url');
            $.ajaxQuery({
                loadingProgress: false,
                url:url
            });
        },

        /**
         * Выполняет get запрос (url берется из data-url) и обновляет содержимое видимой области
         */
        _action_get: function(e) {
            var button = e.currentTarget;
            var self = this;

            if (this._checkConfirm(button)) {
                var url = $(button).data('url') ? $(button).data('url') : $(button).attr('href');
                var dialog = this._getMyDialog(button);

                var ajaxOptions = {
                    url: url,
                    success: function(response) {
                        //Данные успешно отправлены

                        if ($.rs.checkRepeat(response)){
                            var params = $.extend({
                                type:'POST',
                                dataType:'json',
                                data: {},
                                url:url,
                                success: ajaxOptions.success,
                                error: ajaxOptions.error,
                            }, response.queryParams);

                            $.ajaxQuery(params);

                        } else {
                            if (typeof(response) == 'object' && !response.noUpdate) {
                                self._updateTarget(button, null, dialog ? dialog.dialog('option', 'crudOptions') : null);
                            }

                            if ($(button).hasClass('crud-close-dialog') && dialog) {
                                dialog.dialog('close');
                            }
                        }

                    }
                };

                $.ajaxQuery(ajaxOptions);
            }
            return false;
        },


        /**
         * Возвращает элемент окна, в котором находится элемент или false
         *
         * @param element
         */
        _getMyDialog: function(element)
        {
            var $win = $(element).closest('.dialog-window');
            return $win.length ? $win : false;
        },

        /**
         * Возвращает элемент, который оборачивает
         */
        _getGroup: function(element)
        {
            return $(element).closest('.crud-ajax-group');
        },

        /**
         * Возвращает форму, которую будет использовать element
         */
        _getForm: function(element) {
            if ($(element).data('form')) {
                var $form = $( $(element).data('form') );
            } else if (element.tagName == 'FORM') {
                var $form = $(element);
            } else if ($(element).closest('form').length) {
                var $form = $(element).closest('form');
            } else {
                var $form = $('.crud-form', this._getGroup(element));
            }
            return $form;
        },

        /**
         * Возвращает форму, которую будет использовать element
         */
        _getListForm: function(element) {
            if ($(element).data('form')) {
                var $form = $( $(element).data('form') ); //Если форма явно задана в атрибуте элемента
            } else if ($(element).closest('[data-linked-form]').length) {
                var $form = $($(element).closest('[data-linked-form]').data('linkedForm')); //Если элемент обернут в блок с атрибутом data-linked-form
            } else if ($(element).closest('form').length) {
                var $form = $(element).closest('form'); //Если выше есть какая-нибудь форма
            } else {
                var $form = $('.crud-list-form', this._getGroup(element)); //Ищем в ajax-group элемент с классом crud-list-form
            }
            return $form;
        },

        /**
         * Спрашивает у пользователя подтверждение, если это необходимо
         */
        _checkConfirm: function(element)
        {
            if ($(element).data('confirmText')) {
                return confirm($(element).data('confirmText'));
            }
            return true;
        },

        /**
         * Обновляет необходимый контейнер возле элемента element
         */
        _updateTarget: function(element, redirect, crudOptions)
        {
            if (crudOptions && crudOptions.updateElement) {
                element = crudOptions.updateElement;
            }

            var ajaxParam = (crudOptions && crudOptions.ajaxParam) ? crudOptions.ajaxParam : null;

            if (!$.rs.updatable.updateTarget(element, redirect, null, ajaxParam)) {
                location.reload(true);
            }
        }

    });

    /**
     * Строит диапозон чисел
     *
     * @param low - первая цифра
     * @param high - последняя цифра
     * @param step - шаг числа
     * @return {Array}
     */
    function range (low, high, step) {
        var matrix = [];
        var plus;
        var walker = step || 1;
        if (!isNaN(low) && !isNaN(high)) {
            iVal = low;
            endval = high;
        } else {
            iVal = (isNaN(low) ? 0 : low);
            endval = (isNaN(high) ? 0 : high);
        }
        plus = !(iVal > endval);
        if (plus) {
            while (iVal <= endval) {
                matrix.push(iVal);
                iVal += walker;
            }
        } else {
            while (iVal >= endval) {
                matrix.push(iVal);
                iVal -= walker;
            }
        }
        return matrix;
    }

    $.rs.checkboxChange = function() {

        var checkChange = function() {
            if (this.checked) {
                $(this).closest('.chk').addClass('checked');
            } else {
                var $parentform = $(this).closest('form');
                $(this).closest('.chk').removeClass('checked');
                $('.select-all', $parentform).prop('checked', false);
                $('.select-page', $parentform).prop('checked', false);
                $('thead .chk', $parentform).removeClass('checked-all checked');
            }
        };

        //Инициализируем подсветку выбранных чекбоксов
        $('body').on('change.chk', '.chk input:checkbox', checkChange);

        //Инициализируем подсветку выбранных чекбоксов
        $('body').on('click.chk', '.chk', function(e) {
            var checkbox = $('input:checkbox', this).get(0);
            var tag; //Тег для подсчета
            var wrapper; //Обёртка
            //В зависимости от родителя смотрим обёртку и элемент для посчета
            var click_tag_name = $(this).parent().get(0).tagName;
            if (click_tag_name == 'TD' || click_tag_name == 'TR'){
                tag = "tr";
                wrapper = $(this).closest('tbody');
            }else{
                tag = "li";
                wrapper = $(this).closest('ul');
            }

            if (!checkbox.disabled) {
                if (e.target.tagName != 'INPUT') {
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                        $(checkbox).change();
                    }
                }
            }

            if (e.shiftKey){ //Если зажат SHIFT

                //Смотрим первый элемент и до последнего тянем
                var first_selected = $(".preShiftChecked:first", wrapper);
                if (click_tag_name != 'TD' && click_tag_name != 'TR'){
                    wrapper = first_selected.closest('ul');
                }

                if (first_selected.length){
                    var first = first_selected.closest(tag).index(); //Смотрим идекс
                    first_selected.addClass('checked');

                    var last = $(this).closest(tag).index(); //Последний нажатый элемента

                    var elements_range = range(last, first);
                    var need_be_selected = checkbox.checked; //Флаг нужно ли снимать нужный класс выделения или ставить
                    $.each(elements_range, function(k, key) {
                        var search_tag = ">" + tag + ":eq(" + key + ")";
                        if (need_be_selected){
                            $(search_tag + " .chk", wrapper).addClass('checked');
                            $(search_tag + " [type='checkbox']", wrapper).prop('checked', true).change();
                        }else{
                            $(search_tag + " .chk", wrapper).removeClass('checked');
                            $(search_tag + " [type='checkbox']", wrapper).prop('checked', false).change();
                        }
                    });
                }
            }
            $(".chk").removeClass('preShiftChecked');
            $(this).addClass('preShiftChecked');
        });

        var onSelectButton = function() {
            var name = $(this).data('name');
            var state = this.checked;

            var $parentform = $(this).closest('.localform');
            if (!$parentform.length) {
                $parentform = $(this).closest('form');
            }
            $('input:checkbox[name="'+name+'"]:enabled', $parentform).prop('checked', state);

            if (state) {
                $('.chk:has(input:enabled)', $parentform).addClass('checked');
                $('.redmarker', $parentform).addClass('r_on');
            } else {
                $('.select-all', $parentform).prop('checked', false);
                $(this).closest('.chk').removeClass('checked-all');
                $('.chk:has(input:enabled)', $parentform).removeClass('checked');
                $('.redmarker', $parentform).removeClass('r_on');
            }
        };

        $('body').on('change', '.select-page', onSelectButton);
        $('body').on('change', '.select-all', function() {
            var state = this.checked;
            var $parentform = $(this).closest('.localform');
            if (!$parentform.length) {
                $parentform = $(this).closest('form');
            }
            var selectPage = $('.select-page', $parentform).prop('checked', state).get(0);

            onSelectButton.call(selectPage);
            if (state) {
                $(this).closest('.chk').addClass('checked-all');
            } else {
                $(this).closest('.chk').removeClass('checked-all');
            }
        });

        //Подключаемся в самом конце, чтобы фиксировать наличие оставшихся включенных чекбоксов
        $('body').on('change.chk', '.chk input:checkbox', function() {
            var $parentform = $(this).closest('form');
            $(this).closest('.column')
                   .toggleClass('sticky-on', $parentform.find('.chk input:checked').length>0);
            $(this).trigger('stickyUpdate');
        });
    };

    $(function() {
        $.rs.updatable.init();

        $('body').rsAdminFunctions();

        //Дополнение к плагину bootstrap.tab, сообщает вкладке, что её открыли
        $('body').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            var tabTarget = $(e.target).data('target');
            if (!tabTarget) {
                tabTarget = $(e.target).attr('href');
            }
            $(tabTarget).trigger('on-tab-open');
        });

        // Смена состояния поля при мультиредактировании в админ панели
        $('body').on('change.multiedit', '.doedit', function(){
            var row_block = $(this).closest('.editrow'); //Оборачивающий тег
            if ($(this).prop('checked')){
                $(".multi_edit_rightcol",row_block).removeClass('coveron');
            }else{
                $(".multi_edit_rightcol",row_block).addClass('coveron');
            }
        });
        $('body').on('click.multiedit', '.multi_edit_rightcol .cover', function() {
            $(this).closest('tr').find('.doedit').prop('checked', true).trigger('change');
        });

        /**
         * Кнопка очистить хэш
         *
         */
        $('.rs-clean-cache').on('click', function(e) {
            $.ajaxQuery({
                url: $(this).attr('href'),
                success: function() {
                    location.reload();
                }
            });
            e.preventDefault();
        });

        $('body').on($.rs.clickEventName, '.rs-switch', function() {
            $(this).toggleClass('on');
        });

        //Инициализируем подсветку checkbox'ов
        $.rs.checkboxChange();

        $('body')
            .on('clear.bs.fileinput', '[data-provides="fileinput"]', function(e) {
                $('input.remove', this).val(1);
            })
            .on('change.bs.fileinput', '[data-provides="fileinput"]', function(e) {
                $('input.remove', this).val(0);
            })
            //Скрываем всплывающие подсказки при наступлении события
            .on('keydown new-content', function() {
                $('.tooltip[role="tooltip"]').remove();
            })
            .on($.rs.clickEventName, '.rs-open-performance-report', function(e) {
                window.open($(this).attr('href'), 'report', 'width=1000,height=800');
                e.preventDefault();
            });

        //Эмулируем oncontextmenu на сенсорных устройствах
        if ($.rs.hasTouch)
        {
            var
                longPress = 500,
                startTime = 0,
                timer;

            $('body')
                .on('touchstart', '[data-debug-contextmenu]', function(e) {

                    if (!startTime) {
                        startTime = new Date().getTime();
                        var eventData = {
                            currentTarget: e.currentTarget,
                            type: 'contextmenu',
                            pageX: e.originalEvent.touches[0].pageX,
                            pageY: e.originalEvent.touches[0].pageY
                        };
                        timer = setTimeout(function () {
                            var event = $.Event(e, eventData);
                            $(eventData.currentTarget).trigger(event);
                        }, longPress);
                    }
                })
                .on('touchend', '[data-debug-contextmenu]', function(e) {
                    clearTimeout(timer);
                    if ( startTime && new Date().getTime() > startTime + longPress ) {
                        $(e.currentTarget).one('click', function(e) {
                            e.preventDefault();
                        });
                    }
                    startTime = 0;
                });

        }
    });

    $.contentReady(function() {
        //Инициализируем Bootstrap tooltip
        var container;
        if ($('body').hasClass('admin-style')){
            container = $('.admin-style:first');
        }
        else {
            container = $('.admin-style:eq(1)');
        }
        $('.admin-style .tooltip').remove();
        $('.admin-style [title]')
            .on('click remove', function() {
                $(this).tooltip('hide');
            })
            .tooltip({
                trigger:'hover',
                html:true,
                container:container
            });

        if ($.fn.lightGallery) {
            var gallery = $(this).data('lightGallery');
            if (gallery) {
                gallery.destroy(true);
            }
            $(this).lightGallery({
                selector: "[rel='lightbox-tour']",
                thumbnail: false,
                autoplay: false,
                autoplayControls: false
            });
        }

        //Устанавливаем нужный scroll, чтобы активный таб попал в зону видимости
        $('.tab-nav[role="tablist"] > .active', this).each(function() {
            var ul = $(this).parent();
            var offset = $(this).position().left + $(this).width();
            var width = ul.width();
            if (offset > width) {
                $(this).parent().scrollLeft( offset - width );
            }
        });
    });

})(jQuery);