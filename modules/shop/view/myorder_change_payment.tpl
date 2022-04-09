{* Диалог изменения способа оплаты в заказе *}
{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Изменить способ оплаты{/t}{/block}
{block "body"}
    {$is_dialog_wrap=$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}
    {if $errors}
        <div class="alert alert-danger" role="alert">{$errors|join:", "}</div>
    {/if}

    <form method="POST" enctype="multipart/form-data" action="{urlmake}">
        {csrf}
        {$this_controller->myBlockIdInput()}

        <div class="g-4 row row-cols-1">
            {foreach $payments as $payment}
                <div>
                    <label for="payment{$payment.id}" class="check">
                        <input class="radio" id="payment{$payment.id}" {if $payment.id == $order.payment}checked{/if} type="radio" name="payment" value="{$payment.id}"/>
                        <span>{$payment.title}</span>
                    </label>
                </div>
            {/foreach}
            <div>
                <button type="submit" class="btn btn-primary w-100">{t}Отправить{/t}</button>
            </div>
        </div>
    </form>
{/block}