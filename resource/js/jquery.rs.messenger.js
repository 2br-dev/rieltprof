/**
 * Плагин обеспечивает отображение всплывающих сообщений в адмнистративной панели.
 * Пример вызова:
 * $.messenger('hello!'); //Отобразит обычное сообщение
 * $.messenger('show', {text: 'Hello!', theme: 'error'}); //Отобразит сообщение в красном окне
 *
 * @author ReadyScript lab.
 */
(function($) {
    $.messenger = function(method) {
        var defaults = {
            offsetY: 70, //Стартовое смещение по Y
            msg: {
                timer: null,
                theme: '', //Класс сообщений
                distance: 10, //Расстояние между сообщениями
                expire: 20, //В секундах время отображения сообщения
                stopExpireOnClick: true
            }
        }, 
        args = arguments,
        
        $this = $('#messages-container');
        if (!$this.length) {
            $this = $('<div id="messages-container"></div>').appendTo('body');
        }
        var data = $this.data('messenger');
        if (!data) { //Инициализация
            data = {
                options: defaults
            }; 
            $this.data('messenger', data);
        }
        
        var methods = {
            
            show: function(parameters) {
                var $box = getMessageBox(parameters);
                var local_params = $.extend({}, data.options.msg, parameters);
                $box.data('messenger', local_params);
                
                var offset = +(defaults.offsetY);
                var messages = $('.message-box', $this);
                for( var i=messages.length-1; i>=0; i-- ) {
                    offset = offset + $(messages[i]).height() + (local_params.distance);
                }
                
                $box.css({
                    bottom: offset+'px'
                })
                $box
                    .hover(function() {
                        local_params.pause = true;
                    }, function() {
                        local_params.pause = false;
                    })
                    .on('messenger.close', closeBox)
                    .on('click.messenger', '.close', function() {
                        $(this).closest('.message-box').trigger('messenger.close');
                    })
                    .appendTo($this).fadeIn();
                    
                if (local_params.stopExpireOnClick) {
                    $box.on('mousedown.messenger', stopExpire);
                }
                
                if (local_params.expire) {                    
                    local_params.timer = setTimeout(function() {
                        $box.trigger('messenger.close');
                        if (!local_params.pause) {
                            $box.trigger('messenger.close');
                        } else {
                            $box.one('mouseleave.messengerOne', function() {
                                $box.trigger('messenger.close');
                            });
                        }
                    }, local_params.expire * 1000);
                }
            },
            
            update: function() {
                
                var messages = $('.message-box', $this);
                var newOffset = {};
                
                var offset = +(defaults.offsetY);
                newOffset["0"] = offset;
                for( var i=0; i<messages.length; i++ ) {
                    offset = offset + $(messages[i]).height() + ($(messages[i]).data('messenger').distance);
                    newOffset[i+1] = offset;
                }
                
                messages.each(function(i) {
                    $(this).animate({
                        bottom: newOffset[i]+'px'
                    }, 'fast');
                });
            },
            
            hideAll: function() {
                $('.message-box', $this).trigger('messenger.close');
            },
            
            setOptions: function(options) {
                data.options = $.extend(data.options, options);
            }
        }
        
        //private 
        var getMessageBox = function(parameters) {
            return $('<div class="message-box"></div>')
                    .append('<a class="close"></a>')
                    .append($('<div class="msg"></div>').html(parameters.text))
                    .addClass(parameters.theme)
                    .hide();
        },
        
        stopExpire = function() {
            var box = $(this);
            clearTimeout(box.data('messenger').timer);
            box.unbind('.messengerOne');
        },

        closeBox = function() {
            var box = $(this);
            clearTimeout(box.data('messenger').timer);
            box.fadeOut('fast', function() {
                box.remove();
                methods.update();
            });
        };
        
        
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object') {
            return methods.init.apply( this, args );
        } else {
            var params = Array.prototype.slice.call( args, 1 );
            var extend = {text: method};
            if (!params[0]) params[0] = {};
            params[0] = $.extend(params[0], extend);
            methods['show'].apply( this, params );
        }
    }

})(jQuery);