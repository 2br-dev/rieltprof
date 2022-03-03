{addjs file="%shop%/order/rs.checkout.js"}
{addcss file="%shop%/order/checkout.css"}

{$shop_config = ConfigLoader::byModule('shop')}
{$users_config = ConfigLoader::byModule('users')}
{$cart_checkout = $shop_config->getCheckoutType() == 'cart_checkout'}
{$root_url = $router->getRootUrl(true)}
{$url = $router->getUrl('shop-block-checkout', ['_block_id' => $_block_id])}
{$region_change_url = $router->getUrl('shop-front-selectedaddresschange')}
{$pvz_select_url = $router->getUrl('shop-front-selectpvz')}

<div id="order-form" class=" form-style checkout rs-checkout" data-url="{$url}" data-region-change-url="{$region_change_url}" data-pvz-select-url="{$pvz_select_url}" data-root-url="{$root_url}">
    <form class="checkout_form rs-checkout_form {if !$cart_checkout}col-xs-12 col-md-9{/if}" method="POST">

        {$errors=$order->getErrorsStr()}
        {if $errors}
            <div class="page-error">
                {$errors}
            </div>
        {/if}

        {if !$cart_checkout && !$is_auth}
            <div class="cart-authorization-info">
                {t url=$users_config->getAuthorizationUrl()}Если Вы регистрировались ранее, пожалуйста, <a href="%url" class="inDialog">авторизуйтесь</a>.{/t}
            </div>
        {/if}

        <div class="checkout_block rs-checkout_cityBlock">{$blocks.city}</div>

        {if !$shop_config.hide_delivery}
            <div class="checkout_block">
                <div class="rs-checkout_deliveryBlock">{$blocks.delivery}</div>
            </div>
            <div class="rs-checkout_addressBlock">{$blocks.address}</div>
        {/if}

        {if !$shop_config.hide_payment}
            <div class="rs-checkout_paymentBlock">{$blocks.payment}</div>
        {/if}

        <div class="checkout_block" {if $is_auth}style="order: -1;"{/if}>
            <div class="rs-checkout_userBlock">{$blocks.user}</div>
            <div class="rs-checkout_captchaBlock">{$blocks.captcha}</div>
        </div>

        {if $cart_checkout}
            <div class="rs-checkout_totalBlock">{$blocks.total}</div>
        {/if}

        <div class="rs-checkout_commentBlock">{$blocks.comment}</div>

        <div class="t-order_button-block rs-checkout_lockOnUpdate">
            <button class="link link-more{if $is_agreement_require} disabled{/if} rs-checkout_submitButton" type="submit">{t}Подтвердить заказ{/t}</button>
            <a href="{$router->getRootUrl()}" class="link link-del">{t}Продолжить покупки{/t}</a>
        </div>
    </form>

    {if !$cart_checkout}
        <div class="col-xs-12 col-md-3 mobileMoveTop">
            <div class="sticky sidebar">
                <div class="rs-checkout_productBlock">{$blocks.products}</div>
                <div class="rs-checkout_totalBlock">{$blocks.total}</div>
            </div>
        </div>
    {/if}
</div>