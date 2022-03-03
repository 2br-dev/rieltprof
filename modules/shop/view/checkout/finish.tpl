<h2><span>{t}Заказ успешно оформлен{/t}</span></h2>
<p class="mb10 underText">
    {t alias="Конец оформления заказа, текст под заголовком" link=$router->getUrl('shop-front-myorders')}Следить за изменениями статуса заказа можно в разделе <a href="%link" target="_blank">история заказов</a>.
    Все уведомления об изменениях в данном заказе также будут отправлены на электронную почту покупателя.{/t}
</p>
<div class="orderInfo fullwidth">
    <h1 style="font-size:20px; ">{t num=$order.order_num date={$order.dateof|date_format:"d.m.Y"}}Заказ N %num от %date{/t}</h1>
    
    {assign var=user value=$order->getUser()}
    <ul class="section">
        <li><span class="key">{t}Заказчик{/t}:</span> {$user.surname} {$user.name} {$user.midname}</li>
        <li><span class="key">{t}Телефон{/t}:</span> {$user.phone}</li>
        <li><span class="key">E-mail:</span> {$user.e_mail}</li>
    </ul>
    
    {assign var=fmanager value=$order->getFieldsManager()}
    {if $fmanager->notEmpty()}
        <ul class="section">
            {foreach from=$fmanager->getStructure() item=field}
            <li><span class="key">{$field.title}:</span> {$fmanager->textView($field.alias)}</li>
            {/foreach}
        </ul>
    {/if}
    
    {assign var=delivery value=$order->getDelivery()}
    {assign var=address value=$order->getAddress()}
    <ul class="section">
        {if $order.delivery}
            <li><span class="key">{t}Доставка{/t}:</span> {$delivery.title}</li>
        {/if}
        {if $order.only_pickup_points && $order.warehouse} {* Если только самовывоз *}
            <li><span class="key">{t}Пункт самовывоза{/t}:</span> {$order->getWarehouse()->adress}</li>
        {elseif $order.use_addr}
            <li><span class="key">{t}Адрес{/t}:</span> {$address->getLineView()}</li>
        {/if}
    </ul>
    {if $order.payment}
        {assign var=pay value=$order->getPayment()}
        <ul class="section">
            <li><span class="key">{t}Оплата{/t}:</span> {$pay.title}</li>
        </ul>
    {/if}
    {$url=$order->getTrackUrl()}
    {if !empty($url)}
        <ul class="section">
            <li>{t}Данные заказа{/t}: <span><a href="{$url}" target="_blank">{t}Отследить заказ{/t}</a></span></li>
        </ul>
    {/if}
</div>

{if $order->getPayment()->hasDocs()}
<div class="paymentDocuments">
    <h3>{t}Документы на оплату{/t}</h3>
    {if $user.id}
        <p class="helpText underText">{t alias="Конец оформления заказа, документы на оплату" link=$router->getUrl('shop-front-myorders')}Воспользуйтесь следующими документами для оплаты заказа. Эти документы всегда доступны в разделе <a href="%link" target="_blank">история заказов</a>{/t}</p>
    {/if}
    <ul class="docsList">
        {assign var=type_object value=$order->getPayment()->getTypeObject()}
        {foreach from=$type_object->getDocsName() key=key item=doc}
        <li><a href="{$type_object->getDocUrl($key)}" target="_blank">{$doc.title}</a></li>
        {/foreach}
    </ul>
</div>
{/if}

<div class="cartInfo fullwidth">
    {assign var=orderdata value=$cart->getOrderData()}
    <table class="orderBasket">
        <tbody class="head">
            <tr>
                <td></td>
                <td>{t}Количество{/t}</td>
                <td>{t}Цена{/t}</td>
            </tr>
        </tbody>
        <tbody>
            {foreach from=$orderdata.items item=item key=n name="basket"}
            {assign var=orderitem value=$item.cartitem}
            <tr {if $smarty.foreach.basket.first}class="first"{/if}>
                <td>
                    {assign var=barcode value=$orderitem.barcode}
                    {assign var=offer_title value=$orderitem.model}
                    {assign var=multioffer_titles value=$orderitem->getMultiOfferTitles()} 

                    <span class="prod-name">{$orderitem.title}</span>
                    <div class="codeLine">
                        {if $barcode != ''}{t}Артикул{/t}:<span class="value">{$barcode}</span>&nbsp;&nbsp;{/if}
                        {if !empty($multioffer_titles)}
                            <div class="multioffersWrap">
                                {t}Комплектация{/t}:
                                {foreach $multioffer_titles as $multioffer}
                                    <div>
                                        <span class="value">{$multioffer.title} - {$multioffer.value}</span>
                                    </div>
                                {/foreach}
                            </div>
                        {elseif $offer_title != ''}
                            {t}Комплектация{/t}:<span class="value">{$offer_title}</span>
                        {/if}
                    </div>
                </td>
                <td class='amountCell'>
                    {$orderitem.amount} {$orderitem.data.unit}
                </td>
                <td class="totalPriceCell">
                    <span class="priceBlock">
                        <span class="priceValue">{$item.total}</span>
                    </span>
                    <div class="discount">
                        {if $item.discount>0}
                        {t}скидка{/t} {$item.discount}
                        {/if}
                    </div>
                </td>
            </tr>
            {/foreach}
        </tbody>
        <tbody class="additional">
            {foreach from=$orderdata.other item=item name="other"}
            <tr {if $smarty.foreach.other.first}class=""{/if}>
                <td colspan="2">{$item.cartitem.title}</td>
                <td class="price">{if $item.total != 0}{$item.total}{/if}</td>
            </tr>
            {/foreach}
        </tbody>
        <tfoot class="summary">
            <tr>
                <td colspan="2">{t}Итого{/t}</td>
                <td>{$orderdata.total_cost}</td>
            </tr>
        </tfoot>
    </table>

</div>
{if $order->canOnlinePay()}
    <a href="{$order->getOnlinePayUrl()}" class="formSave">{t}Перейти к оплате{/t}</a>
{else}
    <a href="{$router->getRootUrl()}" class="formSave">{t}Завершить заказ{/t}</a>
{/if}