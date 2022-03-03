/**
* Плагин, активирует блок ввода тегов
*/
(function( $ ){

  $.fn.blocktags = function( options ) {  
      options = $.extend({
          getWordsUrl: '',
          delWordUrl: '',
          getHelpListUrl: ''
      }, options);
      
    return this.each(function() {
        var context = this;
        var type = $(this).data('type');
        var linkid = $(this).data('linkid');
        var word_container = $('.word_container', context);
        
        var onSubmit = function()
        {
            var action = $('.tag_form').data('action');
            var data = {};
            $('.tag_form input[name]').each(function() {
                data[$(this).attr('name')] = $(this).val();
            });
            $.post( action + '&random=' + Math.random(), data, function(return_data) 
            {
                refresh();
                $('.autocomplete', context).val('');
            });
            return false;
        }
        
        var refresh = function()
        {
            $.get(options.getWordsUrl, {type: type, link_id: linkid}, function(data)
            {
                $('.tagdel', context).unbind('click');
                word_container.html(data);
                $('.tagdel', context).click(del);
            });
        }
        
        var del = function()
        {
            if (confirm(lang.t('Вы действительно хотите удалить ключевое слово?')))
            {
                var lid = $(this).data('lid');
                $.getJSON(options.delWordUrl, {lid:lid, linkid:linkid}, function(return_data)
                {
                    if (return_data.success) refresh();
                });
            }
        }
        
        var split = function( val ) 
        {
            return val.split( /,\s*/ );
        }
        
        var extractLast = function( term ) 
        {
            return split( term ).pop();
        }

        $(".autocomplete", context).autocomplete({
            source: function( request, response ) {
                $.getJSON(options.getHelpListUrl, {
                    term: extractLast( request.term )
                }, response );
            },
            search: function() {
                // custom minLength
                var term = extractLast( this.value );
                if ( term.length < 3 ) {
                    return false;
                }
            },
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            select: function( event, ui ) {
                var terms = split( this.value );
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push( ui.item.value );
                // add placeholder to get the comma-and-space at the end
                terms.push( "" );
                this.value = terms.join( ", " );
                return false;
            }
        });        
        
        $('.add-btn', context).click(onSubmit);
        $('.tagdel', context).click(del);
    });
  };
})( jQuery );