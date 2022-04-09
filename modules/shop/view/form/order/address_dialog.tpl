<div class="formbox">
    <form id="addressAddForm" method="POST" action="{urlmake}" data-city-autocomplete-url="{$router->getAdminUrl('searchCity')}" data-order-block="#addressBlockWrapper" enctype="multipart/form-data" class="crud-form" data-dialog-options='{ "width":800, "height":700 }'>
        {hook name="shop-form-order-address_dialog:form" title="{t}Редактирование заказа - диалог адреса:форма{/t}"}
            <table class="otable">
                {if $user_id}
                    <tbody class="new-address">
                        <tr>
                            <td class="otitle">{t}Изменяемый адрес{/t}:</td>
                            <td>
                                <select name="use_addr" id="change_addr" data-url="{adminUrl do=getAddressRecord}">
                                    {foreach from=$address_list item=item}
                                        <option value="{$item.id}" {if $current_address.id==$item.id}selected{/if}>{$item->getLineView()}</option>
                                    {/foreach}
                                    <option value="0">{t}Новый адрес для заказа{/t}</option>
                                </select>
                                <div class="fieldhelp">{t}Внимание! если этот адрес используется в других заказах, то он также будет изменен.{/t}</div>
                            </td>
                        </tr>
                    </tbody>
                {else}
                    <input type="hidden" name="use_addr" value="{$order.use_addr}">
                {/if}

                <tbody class="address_part">
                    {$address_part}
                </tbody>
            </table>
        {/hook}
    </form>
    <script>
        /**
        * Получает адрес для получения подсказок для города
        */
        function getCityAutocompleteUrl()
        {
            var form   = $( "#addressCityInput" ).closest('form'); //Объект формы
            var url    = form.data('city-autocomplete-url'); //Адрес для запросов
            
            var country_id = $( "#addressCountryIdSelect" ).val();
            var region_id  = $( "#addressRegionIdSelect" ).val();
       
            url += "&country_id=" + country_id + "&region_id=" + region_id;
            return url;
        }
    
        $(function() {
            /**
            * Назначаем действия, если всё успешно вернулось 
            */
            $('#addressAddForm').on('crudSaveSuccess', function(event, response) {
                if (response.success && response.insertBlockHTML){ //Если всё удачно и вернулся HTML для вставки в блок
                    var insertBlock = $(this).data('order-block');

                    $(insertBlock).html(response.insertBlockHTML).trigger('new-content');
                    $('#orderForm').data('hasChanges', 1);

                    if (typeof(response.use_addr)!='undefined'){ //Если выбран адрес доставки
                       $('input[name="use_addr"]').val(response.use_addr); 
                    }
                    if (typeof(response.address)!='undefined'){ //Если выбран адрес
                       for(var m in response.address){
                          $('input[name="address[' + m + ']"]').val(response.address[m]);  
                       } 
                    }
                }
            });
            
            /**
            * Смена выпадающего списка с адресами
            */
            $('#change_addr').on('change', function() {
                $.ajaxQuery({
                    url: $(this).data('url'),
                    data: {
                        'address_id': $(this).val()
                    },
                    success: function(response) {
                        $('.address_part').html(response.html);
                    }
                });
            });
            
            /**
            * Автозаполнение в строке с вводом города
            */
            $( "#addressCityInput" ).each(function() {
                var url = getCityAutocompleteUrl(); //Установка адреса
                
                $(this).autocomplete({
                    source: url,
                    minLength: 3,
                    select: function( event, ui ) {
                        var region_id  = ui.item.region_id;  //Выбранный регион
                        var country_id = ui.item.country_id; //Выбранная страна
                        var zipcode    = ui.item.zipcode;    //Индекс
                        
                        //Установка индекса
                        if (!$("#addressZipcodeInput").val()){
                            $("#addressZipcodeInput").val(zipcode);
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
            $("#addressRegionIdSelect, #addressCountryIdSelect").on('change', function(){
                var url = getCityAutocompleteUrl(); //Установка адреса
                $( "#addressCityInput" ).autocomplete('option', 'source', url);
            });
        });                                
    </script>
</div>