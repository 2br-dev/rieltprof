(function( $ ){
    $.fn.photoSlider = function( method ) {
        var defaults = {
            interval: 10 * 1000,
            item:'.item',
            itemActClass: 'act',
            selector: '.pages a',
            selectorActClass: 'act',
            prev: '.prev',
            next: '.next',
            current: 1,
            max: 1,
            counter: 0,
            intervalHandler: null
        },
        args = arguments;
        
        return this.each(function() {
            var $this = $(this),
                data = $this.data('photoslider');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('photoslider', data);
                    data.options = $.extend({}, defaults, initoptions);
                    
                    data.options.max = $(data.options.item, $this).length;
                    autoPlay();
                    $(data.options.selector, $this).click(function() {
                        methods.set($(this).index()+1);
                        return false;
                    });
                    $(data.options.prev, $this).click(methods.prev);
                    $(data.options.next, $this).click(methods.next);
                  
                },
                next: function() {
                    if (data.options.current<data.options.max) {
                        methods.set(data.options.current+1);
                    }
                    return false;
                },
                prev: function() {
                    if (data.options.current>1) {
                        methods.set(data.options.current-1);
                    }
                    return false;                    
                },
                set: function(n, autoplay) {
                    if (!autoplay) clearInterval(data.options.intervalHandler);
                    if ($(data.options.item+':eq('+(n-1)+')', $this).is('.'+data.options.itemActClass+',.transform')) return false;
                    $(data.options.item+':eq('+(n-1)+')', $this).addClass('transform').fadeIn(function() {
                        $(data.options.item+'.'+data.options.itemActClass, $this).hide().removeClass(data.options.itemActClass);
                        $(this).removeClass('transform');
                        $(this).addClass(data.options.itemActClass);
                    });
                    
                    $(data.options.selector, $this).removeClass(data.options.selectorActClass);
                    $(data.options.selector+':eq(' + (n-1) + ')', $this).addClass(data.options.selectorActClass);
                    
                    $(data.options.prev, $this).toggle(n > 1);
                    $(data.options.next, $this).toggle(n < data.options.max);
                    data.options.current = n;
                }
            };
            
            //private 
            var autoPlay = function() {
                data.options.intervalHandler = setInterval(function() {
                    var current = (data.options.current >= data.options.max) ? 1 : data.options.current + 1;
                    //if (data.options.counter == data.options.max-1) clearInterval(data.options.intervalHandler);
                    
                    methods.set(current, true);
                    data.options.counter++;
                }, data.options.interval);
            };

            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }
})( jQuery );

$(function() {
    $('.bannerSlider').photoSlider();
});