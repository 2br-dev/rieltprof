{addcss file="%shop%/order/onlinepay.css"}
{addjs file="%shop%/order/onlinepay.js"}

{$need_check_receipt = $transaction->getPayment()->create_cash_receipt}
{$url_check_receipt = $router->getUrl('shop-front-onlinepay', [
    'Act' => 'checktransactionreceiptstatus',
    'id' => $transaction['id']
])}
{$url_check_transaction = $router->getUrl('shop-front-onlinepay', [
    'Act' => 'checktransactionstatus',
    'id' => $transaction['id']
])}

<div class="payment-result">
    <div id="rs-status-params" class="payment-result_content {$transaction.status}" data-url-check-transaction="{$url_check_transaction}" {if $need_check_receipt}data-url-check-receipt="{$url_check_receipt}"{/if}>

        <img id="rs-waitReceiptLoading" class="title-new" src="{$THEME_IMG}/icons/loader.svg" alt=""/>
        <h2 class="title-new">{t}Ожидание получения статуса оплаты{/t}</h2>

        <img class="title-hold" src="{$THEME_IMG}/icons/big-success.svg" alt=""/>
        <h2 class="title-hold">{t}Оплата успешно проведена{/t}</h2>

        <img id="rs-waitReceiptSuccessImg" class="title-success" src="{$THEME_IMG}/icons/big-success.svg" alt="" {if $need_check_receipt}style="display:none;"{/if} />
        {if $need_check_receipt}
            <img class="title-success" id="rs-waitReceiptLoading" src="{$THEME_IMG}/icons/loader.svg" alt=""/>
        {/if}
        <h2 class="title-success">{t}Оплата успешно проведена{/t}{if $need_check_receipt}<span id="rs-waitReceiptStatus">, {t}ожидается получение чека.{/t}</span>{/if}</h2>

        <img class="title-fail" src="{$THEME_IMG}/icons/big-fail.svg">
        <h2 class="title-fail">{t}Оплата не произведена{/t}</h2>

        <p class="link">
            {if $transaction->getOrder()->id}
                <a href="{$router->getUrl('shop-front-myorderview', ['order_id' => $transaction->getOrder()->order_num])}" class="colorButton">{t}перейти к заказу{/t}</a>
            {else}
                <a href="{$router->getUrl('shop-front-mybalance')}" class="colorButton">{t}перейти к лицевому счету{/t}</a>
            {/if}
        </p>
    </div>
</div>
