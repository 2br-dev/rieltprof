/**
* Активирует переключение табов
*/
(function( $ ){
    $.fn.activeTabs = function( method ) {
        var defaults = {
            tabList: '.tabList',
            tabFrame: '.tabFrame',
            tab: 'li a', 
            activeClass: 'act'
        },
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                $tabList,
                data = $this.data('activeTabs'),
                tabInputName, removeClasses;
                

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('activeTabs', data);
                    data.options = $.extend({}, defaults, initoptions);
                    $tabList = $(data.options.tabList, $this);
                    
                    if ($('[data-input-val]', $tabList).length) {
                        tabInputName = $this.data('inputName');
                        if (!tabInputName) tabInputName = 'activeTab';
                    }                    
                    $('[data-class]', $tabList).each(function() {
                        removeClasses = removeClasses + ' ' + $(this).data('class');
                    });
                    $(data.options.tab, $tabList).click(changeTab);
                }
            };
            
            //private 
            var changeTab = function() {
                $(data.options.tab, $tabList).removeClass(defaults.activeClass);
                $(this).addClass(defaults.activeClass);
                
                var openTab = $(this).data('tab');
                
                if (removeClasses) {
                    $(data.options.tabFrame, $this).removeClass(removeClasses);
                    
                    var addClassName = $(this).data('class');
                    if (addClassName) {
                        $(openTab, $this).addClass(addClassName);
                    }
                }
                
                if (tabInputName) {
                    var value = $(this).data('inputVal');
                    var input = $('input[name="'+tabInputName+'"]');
                    if (!input.length) {
                        input = $('<input name="'+tabInputName+'" type="hidden">').appendTo($this);
                    }
                    input.val(value).trigger('change');
                }
                
                $(data.options.tabFrame, $this).hide();
                $(openTab, $this).show();
                
                if (data.options.onTabChange) data.options.onTabChange.call($(openTab).get(0));
                return false;                
            }

            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }

})( jQuery );