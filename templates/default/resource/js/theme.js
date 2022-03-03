/**
* Скрипт инициализирует стандартные функции для работы темы
*/

$.detectMedia = function( checkMedia ) {
    var init = function() {
        var detectMedia = function() {
            var 
                currentMedia = $('body').data('currentMedia'),
                newMedia = '';
                
            if ($(document).width() < 760) {
                newMedia = 'mobile';
            }
            if ($(document).width() >= 760 && $(document).width() <= 980) {
                newMedia = 'portrait';
            }
                        
            if (currentMedia != newMedia) {
                $('body').data('currentMedia', newMedia);
            }
        }        
        $(window).on('resize.detectMedia', detectMedia);
        detectMedia();
    }
    
    var check = function(media) {
        return $('body').data('currentMedia') == media;
    }
    
    if (checkMedia) {
        return check(checkMedia);
    } else {
        init();
    }
};

//Инициализируем работу data-href у ссылок
$.initDataHref = function() {
    $('a[data-href]:not(.addToCart):not(.applyCoupon):not(.ajaxPaginator)').on('click', function() {
        if ($.detectMedia('mobile') || !$(this).hasClass('inDialog')) {
            location.href = $(this).data('href');
        }
    });
};

//Инициализируем работу блока, скрывающего длинный текст
$.initCut = function() {
    $('.rs-cut').each(function(){
        $(this).css('max-height', ($(this).data('cut-height')) ? $(this).data('cut-height') : '200px');
        $(this).append('<div class="cut-switcher"></div>');
        $(this).children().last().click(function(){
            if ($(this).parent().hasClass('open')) {
                $(this).parent().css('max-height', ($(this).parent().data('cut-height')) ? $(this).parent().data('cut-height') : '200px');
            } else {
                $(this).parent().css('max-height', '10000px');
            }
            $(this).parent().toggleClass('open');
        });
    });
};

$(function() {
    
    //Решение для корректного отображения масштаба в Iphone, Ipad
    if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i)) {
        var viewportmeta = document.querySelector('meta[name="viewport"]');
        if (viewportmeta) {
            viewportmeta.content = 'width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0';
            document.body.addEventListener('gesturestart', function () {
                viewportmeta.content = 'width=device-width, minimum-scale=0.25, maximum-scale=1.6';
            }, false);
        }
    }//----
    
    $.cart(); //Инициализируем корзину
    $('.inDialog').openInDialog();
    $('.activeTabs').activeTabs();    
    $('input[type="checkbox"]:not(.noStyle)').each(function() {
        if (!$(this).parents('.admin-style').length) $(this).styler();
    });
    $.detectMedia();
    $.initDataHref();
    $.initCut();

    //Инициализируем быстрый поиск по товарам
    $(window).resize(function() {
        $( ".searchLine .query.autocomplete" ).autocomplete( "close" );
    });
    
    //Инициализируем открытие картинок во всплывающем окне
    $('a[rel="lightbox"], .lightimage').colorbox({
       rel:'lightbox',
       className: 'titleMargin',
       opacity:0.2
    });

    $('[data-toggle="popover"]').popover({placement : 'auto', trigger: 'click'});
    $(document).on('click', function (e) {
        $('[data-toggle="popover"],[data-original-title]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {                
                (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
            }

        });
    });

    /**
    * Автозаполнение в строке поиска
    */
    $( ".searchLine .query.autocomplete" ).each(function() {
        $(this).autocomplete({
            source: $(this).data('sourceUrl'),
            appendTo: '#queryBox',
            minLength: 3,
            select: function( event, ui ) {
                location.href=ui.item.url;
                return false;
            },
            messages: {
                noResults: '',
                results: function() {}
            }
        }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
            ul.addClass('searchItems');
            var li = $( "<li />" );
            var link_class = "";
            if (item.image){
                var img = $('<img />').attr('src', item.image).css('visibility', 'hidden').load(function() {
                    $(this).css('visibility', 'visible');
                });    
                li.append($('<div class="image" />').append(img));
            }else{
                link_class = "class='noimage'";
            }

            if (item.type == 'search'){
                li.addClass('allSearchResults');
            }
            
            
            var item_html = '<a '+link_class+'><span class="label">' + item.label + '</span>';
            if (item.barcode){ //Если артикул есть
                item_html += '<span class="barcode">' + item.barcode + '</span>';
            }else if (item.type == 'brand'){
                item_html += '<span class="barcode">' + lang.t('Проиводитель') + '</span>';
            }else if (item.type == 'category'){
                item_html += '<span class="barcode">' + lang.t('Категория') + '</span>';
            }
            if (item.price){ //Если цена есть
                item_html += '<span class="price">' + item.price + '</span>';
            }
            if (item.preview){ //Если цена превью (для статей)
                item_html += '<span class="preview">' + item.preview + '</span>';
            }
            item_html += '</a>';        
            
            return li
                .append( item_html )         
                .appendTo( ul );
        };
    });     
}); 

//Инициализируем обновляемые зоны
$(window).bind('new-content', function(e) {
    $('.activeTabs', e.target).activeTabs({
        onTabChange: function() {
            if ($(this).closest('#colorbox')) $.colorbox.resize();
        }
    });

    $('input[type="checkbox"]', e.target).each(function() {
        if (!$(this).parents('.admin-style').length) $(this).styler();
    });
    $('.inDialog', e.target).openInDialog();
    $('.rs-parent-switcher', e.target).switcher({parentSelector: '*:first'});
    $.initDataHref();
    $.initCut();
});