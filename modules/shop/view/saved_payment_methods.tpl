{addcss file='%shop%/savedpaymentmethods.css'}
{addjs file="%shop%/savedpaymentmethods.js"}

{$delete_url = $router->getUrl('shop-front-mysavedpaymentmethods', ['Act' => 'savedMethodDelete'])}
{$make_default_url = $router->getUrl('shop-front-mysavedpaymentmethods', ['Act' => 'savedMethodMakeDefault'])}

<div class="savedPaymentMethods rs-payment-methods" data-delete-url="{$delete_url}" data-make-default-url="{$make_default_url}">
    {if $payment_list}
        <div class="savedPaymentMethods_hint">
            {t}На этой странице вы можете отвязать привязанные вами ранее карты{/t}<br><br>
            {t}Также вы можете привязать другую карту. Мы спишем с вашей карты 1 рубль и затем сразу вернем его обратно. Это необходимо, чтобы мы могли автоматически взимать оплату за заказы после их комплектации.{/t}
        </div>
        <div class="savedPaymentMethods_paymentList">
            {foreach $payment_list as $payment}
                {$payment_type = $payment->getTypeObject()}
                {$saved_method_list = $payment_type->getSavedPaymentMethods($user)}
                <div class="savedPaymentMethods_paymentItem rs-payment" data-id="{$payment.id}">
                    <h2>{$payment.title}</h2>
                    {if $saved_method_list}
                        <div class="savedPaymentMethods_methodList">
                            {foreach $saved_method_list as $saved_method}
                                <div class="savedPaymentMethods_methodItem rs-payment-method" data-id="{$saved_method.id}">
                                    <div class="savedPaymentMethods_methodItem_name">
                                        <div class="savedPaymentMethods_methodItem_type">{$saved_method->getType()}</div>
                                        <div class="savedPaymentMethods_methodItem_subtype">{$saved_method.subtype}</div>
                                        <div class="savedPaymentMethods_methodItem_title rs-payment-method-title">{$saved_method.title}</div>
                                        {if $saved_method.is_default}
                                            <div class="savedPaymentMethods_methodItem_default">({t}основная{/t})</div>
                                        {/if}
                                    </div>
                                    <div class="savedPaymentMethods_methodItem_actions">
                                        {if !$saved_method.is_default}
                                            <div class="savedPaymentMethod_makeDefault rs-payment-method-makedefault link link-more link-apply">
                                                {t}сделать основной{/t}
                                            </div>
                                        {/if}
                                        <div class="savedPaymentMethod_delete rs-payment-method-delete link link-white">{t}отвязать{/t}</div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    {else}
                        <div class="savedPaymentMethods_methodEmptyList">
                            {t}Нет привязанных способов оплаты{/t}
                        </div>
                    {/if}
                    <a href="{$add_method_urls[$payment.id]}" class="savedPaymentMethods_add link link-more link-answer">
                        {t}Привязать новую карту{/t}
                    </a>
                </div>
            {/foreach}
        </div>
    {else}
        <div class="savedPaymentMethods_emptyList">
            {t}На сайте нет способов оплаты, поддерживащих рекуррентные платежи{/t}
        </div>
    {/if}
</div>
