/**
 * JQuery Плагин для выбора пользователя из выпадающего списка
 *
 * @author ReadyScript lab.
 */
(function($) {
    $.fn.objectSelect = function() {

        return this.each(function() {
            var input = this;
            var name  = $(this).data('name');
            var inputHidden;

            if ( $('input[name="'+name+'"]', $(input).parent() ).length>0)
                inputHidden = $('input[name="'+name+'"]', $(input).parent() );

            var min_length = $(this).attr('minLength');
            if(min_length == undefined){
                min_length = 2;
            }

            $( this ).autocomplete({
                appendTo  : $(this).data('autocomplete-body') ? null : $(this).parent(),
                source    : $(this).data('requestUrl'),
                minLength : min_length,
                select    : function( event, ui ) {
                    if (!inputHidden) {
                        inputHidden = $('<input type="hidden">').attr({name: name});
                        inputHidden.insertAfter(input);
                    }
                    inputHidden.val(ui.item.id);
                    inputHidden.trigger("change");
                }
            })
                .keyup(function(e) {
                    if (inputHidden && (e.charCode > 0 || e.keyCode == 8 || e.keyCode == 46) && e.keyCode != 13) {
                        inputHidden.remove();
                        inputHidden = null;
                        var removeEvent = $(input).data('removeEventName');
                        $(input).trigger(removeEvent ? removeEvent : "remove-object");
                    }
                }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                    .data( "item.autocomplete", item )
                    .append( "<a>" + item.label + ' (' + item.id + ")<br><small>" + item.desc + "</small></a>" )
                    .appendTo( ul );
            };

        });
    };

    $.contentReady(function() {
        $('.object-select', this).objectSelect();
    });

})(jQuery);