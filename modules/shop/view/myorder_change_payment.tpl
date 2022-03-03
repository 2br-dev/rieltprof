<div class="changePaymentWrapper">
    <h2 class="dialogTitle" data-dialog-options='{ "width": "400" }'>{t}Способы оплаты{/t}</h2>
    {if $success}
        <div class="formResult success">
            {t}Способ доставки изменен{/t}
        </div>
    {else}
        <form method="POST" enctype="multipart/form-data" action="{urlmake}">
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

            <table class="formTable tabFrame">
            {foreach $payments as $payment}

                    <tr>
                        <td class="key">
                            <input id="payment{$payment.id}" {if $payment.id == $order.payment}checked{/if} type="radio" name="payment" value="{$payment.id}"/>
                            <label for="payment{$payment.id}">{$payment.title}</label>
                        </td>
                    </tr>

            {/foreach}
            </table>
            <script type="text/javascript">
                $(function() {
                    if ($.fn.styler) {
                        //Стилизуем выпадающий список
                        $("[name='payment']").styler();
                    }
                });
            </script>
            <input type="submit" class="formSave" value="{t}Отправить{/t}"/>
        </form>
    {/if}
</div>