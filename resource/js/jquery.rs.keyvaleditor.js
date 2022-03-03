/**
* Плагин инициализирует в административной панели работу редакторов ключ => значение
* зависит от jquery.tableDnd
*
* @author ReadyScript lab.
*/
(function( $ ){

    $.fn.keyvalEditor = function( method ) {
        var defaults = {
            table: '.keyvalTable',
            addButton: '.add-pair',
            removeButton: '.remove',
            line: 'tr',
            tbody: 'tbody'
        },
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('keyvalEditor');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('keyvalEditor', data);
                    data.opt = $.extend({}, defaults, initoptions);
                    data.varName = $this.data('var');
                    $this
                        .on('click', data.opt.addButton, function() { methods.add(); })
                        .on('click', data.opt.removeButton, remove);
                    
                    bindEvents();
                },
                /**
                * Добавляет строку с ключём и значением
                * 
                * @param string key - ключ
                * @param string val - значение
                */
                add: function(key, val) {
                    var line = 
                        $('<tr>'+
                            '<td class="kv-sort">'+
                                '<div class="ksort"><i class="zmdi zmdi-unfold-more"></i></div>'+
                            '</td>'+
                            '<td class="kv-key"><input type="text" name="'+data.varName+'[key][]"></td>'+
                            '<td class="kv-val"><input type="text" name="'+data.varName+'[val][]"></td>'+
                            '<td class="kv-del"><a class="remove zmdi zmdi-delete"></a></td>'+
                        '</tr>');
                    
                    var $new_element = $(line);
                    $(data.opt.tbody, $this).append($new_element);
                    
                    if (key || val) {
                        $new_element.find('.kv-key input').val(key);
                        $new_element.find('.kv-val input').val(val);
                    }
                    
                    checkEmpty();
                    bindEvents();
                },
                
                remove: function(item) {
                    $(item).closest(data.opt.line).remove();
                    checkEmpty();
                    return false;
                }
            }
            
            //private
            var remove = function() {
                methods.remove(this);
            },
            
            bindEvents = function() {
                $(data.opt.table).tableDnD({
                    dragHandle: ".kv-sort",
                    onDragClass: "in-drag",
                });                                        
            },
            
            checkEmpty = function() {
                $(data.opt.table, $this).toggleClass('hidden', $(data.opt.table+' tbody', $this).children().length==0 );
            };
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }

    $.contentReady(function() {
        $('.keyval-container', this).keyvalEditor();
    });

})( jQuery );