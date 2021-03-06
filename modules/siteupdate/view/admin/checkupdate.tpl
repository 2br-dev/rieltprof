<div class="viewport">
    {if $canUpdate && $isUpdateExpire}
        <p class="notice-box notice-bg">
            {t}Срок подписки на обновления истек.{/t}
            <a href="{$expireSaleBuyUrl}" target="_blank" class="u-link">{t}Продлите подписку на обновления{/t}</a>{if $expireSale}{t expireSale={$expireSale|dateformat:"@date @time"} expireSaleDays=$expireSaleDays} со <b>скидкой 30%</b> до %expireSale (осталось %expireSaleDays дней){/t}{else}.{/if}
        </p>
    {/if}
</div>

{include file="admin/head.tpl"}

<div class="viewport">
    <br>
    {t alias="ОЦентреОбновлений"}
    <p>Всегда устанавливайте последние обновления, чтобы улучшить безопасность и производительность системы. Обновления также могут содержать исправления ошибок в работе модулей и дополнительный функционал.</p>
    <p>Обновления затрагивают только те файлы, которые изначально присутствовали в дистрибутиве, в том числе и файлы шаблона "по-умолчанию". В случае, если необходимо провести частичное обновление системы(не затронув определенные модули системы), допускается уточнение объектов обновления.</p>
    {/t}
</div>