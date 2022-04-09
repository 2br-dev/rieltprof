/**
* Плагин, инициализирующий работу расчёта стоимости доставки
* Зависит от плагина jquery autocomplete
*/
(function( $ ){
    $.fn.deliveryCost = function( method ) {
        var defaults = {
            //Окно с выбором города
            autocompleteInput      : '.query.cityautocomplete',     //Селектор поля ввода с автозаполнением
            autocompleteInputBlock : '.inputBlock',             //Селектор блока с вставкой информации по выбранному городу
            autocompleteButton     : '.deliveryCostShowButton', //Селектор кнопки с выбором кнопки
            autocompleteList       : 'deliverySearchItems',     //Селектор блока с выпадающим списком
            form                   : 'form',                    //Селектор формы
            vals                   : [], //Массив с подгруженными значениями
            
            
            //Блок с расчётом доставки
            deliveryCostInputDiv : '#deliveryCostInputDiv',  //Селектор блока в который будут возвращатся стоимости доставки
            productIdBlock       : '[data-id]',              //Селектор блока с товаром
            multiOfferName       : '[name^="multioffers["]', //Списки многомерных комплектаций
            multiOfferPhotoBlock : '.multiOfferValueBlock',  //Блок многомерной комплектации представленный как фото
            amountInput          : '[name="amount"]',        //Селектор поля ввода с вводом количества товара
            offerInput           : '[name="offer"]',         //Селектор поля ввода с выбранной комплектацией
            cityidInput          : '[name="city_id"]',       //Селектор поля ввода с id города
            timeout              : 600                       //Таймер для запуска обновления блока повторного
            
        },
        args = arguments;
        
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('deliveryCost');
                
            
            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data                    = {}; $this.data('deliveryCost', data);
                    data.options            = $.extend({}, defaults, initoptions);

                    bindChanges(); //Привязывает события на кнопки и блоки                        
                },
                
                /**
                * Отправляет запрос на сервер и возвращает блок с расчётом стоимости доставки для требуемого товара
                * 
                */
                queryDeliveryBlock: function(){
                    var product_context = $this.closest(data.options.productIdBlock); //Блок с товаром
                    var product_id      = product_context.data('id'); //id товара
                    var offer_obj       = $(data.options.offerInput+":eq(0)", product_context);
                    var offer_val       = offer_obj.val();

                    if (offer_obj.length && (offer_obj.get(0).tagName == 'INPUT') && (offer_obj.attr('type') == 'radio')){
                        offer_val = $(data.options.offerInput + ":checked", product_context).val();
                    }

                    var offer           = offer_val; //Комплектация
                    var amount          = 1; //Количество товара
                    if ($(data.options.amountInput, product_context).length){
                        amount = $(data.options.amountInput, product_context).val(); 
                        if (!amount || amount<0){ //Если количество задано не правильно
                            amount = 1;
                        }
                    }
                    
                    $.ajax({
                        method : 'POST',
                        url    : $this.data('url'),//Адрес запроса
                        dataType : 'json',
                        data   : {
                            '_block_id'  : $this.data('block-id'),
                            'product_id' : product_id,
                            'amount'     : amount,
                            'offer'      : offer
                        },
                        success: function(response){
                            if (response.html) {
                                $this.html(response.html).trigger('new-content'); //Обновим блок и вызовем событие
                                $this.css('opacity', 1);
                            }
                        }
                    });
                }
            };        
            
            //private 
            /**
            * Привязывает события на кнопки и блоки
            */
            var 
            bindChanges = function() {
                var product_context = $this.closest(data.options.productIdBlock); //Блок с товаром
                
                /**
                * Автозаполнение в строке с выбором города
                */
                $(data.options.autocompleteInput, $this).each(function() {
                    $(this).autocomplete({
                        source: $(data.options.form, $this).attr('action'),
                        minLength: 3,
                        select: function( event, ui ) {
                            
                            showItemSelected(ui.item);
                            return false;
                        },
                        response: function (event, ui) {
                            data.options.vals = [];
                            $.each(ui.content, function(i, val){
                                data.options.vals.push(val);    
                            });
                            return false;
                        },
                        close: function( event, ui ) {
                            if ($.colorbox){
                                $.colorbox.resize();    
                            }
                        },
                        messages: {
                            noResults: '',
                            results: function() {}
                        }
                    }).on('keydown', function(e) {
                        if (e.keyCode == 13) {
                            
                            
                            $.each(data.options.vals, function(i, val) {
                                
                                if (val.value.toLowerCase() == $(e.target).val().toLowerCase()) {
                                    if ($(data.options.cityidInput, $this).length){ //Если заранее выбрано, то сохраним автоматически
                                        $('[type="submit"]', $this).click(); 
                                    }
                                    showItemSelected(val);
                                    return false;
                                }
                            });
                            $(".ui-autocomplete").hide();
                            return false;
                        }
                        
                    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                        ul.addClass(data.options.autocompleteList);
                        
                        return $( "<li />" )
                            .append( '<a><span class="">' + item.label + '</span></a>' )
                            .appendTo( ul );
                    };
                });  
                
                $(data.options.multiOfferName, product_context).on('change', updateOfferEvent);
                $(data.options.multiOfferPhotoBlock, product_context).on('click', updateOfferEvent);
                $('select[name="offer"]', product_context).on('change', updateOfferEvent);
                $('input[name="offer"]', product_context).on('change',updateOfferEvent);
            };
            
            /**
            * Показывает выбранное значение города
            * 
            * @param item
            */
            var showItemSelected = function(item){
                $(data.options.autocompleteInputBlock, $this).html('Выбран город: <b>' + item.label + '</b>');
                $(data.options.autocompleteInputBlock, $this).append('<input type="hidden" name="city_id" value="' + item.id + '"/>');
                $(data.options.autocompleteInputBlock, $this).addClass('shown');
                $(data.options.autocompleteButton, $this).show().addClass('shown');
                if ($.colorbox){
                    $.colorbox.resize();    
                }
            };
            
            var updateOfferEvent = function(){
                $this.css('opacity', 0.5);
                setTimeout(methods.queryDeliveryBlock, data.options.timeout);
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
    $('.deliveryCostWrapper').deliveryCost();
});