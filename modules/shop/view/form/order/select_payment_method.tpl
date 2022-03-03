<form action="{adminUrl do='interfaceRecurringPaymentsAction' order_id=$order.id action='select_saved_method'}" class="payment-methods_list crud-form">
    {foreach $saved_payment_methods as $saved_payment_method}
        <label class="payment-methods_item">
            {$checked = false}
            {if $order.saved_payment_method_id}
                {if $order.saved_payment_method_id == $saved_payment_method.id}
                    {$checked = true}
                {/if}
            {elseif $saved_payment_method.is_default}
                {$checked = true}
            {/if}
            <input type="radio" name="saved_payment_method_id" value="{$saved_payment_method.id}" class="payment-methods_item-radio" {if $checked}checked{/if}>
            <div class="payment-methods_item-block">
                <div class="payment-methods_item-type">{$saved_payment_method->getType()}</div>
                <div>{$saved_payment_method.subtype}</div>
                <div class="payment-methods_item-title">{$saved_payment_method.title}</div>
                {if $saved_payment_method.is_default}
                    <div class="payment-methods_item-default">({t}по умолчанию{/t})</div>
                {/if}
            </div>
        </label>
    {/foreach}
</form>