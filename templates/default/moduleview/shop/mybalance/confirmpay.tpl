<h2><span>{t}Подтверждение оплаты{/t}</span></h2>

{if $api->hasError()}
    <div class="pageError">
        {foreach from=$api->getErrors() item=item}
        <p>{$item}</p>
        {/foreach}
    </div>
{/if}

<table class="confirmPayTable">
    <tr>
        <td class="key">{t}Сумма{/t}</td>
        <td class="val"><span class="scost">{$transaction->getCost(true, true)}</span></td>
    </tr>
    <tr>
        <td class="key">{t}Назначение платежа{/t}</td>
        <td class="val">{$transaction->reason}</td>
    </tr>    
    <tr>
        <td class="key">{t}Источник{/t}</td>
        <td class="val">{t}Лицевой счет{/t}</td>
    </tr>        
</table>

{if !$api->hasError()}
<form method="post">
    <button type="submit" class="formSave">{t}Оплатить{/t}</button>
</form>
{/if}
