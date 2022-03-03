try {
  document.execCommand('BackgroundImageCache', false, true);
} catch(e) {}


/**
* Объект - блок меню.
*/
menuItem = function(parent, li)
{
	var _this = this;
	var timeout;
	var is_node;
	
	this.init = function()
	{
		is_node = $(li).hasClass('node');
		$('>.mblock ul:first>li.node', li).each(function() {
			new menuItem(_this, this); //Рекурсивно создаем подпункты
			
		});
		if (parent) {
			$(li).bind('mouseover',_this.over);
   		   	$(li).bind('mouseout',_this.out);			
		}
	};
	
	this.over = function()
	{
		clearTimeout(timeout);
        $('>.mblock', this).css('visibility','visible');
        $(this).addClass('hover');
	};
	
	this.out = function()
	{
        if (is_node) {
			clearTimeout(timeout);
			timeout = setTimeout(_this.realclose,10);        
		} else {
			$('>.mblock', this).css('visibility','hidden');
			$(this).removeClass('hover');
		}
	};
	
	this.realclose = function()
	{
		$('>.mblock', li).css('visibility','hidden');
		$(li).removeClass('hover');
	};
		
	this.init();
};


$(document).ready(function()
{
   $('#menu ul').each(function() {
       var maxwidth = 0;
       $('>li', this).each(function()
       {
           if (maxwidth< $(this).width()) maxwidth = $(this).width();
       });

       $('>li', this).width(maxwidth);
   });
 }); 
