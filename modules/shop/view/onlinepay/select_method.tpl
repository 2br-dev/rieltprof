<div class="section 100vh">
    <div class="container">
        <div class="col-xxl-4 col-xl-6 col-md-8">
            <form action="{$url->getSelfUrl()}">
                <input type="hidden" name="json_params" value='{$json_params}'>
                <input type="hidden" name="sign" value="{$sign}">

                {if empty($saved_methods)}
                    <input type="hidden" name="params[payment_method]" value="0">
                    {if $recurring_type == 'only_save_method'}
                        <h2 class="mb-4">{t}Призязка карты{/t}</h2>
                        <div class="mb-5">
                            {t}Привяжите вашу карту к вашему аккаунту на сайте{/t} {$url->getDomainStr()}.
                            {t}Мы спишем с вашей карты 1 рубль и затем сразу вернем его обратно.{/t}
                            <br><br>
                            {t}Это необходимо, чтобы мы могли автоматически взимать оплату за собранные заказы.{/t}
                            {t}Средства будут списаны только после полной комплектации вашего заказа.{/t}
                        </div>
                        <button class="btn btn-primary col-12 col-md-auto" type="submit">{t}Привязать карту{/t}</button>
                    {else}
                        <h2 class="mb-4">{t}Выбор карты для оплаты{/t}</h2>
                        <div class="mb-5">{t}У вас нет сохранённых карт. Произведите первую оплату, чтобы привязать карту к вашему аккаунту.{/t}</div>

                        <button class="btn btn-primary col-12 col-md-auto" type="submit">{t}Оплатить{/t}</button>
                    {/if}
                {else}
                    {$submit_disabled = true}

                    <h2 class="mb-4">{t}Выбор карты{/t}</h2>
                    <div class="mb-5">
                        {if $recurring_type == 'only_save_method'}
                            {t}Выберите одну из ранее привязанных Вами карт для последующей оплаты заказа или привяжите новую карту.{/t}
                        {else}
                            {t}Выберите одну из ранее привязанных Вами карт или используйте новую карту для оплаты.{/t}
                        {/if}
                    </div>

                    <ul class="select-card-list">
                        {foreach $saved_methods as $saved_method}
                            {if $saved_method.is_default}
                                {$submit_disabled = false}
                            {/if}
                            <li>
                                <div class="select-card-radio">
                                    <input  type="radio" name="params[payment_method]" value="{$saved_method.id}"
                                            id="card-{$saved_method.id}" {if $saved_method.is_default}checked{/if} class="rs-payment-method-radio">
                                    <label for="card-{$saved_method.id}">
                                        <span class="select-card-radio__title">
                                            <span>{$saved_method->getType()}</span>
                                            <span class="select-card-radio__info">
                                                <span>{$saved_method.subtype}</span>
                                                <span class="fw-bold ms-3">{$saved_method.title}</span>
                                            </span>
                                        </span>
                                        <span class="text-gray fs-5">Сохраненная ранее карта
                                            {if $saved_method.is_default}
                                                <span>({t}основная{/t})</span>
                                            {/if}
                                        </span>
                                    </label>
                                </div>
                            </li>
                        {/foreach}
                        <li>
                            <div class="select-card-radio">
                                <input  type="radio" name="params[payment_method]" value="0"
                                        id="card-0" {if $saved_method.is_default}checked{/if} class="rs-payment-method-radio">

                                <label for="card-0" class="select-card-add">
                                    <img class="mb-2" src="{$THEME_IMG}/icons/add.svg" alt="">
                                    <div class="mb-5">
                                        {if $recurring_type == 'only_save_method'}{t}Привязать новую карту{/t}{else}{t}Оплатить с другой карты{/t}{/if}
                                    </div>

                                    {if $recurring_type == 'only_save_method'}
                                        <div>
                                            {t}Мы спишем с вашей карты 1 рубль и затем сразу вернем его обратно.{/t}
                                            {t}Это необходимо, чтобы мы могли автоматически взимать оплату за собранные заказы.{/t}
                                            {t}Средства будут списаны только после полной комплектации вашего заказа.{/t}
                                        </div>
                                    {/if}
                                </label>
                            </div>
                        </li>
                    </ul>
                {/if}

                <input type="submit" value="{if $recurring_type == 'only_save_method'}{t}Выбрать{/t}{else}{t}Оплатить{/t}{/if}"
                       class="btn btn-primary col-12 col-md-auto rs-select-payment" {if $submit_disabled}disabled{/if}>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.rs-payment-method-radio').forEach((element) => {
        element.addEventListener('change', () => {
            document.querySelector('.rs-select-payment').disabled = false;
        });
    });
</script>
