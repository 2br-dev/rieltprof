{* Личный кабинет - список моих заказов *}

{if count($order_list)}
    <div class="page-orders">
        {foreach $order_list as $order}
            {$cart = $order->getCart()}
            {$products = $cart->getProductItems()}
            {$order_data = $cart->getOrderData()}

            <div class="t-order-wrapper">
            <div class="t-order-card">
                <div class="t-order-card_description">
                    <a class="h2 t-order_title" href="{$router->getUrl('shop-front-myorderview', ["order_id" => $order.order_num])}">
                        {t num=$order.order_num date="{$order.dateof|dateformat:"@date"}"}Заказ № %num от %date{/t}
                    </a>
                    <table class="t-order_table">
                        <tbody>
                        <tr>
                            <td><span>{t}Оплата{/t}</span></td>
                            <td><span>{$order->getPayment()->title}</span></td>
                        </tr>
                        <tr>
                            <td><span>{t}Доставка{/t}</span></td>
                            <td><span>{$order->getDelivery()->title}</span></td>
                        </tr>
                        {if $order.use_addr || $order.warehouse}
                            <tr>
                                <td><span>{t}Адрес получения{/t}</span></td>
                                <td><span>{if $order.use_addr}{$order->getAddress()->getLineView()}{elseif $order.warehouse}{$order->getWarehouse()->adress}{/if}</span></td>
                            </tr>
                        {/if}
                        {if $order.contact_person}
                        <tr>
                            <td><span>{t}Контактное лицо{/t}</span></td>
                            <td><span>{$order.contact_person}</span></td>
                        </tr>
                        {/if}
                        <tr class="t-order_table_margin">
                            <td><span>{t}Статус{/t}</span></td>
                            <td><span>{$order->getStatus()->title}</span>
                                <div class="t-order_table_buttons">
                                    {hook name="shop-myorders:actions" title="{t}Мои заказы:действия над одним заказом{/t}"}
                                    {if $order->canOnlinePay()}
                                        <a href="{$order->getOnlinePayUrl()}" class="link link-more">{t}оплатить{/t}</a><br>
                                    {/if}
                                    {if $order->getPayment()->hasDocs()}
                                        {$type_object = $order->getPayment()->getTypeObject()}
                                        {foreach $type_object->getDocsName() as $key=>$doc}
                                            <a href="{$type_object->getDocUrl($key)}" class="link link-one-click" target="_blank">{$doc.title}</a>
                                        {/foreach}
                                    {/if}
                                    {/hook}
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="t-order-card_sum-info">
                    <div class="t-order-card_sum-info_icon">
                        <img src="{$THEME_IMG}/icons/basket.svg" width="60" height="65" alt="">
                    </div>
                    <span class="t-order-card_sum-info_num">{t n=count($products)}%n [plural:%n:товар|товара|товаров] на сумму:{/t}</span>
                    <span class="t-order-card_sum-info_sum">{$order_data.total_cost}</span>
                    <a class="link link-del" href="{$router->getUrl('shop-front-myorderview', ["order_id" => $order.order_num])}"><i class="pe-2x pe-7s-angle-right"></i>{t}Подробнее{/t}</a></div>

                </div>
            </div>
        {/foreach}
    </div>


{else}
    <div class="empty-list">
        {t}Еще не оформлено ни одного заказа{/t}
    </div>
{/if}

{include file="%THEME%/paginator.tpl"}