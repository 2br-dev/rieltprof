{if $order->__code->isEnabled()}
    <div class="form-group captcha">
        <label class="label-sup">{$order->__code->getTypeObject()->getFieldTitle()}</label>
        {$order->getPropertyView('code')}
    </div>
{/if}

{*
{if $order->__code->isEnabled()}
    <div class="uk-width-1-1 captcha">
        <label class="uk-form-label" for="name">{$order->__code->getTypeObject()->getFieldTitle()}*</label>
        {$order->getPropertyView('code')}
    </div>
{/if}*}
