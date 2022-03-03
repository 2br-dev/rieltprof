<h3 class="h3">{t}Выбор способа доставки{/t}</h3>

{if $order->getErrorsByForm('delivery')}
    <div class="formFieldError margin-bottom">{$order->getErrorsByForm('delivery', ', ')}</div>
{/if}

<div class="order-list-items">
    {foreach $delivery_list as $item}
        {$type_object = $item->getTypeObject()}

        <div class="item">
            <div class="radio-column">
                <input type="radio" name="delivery" value="{$item.id}" class="rs-checkout_triggerUpdate" id="dlv_{$item.id}" {if $order.delivery == $item.id}checked{/if} {if $type_object->hasSelectError($order)}disabled="disabled"{/if}>
            </div>

            <div class="info-column">
                <div class="orderList_itemLine">
                    <label class="h3" for="dlv_{$item.id}" class="title">{$item.title}</label>
                    {if $type_object->canCalculateCostByDeliveryAddress($address) && !$type_object->getSelectError($order) && !$type_object->hasCheckoutError($order)}
                        <span class="extra">{$order->getDeliveryExtraText($item)}</span>
                        <span class="price-value">{$order->getDeliveryCostText($item)}</span>
                    {/if}
                </div>

                <div class="descr">
                    {if $type_object->hasSelectError($order)}
                        <div class="something-wrong">{$type_object->getSelectError($order)}</div>
                    {/if}
                    {if !empty($item.picture)}
                        <img class="logoService" src="{$item.__picture->getUrl(100, 100, 'xy')}" alt="{$item.title}"/>
                    {/if}
                    {$item.description}
                </div>
            </div>
        </div>
    {/foreach}
</div>