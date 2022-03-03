{* Оформление заказа. Шаг - Выбор доставки *}

<div class="page-registration-steps">
    <div class="t-registration-steps">

        {* Текущий шаг оформления заказа *}
        {moduleinsert name="\Shop\Controller\Block\CheckoutStep"}

        <div class="form-style">
            <form method="POST" class="t-order" id="order-form">
                <div class="t-order_method-of-payment">
                    <h3 class="h3">{t}Выбор способа доставки{/t}</h3>

                    {if $order->hasError()}
                        <div class="page-error">
                            {foreach $order->getErrors() as $item}
                                <p>{$item}</p>
                            {/foreach}
                        </div>
                    {/if}
                    <input type="hidden" name="delivery" value="0">

                    <div class="order-list-items">
                        {foreach $delivery_list as $item}
                            {$addittional_html = $item->getAddittionalHtml($order)}
                            {$something_wrong = $item->getTypeObject()->somethingWrong($order)}
                            <div class="item">
                                <div class="radio-column">
                                    <input type="radio" name="delivery" value="{$item.id}" id="dlv_{$item.id}" {if $order.delivery==$item.id}checked{/if} {if $something_wrong}disabled="disabled"{/if}>
                                </div>

                                <div class="info-column">
                                    <div class="line">
                                        <label class="h3 title" for="dlv_{$item.id}">{$item.title}</label>
                                        <span class="price">
                                            {if !$something_wrong}
                                                <span class="help">{$order->getDeliveryExtraText($item)}</span>
                                                <span class="price-value">{$order->getDeliveryCostText($item)}</span>
                                            {/if}
                                        </span>
                                    </div>

                                    <div class="descr">
                                        {if $something_wrong}
                                            <div class="something-wrong">{$something_wrong}</div>
                                        {/if}
                                        {if !empty($item.picture)}
                                            <img class="logoService" src="{$item.__picture->getUrl(100, 100, 'xy')}" alt="{$item.title}"/>
                                        {/if}
                                        {$item.description}
                                    </div>

                                    <div class="additionalInfo">{$addittional_html}</div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>

                <div class="form__menu_buttons text-center next">
                    <button type="submit" class="link link-more">{t}Далее{/t}</button>
                </div>
            </form>
        </div>
    </div>
</div>