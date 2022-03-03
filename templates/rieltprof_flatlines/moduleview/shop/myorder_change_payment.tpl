<div class="changePaymentWrapper form-style modal-body reserve-form">
    <h2 class="h2"  data-dialog-options='{ "width": "400" }'>{t}Способы оплаты{/t}</h2>
    {if $success}
        <div class="infotext forms success">
            {t}Способ оплаты изменен{/t}
        </div>
    {else}
        <form method="POST" class="forms" enctype="multipart/form-data" action="{urlmake}">
            {csrf}
            {$this_controller->myBlockIdInput()}

            {if $errors}
                <div class="pageError">
                    {foreach from=$errors item=error_field}
                        {foreach from=$error_field item=error}
                            <p>{$error}</p>
                        {/foreach}
                    {/foreach}
                </div>
            {/if}

            {foreach $payments as $payment}
                <div class="formLine">
                    <input id="payment{$payment.id}" {if $payment.id == $order.payment}checked{/if} type="radio" name="payment" value="{$payment.id}">
                    <label for="payment{$payment.id}" class="fielName">{$payment.title}</label>
                </div>
            {/foreach}
            <div class="form__menu_buttons mobile-center">
                <button type="submit" class="link link-more">{t}Отправить{/t}</button>
            </div>
        </form>
    {/if}
</div>