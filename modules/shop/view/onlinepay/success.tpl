{*
    В этом шаблоне доступна переменная $transaction
    Объект заказа можно получить так: $transaction->getOrder()
    $need_check_receipt - Флаг, если нужно проверить статус чека после оплаты 
*}
{$order = $transaction->getOrder()}
<div class="section 100vh">
        <div class="container col-xl-5 col-lg-7 col-md-8 col-sm-11 text-center">
            <div class="mb-md-5 mb-3">
                <img class="rs-waitReceiptSuccessImg {if $need_check_receipt}d-none{/if}" width="64" height="64" src="{$THEME_IMG}/decorative/success.svg" alt="">
                {if $need_check_receipt}
                    <img class="rs-waitReceiptLoading" width="64" height="64" src="{$THEME_IMG}/icons/loader.svg" alt="">
                {/if}
            </div>
            <div class="mb-md-6 mb-5">
                {t}Оплата успешно проведена{/t}
                {if $need_check_receipt}<span class="rs-waitReceiptStatus">, {t}ожидается получение чека.{/t}</span>{/if}
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

{if $need_check_receipt} {* Если нужно проверить статус чека после оплаты *}
    {addjs file="%shop%/rscomponent/successreceipt.js"}
{/if}