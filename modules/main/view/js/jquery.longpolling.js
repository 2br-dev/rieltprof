/**
 * Скрипт обеспечивает работу механизма мгновенного получения
 * уведомлений авторизованным администратором из backend.
 *
 * Скрипт бросает событие rs-event-<ИМЯ СОБЫТИЯ> на элемент body.
 * Для подписки на событие, следует выполнить следующий код:
 * $('body').on('rs-event-test', function(e, data) {
 *      //data - данные
 * });
 */
(function($) {
    $.longPolling = function (method) {
        var defaults = {
                requestRepeatInterval: 1000
            },
            args = arguments,
            $this = $('body');

        var data = $this.data('longPolling');
        if (!data) { //Инициализация
            data = {
                options: defaults
            };
            $this.data('longPolling', data);
        }

        var methods = {
            start: function() {

                var lastId = global.longPollingLastId;

                var pollRequest = function() {

                    if (data.xhr) {
                        data.xhr.abort();
                    }

                    data.xhr = $.ajax({
                        url: global.longPollingUrl,
                        data: {
                            last_id: lastId
                        },
                        dataType:'json',
                        success: function(response) {
                            if (response.success) {
                                $.each(response.events, function(i, event) {
                                    $this.trigger('rs-event-' + event.event_name, [event.event_data]);
                                });
                                lastId = response.last_id;
                            }
                        },
                        complete: function() {
                            data.timeout = setTimeout(pollRequest, data.options.requestRepeatInterval);
                        }
                    });
                };

                pollRequest();
            },

            stop: function() {
                clearTimeout(data.timeout);
                if (data.xhr) {
                    data.xhr.abort();
                }
            }
        };

        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else {
            return methods.start.apply( this );
        }
    };

    $(function() {
        if (global.enableLongPolling) {
            $.longPolling();
        }
    });
})(jQuery);