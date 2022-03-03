{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {$delivery=$data->order->getDelivery()}
    {$address=$data->order->getAddress()}
    {$pay=$data->order->getPayment()}
    {$cart=$data->order->getCart()}
    {$user=$data->order->getUser()}
    {$order_data=$cart->getOrderData(true, false)}
    {$products=$cart->getProductItems()}

    <h1>{t url=$url->getDomainStr()}Вы сделали заказ в интернет-магазине %url.{/t}</h1>
    {t}Ваш заказ будет обработан в течение 1 рабочего дня.{/t}<br>
    {t}При необходимости, с Вами свяжется наш менеджер.{/t}<br>
    {t}Номер Вашего заказа:{/t} {$data->order.order_num}<br>
    {t}Заказ оформлен:{/t} {$data->order.dateof|date_format:"%d.%m.%Y"}<br>

    <p><strong>{t}Параметры заказа{/t}</strong></p>
    {if $data->order.delivery && !$delivery->getTypeObject()->isMyselfDelivery()}
        {t}Адрес доставки{/t}: {$address->getLineView()}<br>
    {/if}
    {if $data->order.payment}
        {t}Способ оплаты{/t}: {$pay.title}<br>
        {if $pay->hasDocs()}{assign var=type_object value=$pay->getTypeObject()}
            {t}Документы на оплату{/t}: {foreach from=$type_object->getDocsName() key=key item=doc}<a href="{$type_object->getDocUrl($key, true)}" target="_blank">{$doc.title}</a> {/foreach}<br>
        {/if}
        {if $data->order->canOnlinePay()}
            <a href="{$data->order->getOnlinePayUrl(true)}">{t}Ссылка на оплату{/t}</a><br>
        {/if}
    {/if}
    {if $data->order.delivery}
        {t}Способ доставки{/t}: {$delivery.title}<br>

        {$pvz = $data->order->getSelectedPvz()}
        {if $pvz}
            {t}Пункт самовывоза{/t} - "{$pvz->getTitle()}" ({t}Адрес{/t}: {$pvz->getAddress()}) <br>
        {elseif $data->order.warehouse && $delivery->getTypeObject()->isMyselfDelivery()}
            {$warehouse=$data->order->getWarehouse()}
            {t}Склад самовывоза{/t} - "{$warehouse.title}" ({t}Адрес{/t}: {$warehouse.adress}) <br>
        {/if}
    {/if}

    {$check_url=$data->order->getTrackUrl()}
    {if !empty($check_url)}
        {t}Ссылка для отслеживания{/t}: <a href="{$check_url}" target="_blank">{t}Перейти{/t}</a>
    {/if}

    <p><strong>{t}Состав заказа{/t}</strong></p>

    <table cellpadding="5" border="1" bordercolor="#969696" style="border-collapse:collapse; border:1px solid #969696">
        <thead>
        <tr>
            <th>{t}Наименование{/t}</th>
            <th>{t}Код{/t}</th>
            <th>{t}Цена{/t}</th>
            <th>{t}Кол-во{/t}</th>
            <th>{t}Стоимость{/t}</th>
        </tr>
        </thead>
        <tbody>
            {foreach from=$order_data.items key=n item=item}
            {assign var=product value=$products[$n].product}
            <tr>
                <td>
                    {$item.cartitem.title}
                    <br>
                    {if !empty($item.cartitem.multioffers)}
                    {$multioffers_values = unserialize($item.cartitem.multioffers)}
                    {if !empty($multioffers_values)}
                        {$offer = array()}
                        {foreach $multioffers_values as $mo_value}
                            {$offer[] = "{$mo_value.title}: {$mo_value.value}"}
                        {/foreach}
                        {implode(', &nbsp; ', $offer)}
                    {elseif !empty($item.cartitem.model)}
                        {t}Модель{/t}: {$item.cartitem.model}
                    {/if}
                    {/if}
                </td>
                <td>{$item.cartitem.barcode}</td>
                <td>{$item.single_cost}</td>
                <td>{$item.cartitem.amount}</td>
                <td>
                    <span class="cost">{$item.total}</span>
                    {if $item.discount>0}{t}скидка{/t} {$item.discount}{/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
        <tbody>
            {foreach from=$order_data.other key=n item=item}
            <tr>
                <td colspan="4">{$item.cartitem.title}</td>
                <td>{if $item.total>0}{$item.total}{/if}</td>
            </tr>
            {/foreach}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"></td>
                <td>
                    {t}Итого{/t}: {$order_data.total_cost}
                </td>
            </tr>
        </tfoot>
    </table>

    <p>{t href=$router->getUrl('shop-front-myorders',[], true)}Вы можете изменить свои данные и ознакомиться со статусом заказа в разделе <a href="%href">«Личный кабинет»</a>.{/t}</p>
{/block}