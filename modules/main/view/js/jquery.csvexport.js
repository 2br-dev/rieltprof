/**
* Плагин, инициализирующий работу диалогов импорта/экспорта в формате CSV
*/
(function( $ ){
    $.fn.csvExport = function( method ) {
        var defaults = {
            maps: '.maps',
            saveMap: '.saveMap',
            removeMap: '.removeMap',
            source: '.source', 
            destination: '.destination',
            add: '.add',
            remove: '.remove',
            moveUp: '.up',
            moveDown: '.down'
        },
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('csvExport');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('csvExport', data);
                    data.options = $.extend({}, defaults, initoptions);
                    $this
                        .on('click', data.options.add, addColumns)
                        .on('click', data.options.remove, removeColumns)
                        .on('click', data.options.moveUp, moveUp)
                        .on('click', data.options.moveDown, moveDown)
                        .on('click', data.options.saveMap, saveMap)
                        .on('click', data.options.removeMap, removeMap)
                        .on('change', data.options.maps, selectMap);
                }, 
            }
            
            //private
            var addColumns = function() {
                var dst = $(data.options.destination, $this);
                $('option:selected', $(data.options.source, $this)).appendTo(dst);
                
            },
            removeColumns = function() {
                var src = $(data.options.source, $this);
                $('option:selected', $(data.options.destination, $this)).appendTo(src);
            },
            moveUp = function() {
                var dst = $(data.options.destination, $this);
                $('option:selected', dst).each(function() {
                    var prev = $(this).prev();
                    if (prev.length && !prev.is(':selected')) {
                        $(this).insertBefore(prev);
                    }
                });
            },
            moveDown = function() {
                var dst = $(data.options.destination, $this);
                $( $('option:selected', dst).get().reverse() ).each(function() {
                    var next = $(this).next();
                    if (next.length && !next.is(':selected')) {
                        $(this).insertAfter(next);
                    }
                });                
            },
            saveMap = function() {
                var columns = {};
                
                $('option', $(data.options.destination, $this)).each(function() {
                    columns[$(this).val()] = $(this).text();
                });
                if ($.isEmptyObject(columns)) return;                
                var title = prompt(lang.t('Введите название предустановки'), "");
                if (title) {
                    
                    $.ajaxQuery({
                        url: $(this).data('url'),
                        type: 'post',
                        data: {
                            title: title,
                            columns: columns
                        },
                        success: function(response) {
                            $(data.options.maps).append($(response.html).prop('selected', 'selected'));
                        }
                    });
                }
            },
            removeMap = function() {
                var map_id = $(data.options.maps).val();
                if (map_id>0 && confirm(lang.t('Вы действительно хотеле удалить предустановку?'))) {
                    $.ajaxQuery({
                        url: $(this).data('url'),
                        type: 'post',
                        data: {
                            id: map_id
                        },
                        success: function() {
                            $('option:selected', $(data.options.maps)).remove();
                            $(data.options.maps).val(0);
                        }
                    });
                }
            },
            selectMap = function() {
                if ($(this).val() > 0) {
                    var item=$('option:selected', this),
                        map = item.data('value'),
                        source = $(data.options.source, $this),
                        destination = $(data.options.destination, $this);
                    
                    $('option', destination).appendTo(source);
                    for(var key in map) {
                        $('option[value="'+key+'"]', source).appendTo(destination);
                    }
                }
            };
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }

    
    $.fn.csvImport = function( method ) {
        var defaults = {
            maps: '.maps',
            saveMap: '.saveMap',
            removeMap: '.removeMap',
            destination: '.destination',
        },
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('csvExport');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('csvExport', data);
                    data.options = $.extend({}, defaults, initoptions);
                    $this
                        .on('click', data.options.saveMap, saveMap)
                        .on('click', data.options.removeMap, removeMap)
                        .on('change', data.options.maps, selectMap);
                }, 
            }
            
            //private
            var saveMap = function() {
                
                var columns = {};
                $(data.options.destination, $this).each(function() {
                    columns[$(this).data('name')] = $(this).val();
                });
                if ($.isEmptyObject(columns)) return;
                                
                var title = prompt(lang.t('Введите название предустановки'), "");
                if (title) {
                    
                    $.ajaxQuery({
                        url: $(this).data('url'),
                        type: 'post',
                        data: {
                            title: title,
                            columns: columns
                        },
                        success: function(response) {
                            $(data.options.maps).append($(response.html).prop('selected', 'selected'));
                        }
                    });
                }
            },
            removeMap = function() {
                var map_id = $(data.options.maps).val();
                if (map_id>0 && confirm(lang.t('Вы действительно хотеле удалить предустановку?'))) {
                    $.ajaxQuery({
                        url: $(this).data('url'),
                        type: 'post',
                        data: {
                            id: map_id
                        },
                        success: function() {
                            $('option:selected', $(data.options.maps)).remove();
                            $(data.options.maps).val(0);
                        }
                    });
                }
            },
            selectMap = function() {
                if ($(this).val() > 0) {
                    var item=$('option:selected', this),
                        map = item.data('value'),
                        destination = $(data.options.destination, $this);
                    
                    for(var key in map) {
                        $(data.options.destination+'[name="columns['+key+']"]', $this).val(map[key]);
                    }
                }
            };
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }    
    
    
})( jQuery );