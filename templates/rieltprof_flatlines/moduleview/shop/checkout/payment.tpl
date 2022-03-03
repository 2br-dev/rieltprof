{* Оформление заказа. Шаг - Выбор способа оплаты *}

<div class="page-registration-steps">
    <div class="t-registration-steps">

        {* Текущий шаг оформления заказа *}
        {moduleinsert name="\Shop\Controller\Block\CheckoutStep"}

        <div class="form-style">
            <form method="POST" class="t-order">
                <div class="t-order_method-of-payment">
                    <h3 class="h3">{t}Выбор способа оплаты{/t}</h3>

                    {if $order->hasError()}
                        <div class="page-error">
                            {foreach $order->getErrors() as $item}
                                <p>{$item}</p>
                            {/foreach}
                        </div>
                    {/if}
                    <input type="hidden" name="payment" value="0">

                    <div class="order-list-items">
                        {foreach $pay_list as $item}
                            <div class="item">
                                <div class="radio-column">
                                    <input type="radio" name="payment" value="{$item.id}" id="pay_{$item.id}" {if $order.payment==$item.id}checked{/if}>
                                </div>

                                <div class="info-column">
                                    <div class="line">
                                        <label class="h3 title" for="pay_{$item.id}">{$item.title}</label>
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

                <div class="form__menu_buttons text-center next">
                    <button type="submit" class="link link-more">{t}Далее{/t}</button>
                </div>
            </form>
        </div>
    </div>
</div>