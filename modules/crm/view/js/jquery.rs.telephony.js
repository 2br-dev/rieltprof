/**
 * Обеспечивает корректную работу всплывающих окон телефонии
 *
 * @author ReadyScript lab.
 */
var telephony = {};

/**
 * Менеджер табов всплывающих окон для телефонии. Если одновременно всплывают
 * несколько окон, то они должны упаковываться в разные табы
 */
telephony.tabManager = {
    list: [],
    container: null,
    tabContainer: null,
    paneContainer: null,

    init: function() {
        var _this = this;
        this.container = $('<div class="tel-contnainer" id="tel-win-container">' +
                                '<a class="icon-circle tel-expand-button" title="' + lang.t('Развернуть') + '">' +
                                '    <i class="zmdi zmdi-phone-setting rubberBand animated infinite"></i>' +
                                '</a>' +
                            '</div>'
                           ).appendTo('body');

        if (global.telephonyOffsetBottom) {
            this.container.css('margin-bottom', global.telephonyOffsetBottom + 'px');
        }

        this.tabContainer = $('<ul class="tab-nav tel-tabs" role="tablist" data-tab-color="green"></ul>');
        this.paneContainer = $('<div class="tab-content tel-panes"></div>');

        this.tabContainer.on('click', 'a', function() {
            //Сохраняем активный таб
            $.cookie('crm-tel-active-tab', $(this).closest('[data-id]').data('id'), {
                expires:5000,
                path:'/'
            });
        });

        this.container
                .append(this.tabContainer, this.paneContainer)
                .on('click', '.tel-expand-button', function() {
                    _this.expand();
                });

        if ($.cookie('crm-tel-collapse-window')) {
            this.collapse();
        }

        this.paneContainer
                .on('click', '.tel-view-toggler', function() {
                    _this.collapse();
                })
                .on('click', '.close', function() {
                    var callId = $(this).closest('[data-id]').data('id');
                    _this.close(callId);
                })
                .on('click', '.tel-action', function() {
                    var callId = $(this).closest('[data-id]').data('id');
                    _this.doAction(callId, $(this).data('url'));
                });
    },

    /**
     * Восстанавливает активную вкладку
     */
    setActiveTab: function() {
        var tab = this.tabContainer.find('[data-id="' + $.cookie('crm-tel-active-tab') + '"] a');
        if (tab.length) {
            tab.click();
        } else {
            this.tabContainer.find(' > li:first > a').click();
        }
    },

    /**
     * Выполняет одно действие со звонком
     *
     * @param callId
     * @param url
     */
    doAction: function(callId, url) {
        var _this = this;

        $.ajaxQuery({
            loadingProgress: false,
            url: url,
            success:function(response) {
                if (!response.success) {
                    _this.showError(callId, response.error);
                }
            },
            error:function() {
                _this.showError(callId, lang.t('Не удалось выполнить запрос'));
            }
        })
    },

    /**
     * Отображает ошибку
     *
     * @param callId
     * @param error
     */
    showError: function(callId, error) {
        var errorContainer = this.paneContainer.find('[data-id="' + callId + '"] .tel-error');
        if (errorContainer.length) {
            errorContainer.text(error);
        }
        errorContainer.toggleClass('hidden', error == '');
    },

    /**
     * Добавляет новый таб со звонящим
     *
     * @param callerWin
     * @param noExpand
     */
    addTab: function(callerWin, noExpand, noSelectTab) {

        var existPane = this.paneContainer.find('[data-id="'+callerWin.id+'"]');
        var tab = this.tabContainer.find('[data-id="'+callerWin.id+'"]');
        var pane = this._renderTabContent(callerWin);

        if (existPane.length) {
            var isActive = existPane.hasClass('active');
            pane.toggleClass('active', isActive);
            existPane.replaceWith(pane);

        } else {
            pane.appendTo(this.paneContainer);

            var tab = this._renderTab(callerWin);
            tab.appendTo(this.tabContainer);

            this.list.push(callerWin);
            pane.trigger('new-content');
        }

        if (!noSelectTab) {
            tab.find('a').click(); //Активируем таб
        }

        this.updateActive();
        if (!noExpand) {
            this.expand();
        }
    },

    /**
     * Закрывает вкладку со звонком с пометкой
     *
     * @param id
     */
    close: function(id) {
        var url = this.paneContainer.find('[data-id="'+id+'"] .close').data('url');
        this.removeTab(id);
        $.get(url);
    },

    /**
     * Разворачивает окно с телефонией
     */
    expand: function() {
        this.container.removeClass('tel-collapsed');
        $.cookie('crm-tel-collapse-window', null, {
            path:'/'
        });
    },

    /**
     * Сворачивает окно с телефонией
     */
    collapse: function() {
        this.container.addClass('tel-collapsed');
        $.cookie('crm-tel-collapse-window', 1, {
            expires:5000,
            path:'/'
        });
    },

    /**
     * Закрывает вкладку. Переключает активную вкладку на другую
     *
     * @param id
     */
    removeTab: function(id) {
        var tab = this.tabContainer.find('[data-id="'+id+'"]');
        if (tab.is('.active')) {
            var nextActiveTab = tab.prev().length ? tab.prev() : tab.next();
            if (nextActiveTab.length) {
                nextActiveTab.find('> a').click(); //Меняем активный таб
            }
        }

        tab.remove();
        this.paneContainer.find('[data-id="'+id+'"]').remove();

        this.updateActive();
    },

    /**
     * Возвращает объект окна по ID
     *
     * @param id
     * @returns {boolean}
     */
    getCallerWinById: function(id) {
        this.list.forEach(function (callWin) {
            if (callWin.id == id) return callWin;
        });

        return false;
    },

    /**
     * Изменяет видимость блока телефонии, в зависимости от наличия активных табов
     * Скрывает табы, если звонков меньше 2х
     */
    updateActive: function() {
        var active = this.paneContainer.children().length > 0;
        this.container.toggleClass('active', active);

        var tabVisible = this.paneContainer.children().length > 1;
        this.tabContainer.toggleClass('hidden', !tabVisible);
    },

    /**
     * Возвращает jquery объект подготовленной вкладки (Таб)
     *
     * @param callerWin
     * @returns {jQuery|HTMLElement}
     */
    _renderTab: function(callerWin) {
        var n = this.list.length + 1;
        var li = $('<li>').attr('data-id', callerWin.id);
        var link = $('<a>').attr({
            href:'#tab-'+callerWin.id,
            role:'tab'
        }).attr('data-toggle', 'tab');

        link.text(callerWin.getTitle()).appendTo(li);

        return li;
    },

    /**
     * Возвращает jQuery объект подготовленного содержимого вкладки
     *
     * @param callerWin
     * @returns {jQuery|HTMLElement}
     */
    _renderTabContent: function(callerWin) {
        var pane = $('<div class="tab-pane tel-pane"/>');
        pane
            .attr('data-id', callerWin.id)
            .attr('id', 'tab-' + callerWin.id)
            .html(callerWin.getContentHtml());
        return pane;
    }
};

/**
 * Класс одного всплывающего окна
 */
telephony.callerWindow = function(id) {
    this.id = id;
    this.contentHtml = '';

    /**
     * Устанавливает HTML содержимого всплывающего окна
     *
     * @param html
     */
    this.setContentHtml = function(html) {
        this.contentHtml = html;
    };

    /**
     * Возвращает содержимое всплывающего окна
     *
     * @returns string
     */
    this.getContentHtml = function() {
        return this.contentHtml;
    };

    /**
     * Устанавливает заголовок вкладки окна
     *
     * @param title
     */
    this.setTitle = function(title) {
        this.title = title;
    };

    /**
     * Возвращает заголовок вкладки окна
     *
     * @returns string
     */
    this.getTitle = function() {
        return this.title;
    };
};

/**
 * Менеджер, управляющий отображением и обновлением
 * содержимого окон при наступлении различных событий
 */
telephony.eventManager = {

    init: function() {
        $('body').on('rs-event-crm.telephony.event', telephony.eventManager.onEvent);
        if (global.currentTelephonyMessages) {

            for(var key in global.currentTelephonyMessages) {
                this.onEvent(null, global.currentTelephonyMessages[key]); //Инициализируем текущие сообщения телефонии
            }

            telephony.tabManager.setActiveTab();
        }
    },

    onEvent: function(event, data) {
        var winId = data.id;
        if (winId) {
            if (data.closeCall) {

                telephony.tabManager.removeTab(winId);

            } else {

                var win = telephony.tabManager.getCallerWinById(winId);
                if (!win) {
                    win = new telephony.callerWindow(winId);
                }

                win.setContentHtml(data.html);
                win.setTitle(data.username);
                telephony.tabManager.addTab(win, event === null, event === null);
            }
        }
    }
};


/**
 * Инициализирует работу всплывающих окон телефонии
 */
$(function() {
    telephony.tabManager.init();
    telephony.eventManager.init();
});