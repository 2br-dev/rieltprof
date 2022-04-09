{*
    В этом шаблоне доступна переменная $transaction
    Объект заказа можно получить так: $transaction->getOrder()
*}

<div class="failPay">
    <img src="{$THEME_IMG}/failpay.png">
    <p class="title">{t}Оплата не произведена{/t}</p>
    {*<p></p>*}
    {if $transaction->getOrder()->id}
        <a href="{$router->getUrl('shop-front-myorderview', ['order_id' => $transaction->getOrder()->order_num])}">{t}перейти к заказу{/t}</a>
    {else}
        <a href="{$router->getUrl('shop-front-mybalance')}">{t}перейти к лицевому счету{/t}</a>
    {/if}
</div>