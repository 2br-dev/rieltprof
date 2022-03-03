{if $order->hasError()}
    <div class="pageError">
        {foreach from=$order->getErrors() item=item}
        <p>{$item}</p>
        {/foreach}
    </div>
{/if}
<form method="POST" id="order-form">
    <input type="hidden" name="warehouse" value="0">
    <div class="formSection">
        <span class="formSectionTitle">{t}Доставка - выбор склада для вывоза товара{/t}</span>
    </div>
    
    <table class="formTable">
        <tbody>
        {$pvzList = $order->getDelivery()->getTypeObject()->getOption('pvz_list')}
        {foreach $warehouses_list as $item}
            {if empty($pvzList) || in_array(0, $pvzList) || in_array($item.id, $pvzList)}

                <tr class="row">
                    <td class="value fixedRadio topPadd">
                        <input type="radio" name="warehouse" value="{$item.id}" id="dlv_{$item.id}" {if ($order.warehouse>0)&&($order.warehouse==$item.id)}checked{elseif ($order.warehouse==0) && $item.default_house}checked{/if} >
                    </td>
                    <td class="value marginRadio topPadd">
                        {if !empty($item.image)}
                            <img class="logoService" src="{$item.__image->getUrl(100, 100, 'xy')}"/>
                        {/if}
                        <div class="middleBox">
                            <label for="dlv_{$item.id}">{$item.title}</label>
                            <div class="help">
                                {if !empty($item.adress)}{t}Адрес{/t}:{$item.adress}<br/>{/if} 
                                {if !empty($item.phone)}{t alias="сокращение 'телефон'"}Тел.{/t}:{$item.phone}<br/>{/if}
                                {if !empty($item.work_time)}{t}Время работы{/t}.:{$item.work_time}{/if}
                            </div>
                        </div>
                    </td>
                </tr>

            {/if}
        {/foreach}
        </tbody>
    </table>
    <button type="submit" class="formSave">{t}Далее{/t}</button>
</form>
<br><br><br>