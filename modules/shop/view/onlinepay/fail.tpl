{$order = $transaction->getOrder()}
<div class="section 100vh">
        <div class="container col-xl-6 col-lg-8 col-md-10">
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
            <div class="pay-fail-consultation">
                <p>{t}Если у вас возникли затруднения при оплате заказа, вы можете завершить его оформление с менеджером{/t}</p>
                {if $THEME_SETTINGS.default_phone}
                    <a href="tel:{$THEME_SETTINGS.default_phone|format_phone}">{$THEME_SETTINGS.default_phone}</a>
                {/if}
            </div>
    </div>
</div>