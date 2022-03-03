/**
* Данный файл подключается только в административной части сайта и содержит
* компоненты, необходимые для административной части сайта.
*
* @author ReadyScript lab.
*/

(function($) {

    /**
     * Plugin, активирующий отображение элемента в боковой панели
     */
    $.widget("rs.sidePanel", {
        options: {
            title: '', //Заголовок сайд бара. Может быть переназначен через параметр title в ответе на ajax запрос
            className: '', //Имя класса, добавляемого к панели
            position: 'left', //Позиция сайд бара. Возможны значения left, right
            htmlHead: null, //Html, который будет добавлен после заголовка
            activateCustomScroll: true,
            closeOnOverlayClick: true,
            ajaxQuery: null, //параметры для плагина ajaxQuery (выполняет загрузку содержимого через Ajax)
            onLoad: function (e, data) {
            },
            onClose: function (e, data) {
            },
            onShow: function (e, data) {
            }
        },

        _create: function () {
            if (this.options.ajaxQuery) {
                this._load();
            } else {
                this._show();
            }
        },

        close: function () {
            if (!this._trigger('onClose', null, {element: this.element})) return false;
            var self = this;

            if (this.panel) {
                this.panel.removeClass('show');
                setTimeout(function () {
                    self._hideOverlay();
                    self.panel.remove();
                }, 200);
            }
        },

        _load: function () {
            var self = this;
            $.ajaxQuery($.extend(this.options.ajaxQuery, {
                success: function (response) {
                    self.element.html(response.html)
                    self._trigger('onLoad', null, {
                        element: self.element,
                        response: response
                    });
                    self._show(response);
                }
            }));
        },

        _show: function (response) {
            var self = this;

            this._showOverlay();

            var title = (response && response.title) ? response.title : this.options.title;

            this.panel = $('<div class="rs-side-panel">\
                        <div class="rs-side-panel__title">\
                            <a class="rs-side-close"><i class="zmdi zmdi-close"></i>' + title + '</a>\
                        </div>\
                        <div class="rs-side-panel__body">\
                        </div>')
                .addClass(this.options.className)
                .addClass('rs-side-panel_' + this.options.position);

            this.panel.find('.rs-side-close').click(function () {
                self.close()
            });

            if ((response && response.html_head) || this.options.htmlHead) {
                this.panel.find('.rs-side-panel__title')
                    .append(this.options.htmlHead ? this.options.htmlHead : response.html_head);
            }

            var content = this.panel.appendTo('body')
                .find('.rs-side-panel__body')
                .append(this.element);

            $('body').on('keypress.rsSidePanel', function (e) {
                if (e.keyCode == 27) self.close();
            });

            if (this.options.activateCustomScroll) {
                content.mCustomScrollbar({
                    theme: 'minimal-dark',
                    autoHideScrollbar: false,
                    mouseWheel: {preventDefault: true},
                    scrollInertia: 0
                });
            }

            setTimeout(function () {
                self.panel.addClass('show');
            }, 0);
            this.panel.trigger('new-content');

            this._trigger('onShow', null, {
                element: this.element,
                panel: this.panel,
                response: response
            });
        },

        _destroy: function () {
            $('body').off('.rsSidePanel');
        },

        _showOverlay: function () {
            var self = this;
            if (!$('#rs-slide-overlay').length) {
                this.overlay = $('<div id="rs-slide-overlay">').addClass('rs-overlay').appendTo('body');
                if (this.options.closeOnOverlayClick) {
                    this.overlay.click(function () {
                        self.close()
                    });
                }
            }
        },

        _hideOverlay: function () {
            if (this.overlay) {
                this.overlay.remove();
                this.overlay = null;
            }
        }
    });

    /**
     * Plugin, активирующий отображение системных уведомлений
     */
    $.widget("rs.rsAlerts", {
        options: {
            onLoad: function (e, data) {
            }
        },
        _create: function () {
            var self = this;
            this.element.click(function () {
                self.show()
            });
        },
        show: function () {
            if ($.rs.loading.inProgress) return;

            var _this = this;
            this.panel = $('<div>').sidePanel({
                position: 'right',
                ajaxQuery: {
                    url: this.element.data('urls').list
                },
                onLoad: function (e, data) {
                    _this._trigger('onLoad', data);
                }
            }).on('click', '.rs-alert-close', function() {
                var item = $(this).closest('.list-group-item').css('opacity', 0.5);
                var url = $(this).data('url');

                var closeItem = function() {
                    item.remove();
                    var notice_counter = $('.rs-alerts i[data-meter="rs-notice"]');
                    notice_counter.text( notice_counter.text()-1 );
                };

                if (url) {
                    $.ajaxQuery({
                        url: url,
                        loadingProgress: false,
                        success: function (response) {
                            if (response.success) {
                                closeItem();
                            } else {
                                item.css('opacity', 1);
                            }
                        }
                    });
                }

                return false;
            });
        },
        close: function () {
            if (this.panel)
                this.panel.sidePanel('close');
        }
    });

    /**
     * Plugin, активирующий пересчет счетчиков
     */
    $.rsMeters = function (method) {
        var defaults = {
                group: '.rs-meter-group',
                node: '> a .rs-meter-node',
                counter: '.hi-count',
                nextRecalculation: global.meterNextRecalculation,
                recalculationUrl: global.meterRecalculationUrl
            },
            $this = $('body');

        var
            data = $this.data('rsMeters'),
            timeout;

        if (!data) { //Инициализация
            data = {
                options: defaults
            };
            $this.data('rsMeters', data);
        }

        //public
        var methods = {
            init: function (parameters) {
                $(data.options.group).each(function () {
                    calculateNode(this);
                });

                recalculate(data.options.nextRecalculation);
            },

            /**
             * Обновляет счетчик на странице
             * @param params
             */
            update: function (key, value) {
                var arr = {};
                if (typeof(key) != 'object') {
                    arr[key] = value;
                } else {
                    arr = key;
                }

                $.each(arr, function (key, value) {
                    var counter = $(data.options.counter + '[data-meter="' + key + '"]');
                    if (counter.length) {
                        counter.text(formatValue(value))
                            .data('number', value)
                            .toggleClass('visible', value > 0)
                            .parents(data.options.group).each(function () {
                            calculateNode(this);
                        });
                    }
                });
            },

            refresh: function () {
                recalculate(0);
            },

            setOptions: function (options) {
                data.options = $.extend(data.options, options);
            }
        }

        //private
        var
            formatValue = function (sum) {
                return sum > 99 ? '99+' : sum;
            },

            calculateNode = function (node) {
                var sum = 0;
                $(data.options.counter, node).each(function () {
                    if ($(this).data('number')) {
                        sum += parseInt($(this).data('number'));
                    }
                });
                var visibleSum = formatValue(sum);
                $(data.options.node, node).text(visibleSum).toggleClass('visible', sum > 0);
            },
            recalculate = function (timeoutSec) {
                timeout = setTimeout(function () {
                    $.ajax({
                        dataType: 'json',
                        url: data.options.recalculationUrl,
                        success: function (response) {
                            if (response.success) {
                                $.each(response.numbers, function (key, value) {
                                    methods.update(key, value);
                                });

                                //Планируем следующий вызов
                                if (response.nextRecalculation > 0) {
                                    recalculate(response.nextRecalculation);
                                }
                            }
                        }
                    })

                }, timeoutSec * 1000);
            }


        if (methods[method]) {
            methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof(method) === 'object' || typeof(method) == 'undefined') {
            return methods.init.apply(this, arguments);
        }
    }

    $.widget("rs.rsBottomSticky", {
        options: {
            wrapper: '.column',
            placeholderClass: 'sticky-placeholder',
            stickyOnClass: 'sticky-on'
        },

        _create: function () {
            var self = this;

            if (this.element.data('bottomSticky')
                || this.element.hasClass(this.options.placeholderClass))
                return false;

            this.isSticky = false;
            this.placeholder = null;
            this.element.data('bottomSticky', this);
            this.wrapper = this.element.closest(this.options.wrapper);
            this.startClass = this.element.attr('class');

            //this.resize();

            $(window)
                .on('scroll.rsBT', function () {
                    self._scroll();
                })
                .on('resize.rsBT', function () {
                    self.resize();
                });

            this._on(this.wrapper, {
                'stickyUpdate': function () {
                    self.resize()
                }
            });
        },

        _calculatePoints: function () {
            var element = this.placeholder ? this.placeholder : this.element;
            this.bottomToolbarPoint = element.offset().top + element.outerHeight();
        },

        _scroll: function () {
            if (this.wrapper.hasClass(this.options.stickyOnClass)) {
                var bottomBrowserPoint = $(window).height() + $(window).scrollTop();
                var isSticky = bottomBrowserPoint < this.bottomToolbarPoint
                    && bottomBrowserPoint > this.wrapper.offset().top;
            } else {
                var isSticky = false;
            }
            var width = false;

            if (isSticky) {
                //Создаем placeholder
                if (!this.placeholder) {
                    this.placeholder = $('<div>').addClass(this.options.placeholderClass)
                        .addClass(this.startClass)
                        .height(this.element.outerHeight())
                        .insertAfter(this.element);

                    var width = this.placeholder.width();
                }
            } else {
                if (this.placeholder) {
                    this.placeholder.remove();
                    this.placeholder = null;

                    var width = 'auto';
                }
            }

            this.element.toggleClass('sticky', isSticky);
            if (width !== false) {
                this.element.width(width);
            }

            return isSticky;
        },

        resize: function () {
            this._calculatePoints();
            var isSticky = this._scroll();

            if (isSticky) {
                if (this.placeholder) {
                    var width = this.placeholder.width();
                    this.element.width(width);
                } else {
                    this.element.width('auto');
                }
            }
        }
    });


    $(function () {
        $.rsMeters(); //Инициализируем плагин, который будет показывать кол-во новых объектов в админ.панели
        $('.rs-alerts').rsAlerts(); //Инициализируем плагин, который будут показывать системные уведомления в админ.панели

        //Активируем стандартную разметку для переключателей
        var findTarget = function (target) {
            if($(target).data('targetClosest')){
                return $(target).closest($(target).data('targetClosest'));
            }else if($(target).data('targetNext')){
                return $(target).next($(target).data('targetNext'));
            }else{
                return $(target).data('target');
            }
        };

        $('body')
            .on('click', '[data-toggle-class]', function () {
                var element = findTarget(this);
                var state = $(element)
                    .toggleClass($(this).data('toggleClass'))
                    .hasClass($(this).data('toggleClass'));

                $(element).trigger('resize', {source: this});

                var cookieName = $(this).data('toggle-cookie');
                if (cookieName) {
                    $.cookie(cookieName, state ? 1 : null, {
                        expires: 365 * 5,
                        path: '/'
                    });
                }
            })

            .on('click', '[data-add-class]', function () {
                var element = findTarget(this);

                $(element).addClass($(this).data('addClass'));
                $(element).trigger('resize');
            })

            .on('click', '[data-remove-class]', function () {
                var element = findTarget(this);

                $(element).removeClass($(this).data('removeClass'));
                $(element).trigger('resize');
            })

            .on('click', '[data-set-cookie]', function () {
                var cookieName = $(this).data('setCookie'),
                    cookieValue = $(this).data('setCookieValue'),
                    path = $(this).data('setCookiePath');

                if (cookieName) {
                    $.cookie(cookieName, cookieValue ? cookieValue : null, {
                        expires: 365 * 5,
                        path: typeof(path) != 'undefined' ? path : '/'
                    });
                }
            })
            .on('click', '.treebody  a.call-update', function () {
                $(this).closest('.crud-view-table-tree').one('new-content', function () {
                    $(this).removeClass('left-open');
                });
            })
            .on('click', '.categorybody  a.call-update', function () {
                $(this).closest('.crud-view-table-category').one('new-content', function () {
                    $(this).removeClass('left-open');
                });
            })
            .on('click', '[data-side-panel]', function () {
                //Защита от двойного срабатывания
                if ($(this).data('side-panel-lading')) return false;

                var self = this;
                var url = $(this).data('sidePanel');
                var options = $(this).data('sidePanelOptions') ? $(this).data('sidePanelOptions') : {};
                $(this).data('side-panel-lading', true);

                $('<div>').sidePanel($.extend({
                    "ajaxQuery": {
                        url: url
                    },
                    "onClose": function () {
                        $(self).removeData('side-panel-lading');
                    }
                }, options));
            })
            .on('click', '.visible-alerts-block .close', function() {
                //Записываем в cookie информацию о времни закрытия блока уведомлений
                var cookieName = $(this).data('cookieName');
                if (cookieName) {
                    $.cookie(cookieName, $(this).data('cookieValue'), {
                        expires: 365 * 5,
                        path: '/'
                    });
                }

                $(this).closest('.visible-alerts-block').remove();
            })
            .on('click', '.go-to-menu[data-main-menu-index]', function(event) {
                $('#menu-trigger').addClass('toggled');
                $('#sidebar').addClass('sm-opened toggled');
                $('#sidebar .side-main > li').removeClass('open');
                $('#sidebar .side-main > li:eq('+$(this).data('mainMenuIndex')+')').addClass('open');
            });
    });

    $.contentReady(function () {

        //Активируем изменение размера колонок в представлении tree-table
        $('.admin-style .columns .left-column, .resizable-column').resizable({
            handles: 'e',
            create: function (e, ui) {
                this.parentElement = $(this).closest('.columns');
                if (!this.parentElement.length) {
                    this.parentElement = $(this).parent();
                }

                this.dependColumn = $(this.parentElement).find('.depend-resizable-column');

                //Установим ширину по-умолчанию
                if (defaultWidth = +($.cookie('columnResizer-width'))) {
                    $(this).css('flex-basis', defaultWidth);
                }

                if (this.dependColumn.length && defaultWidth) {
                    this.dependColumn
                        .css('flex', 'auto')
                        .css('width', 'calc(100% - '+defaultWidth+'px)');
                }
            },
            resize: function (e, ui) {
                var maxWidth = parseInt($(this.parentElement).width() * 0.5);
                var minWidth = parseInt($(ui.element).data('min-width') ? $(ui.element).data('min-width') : 0);
                if (ui.size.width > maxWidth) {
                    ui.size.width = maxWidth;
                }
                else if (ui.size.width < minWidth) {
                    ui.size.width = maxWidth;
                } else {
                    ui.element.css('flex-basis', ui.size.width);

                    if (this.dependColumn.length) {
                        this.dependColumn
                            .css('flex', 'auto')
                            .css('width', 'calc(100% - ' + ui.size.width + 'px)');
                    }
                }
            },
            stop: function (e, ui) {
                var cookie_options = {
                    expires: new Date((new Date()).valueOf() + 1000 * 3600 * 24 * 365 * 5)
                };
                $.cookie('columnResizer-width', ui.size.width, cookie_options);
            }
        });

        //Активируем прилипающий toolbar
        $('body').off('.rsBT');
        $('.bottom-toolbar:not(.fixed)').rsBottomSticky();
    });

})(jQuery);