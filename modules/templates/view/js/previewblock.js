/**
 * Плагин, отвечающий за отображние блока предварительного просмотра HTML-кода
 */
$.fn.previewBlock = function(method) {
    var defaults = {
            previewCode: '.previewCode',
            previewBody: '.previewBody'
        },
        args = arguments;

    return this.each(function() {
        var $this = $(this),
            data = $this.data('previewBlock');

        //public
        var methods = {
            init: function(initoptions) {
                if (data) return;
                data = {}; $this.data('previewBlock', data);
                data.options = $.extend({}, defaults, initoptions);

                $('textarea, input, select', $this).on('change', update);
                $('input[type=text]', $this).on('keyup', function() {
                    clearTimeout(data.timeout);
                    data.timeout = setTimeout(update, 500);
                });
                update();
            }
        };

        //private
        var update = function() {
            var params = $('form', $this).serializeArray();
            if (data.xhr) {
                data.xhr.abort();
            }
            data.xhr = $.ajax({
                method:'POST',
                dataType:'json',
                url: $(data.options.previewCode, $this).data('url'),
                data: params,
                success: function(response) {
                    $(data.options.previewBody, $this).html(response.html);
                }
            });
        };

        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, args );
        }
    });

};

$.contentReady(function() {
    $('.previewConstructor').previewBlock();
});