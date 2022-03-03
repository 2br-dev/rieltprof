(function($){

    var elems = $([]),
        defaults = {
            timeToTriggered            : 100,
            charsCountToTriggered      : 8,
        };

    $.event.special.barcodescanned = {
        setup: function(data){
            $(this)
                .data( 'barcodescanned', {
                    chars: [],
                    settings: $.extend({}, defaults, data)
                })
                .bind( 'keypress', keypress_handler );
        },
        teardown: function(){
            $(this)
                .removeData( 'barcodescanned' )
                .unbind( 'keypress', keypress_handler );
        }
    };

    function keypress_handler( event ) {

        var elem = $(event.target),
            data = $('body').data( 'barcodescanned' );
        var time_to_triggered               = data.settings.timeToTriggered,
            chars_count_to_triggered        = data.settings.charsCountToTriggered,
            key_code                        = (event.which) ? event.which : event.keyCode;

        if (elem.is('input') || elem.is('textarea')) {
            return;
        }
        if ((key_code >= 65 && key_code <= 90) ||
            (key_code >= 97 && key_code <= 122) ||
            (key_code >= 48 && key_code <= 57) || key_code == 13
        ) {
            data.chars.push(String.fromCharCode(key_code));
        }

        setTimeout(function() {
            if (data.chars.length >= chars_count_to_triggered) {
                    let sku_val = data.chars.join('');
                    $('body').triggerHandler( 'barcodescanned', sku = sku_val);
            }
            data.chars = [];

        }, time_to_triggered);
    }

})(jQuery);

$(function () {
    $('body').on('barcodescanned', function(event, sku) {
        var input = $('.ui-dialog:last input[data-sku]:first');
        if (!input.length) {
            input = $('[data-sku]:first');
        }

        if (input.length) {
            input.val(sku);
            if (input.hasClass('submit')) {
                input.closest('form').submit();
            } else {
                input.trigger($.Event('keypress', {which: 13}));
            }
        }
    });
});