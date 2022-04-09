{extends file="%THEME%/helper/wrapper/my-cabinet.tpl"}
{block name="content"}
    {addjs file="%shop%/rscomponent/savedpaymentmethods.js"}
    {$delete_url = $router->getUrl('shop-front-mysavedpaymentmethods', ['Act' => 'savedMethodDelete'])}
    {$make_default_url = $router->getUrl('shop-front-mysavedpaymentmethods', ['Act' => 'savedMethodMakeDefault'])}

    <div class="col-lg-8 col-xl-9 col-xxl-7 rs-payment-methods" data-delete-url="{$delete_url}" data-make-default-url="{$make_default_url}">
        <h1 class="mb-lg-5">{t}Мои карты{/t}</h1>
        {if $payment_list}
            <div class="mb-5">
                {t}На этой странице вы можете отвязать привязанные вами ранее карты{/t}<br><br>
                {t}Также вы можете привязать другую карту. Мы спишем с вашей карты 1 рубль и затем сразу вернем его обратно. Это необходимо, чтобы мы могли автоматически взимать оплату за заказы после их комплектации.{/t}
            </div>
            {foreach $payment_list as $payment}
                {$payment_type = $payment->getTypeObject()}
                {$saved_method_list = $payment_type->getSavedPaymentMethods($user)}

                <div class="rs-payment mb-5" data-id="{$payment.id}">
                    <h2>{$payment.title}</h2>
                    {if $saved_method_list}
                        <div class="mb-lg-6 mb-5 last-child-margin-remove">
                            {foreach $saved_method_list as $saved_method}
                            <div class="lk-my-card mb-3 rs-payment-method" data-id="{$saved_method.id}">
                                <div class="text-gray">{$saved_method->getType()}</div>
                                <div class="lk-my-card__info">{$saved_method.subtype} <span class="fw-bold ms-3 rs-payment-method-title">{$saved_method.title}</span>
                                    {if $saved_method.is_default}
                                        <span>({t}основная{/t})</span>
                                    {/if}
                                </div>
                                <div class="ms-sm-4">
                                    {if !$saved_method.is_default}
                                        <a class="warning-link me-3 fs-5 rs-payment-method-makedefault">{t}Сделать основной{/t}</a>
                                    {/if}
                                    <a class="danger-link fs-5 rs-payment-method-delete">{t}Отвязать{/t}</a>
                                </div>
                            </div>
                            {/foreach}
                        </div>
                    {/if}
                    <div>
                        <a href="{$add_method_urls[$payment.id]}" class="btn btn-primary col-12 col-sm-auto">{t}Привязать новую карту{/t}</a>
                    </div>
                </div>
            {/foreach}
        {else}
            {include file="%THEME%/helper/usertemplate/include/empty_list.tpl" reason="{t}На сайте нет способов оплаты, <br>поддерживащих рекуррентные платежи{/t}"}
        {/if}
    </div>
{/block}