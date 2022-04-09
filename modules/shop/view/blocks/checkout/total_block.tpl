
<ul class="checkout-total mb-5">
    {hook name="shop-block-checkout:total" title="{t}Подтверждение заказа:итого{/t}"}
    <li>
        <div class="text-gray me-3">{t}Товаров на сумму{/t}:</div>
        <div class="text-nowrap">{$cart_data.total_without_delivery}</div>
    </li>
    {if $cart_data.total_discount_unformatted > 0}
        <li>
            <div class="text-gray me-3">{t}Скидка на заказ{/t}:</div>
            <div class="text-nowrap">{$cart_data.total_discount}</div>
        </li>
    {/if}
    {foreach $cart_data.taxes as $tax}
        <li {if !$tax.tax.included}class="fw-bold"{/if}>
            <div class="text-gray me-3">{$tax.tax->getTitle()}</div>
            <div class="text-nowrap">{$tax.cost}</div>
        </li>
    {/foreach}
    {if $order.delivery}
        <li class="bold">
            <div class="text-gray me-3">{t}Доставка{/t}: {$delivery.title}</div>
            <div class="text-nowrap">{$cart_data.delivery.cost}</div>
        </li>
    {/if}
    {if $cart_data.payment_commission}
        <li class="fw-bold">
            <div class="text-gray me-3">{if $cart_data.payment_commission.cost>0}{t}Комиссия{/t}{else}{t}Скидка{/t}{/if} {t}при оплате через{/t} "{$order->getPayment()->title}": </div>
            <div class="text-nowrap">{$cart_data.payment_commission.cost}</div>
        </li>
    {/if}
    {foreach $cart->getCouponItems() as $item}
        <li>
            <div class="text-gray me-3">{t}Купон на скидку{/t} {$item.coupon.code}</div>
            <div class="text-nowrap"></div>
        </li>
    {/foreach}
    <li id="checkout-total-fixed-limit">
        <div class="text-gray me-3">{t}Итого{/t}:</div>
        <div class="fs-2 fw-bold text-nowrap">{$cart_data.total}</div>
    </li>
    {/hook}
</ul>

<div class="row g-3 align-items-center justify-content-end">
    {hook name="shop-block-checkout:agreement" title="{t}Подтверждение заказа:соглашение{/t}"}
    <div class="col-sm">
        {if $is_agreement_require = $shop_config->require_license_agree}
            <div class="checkbox fs-5">
                <input type="checkbox" name="license_agree" class="rs-checkout_licenseAgreementCheckbox" value="1" id="iagree" {if $smarty.post.license_agree}checked{/if}>
                <label class="align-items-center" for="iagree">
                    <span class="checkbox-attr">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z" />
                            <path class="checkbox-attr__check"  fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z" />
                        </svg>
                    </span>
                    <span>{t alias="Заказ на одной странице - ссылка на условия предоставления услуг" agreement_url=$router->getUrl('shop-front-licenseagreement')}Я даю согласие на обработку своих персональных данных и согласен с <a href="%agreement_url" class="rs-in-dialog" target="_blank">условиями предоставления услуг</a>{/t}</span>
                </label>
            </div>
        {/if}
    </div>
    <div class="col-sm-auto">
        <button type="submit" class="btn btn-primary col-12 col-sm-auto {*if $is_agreement_require} disabled{/if*} rs-checkout_submitButton">{t}Оформить заказ{/t}</button>
    </div>
    {/hook}
</div>

<div class="checkout-total-fixed">
    {hook name="shop-block-checkout:confirm" title="{t}Подтверждение заказа:кнопка подтверждения{/t}"}
    <div class="container">
        <div class="row ms-0 me-0">
            <div class="{if $shop_config->getCheckoutType() == 'cart_checkout'}col-xl-6 offset-xl-6{else}col-lg-7{/if}">
                <div class="row g-2 justify-content-between align-items-center row-cols-sm-auto row-cols-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="checkout-total-fixed__key">{t}Итого:{/t}</div>
                        <div class="checkout-total-fixed__sum">{$cart_data.total}</div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary w-100">{t}Оформить заказ{/t}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/hook}
</div>