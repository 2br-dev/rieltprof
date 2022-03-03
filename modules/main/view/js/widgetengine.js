/**
* Plugin, активирующий виджеты ReadyScript
*/
(function($){
    $.fn.adminWidgets = function(method) {
        var defaults = {
            dialogId: 'addWidgetDialog',
            buttons: {
                addDialog: '.addwidget'
            },
            widgetDialog: {
                item: '.item',
                add: '.add',
                remove: '.remove'
            },
            modeMedia: {
                '(min-width:1350px)': 3,
                '(min-width:768px)': 2,
                '(min-width:0)': 1
            },
            urls: {}
        }, 
        $this = this,
        WControl,
        options;
        
        /**
        * Класс управления виджетами на рабочем столе
        */
        CWidgetControl = function(opt)
        {
            var 
                _this = this,
                mode = null;
            
            this.init = function()
            {
                var connectWith = [
                    '.widget-column[data-column="2"], .widget-column[data-column="3"]',
                    '.widget-column[data-column="1"], .widget-column[data-column="3"]',
                    '.widget-column[data-column="1"], .widget-column[data-column="2"]'
                ];
                
                for(var i=1; i<=3; i++) {
                    //Инициализируем функции перетаскивания виджетов
                    $('.widget-column[data-column="'+i+'"]').sortable({
                        handle: '.widget-title',
                        tolerance: 'pointer',
                        connectWith: connectWith[i-1],
                        cursor: 'move',
                        placeholder: "sortable-placeholder",
                        forcePlaceholderSize: true,            
                        stop: function(e, ui) { if (mode) _this.onUpdate(e, ui); }
                    });                
                }              
                
                $('.widget .widget-title').disableSelection();
                
                this.draw();
                $(window).resize(function() {_this.draw()});
                
                $('.widget-change-position').click(function() {
                    $this.toggleClass('edit-mode');
                    if (mode == 1) {
                        $('.widget-column').sortable($this.is('.edit-mode') ? 'enable' : 'disable');
                    }
                });

                $(window).on('widgetAdd', _this.addWidget);
                $(window).on('widgetRemove', _this.removeWidget);
                
                _this.initWidget();
                $(window).on('widgetAfterAdd.wc', _this.initWidget);
            }
            
            this.draw = function() 
            {
                for(var media in opt.modeMedia) {
                    if (window.matchMedia(media).matches) {
                        var newColumns = opt.modeMedia[media];
                        break;
                    }
                }
                
                //Не переинициализируем, если колоночность не изменилась
                if (mode == newColumns) return;
                mode = newColumns;
                
                $('#widget-zone').data('columns', mode);
                
                //Отобразим нужные колонки
                $('.widget-column').each(function() {
                    $(this).toggle( $(this).data('column') <= mode );
                });
                
                //Перебросим виджеты в нужные колонки
                $('#widget-zone .widget').each(function() {
                    var positions = $(this).data('positions');
                    $(this).appendTo('.widget-column[data-column="'+positions[mode]['column']+'"]');
                });
                
                //Сортируем виджеты
                $('.widget-column').each(function() {
                    $('.widget', this).sort(function (a, b) {
                      var contentA = parseInt($(a).data('positions')[mode].position);
                      var contentB = parseInt($(b).data('positions')[mode].position);
                      return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                   }).each(function (_, widget) {
                      $(widget).parent().append(widget);
                   });
                });

                $('.widget-column').sortable(mode == 1 ? 'disable' : 'enable');
            }
            
            this.onUpdate = function(event, ui)
            {
                $(ui.item).trigger('widgetMoved');
                
                //Обновим сортировочные индексы у виджетов
                var toColumn = ui.item.closest('[data-column]');
                ui.item.data('positions')[mode]['column'] = toColumn.data('column');

                this.updatePositions(toColumn);

                $('.widget', toColumn).each(function() {
                    $(this).data('positions')[mode]['position'] = $(this).index();
                });
                
                $.ajaxQuery({
                    url: opt.urls.moveWidget, 
                    data: {
                        mode: mode,
                        wid: ui.item.attr('wid'),
                        col: toColumn.data('column'),
                        pos: ui.item.prevAll().length
                    }
                });
            }

            this.updatePositions = function(column)
            {
                $('.widget', column).each(function() {
                    $(this).data('positions')[mode]['position'] = $(this).index();
                });
            }
            
            this.close = function()
            {
                var $widget = $(this).closest('.widget');
                var wclass = $widget.attr('wclass');
                $widget.trigger('widgetRemove', [wclass, $('#'+options.dialogId).get(0)]);
            }
            
            this.checkEmptyDesktop = function()
            {
                var exists = $('#widget-zone .widget').length;
                $this.toggleClass('empty', !exists);
                $this.toggleClass('cansort', exists > 1);
            }
            
            this.initWidget = function(e, wclass) 
            {
                 var $context = (wclass) ? $(".widget[wclass='"+wclass+"']") : $('body');
                 $('.widget-tools .widget-close', $context).off('.wc').on('click.wc', _this.close);
            }
            
            this.addWidget = function(e, wclass, column, position)
            {
                if (column === null) {
                    //Вычисляем колонку для вставки виджета автоматически
                    column = Math.ceil(mode/2);
                }

                $.ajaxQuery({
                    url: opt.urls.addWidget,
                    data: {
                        wclass:wclass,
                        column: column,
                        position: position,
                        mode:mode
                    },
                    success: function(response) {
                        var widget = $(response.html);
                        var widgetColumn = $('.widget-column[data-column="'+column+'"]');
                        if (position>0) {
                            widget.insertAfter($('.widget:eq(' + (position-1) + ')', widgetColumn));
                        } else {
                            widget.prependTo(widgetColumn);
                        }

                        _this.updatePositions(widgetColumn);
                        _this.checkEmptyDesktop();
                        
                        var onWidgetAdd = function() {
                            $(widget).trigger('widgetAfterAdd', [wclass]); //Сообщаем, что виджет добавлен в колонку
                            $(widget).trigger('new-content'); //Сообщаем, что на странице появился новый контент
                        };

                        //Вызываем событие о добавлении виджета только после загрузки всех скриптов виджета
                        if (typeof($LAB) != 'undefined' && $LAB.loading) {
                            $(window).one('LAB-loading-complete', onWidgetAdd);
                        } else {
                            onWidgetAdd();
                        }
                    }
                });
            },
            
            this.removeWidget = function(e, wclass)
            {
                var widget = $('.widget[wclass="'+wclass+'"]');
                widget.remove();
                
                _this.checkEmptyDesktop();
                $.ajaxQuery({
                    url: opt.urls.removeWidget,
                    data: {wclass:wclass}
                });
            }

        }        
            
        //public
            
        var methods = {
            init: function(initoptions) 
            {
                if ($this.data('propertyBlock')) return false;
                $this.data('propertyBlock', {});
                options = $.extend(defaults, initoptions);
                options.urls = $this.data('widgetUrls');
                
                //Инициализируем управление виджетами на рабочем столе
                WControl = new CWidgetControl(options);
                WControl.init();

                $(options.buttons.addDialog).on('click', methods.addWidgetDialog);
            },
            
            addWidgetDialog: function() 
            {
                if ($.rs.loading.inProgress) return;

                $('<div>').sidePanel({
                    position: 'left',
                    ajaxQuery: {
                        url: options.urls.widgetList
                    },
                    onLoad: function(event, data) {

                        //Инициализируем перетаскивание
                        $('.widget-collection', data.element).sortable({
                            handle: '.move-handle',
                            tolerance: 'pointer',
                            connectWith: '.widget-column[data-column="1"], .widget-column[data-column="2"], .widget-column[data-column="3"]',
                            cursor: 'move',
                            placeholder: "sortable-placeholder",
                            forcePlaceholderSize: true,
                            start: function(e, ui) {
                                $('body').addClass('rs-adding-widget');
                                $(e.target).sortable( "refreshPositions" );
                            },
                            stop: function(e, ui) {
                                $('body').removeClass('rs-adding-widget');
                                var column = ui.item.closest('[data-column]').data('column');
                                if (column == 'source') return false;

                                if ($(e.target).data('cancelling') == true) {
                                    $(e.target).data('cancelling', false);
                                    return false;
                                }

                                var position = $(ui.item).index();
                                var wclass = $(ui.item).data('wclass');
                                ui.item.remove();
                                //Добавляем виджет
                                onAddWidget(data.element, wclass, column, position);
                            }
                        });

                        //Инициализируем добавление кликом
                        $('.widget-collection .item', data.element).click(function(e) {
                            if ($(e.target).closest('.move-handle').get(0) == $('.move-handle', this).get(0)) {
                                return false;
                            }

                            var wclass = $(this).data('wclass');
                            var position = 0;
                            $(this).remove();
                            onAddWidget(data.element, wclass, null, position);
                        });
                    },
                    onClose: function(event, data) {
                        $('.widget-collection', data.element).data('cancelling', true).sortable('cancel');
                    }
                });
            }
        }
        
        //private 
        var onAddWidget = function(panel, wclass, column, position)
        {
            $.when( $(window).trigger('widgetAdd', [wclass, column, position]) )
                .done(checkEmptyCollection(panel));
        },
        checkEmptyCollection = function(panel)
        {
            $('.widget-collection-block', panel).toggleClass('empty', !$('.widget-collection .item').length);
        };

        
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        }
    }
})(jQuery);    

$(function() {
    $('#widgets-block').adminWidgets();
});