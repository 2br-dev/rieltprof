<div class="checkout-block mb-lg-5 mb-4">
    <div class="mb-4">
        <div class="d-flex align-items-center">
            <div class="checkout-block__num"></div>
            <div class="checkout-block__title">{t}Оплата{/t}</div>
        </div>
        {if $order->getErrorsByForm('payment')}
            <div class="invalid-feedback d-block">{$order->getErrorsByForm('payment', ', ')}</div>
        {/if}
    </div>
    <div class="row row-cols-1 g-3">
        {foreach $payment_list as $item}
            <div>
                <div class="checkout-radio">
                    <input type="radio" name="payment" value="{$item.id}" class="rs-checkout_triggerUpdate"
                           id="pay_{$item.id}" {if $order.payment==$item.id || count($payment_list) == 1}checked{/if}>
                    <label for="pay_{$item.id}">
                        <span class="checkout-radio__title">
                            <span class="d-flex align-items-center">
                                {if !empty($item.picture)}
                                    <span class="checkout-radio__img">
                                        <noscript class="loading-lazy">
                                            <img src="{$item.__picture->getUrl(32, 32, 'xy')}"
                                                 srcset="{$item.__picture->getUrl(64, 64, 'xy')} 2x" alt="{$item.title}" loading="lazy">
                                        </noscript>
                                    </span>
                                {/if}
                                <span>{$item.title}</span>
                            </span>
                        </span>
                        <span class="fs-5 text-gray">{$item.description}</span>
                    </label>
                </div>
            </div>
        {/foreach}
    </div>
</div>