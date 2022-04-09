/**
* Plugin, активирующий поиск по городам
* Зависит от jquery.autocomplete
*/
(function($){
    $.fn.searchAffiliates = function(method) {
        var defaults = {
            uiMenuClass: 'searchAffiliates',  //Класс, добавляемый к блоку со списком вариантов
            uiOtherItemClass: 'other', //Класс блока с элементом Другой город
            otherText: lang.t('Нет вашего города? Выберите <a href="%url">%value</a>'), //Текст блока с элементом "Другой город"
            context: '.affiliates', //Родительский элемент, ограничивающий область выбора городов
            city: 'a.city', //Город, который войдет в базу
            urlAttr: 'data-href', //Артибут элемента city, из которого будет получена ссылка для данного города
            defaultAttr: 'data-is-default', //Артибут элемента city, из которого будет полена информация о Другом городе
            source: null //База городов. Если null, То будет сформирована автоматически из HTML 
        }, 
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('searchAffiliates');
            
            var methods = {
                init: function(initoptions) {                    
                    if (data) return;
                    data = {}; $this.data('searchAffiliates', data);
                    data.options = $.extend({}, defaults, initoptions);
                    data.context = $this.closest(data.options.context);
                    methods.initCities();
                    
                    //Выделяем город по умолчанию
                    var source = data.source.slice();                    
                    var defaultCity = null;
                    $.each(source, function(i, item) {
                        if (item && item.isDefault == 1) {
                            source.splice(i, 1);
                            defaultCity = item;
                        }
                    });                    
                    
                    $this.autocomplete({
                        minLength:2,
                        source: source,
                        select: function( event, ui ) {
                            location.href = ui.item.url;
                            return false;
                        },
                        response: function (event, ui) {
                            if (defaultCity) {
                                //Город по умолчанию всегда в конце
                                ui.content.push(defaultCity);                                
                            }
                        },
                        messages: {
                            noResults: '',
                            results: function() {}
                        }
                    })
                    .on('keydown', function(e) {
                        if (e.keyCode == 13) {
                            $.each(data.source, function(i, val) {
                                if (val.value.toLowerCase() == $(e.target).val().toLowerCase()) {
                                    location.href = val.url;
                                    return false;
                                }
                            });
                        }
                    })
                    .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                        ul.addClass(data.options.uiMenuClass);
                        var li = $( "<li>" );
                            
                        if (item.isDefault == 1) {
                            li.addClass(data.options.uiOtherItemClass).append('<span class="item">'+lang.t(data.options.otherText, item)+"</span>");
                        } else {
                            li.append( '<a class="item">' + item.label + "</a>" );
                        }
                            
                        return li.appendTo( ul );
                    };
                                        
                    $this.focus();
                },
                
                /**
                * Переинициализирует список городов
                */
                initCities: function() {
                    if (data.options.source) {
                        data.source = data.options.source;
                    } else {
                        data.source = [];
                        $(data.options.city, data.context).each(function() {
                            var url = $(this).attr(data.options.urlAttr) ? $(this).attr(data.options.urlAttr) : $(this).attr('href');
                            data.source.push({
                                value: $(this).text(),
                                label: $(this).text(),
                                isDefault: $(this).attr(data.options.defaultAttr),
                                url: url
                            });
                        });
                    }
                }
            }
            
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }
})(jQuery);    