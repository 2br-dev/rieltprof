{addjs file="%shop%/selectaddresschange.js"}
{addjs file="%shop%/rscomponent/checkout.js"}

{$main_config = ConfigLoader::byModule('main')}
{$main_config->initMapJs()}
{$shop_config = ConfigLoader::byModule('shop')}
{$users_config = ConfigLoader::byModule('users')}
{$cart_checkout = $shop_config->getCheckoutType() == 'cart_checkout'}

{$root_url = $router->getRootUrl(true)}
{$url = $router->getUrl('shop-block-checkout', ['_block_id' => $_block_id])}
{$region_change_url = $router->getUrl('shop-front-selectedaddresschange')}
{$pvz_select_url = $router->getUrl('shop-front-selectpvz')}

{function helpText}
    <div class="checkout-help">
        <div class="mb-4">{t}Если у вас возникли вопросы при оформлении заказа, продолжите его оформление с менеджером. Или свяжитесь с нами для консультации.{/t}</div>
        <div><a class="text-inherit fs-3 fw-bold" href="tel:{$THEME_SETTINGS.default_phone|format_phone}">{$THEME_SETTINGS.default_phone}</a></div>
    </div>
{/function}

<div class="col rs-checkout"
     data-url="{$url}"
     data-region-change-url="{$region_change_url}"
     data-pvz-select-url="{$pvz_select_url}"
     data-root-url="{$root_url}">

    <div class="row">
        {if !$cart_checkout}
            <div class="offset-xl-1 col order-lg-last mb-6 mb-lg-0">
                <div class="rs-checkout_productBlock">
                    {$blocks.products}
                </div>
                <div class="mt-5 aside-sticky d-none d-lg-block">
                    {helpText}
                </div>
            </div>
        {/if}
        <div {if !$cart_checkout}class="col-lg-7"{/if}>
            {if !$cart_checkout && !$is_auth}
                <div class="checkout-auth mb-5">
                    {t url=$users_config->getAuthorizationUrl()}Если Вы регистрировались ранее, пожалуйста, <a href="%url" class="rs-in-dialog">авторизуйтесь</a>.{/t}
                </div>
            {/if}
            <form class="rs-checkout_form" method="POST">
                <div class="last-child-margin-remove mb-3 d-flex flex-column">
                    <div class="checkout-block mb-lg-5 mb-4">
                        <div class="d-flex align-items-center mb-5">
                            <div class="checkout-block__num"></div>
                            <div class="checkout-block__title">{t}Доставка{/t}</div>
                        </div>
                        <div class="rs-checkout_cityBlock">{$blocks.city}</div>
                        {if !$shop_config.hide_delivery}
                            <div class="rs-checkout_deliveryBlock">{$blocks.delivery}</div>
                            <div class="rs-checkout_addressBlock">{$blocks.address}</div>
                        {/if}
                    </div>
                    {if !$shop_config.hide_payment}
                        <div class="rs-checkout_paymentBlock">{$blocks.payment}</div>
                    {/if}
                    <div {if $is_auth}style = "order:-1"{/if}>
                        <div class="rs-checkout_userBlock">{$blocks.user}</div>
                        <div class="rs-checkout_captchaBlock">{$blocks.captcha}</div>
                    </div>
                    <div class="rs-checkout_commentBlock">{$blocks.comment}</div>
                </div>
                <div class="rs-checkout_totalBlock">{$blocks.total}</div>
            </form>
        </div>
        <div class="d-lg-none mt-5">
            {helpText}
        </div>
    </div>
</div>