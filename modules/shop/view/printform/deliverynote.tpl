<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <link type="text/css" href="{$mod_css}orderform.css" media="all" rel="stylesheet">
</head>
<body>
{$hl=["n","hl"]}
{$orders = (is_array($order)) ? $order : [$order]}

{foreach $orders as $order}
    {$user = $order->getUser()}
    {$cart = $order->getCart()}
    {$order_data = $cart->getOrderData(true, false)}
    {$products = $cart->getProductItems()}

    <div class="oneOrder">
        {include file="%shop%/printform/head.tpl"}
        <h1>{t}Лист доставки{/t}</h1>
        <div class="floatbox">
            <div class="left-49p">
                <div class="bordered">
                    <h3>{t}Получатель{/t}</h3>
                    <table class="order-table">
                        <tr class="{cycle values=$hl name="user"}">
                            <td class="otitle">
                                {t}Контактное лицо{/t}:
                            </td>
                            <td>{$order.contact_person|default:$order->getUser()->getFio()}</td>
                        </tr>
                        <tr class="{cycle values=$hl name="user"}">
                            <td class="otitle">
                                {t}Адрес{/t}:
                            </td>
                            <td>{$order->getAddress()->getLineView()}</td>
                        </tr>
                        <tr class="{cycle values=$hl name="user"}">
                            <td class="otitle">{t}Телефон{/t}:</td>
                            <td>{$user.phone}</td>
                        </tr>
                    </table>

                </div>
            </div>
            <div class="right-49p">
                <div class="bordered">
                    <h3>{t}Информация о заказе{/t}</h3>
                    <table class="order-table">
                        <tr class="{cycle values=$hl name="order"}">
                            <td class="otitle">
                                {t}Номер{/t}
                            </td>
                            <td>{$order.order_num}</td>
                        </tr>
                        <tr class="{cycle values=$hl name="order"}">
                            <td class="otitle">
                                {t}Дата оформления{/t}
                            </td>
                            <td>{$order.dateof}</td>
                        </tr>
                        <tr class="status-bar {cycle values=$hl name="order"}">
                            <td class="otitle">
                                {t}Комментарий к заказу{/t}:
                            </td>
                            <td>{$order.comments}</td>
                        </tr>
                        {assign var=fm value=$order->getFieldsManager()}
                        {foreach from=$fm->getStructure() item=item}
                            <tr class="{cycle values=$hl name="order"}">
                                <td class="otitle">
                                    {$item.title}
                                </td>
                                <td>{$item.current_val}</td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </div>
        </div>
        <br><br>
        <table class="pr-table">
            <thead>
            <tr>
                <th></th>
                <th>{t}Наименование{/t}</th>
                <th>{t}Код{/t}</th>
                <th>{t}Кол-во{/t}</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$order_data.items key=n item=item}
                {assign var=product value=$products[$n].product}
                <tr data-n="{$n}" class="item">
                    <td>
                        <img src="{$product->getMainImage(36,36, 'xy')}">
                    </td>
                    <td>
                        <b>{$item.cartitem.title}</b><br>
                        {$multioffers_values = unserialize($item.cartitem.multioffers)}
                        {if !empty($multioffers_values)}
                            <div class="parameters">
                                {$offer = array()}
                                {foreach $multioffers_values as $mitem}
                                    {$offer[] = "{$mitem.title}: {$mitem.value}"}
                                {/foreach}
                                {implode(', &nbsp; ', $offer)}
                            </div>
                        {elseif !empty($item.cartitem.model)}
                            {t}Модель{/t}: {$item.cartitem.model}
                        {/if}
                    </td>
                    <td>{$item.cartitem.barcode}</td>
                    <td>{$item.cartitem.amount}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        <br>
        <div>
            <strong>{t}Товары получены в надлежащем качестве и количестве.{/t}</strong>
            <br><br>
            <div class="floatbox">
                <div class="fleft nowrap">{t}Получатель{/t} __________________</div>
                <div class="fleft nowrap">{t}Подпись{/t} __________________</div>
                <div class="fright nowrap">{t}Дата{/t} _______________</div>
            </div>
        </div>
    </div>
{/foreach}
</body>
</html>