/**
* Плагин, активирует верхнее меню
* @author ReadyScript lab.
*/
(function( $ ){
  $.fn.mainmenu = function( options ) {  
    options = $.extend({
        escapeDelay: 30
    }, options);
      
    return $(this).each(function() {
        var menu = this;
        $('.node', this).hover(function() {
            var _this = this;
            clearTimeout($('>ul', this).data('timer'));
            $(this).addClass('over');
            $('>ul', this).show();
        }, function() {
            var _this = this;            
            clearTimeout($('>ul', this).data('timer'));
            $('>ul', this).data('timer', setTimeout(function() {
                $('>ul', _this).hide();
                $(_this).removeClass('over');
            }, options.escapeDelay));
        });
    });
  }
})( jQuery );