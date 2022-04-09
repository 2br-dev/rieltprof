{if $order->hasError()}
    <div class="pageError">
        {foreach from=$order->getErrors() item=item}
            <p>{$item}</p>
        {/foreach}
    </div>
{/if}
<form method="POST" id="order-form">
    <input type="hidden" name="payment" value="0">
    <div class="formSection">
        <span class="formSectionTitle">{t}Оплата{/t}</span>
    </div>
    <table class="formTable">
        <tbody>
        {foreach from=$pay_list item=item}
            <tr>
                <td class="value fixedRadio topPadd">
                    <input type="radio" name="payment" value="{$item.id}" id="pay_{$item.id}"
                           {if $order.payment==$item.id}checked{/if}>
                </td>
                <td class="value marginRadio topPadd">
                    {if !empty($item.picture)}
                        <img class="logoService" src="{$item.__picture->getUrl(100, 100, 'xy')}" alt="{$item.title}"/>
                    {/if}
                    <div class="middleBox">
                        <label for="pay_{$item.id}">{$item.title}</label>
                        <div class="help">{$item.description}</div>
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <button type="submit" class="formSave">{t}Далее{/t}</button>
</form>
<br><br><br>