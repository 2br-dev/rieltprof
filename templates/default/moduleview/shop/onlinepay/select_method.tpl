{addcss file='%shop%/savedpaymentmethods.css'}

<form action="{$url->getSelfUrl()}" class="selectPaymentMethod  col-md-6">
    <input type="hidden" name="json_params" value='{$json_params}'>
    <input type="hidden" name="sign" value="{$sign}">

    {if empty($saved_methods)}
        <input type="hidden" name="params[payment_method]" value="0">
        {if $recurring_type == 'only_save_method'}
            <h2>{t}Призязка карты{/t}</h2>
            <div class="selectPaymentMethod_hint">
                {t}Привяжите вашу карту к вашему аккаунту на сайте{/t} {$url->getDomainStr()}.
                {t}Мы спишем с вашей карты 1 рубль и затем сразу вернем его обратно.{/t}
                <br><br>
                {t}Это необходимо, чтобы мы могли автоматически взимать оплату за собранные заказы.{/t}
                {t}Средства будут списаны только после полной комплектации вашего заказа.{/t}
            </div>
            <input type="submit" value="{t}Привязать карту{/t}" class="link link-more link-answer">
        {else}
            <h2>{t}Выбор карты{/t}</h2>
            <div class="selectPaymentMethod_hint">
                {t}У вас нет сохранённых карт.{/t}
            </div>
            <input type="submit" value="{t}Оплатить{/t}" class="link link-more link-answer">
        {/if}
    {else}
        {$submit_disabled = true}

        <h2>{t}Выбор карты{/t}</h2>
        <div class="selectPaymentMethod_hint">
            {if $recurring_type == 'only_save_method'}
                {t}Выберите одну из ранее привязанных Вами карт для последующей оплаты заказа или привяжите новую карту.{/t}
            {else}
                {t}Выберите одну из ранее привязанных Вами карт или используйте новую карту для оплаты.{/t}
            {/if}
        </div>
        <div class="selectPaymentMethod_list">
            {foreach $saved_methods as $saved_method}
                {if $saved_method.is_default}
                    {$submit_disabled = false}
                {/if}
                <label class="selectPaymentMethod_item">
                    <input type="radio" name="params[payment_method]" value="{$saved_method.id}" class="selectPaymentMethod_itemRadio" {if $saved_method.is_default}checked{/if}>
                    <div class="selectPaymentMethod_itemBlock">
                        <div>
                            <div class="selectPaymentMethod_methodItem_type">{$saved_method->getType()}</div>
                            <div class="selectPaymentMethod_methodItem_subtype">{$saved_method.subtype}</div>
                            <div class="selectPaymentMethod_methodItem_title">{$saved_method.title}</div>
                            {if $saved_method.is_default}
                                <div class="selectPaymentMethod_methodItem_default">({t}основная{/t})</div>
                            {/if}
                        </div>
                        <div class="selectPaymentMethod_itemBlock_hint">{t}Сохранённая ранее карта{/t}</div>
                    </div>
                </label>

            {/foreach}
            <label class="selectPaymentMethod_item">
                <input type="radio" name="params[payment_method]" value="0" class="selectPaymentMethod_itemRadio">
                <div class="selectPaymentMethod_itemBlock">
                    <b>
                        {if $recurring_type == 'only_save_method'}{t}Привязать новую карту{/t}{else}{t}Оплатить с другой карты{/t}{/if}
                    </b>
                    {if $recurring_type == 'only_save_method'}
                        <div class="selectPaymentMethod_itemBlock_hint">
                            {t}Мы спишем с вашей карты 1 рубль и затем сразу вернем его обратно.{/t}
                            {t}Это необходимо, чтобы мы могли автоматически взимать оплату за собранные заказы.{/t}
                            {t}Средства будут списаны только после полной комплектации вашего заказа.{/t}
                        </div>
                    {/if}
                </div>
            </label>
        </div>

        <input type="submit" value="{if $recurring_type == 'only_save_method'}{t}Выбрать{/t}{else}{t}Оплатить{/t}{/if}" class="link link-more link-answer" {if $submit_disabled}disabled{/if}>
    {/if}
</form>




<script>
    document.querySelectorAll('.selectPaymentMethod_itemRadio').forEach((element) => {
        element.addEventListener('change', () => {
            document.querySelector('.selectPaymentMethod [type="submit"]').disabled = false;
        });
    });
</script>