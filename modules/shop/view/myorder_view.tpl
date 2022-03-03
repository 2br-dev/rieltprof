{assign var=catalog_config value=ConfigLoader::byModule('catalog')}
{addjs file="jcarousel/jquery.jcarousel.min.js"}
{addjs file="myorder_view.js"}
{assign var=cart value=$order->getCart()}
{assign var=products value=$cart->getProductItems()}
{assign var=order_data value=$cart->getOrderData()}

<div class="viewOrder">
    <div class="floatWrap">
        <span class="statusItem" style="background: {$order->getStatus()->bgcolor}">{$order->getStatus()->title}</span>

        <div class="orderViewCaption oh">

            <div class="fright">
                {if $order->getPayment()->hasDocs()}
                    {$type_object=$order->getPayment()->getTypeObject()}
                    {foreach $type_object->getDocsName() as $key=>$doc}
                        <a href="{$type_object->getDocUrl($key)}" class="button" target="_blank">{$doc.title}</a>
                    {/foreach}
                {/if}
                {if $order->canOnlinePay()}
                    <a href="{$order->getOnlinePayUrl()}" class="colorButton">{t}Оплатить{/t}</a><br>
                {/if}
            </div>
        </div>
    </div>
    
    <div class="products">
        <a class="control prev"></a>
        <a class="control next"></a>
        <div class="itemsWrap">
            <ul class="items">
                {foreach from=$order_data.items key=key item=item}
                {assign var=product value=$products[$key].product}
                {assign var=multioffer_titles value=$item.cartitem->getMultiOfferTitles()}
                <li>
                    {$main_image=$product->getMainImage()}
                    {if $product.id>0}
                        <a href="{$product->getUrl()}" class="image"><img src="{$main_image->getUrl(220, 200, 'xy')}" alt="{$main_image.title|default:"{$product.title}"}"></a>
                        <a href="{$product->getUrl()}" class="title">{$item.cartitem.title}
                            {if $item.cartitem.model}
                                <br>{$item.cartitem.model}
                            {/if}
                        </a>
                    {else}
                        <span class="image"><img src="{$main_image->getUrl(220, 200, 'xy')}" alt="{$main_image.title|default:"{$product.title}"}"/></span>
                        <span class="title">{$item.cartitem.title}
                            {if $item.cartitem.model}
                                <br>{$item.cartitem.model}
                            {/if}
                        </span>
                    {/if}                
                    {hook name="shop-myorder_view:product-info-items" title="{t}Просмотр заказа:информация о товаре{/t}"}                    
                        <ul class="keyval">
                            {if !empty($multioffer_titles)}
                                {foreach $multioffer_titles as $multioffer}
                                    <li>
                                        <span class="key">{$multioffer.title} -</span> <span class="value">{$multioffer.value}</span>
                                    </li>
                                {/foreach}
                            {/if}
                            <li>
                                <span class="key">{t}Количество{/t} -</span>
                                <span class="value">{$item.cartitem.amount}
                                    {if $catalog_config.use_offer_unit}
                                        {$item.cartitem.data.unit}
                                    {/if}
                                </span>
                            </li>
                            <li><span class="key">{t}Стоимость{/t} -</span> <span class="value">{$item.cost_with_discount} {$order.currency_stitle}</span></li>
                            {if $item.discount>0}
                            <li><span class="key">{t}Скидка{/t} -</span> <span class="value">{$item.discount} {$order.currency_stitle}</span></li>
                            {/if}
                        </ul>
                    {/hook}                    
                </li>
                {/foreach}
            </ul>
        </div>
    </div>
    <div class="orderActionButtons">
        <a href="{$router->getUrl('shop-front-cartpage', ['Act'=>'repeatOrder', 'order_num' => $order.order_num])}" rel="nofollow" class="formSave repeatOrder">{t}Повторить заказ{/t}</a>
        {if $order->canChangePayment()}
            <a href="{$router->getUrl('shop-front-myorderview', ['Act'=>'changePayment', 'order_id' => $order.order_num])}" rel="nofollow" class="formSave inDialog">{t}Изменить оплату{/t}</a>
        {/if}
    </div>
    <table class="formTable">
        <tbody>
            {hook name="shop-myorder_view:order-info-items" title="{t}Просмотр заказа:информация о заказе{/t}"}
                <tr>
                    <td class="key">{t}Дата заказа{/t}</td>
                    <td class="value">{$order.dateof|dateformat}</td>
                </tr>
                {if $order.delivery}
                    <tr>
                        <td class="key">{t}Тип доставки{/t}</td>
                        <td class="value">{$order->getDelivery()->title}</td>
                    </tr>
                {/if}
                {if $order.use_addr || $order.warehouse}
                    <tr>
                        <td class="key">{t}Адрес получения{/t}</td>
                        <td class="value">{if $order.use_addr}{$order->getAddress()->getLineView()}{elseif $order.warehouse}{$order->getWarehouse()->adress}{/if}</td>
                    </tr>
                {/if}
                {if $order.track_number}
                    <tr>
                        <td class="key">{t}Трек-номер заказа{/t}</td>
                        <td class="value">{$order.track_number}</td>
                    </tr>
                {/if}
                <tr>
                    <td class="key">{t}Контактное лицо{/t}</td>
                    <td class="value">{$order->contact_person}</td>
                </tr>
                {assign var=fm value=$order->getFieldsManager()}
                {foreach from=$fm->getStructure() item=item}
                    <tr>
                        <td class="key">{$item.title}</td>
                        <td class="value">{$item.current_val}</td>
                    </tr>
                {/foreach}    
                {foreach from=$order_data.other item=item}
                {if $item.cartitem.type != 'coupon'}
                <tr>
                    <td class="key">{$item.cartitem.title}</td>
                    <td class="value">{if $item.total >0}{$item.total}{/if}</td>
                </tr>
                {/if}
                {/foreach}
                
                {if $order->getPayment()->hasDocs()}
                <tr>
                    <td class="key">{t}Документы{/t}:</td>
                    <td class="value">
                    {assign var=type_object value=$order->getPayment()->getTypeObject()}
                    {foreach from=$type_object->getDocsName() key=key item=doc name="docs"}
                        <a href="{$type_object->getDocUrl($key)}" class="underline" target="_blank">{$doc.title}</a>{if !$smarty.foreach.docs.last},{/if}
                    {/foreach}
                    </td>
                </tr>
                {/if}
                
                {if $files=$order->getFiles()}
                <tr>
                    <td class="key">{t}Файлы{/t}:</td>
                    <td class="value">
                    {foreach $files as $file}
                        <a href="{$file->getUrl()}" class="underline" target="_blank">{$file.name}</a>{if !$file@last},{/if}
                    {/foreach}
                    </td>
                </tr>
                {/if}
                {$url=$order->getTrackUrl()}
                {if !empty($url)}
                    <tr>
                        <td class="key">{t}Ссылка для отслеживания заказа{/t}:</td>
                        <td class="value">
                            <a href="{$url}" target="_blank">{t}Перейти к отслеживанию{/t}</a>
                        </td>
                    </tr>
                {/if}
            {/hook}
        </tbody>
        <tfoot class="summary">
            <tr>
                <td class="key">{t}Итого{/t}</td>
                <td class="value">{$order_data.total_cost}</td>
            </tr>
        </tfoot>
    </table>
    
    {if !empty($order.user_text)}
    <div class="userText">
        {$order.user_text}
    </div>
    {/if}
</div>