/** Инициализация блока тегов */
$(function()
{
    $('.tags').each(function()
    {
        new ctags({element: this, form: 'form'});
    });
});


/**
* Класс управления тегами
*/
ctags = function(options)
{
    var _this = this;
    var context = options.element;
    var word_container = $('.word_container');
    
    this.init = function()
    {
        _this.type = $(context).attr('type');
        _this.linkid = $(context).attr('linkid');
        $(options.form, context).submit(_this.onSubmit);
        $('.tagdel', context).click(_this.del);
    }
    
    this.onSubmit = function()
    {
        var action = $(this).attr('action');
        var data = $(this).serializeArray();
        $.post( action + '&random=' + Math.random(), data, function(return_data) 
        {
            _this.refresh();
            $('.autocomplete', context).val('');
        });
        return false;
    }
    
    this.refresh = function()
    {
        $.get('/ajax.php?mod=MTags_Adm_Block&kdo=getWords', {type: _this.type, link_id: _this.linkid}, function(data)
        {
            $('.tagdel', context).unbind('click');
            word_container.html(data);
            $('.tagdel', context).click(_this.del);
        });
    }
    
    this.del = function()
    {
        if (confirm(lang.t('Вы действительно хотите удалить ключевое слово?')))
        {
            var lid = $(this).attr('lid');
            $.getJSON('/ajax.php?mod=MTags_Adm_Block&kdo=del', {lid:lid, linkid:_this.linkid}, function(return_data)
            {
                if (return_data.status == 'ok') _this.refresh();
            });
        }
    }
    
    this.init();
}

/** Активируем autocomplete **/

$(function() 
{
    function split( val ) {
        return val.split( /,\s*/ );
    }
    function extractLast( term ) {
        return split( term ).pop();
    }

    $( ".tags .autocomplete" ).autocomplete({
        source: function( request, response ) {
            $.getJSON( "/ajax.php?mod=MTags_Adm_Block&kdo=getHelpList", {
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
});