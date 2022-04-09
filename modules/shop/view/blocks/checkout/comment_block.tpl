<div class="checkout-block mb-lg-5 mb-4">
    <div class="d-flex align-items-center mb-5">
        <div class="checkout-block__num"></div>
        <div class="checkout-block__title">{t}Дополнительные данные{/t}</div>
    </div>
    <div class="row row-cols-1 g-3">
        <div>
            <label class="form-label">{t}Комментарий к заказу{/t}</label>
            {$order->getPropertyView('comments')}
        </div>

        {if $order_user_fields->notEmpty()}
            {foreach $order_user_fields->getStructure() as $field}
                <div>
                    <label class="form-label">{$field.title}</label>
                    {$order_user_fields->getForm($field.alias, '%THEME%/helper/forms/userfields_forms.tpl')}

                    {$errname = $order_user_fields->getErrorForm($field.alias)}
                    {$error = $order->getErrorsByForm($errname, ', ')}
                    {if !empty($error)}
                        <span class="invalid-feedback d-block">{$error}</span>
                    {/if}
                </div>
            {/foreach}
        {/if}
    </div>
</div>

{if $cart_data.has_error}
    <div class="invalid-feedback d-block rs-checkout_cartError">{t}В корзине есть ошибки, оформление заказа невозможно{/t}</div>
{/if}

{if $order->getNonFormErrors()}
    <div class="invalid-feedback d-block">{implode(', ', $order->getNonFormErrors())}</div>
{/if}