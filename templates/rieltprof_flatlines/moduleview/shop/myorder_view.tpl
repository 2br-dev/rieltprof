{* Личный кабинет -> Просмотр одного заказа *}

{$catalog_config=ConfigLoader::byModule('catalog')}
{$cart=$order->getCart()}
{$products=$cart->getProductItems()}
{$order_data=$cart->getOrderData()}

<div class="t-order-wrapper">
    <div class="t-order-card">
        <div class="t-order-card_description">
            <a class="h2 t-order_title" href="{$router->getUrl('shop-front-myorderview', ["order_id" => $order.order_num])}">
                {t num=$order.order_num date="{$order.dateof|dateformat:"@date"}"}Заказ № %num от %date{/t}
            </a>
            <table class="t-order_table">
                <tbody>
                {hook name="shop-myorder_view:order-info-items" title="{t}Просмотр заказа:информация о заказе{/t}"}
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
                {if $order.track_number}
                    <tr>
                        <td class="key">{t}Трек-номер{/t}</td>
                        <td class="value">{$order.track_number}</td>
                    </tr>
                {/if}
                {if $order.contact_person}
                    <tr>
                        <td><span>{t}Контактное лицо{/t}</span></td>
                        <td><span>{$order.contact_person}</span></td>
                    </tr>
                {/if}
                {$fm=$order->getFieldsManager()}
                {foreach $fm->getStructure() as $item}
                    <tr>
                        <td>{$item.title}</td>
                        <td>{$item.current_val}</td>
                    </tr>
                {/foreach}
                {if $files=$order->getFiles()}
                    <tr>
                        <td>{t}Файлы{/t}</td>
                        <td>
                            {$type_object=$order->getPayment()->getTypeObject()}
                            {foreach $files as $file}
                                <a href="{$file->getUrl()}" class="underline" target="_blank">{$file.name}</a>{if !$file@last},{/if}
                            {/foreach}
                        </td>
                    </tr>
                {/if}
                {if $order->comments}
                    <tr>
                        <td>{t}Комментарий{/t}</td>
                        <td>{$order->comments}</td>
                    </tr>
                {/if}
                {$url=$order->getTrackUrl()}
                {if !empty($url)}
                    <tr>
                        <td>{t}Ссылка для отслеживания заказа{/t}:</td>
                        <td>
                            <a href="{$url}" target="_blank">{t}Перейти к отслеживанию{/t}</a>
                        </td>
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
                {/hook}
                </tbody>
            </table>
        </div>
        <!-- END .t-order-card_description-->
        <div class="t-order-card_sum-info">
            <div class="t-order-card_sum-info_icon">
                <img src="{$THEME_IMG}/icons/basket.svg" width="60" height="65" alt="">
            </div>
            <span class="t-order-card_sum-info_num">{t n=count($products)}%n [plural:%n:товар|товара|товаров] на сумму:{/t}</span>
            <span class="t-order-card_sum-info_sum">{$order_data.total_cost}</span>

            <a href="{$router->getUrl('shop-front-cartpage', ['Act'=>'repeatOrder', 'order_num' => $order.order_num])}" rel="nofollow"
                    class="link link-more t-order-repeat_order rs-repeat-order">Повторить заказ</a>
            {if $order->canChangePayment()}
                <a href="{$router->getUrl('shop-front-myorderview', ['Act'=>'changePayment', 'order_id' => $order.order_num])}" rel="nofollow" class="link link-more rs-in-dialog">{t}Изменить оплату{/t}</a>
            {/if}
        </div>

    </div>

    {if !empty($order.user_text)}
        <div class="t-order-usertext">
            {$order.user_text}
        </div>
    {/if}

    <ul class="t-order-list open">
        {foreach $order_data.items as $key=>$item}
            {$product=$products[$key].product}
            {$multioffer_titles=$item.cartitem->getMultiOfferTitles()}
            <li>
                <div class="t-order_left">
                    <span>{t}Кол-во{/t}: {$item.cartitem.amount} {if $catalog_config.use_offer_unit}{$item.cartitem.data.unit}{else}{$product->getUnit()->stitle}{/if}</span>
                </div>
                <div class="t-order_center">
                    <div class="t-order_img">
                        {$main_image=$product->getMainImage()}
                        {if $product.id>0}
                            <a href="{$product->getUrl()}" class="image"><img src="{$main_image->getUrl(100, 100, 'xy')}" alt="{$main_image.title|default:"{$product.title}"}"/></a>
                        {else}
                            <img src="{$main_image->getUrl(100, 100, 'xy')}" alt="{$main_image.title|default:"{$product.title}"}"/>
                        {/if}
                    </div>
                    <div class="t-order_description">
                        {hook name="shop-myorder_view:product-info-items" title="{t}Просмотр заказа:информация о товаре{/t}" item=$item}
                            {if $maindir=$product->getMainDir()}
                                <a href="{$maindir->getUrl()}" class="t-order_category"><small>{$maindir->name}</small></a>
                            {/if}
                            <a {if $product.id>0}href="{$product->getUrl()}"{/if} class="t-order_name">{$item.cartitem.title}
                                {if $item.cartitem.model}  <br>{$product->getOfferTitle($item.cartitem.offer)}{/if}
                            </a>
                            {if $item.cartitem.barcode}<span>{t}Артикул{/t}: {$item.cartitem.barcode}</span><br>{/if}
                            {if !empty($multioffer_titles)}
                                {foreach $multioffer_titles as $multioffer}
                                 <span>{$multioffer.title}: {$multioffer.value}</span><br>
                                {/foreach}
                            {/if}
                            <span>{t}Цена{/t}: {$item.single_cost}</span><br>
                        {/hook}
                    </div>
                </div>
                <div class="t-order_right">
                    <span>{$item.cost_with_discount} {$order.currency_stitle}</span>
                    {if $item.discount >0}
                        <br>{t}Скидка{/t} {$item.discount} {$order.currency_stitle}
                    {/if}
                </div>
            </li>
        {/foreach}

        {foreach $order_data.other as $item}
            <li>
                <div class="t-order_wide"><span>{$item.cartitem.title}</span></div>
                <div class="t-order_right"><span>{if $item.total >0}{$item.total}{/if}</span></div>
            </li>
        {/foreach}
    </ul>
    </div>
</div>