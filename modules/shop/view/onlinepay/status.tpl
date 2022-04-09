{addjs file="%shop%/rscomponent/successreceipt.js"}

{$need_check_receipt = $transaction->getPayment()->create_cash_receipt}
{$url_check_receipt = $router->getUrl('shop-front-onlinepay', [
    'Act' => 'checktransactionreceiptstatus',
    'id' => $transaction['id']
])}
{$url_check_transaction = $router->getUrl('shop-front-onlinepay', [
    'Act' => 'checktransactionstatus',
    'id' => $transaction['id']
])}
{$order = $transaction->getOrder()}

<div class="section {$transaction.status}"
     id="rs-status-params"
     data-url-check-transaction="{$url_check_transaction}"
     {if $need_check_receipt}data-url-check-receipt="{$url_check_receipt}"{/if}>

    <div class="container col-xl-5 col-lg-7 col-md-8 col-sm-11 text-center">
        <div class="mb-md-5 mb-3">
            <img class="rs-waitReceiptSuccessImg rs-visible-success rs-visible-hold" width="64" height="64" src="{$THEME_IMG}/decorative/success.svg" alt="">
            <img class="rs-waitReceiptLoading rs-visible-new" width="64" height="64" src="{$THEME_IMG}/icons/loader.svg" alt="">
        </div>
        <div class="mb-md-6 mb-5">
            <div class="rs-visible-new">{t}Ожидание получения статуса оплаты{/t}</div>
            <div class="rs-visible-hold">{t}Оплата успешно проведена{/t}</div>
            <div class="rs-visible-success">{t}Оплата успешно проведена{/t}
                {if $need_check_receipt}<span class="rs-waitReceiptStatus">, {t}ожидается получение чека.{/t}</span>{/if}</div>
        </div>

        <div class="rs-visible-fail">
            <div class="pay-fail">
                <div class="pay-fail__img">
                    <img width="64" height="64" src="{$THEME_IMG}/decorative/danger.svg" alt="">
                </div>
                <div class="last-child-margin-remove">
                    <p>{t}Оплата не произведена.{/t}
                        {if $order.id && $order.user_id > 0}
                            <a href="{$transaction->getOrder()->getOnlinePayUrl()}">{t}Повторить попытку оплаты{/t}</a>
                        {/if}
                    </p>
                    {if $order.id && $order.user_id > 0}
                        <p>{t href=$router->getUrl('shop-front-myorderview', ['order_id' => $transaction->getOrder()->order_num])}Оплатить заказ позже можно через <a href="%href">личный кабинет</a>{/t}</p>
                    {/if}
                </div>
            </div>
        </div>

        <div class="row row-cols-sm-2 g-3 justify-content-center">
            {if $is_auth}
                {if $order.id}
                    {if $order->user_id > 0}
                        <div>
                            <a href="{$router->getUrl('shop-front-myorderview', ['order_id' => $order->order_num])}" class="btn btn-outline-primary w-100">{t}перейти к заказу{/t}</a>
                        </div>
                    {/if}
                {elseif $this_controller->getModuleConfig()->use_personal_account}
                    <div>
                        <a href="{$router->getUrl('shop-front-mybalance')}" class="btn btn-outline-primary w-100">{t}перейти к лицевому счету{/t}</a>
                    </div>
                {/if}
            {/if}

            <div>
                <a href="{$router->getRootUrl()}" class="btn btn-primary w-100">{t}Продолжить покупки{/t}</a>
            </div>
        </div>
    </div>
</div>
