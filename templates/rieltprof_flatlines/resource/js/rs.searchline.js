/* Скрипт инициализирует "живой" поиск. Зависит от jquery.autocomplete */

$(function() {
    //Инициализируем быстрый поиск по товарам
    $(window).resize(function() {
        $( ".rs-autocomplete" ).autocomplete( "close" );
    });

    /**
     * Автозаполнение в строке поиска
     */
    $( ".rs-autocomplete" ).each(function() {
        var queryBox = $(this).closest('form');

        $(this).autocomplete({
            source: $(this).data('sourceUrl'),
            appendTo: queryBox,
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
            ul.addClass('search-items');
            var li = $( "<li />" );
            var link_class = "";
            // if (item.image){
            //     var img = $('<img />').attr('src', item.image).css('visibility', 'hidden').load(function() {
            //         $(this).css('visibility', 'visible');
            //     });
            //     li.append($('<div class="image" />').append(img));
            // }else{
            //     link_class = "class='noimage'";
            // }

            if (item.type == 'search'){
                li.addClass('all-search-results');
            }

            var item_html = '<a '+link_class+'><span class="title">' + item.label + '</span>';
            if (item.barcode){ //Если артикул есть
                // item_html += '<span class="barcode">' + item.barcode + '</span>';
            }else if (item.type == 'brand'){
                // item_html += '<span class="barcode">' + lang.t('Производитель') + '</span>';
            }else if (item.type == 'category'){
                item_html += '<span class="barcode">' + lang.t('Категория') + '</span>';
            }
            if (item.price){ //Если цена есть
                // item_html += '<span class="price">' + item.price + '</span>';
            }
            if (item.preview){ //Если цена превью (для статей)
                // item_html += '<span class="preview">' + item.preview + '</span>';
            }
            item_html += '</a>';

            return li
                .append( item_html )
                .appendTo( ul );
        };
    });
});
