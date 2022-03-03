<div class="formbox">
    {* Типы доставки, которые не требуют установки флага отправки на удалённый сервис доставки *}
    {$user_delivery_type=[
        'myself',
        'fixedpay',
        'universal',
        'manual'
    ]}
    <form id="deliveryAddForm" method="POST" action="{urlmake}" data-city-autocomplete-url="{$router->getAdminUrl('searchCity')}" data-order-block="#deliveryBlockWrapper" enctype="multipart/form-data" class="crud-form" data-dialog-options='{ "width":500, "height":400 }'>
        {hook name="shop-form-order-delivery_dialog:form" title="{t}Редактирование заказа - диалог доставки:форма{/t}"}
            <table class="otable">
                <tbody class="new-address">
                    <tr>
                        <td class="otitle">{t}Cпособ доставки{/t}: </td>
                        <td>
                            {$selected_delivery_type="myself"}
                            <select id="change_delivery" name="delivery">
                                {foreach $dlist as $category=>$delivery_list}
                                    <optgroup label="{$category}">
                                    {foreach $delivery_list as $item}
                                        {$delivery_type=$item->getTypeObject()->getShortName()}
                                        {if $item.id==$delivery_id}
                                            {$selected_delivery_type=$delivery_type}
                                        {/if}
                                        <option value="{$item.id}" data-delivery-query-flag="{if in_array($delivery_type, $user_delivery_type)}0{else}1{/if}" {if $item.id==$delivery_id}selected{/if}>
                                            {$item.title}{if !empty($item.admin_suffix)} ({$item.admin_suffix}){/if}
                                        </option>
                                    {/foreach}
                                    </optgroup>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                </tbody>
                <tbody>
                    <tr class="last">
                        <td class="caption">{t}Стоимость{/t}:</td>
                        <td>
                            <input size="10" type="text" maxlength="20" value="{$order.user_delivery_cost}" name="user_delivery_cost">
                            <div class="fieldhelp">{t}Если стоимость доставки не указана, то сумма доставки будет рассчитана автоматически.{/t}</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        {/hook}
    </form>
    <script type="text/javascript">
    
        $(function() {
            /**
            * Назначаем действия, если всё успешно вернулось 
            */
            $('#deliveryAddForm').on('crudSaveSuccess', function(event, response) {
                if (response.success && response.insertBlockHTML){ //Если всё удачно и вернулся HTML для вставки в блок
                    var insertBlock = $(this).data('order-block');

                    $(insertBlock).html(response.insertBlockHTML).trigger('new-content');
                    $('#orderForm').data('hasChanges', 1);

                    if (typeof(response.delivery)!='undefined'){ //Если указан id доставки
                       $('input[name="delivery"]').val(response.delivery); 
                    }
                    
                    if (typeof(response.user_delivery_cost)!='undefined'){ //Если указан id доставки
                       $('input[name="user_delivery_cost"]').val(response.user_delivery_cost); 
                    }
                    //Снимем флаг показа дополнительных кнопок доставки 
                    if ($("#showDeliveryButtons").length){
                        $("#showDeliveryButtons").val(0);     
                    }                           
                    //Обновимм корзину, т.к. доставка может прибавить стоимость
                    $(this).closest('.dialog-window').on('dialogclose', function() {
                        $.orderEdit('refresh');
                    });
                }
            });
        });                                
    </script>
</div>