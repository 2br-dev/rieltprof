/**
 * Jquery плагин, инициализирует произвольные поля (ключ-значение) в административной панели
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.variableList = function( method ) {
        var defaults = {
                addLine: '.add-line',
                table: '.variable-list_table',
                newLine: '.new-line',
                deleteRow: '.delete-row'
            },
            args = arguments;

        return this.each(function() {
            var $this = $(this),
                currentEdit,
                data = $this.data('variableList');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {};
                    $this.data('variableList', data);
                    data.opt = $.extend({}, defaults, initoptions);
                    data.table = $this.find(data.opt.table);

                    $(data.opt.newLine + ' [name]', $this).attr('disabled', true);

                    $this
                        .on('click', data.opt.deleteRow, function(){
                            $(this).closest('tr').remove();
                        })
                        .on('click', data.opt.addLine, function() {

                            var template = $(data.opt.newLine, $this).clone();
                            $('[disabled]', template).removeAttr('disabled');

                            var num = Math.random().toString(36).substr(2, 9);
                            template = template.html().replace(/%index%/g, num);
                            $('tbody', data.table).append( $('<tr></tr>').html(template) ).trigger('new-content');
                        });

                    $.contentReady(function() {
                        $('table:has(".vl-dndsort")', $this).tableDnD({
                            dragHandle: ".drag-handle",
                            onDragClass: "in-drag"
                        });
                    })

                }

            };


            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        })
    };

    $.contentReady(function() {
        $('.variable-list').variableList();
    });

})( jQuery );