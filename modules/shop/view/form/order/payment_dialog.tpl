<div class="formbox">
    <form id="paymentAddForm" method="POST" action="{urlmake}" data-order-block="#paymentBlockWrapper" enctype="multipart/form-data" class="crud-form" data-dialog-options='{ "width":500, "height":300 }'>
        <table class="otable">
            <tbody class="new-address">
            <tr class="last">
                <td width="100" class="otitle">{t}Cпособ оплаты{/t}: </td>
                <td>
                {if !empty($plist)}
                    <select name="payment">
                        {foreach from=$plist item=item}
                            <option value="{$item.id}" {if $item.id==$order.payment}selected{/if}>
                                {$item.title}{if !empty($item.admin_suffix)} ({$item.admin_suffix}){/if}
                            </option>
                        {/foreach}
                    </select>
                {else}
                    <p>{t}Не создано ни одной оплаты.{/t}</p> 
                    <a href="{$router->getAdminUrl(false, null, 'shop-paymentctrl')}" class="all-user-orders" target="_blank">{t}Перейти в список оплат{/t}</a>
                {/if}
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>

<script type="text/javascript">
    $(function() {
        /**
        * Назначаем действия, если всё успешно вернулось 
        */
        $('#paymentAddForm').on('crudSaveSuccess', function(event, response) {
            if (response.success && response.insertBlockHTML){ //Если всё удачно и вернулся HTML для вставки в блок
                var insertBlock = $(this).data('order-block');

                $(insertBlock).html(response.insertBlockHTML).trigger('new-content');
                $('#orderForm').data('hasChanges', 1);

                if (response.payment){ //Если указан id оплаты
                   $('input[name="payment"]').val(response.payment); 
                }

                $.orderEdit('refresh');
            }
        });    
    });
</script>