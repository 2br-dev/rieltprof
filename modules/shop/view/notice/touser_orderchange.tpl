{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {$order=$data->order}
    {$order_before=$data->order->this_before_write}
    {if $data->order->before_address}
    {$address_before=$data->order->before_address->getLineView()} {* Предыдущий адрес, если есть *}
    {/if}
    {$currency_api=$data->currency_api}

    <h1>{t order_num=$order.order_num date={$order.dateof|date_format:"%d.%m.%Y"}}Заказ N%order_num от %date был изменен.{/t}</h1>

    <p>{t}Произошли следующие изменения:{/t}</p>
    {if $order_before.status != $order.status} {* Статус *}
       - {t}Статус заказа{/t} "{$order->getStatus()->title}"<br>
    {/if}
    {if ($order_before.cart_md5 != $order->getProductsHash())} {* Товары *}
       - {t}Изменился состав товаров{/t}<br>
    {/if}
    {if floatval($order_before.totalcost) != floatval($order.totalcost)}
       {$cost = $currency_api->getDefaultCurrency()}
       - {t}Общая сумма заказа{/t} - {$order.totalcost|format_price} {$cost.title}<br>
    {/if}
    {if $address_before != $order->getAddress()->getLineView()} {* Адрес *}
       - {t}Адрес доставки{/t} - "{$order->getAddress()->getLineView()}"<br>
    {/if}
    {if $order_before.delivery != $order.delivery} {* Способ доставки *}
       {$delivery=$order->getDelivery()}
       - {t}Способ доставки Вашего заказа{/t} - "{$delivery.title}" (Стоимость: {$order->getDeliveryCostText($delivery)})<br>
    {/if}
    {if $order_before.warehouse != $order.warehouse && $order.warehouse != 0} {* Склад *}
       {$warehouse=$order->getWarehouse()}
       - {t}Склад самовывоза{/t} - "{$warehouse.title}" (Адрес: {$warehouse.adress}) <br>
    {/if}
    {if $order_before.contact_person != $order.contact_person} {* Контактное лицо *}
       - {t}Контактное лицо{/t} - "{$order.contact_person}"<br>
    {/if}
    {$payment=$order->getPayment()}
    {if $order_before.payment != $order.payment} {* Способ оплаты *}
       - {t}Способ оплаты у Вашего заказа{/t} "{$payment.title}".<br>
    {/if}
    {if $order->canOnlinePay()} {* Ссылка "перейти к оплате" *}
        <a href="{$order->getOnlinePayUrl(true)}" class="formSave">{t}Перейти к оплате{/t}</a>
    {elseif $payment->hasDocs()}
        {$type_object=$payment->getTypeObject()}
        {foreach from=$type_object->getDocsName() key=key item=doc}
            <a href="{$type_object->getDocUrl($key, true)}" target="_blank">{$doc.title}</a>{if !$doc@last}<br/>{/if}
        {/foreach}
    {/if}
    {if $order_before.user_text != $order.user_text && !empty($order.user_text)} {* Комментарий пользователю *}
       - {t}Текст покупателю{/t}:
       "{$order.user_text}"<br>
    {/if}

    <p>{t href=$router->getUrl('shop-front-myorders', [], true)}Все подробности заказа Вы можете посмотреть в <a href="%href">личном кабинете</a>, там же будет виден текущий статус Вашего заказа.{/t}</p>
{/block}