<div class="mb-5">
    <div class="mb-4">
        <div class="fs-3">{t}Способ получения{/t}</div>

        {if $order->getErrorsByForm('delivery')}
            <div class="invalid-feedback d-block">{$order->getErrorsByForm('delivery', ', ')}</div>
        {/if}
    </div>

    <div class="row row-cols-1 g-3">
        {foreach $delivery_list as $item}
        {$type_object = $item->getTypeObject()}
            <div>
                <div class="checkout-radio">
                    <input type="radio" name="delivery" value="{$item.id}"
                           class="rs-checkout_triggerUpdate" id="dlv_{$item.id}"
                           {if $order.delivery == $item.id}checked{/if} {if $type_object->hasSelectError($order)}disabled="disabled"{/if}>

                    <label for="dlv_{$item.id}">
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
                        <span class="text-gray">{$item.description}</span>

                        {if $type_object->canCalculateCostByDeliveryAddress($address) && !$type_object->getSelectError($order) && !$type_object->hasCheckoutError($order)}
                            <span class="d-flex align-items-center justify-content-between mt-3">
                                <span class="me-3">{$order->getDeliveryCostText($item)}</span>
                                <span>{$order->getDeliveryExtraText($item)}</span>
                            </span>
                        {/if}

                        {if $select_error = $type_object->getSelectError($order)}
                            <div class="fs-6 danger-link mt-3">{$select_error}</div>
                        {elseif $checkout_error = $type_object->getCheckoutError($order)}
                            <div class="fs-6 text-warning mt-3">{$checkout_error}</div>
                        {/if}
                    </label>
                </div>
            </div>
        {/foreach}
    </div>
</div>