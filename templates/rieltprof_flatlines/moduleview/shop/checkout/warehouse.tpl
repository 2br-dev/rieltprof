{* Оформление заказа. Шаг - Выбор склада самовывоза *}

<div class="page-registration-steps">
    <div class="t-registration-steps">

        {* Текущий шаг оформления заказа *}
        {moduleinsert name="\Shop\Controller\Block\CheckoutStep"}

        <div class="form-style">
            <form method="POST">
                <div class="t-order_method-of-payment">
                    <h3 class="h3">{t}Выбор склада самовывоза{/t}</h3>

                    {if $order->hasError()}
                        <div class="page-error">
                            {foreach $order->getErrors() as $item}
                                <p>{$item}</p>
                            {/foreach}
                        </div>
                    {/if}
                    <input type="hidden" name="warehouse" value="0">

                    <div class="order-list-items">
                        {$pvzList = $order->getDelivery()->getTypeObject()->getOption('pvz_list')}
                        {foreach $warehouses_list as $item}
                            {if empty($pvzList) || in_array(0, $pvzList) || in_array($item.id, $pvzList)}
                                <div class="item">
                                    <div class="radio-column">
                                        <input type="radio" name="warehouse" value="{$item.id}" id="wh_{$item.id}" {if ($order.warehouse>0)&&($order.warehouse==$item.id)}checked{elseif ($order.warehouse==0) && $item.default_house}checked{/if} >
                                    </div>

                                    <div class="info-column">
                                        <div class="line">
                                            <label class="h3 title" for="wh_{$item.id}">{$item.title}</label>
                                        </div>

                                        <div class="descr">
                                            {if !empty($item.adress)}<p>{t}Адрес{/t}: {$item.adress}</p>{/if}
                                            {if !empty($item.phone)}<p>{t}Телефон{/t}: {$item.phone}</p>{/if}
                                            {if !empty($item.work_time)}<p>{t}Время работы{/t}: {$item.work_time}</p>{/if}
                                            <p><a href="{$item->getUrl()}" class="link link-white" target="_blank">{t}Подробнее{/t}</a></p>
                                        </div>
                                    </div>
                                </div>
                            {/if}
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