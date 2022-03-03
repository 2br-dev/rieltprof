{if $order->hasError()}
    <div class="pageError">
        {foreach from=$order->getErrors() item=item}
        <p>{$item}</p>
        {/foreach}
    </div>
{/if}

<form method="POST" id="order-form">
    <input type="hidden" name="delivery" value="0">
    <div class="formSection">
        <span class="formSectionTitle">{t}Доставка{/t}</span>
    </div>    
    <table class="formTable">
        <tbody>
        {foreach from=$delivery_list item=item}
            {$addittional_html = $item->getAddittionalHtml($order)}
            {$something_wrong = $item->getTypeObject()->somethingWrong($order)} 
            <tr class="row">
                <td class="value fixedRadio topPadd">
                    <input type="radio" name="delivery" value="{$item.id}" id="dlv_{$item.id}" {if $order.delivery==$item.id}checked{/if} {if $something_wrong}disabled="disabled"{/if}>
                </td>
                <td class="value marginRadio topPadd">
                    {if !empty($item.picture)}
                       <img class="logoService" src="{$item.__picture->getUrl(100, 100, 'xy')}" alt="{$item.title}"/>
                   {/if}
                   <div class="middleBox">
                       <label for="dlv_{$item.id}">{$item.title}</label>
                       <div class="help">{$item.description}</div>
                       <div class="additionalInfo">{$addittional_html}</div>
                   </div>
                </td>
                <td class="value marginRadio checkoutPriceCol">
                    {if $something_wrong}
                        <span style="color:red;">{$something_wrong}</span>
                    {else}
                        <span class="help">{$order->getDeliveryExtraText($item)}</span>
                        {assign var=dcost value=$order->getDeliveryCostText($item)}
                        {if $dcost>0}
                            <span id="scost_{$item.id}" class="scost">{$dcost}</span>
                        {else}
                            {$dcost}
                        {/if}
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <button type="submit" class="formSave">{t}Далее{/t}</button>
</form>
<br><br><br>