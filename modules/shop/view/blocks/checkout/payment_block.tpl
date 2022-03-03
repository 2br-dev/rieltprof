<div class="checkout_block">
    <h3 class="h3">{t}Выбор способа оплаты{/t}</h3>
{*    <input type="hidden" name="payment" value="0">*}

    {if $order->getErrorsByForm('payment')}
        <div class="formFieldError margin-bottom">{$order->getErrorsByForm('payment', ', ')}</div>
    {/if}

    <div class="order-list-items">
        {foreach $payment_list as $item}
            <div class="item">
                <div class="radio-column">
                    <input type="radio" name="payment" value="{$item.id}" class="rs-checkout_triggerUpdate" id="pay_{$item.id}" {if $order.payment==$item.id || count($payment_list) == 1}checked{/if}>
                </div>

                <div class="info-column">
                    <div class="line">
                        <label class="h3" for="pay_{$item.id}" class="title">{$item.title}</label>
                    </div>

                    <div class="descr">
                        {if !empty($item.picture)}
                            <img class="logoService" src="{$item.__picture->getUrl(100, 100, 'xy')}" alt="{$item.title}"/>
                        {/if}
                        {$item.description}
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
</div>

{if $order_user_fields->notEmpty()}
    <div class="checkout_block">
        <h3 class="h3">{t}Дополнительные сведения{/t}</h3>
        {foreach $order_user_fields->getStructure() as $field}
            <div class="form-group">
                <label class="label-sup">{$field.title}</label>
                {$order_user_fields->getForm($field.alias)}
                {$errname = $order_user_fields->getErrorForm($field.alias)}
                {$error = $order->getErrorsByForm($errname, ', ')}
                {if !empty($error)}
                    <span class="formFieldError">{$error}</span>
                {/if}
            </div>
        {/foreach}
    </div>
{/if}