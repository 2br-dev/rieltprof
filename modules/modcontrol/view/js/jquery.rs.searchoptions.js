/**
 * Плагин, активирующий поиск по настройкам модулей
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.searchOptions = function( method ) {
        var defaults = {
                term:'.term'
            },
            args = arguments;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('searchOptions');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('searchOptions', data);
                    data.opt = $.extend({}, defaults, initoptions);

                    $this.on('keyup', data.opt.term, onWrite);
                },

                search:function(term) {

                    clearTimeout(data.timer);
                    data.timer = setTimeout(function() {

                        $.ajaxQuery({
                            url: $this.data('searchUrl'),
                            data: {
                                term: term
                            },
                            success: function(response) {

                                if (response.success) {
                                    $('.result-zone').html(response.html);
                                }

                            }
                        });

                    }, 300);
                }
            };

            //private
            var onWrite = function(e) {
                var _this = this;
                setTimeout(function() {
                    var code = e.keyCode;
                    if(code >= 37 && code <= 40) {
                        return;
                    }
                    var term = $(_this).val();
                    methods.search(term);

                }, 10);
            };

            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }

    $.contentReady(function() {
        $('.search-options-block', this).searchOptions();
    });
})( jQuery );