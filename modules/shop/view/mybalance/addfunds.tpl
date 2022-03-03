{addjs file='%shop%/mybalance.js'}

{if $api->hasError()}
    <div class="pageError">
        {foreach from=$api->getErrors() item=item}
            <p>{$item}</p>
        {/foreach}
    </div>
{/if}


<form method="POST" id="order-form">
    <input type="hidden" name="payment" value="0">
    <div class="formSection">
        <span class="formSectionTitle">{t}Выберите способ оплаты{/t}</span>
    </div>
    <table class="formTable">
        <tbody>
        {foreach from=$pay_list item=item}
            <tr>
                <td class="value fixedRadio topPadd">
                    <input type="radio" name="payment" value="{$item.id}" id="dlv_{$item.id}"
                           {if $smarty.post.payment==$item.id}checked{/if}>
                </td>
                <td class="value marginRadio topPadd">
                    <label for="dlv_{$item.id}">{$item.title}</label>
                    <div class="help">{$item.description}</div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>

    <div class="formSection">
        <span class="formSectionTitle">{t}Укажите сумму пополнения{/t} ({$base_currency.stitle})</span>
    </div>
    <table class="formTable">
        <tr>
            <td class="value">
                <br>
                {t}Сумма{/t}: <input class="cost_field" name="cost" value="{$smarty.post.cost}">
            </td>
            <td class="value">
                {if $current_currency.stitle != $base_currency.stitle}
                    <label class="label_curr"></label>
                {/if}
                <input class="hidden_curr" data-ratio="{$current_currency.ratio}" data-liter="{$current_currency.stitle}" type="hidden" value="">
            </td>
            <td class="value">
                <button type="submit" class="formSave">{t}Пополнить{/t}</button>
            </td>
        </tr>
    </table>

</form>
<br><br><br>
