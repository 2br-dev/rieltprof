$(function() {
    /**
    * Выбор страны 
    */
    $('select[name="addr_country_id"]').change(function() {
        var regions = $('select[name="addr_region_id"]').attr('disabled','disabled');
        
        $.getJSON($(this).data('regionUrl'), {
            parent: $(this).val()
        }, 
        function(response) {
            if (response.list.length>0) {
                regions.html('');
                for(var i=0; i< response.list.length; i++) {
                    var item = $('<option value="'+response.list[i].key+'">'+response.list[i].value+'</option>');
                    regions.append(item);
                }
                regions.removeAttr('disabled');
                $('#region-input').val('').hide();
                $('#region-select').show();
            } else {
                $('#region-input').show();
                $('#region-select').hide();
            }
        });
    });
    
    /**
    * Показ/Скрытие блока с вводом адреса
    */
    if ($('#address-list').length>0 && $('input[name="use_addr"]:checked').val()!='0') {
        $('.new-address').hide();
    }

    /**
    * Переключение радиокнопок с адресами
    */
    $('input[name="use_addr"]').click(function() {
        if (this.value == '0'){
            $('.new-address').show();
        }else{
            $('.new-address').hide();
        } 
    });
    
    /**
    * Смена пользователя
    */
    $('#changeuser').click(function() {
        $('#order-form').append('<input type="hidden" name="logout" value="1">');
        $('#order-form').submit();
        return false;
    });
    
    /**
    * Переключение на авторизацию
    */
    $('#order_login').click(function() {
        $('#order-form').append('<input type="hidden" name="ologin" value="1">');
        $('#order-form').submit();
        return false;
    }); 

    /**
    * Показ/скрытие получения логина и пароля автоматически
    */
    $('input[name="reg_autologin"]').change(function() {
        $('#manual-login').toggle(!this.checked);
    });   
    
    /**
    * Получает адрес для получения подсказок для города
    */
    function getCityAutocompleteUrl()
    {
        var form   = $( "[name='addr_city']" ).closest('form'); //Объект формы
        var url    = form.data('city-autocomplete-url'); //Адрес для запросов
        var params = []; //Дополнительные параметры

        //Если есть селектор страны, то сузим поиск до этой страны
        if ($( "[name='addr_country_id']" ).length){
            params.push({
                name   : 'country_id',
                value : $( "[name='addr_country_id']" ).val()
            });
        }
        //Если есть селектор региона, то сузим поиск до этого региона
        if ($( "[name='addr_region_id']" ).length){
            params.push({
                name : 'region_id',
                value : $( "[name='addr_region_id']" ).val()
            });
        }
        
        var str_params = $.param(params);
        if (str_params){
            url += "?"+str_params; 
        }
        return url;
    }
    
    /**
    * Смена выбора забора или доставки товара
    */
    $("[name='only_pickup_points']").on('change', function(){
        $("#formAddressSectionWrapper").toggleClass('hidden', parseInt($("[name='only_pickup_points']:checked").val())>0);
    });
    
    /**
    * Автозаполнение в строке с вводом города
    */
    $("[name='addr_city']").each(function() {
        var url = getCityAutocompleteUrl();
        
        $(this).autocomplete({
            source: url,
            minLength: 3,
            select: function( event, ui ) {
                var region_id  = ui.item.region_id;  //Выбранный регион
                var country_id = ui.item.country_id; //Выбранная страна
                var zipcode    = ui.item.zipcode;    //Индекс
                
                //Установка индекса
                if ($("[name='addr_zipcode']").length){
                    $("[name='addr_zipcode']").val(zipcode);
                }
            },
            messages: {
                noResults: '',
                results: function() {}
            }
        }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
            ul.addClass('searchCityItems');
            
            return $( "<li />" )
                .append( '<a>' + item.label + '</a>' )
                .appendTo( ul );
        };
    }); 
    
    
    /**
    * Если меняется регион или страна в выпадающем списке
    */
    $("select[name='addr_region_id'], select[name='addr_country_id']").on('change', function(){
        var url = getCityAutocompleteUrl(); //Установка адреса
        $( "[name='addr_city']" ).autocomplete('option', 'source', url);
    });
    
    
    /**
    * Отработка удаления адреса доставки на странице оформления заказа
    */
    $(".existsAddress .deleteAddress").on('click', function(){
        var parent = $(this).closest('tr');
        parent.css('opacity', '0.5');
        $.get($(this).attr('href') ? $(this).attr('href') : $(this).data('href'), function( response ) {
            parent.css('opacity', '1');
            if (response.success){
               parent.remove(); 
               $("#address-list input[name='use_addr']:eq(0)").click();
            }
        }, "json");
        return false;
    });

});   