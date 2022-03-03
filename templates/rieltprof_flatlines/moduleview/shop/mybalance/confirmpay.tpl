{* Шаблон страницы подтверждения оплаты с лицевого счета *}

<div class="sec sec-content_wrapper">
    <h1 class="h1">{t}Подтверждение оплаты{/t}</h1>

    {if $api->hasError()}
        <div class="page-error">
            {foreach $api->getErrors() as $item}
            <p>{$item}</p>
            {/foreach}
        </div>
    {/if}

    <table class="table-underlined">
        <tr class="table-underlined-text">
            <td><span>{t}Сумма{/t}</span></td>
            <td><strong>{$transaction->getCost(true, true)}</strong></td>
        </tr>
        <tr class="table-underlined-text">
            <td><span>{t}Назначение платежа{/t}</span></td>
            <td>{$transaction->reason}</td>
        </tr>    
        <tr class="table-underlined-text">
            <td><span>{t}Источник{/t}</span></td>
            <td>{t}Лицевой счет{/t}</td>
        </tr>        
    </table>

    {if !$api->hasError()}
        <br>
        <form method="POST" class="form__menu_buttons">
            <button type="submit" class="link link-more">{t}Оплатить{/t}</button>
        </form>
    {/if}
</div>