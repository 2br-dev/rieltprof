{*
    В этом шаблоне доступна переменная $transaction
    Объект заказа можно получить так: $transaction->getOrder()
    $need_check_receipt - Флаг, если нужно проверить статус чека после оплаты 
*}
<div class="successPay">
    <img id="rs-waitReceiptSuccessImg" src="{$THEME_IMG}/successpay.png" alt="" {if $need_check_receipt}style="display:none"{/if}/>
    {if $need_check_receipt}
        <img id="rs-waitReceiptLoading" src="{$THEME_IMG}/loading.gif" alt=""/>
    {/if}
    <p class="title">
        {t}Оплата успешно проведена{/t}. 
        {if $need_check_receipt}<br/><span id="rs-waitReceiptStatus">{t}Ожидается получение чека{/t}.</span>{/if}
    </p>
    {if $transaction->getOrder()->id}
        <a href="{$router->getUrl('shop-front-myorderview', ['order_id' => $transaction->getOrder()->order_num])}">{t}перейти к заказу{/t}</a>
    {else}
        <a href="{$router->getUrl('shop-front-mybalance')}">{t}перейти к лицевому счету{/t}</a>
    {/if}
</div>

{if $need_check_receipt} {* Если нужно проверить статус чека после оплаты *}
   {addjs file="%shop%/order/success_receipt.js"}
{/if}

