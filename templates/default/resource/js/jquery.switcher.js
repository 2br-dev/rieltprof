/**
* Плагин предназначени для переключения состояния элементов, путем добавления дополнительного класса
*/
$.fn.switcher = function( method ) {  
    var defaults = {
        parentSelector: null,
        onClass: 'on'
    }, 
    args = arguments;

    return this.each(function() {
        var $this = $(this), 
            data = $this.data('switcher');

        var methods = {
            init: function(initoptions) {
                if (data) return;
                data = {}; $this.data('switcher', data);
                
                data.options = $.extend({}, defaults, initoptions);
                data.options.onText = $this.data('onText');
                data.options.offText = $this.html();
                methods.checkText();
                $this.on('click', methods.toggle);
                if ($this.data('cookieId') && $.cookie) {
                    $.cookie('switcher-'+$this.data('cookieId')) == 1 ? methods.switchOn() : methods.switchOff();
                }
            },
            toggle: function() {
                getSwitchElement().toggleClass( data.options.onClass );
                methods.checkText();
                setCookie();
                return false;
            },
            switchOn: function() {
                getSwitchElement().addClass( data.options.onClass );
                methods.checkText();
                setCookie();
                return false;
            },
            switchOff: function() {
                getSwitchElement().removeClass( data.options.onClass );
                methods.checkText();
                setCookie();
                return false;                
            },
            checkText: function() {                
                if (getSwitchElement().hasClass(data.options.onClass)) {
                    $this.html(data.options.onText);
                } else {
                    $this.html(data.options.offText);
                }
            }
        }
        
        var getSwitchElement = function() {
            return (data.options.parentSelector) ? $this.parents(data.options.parentSelector) : $this;
        },
        setCookie = function() {
            if ($this.data('cookieId') && $.cookie) {
                $.cookie('switcher-'+$this.data('cookieId'), (+getSwitchElement().hasClass(data.options.onClass)), {path: '/'} );
            }            
        };
        
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, args );
        }            
    });
}  

$(function() {
    $('.rs-parent-switcher').switcher({parentSelector: '*:first'});
});