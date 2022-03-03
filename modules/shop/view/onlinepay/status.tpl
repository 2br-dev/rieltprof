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
        <h2 class="title-new">
            <img id="rs-waitReceiptLoading" src="{$THEME_IMG}/icons/loader.svg" alt=""/>
            <span>{t}Ожидание получения статуса оплаты{/t}</span>
        </h2>
        <h2 class="title-hold">
            <img id="rs-waitReceiptSuccessImg" src="{$THEME_IMG}/icons/big-success.svg" alt="" />
            <span>{t}Оплата успешно проведена{/t}</span>
        </h2>
        <h2 class="title-success">
            <img id="rs-waitReceiptSuccessImg" src="{$THEME_IMG}/icons/big-success.svg" alt="" {if $need_check_receipt}style="display:none;"{/if} />
            {if $need_check_receipt}
                <img id="rs-waitReceiptLoading" src="{$THEME_IMG}/icons/loader.svg" alt=""/>
            {/if}
            <span>{t}Оплата успешно проведена{/t}{if $need_check_receipt}<span id="rs-waitReceiptStatus">, {t}ожидается получение чека.{/t}</span>{/if}</span>
        </h2>
        <h2 class="title-fail">
            <img src="{$THEME_IMG}/icons/big-fail.svg">
            <span>{t}Оплата не произведена{/t}</span>
        </h2>
        <p class="descr">
            {if $transaction->getOrder()->id}
                <a href="{$router->getUrl('shop-front-myorderview', ['order_id' => $transaction->getOrder()->order_num])}" class="colorButton">{t}перейти к заказу{/t}</a>
            {else}
                <a href="{$router->getUrl('shop-front-mybalance')}" class="colorButton">{t}перейти к лицевому счету{/t}</a>
            {/if}
        </p>
    </div>
</div>
