{if count($order_list)}
<table class="orderList">
    {foreach $order_list as $order}
    <tr>
        <td class="date">№ {$order.order_num}<br>{$order.dateof|date_format:"d.m.Y"}</td>
        <td class="products">
            {$cart=$order->getCart()}
            {$products=$cart->getProductItems()}
            {$order_data=$cart->getOrderData()}
            
            {$products_first=array_slice($products, 0, 5)}
            {$products_more=array_slice($products, 5)}
            
            {hook name="shop-myorders:products" title="{t}Мои заказы:список товаров одного заказа{/t}"}
                <ul>
                    {foreach $products_first as $item}
                        {$multioffer_titles=$item.cartitem->getMultiOfferTitles()}
                        <li>
                            {$main_image=$item.product->getMainImage()}
                            {if $item.product.id>0}
                                <a href="{$item.product->getUrl()}" class="image"><img src="{$main_image->getUrl(36, 36, 'xy')}" alt="{$main_image.title|default:"{$item.cartitem.title}"}"/></a>
                                <a href="{$item.product->getUrl()}" class="title">{$item.cartitem.title}</a>
                            {else}
                                <span class="image"><img src="{$main_image->getUrl(36, 36, 'xy')}" alt="{$main_image.title|default:"{$item.cartitem.title}"}"/></span>
                                <span class="title">{$item.cartitem.title}</span>
                            {/if}
                            {if $multioffer_titles || $item.cartitem.model}
                                <div class="multioffersWrap">
                                    {foreach $multioffer_titles as $multioffer}
                                    {$multioffer.value}{if !$multioffer@last}, {/if}
                                    {/foreach}
                                    {if !$multioffer_titles}
                                        {$item.cartitem.model}
                                    {/if}
                                </div>
                            {/if}
                        </li>
                    {/foreach}
                </ul>
                {if !empty($products_more)}
                <div class="moreItems">
                    <a class="expand rs-parent-switcher">{t}показать все...{/t}</a>
                    <ul class="items">
                        {foreach $products_more as $item}
                            {$multioffer_titles=$item.cartitem->getMultiOfferTitles()}
                            <li>
                                {if $item.product.id>0}
                                    <a href="{$item.product->getUrl()}" class="image"><img src="{$item.product->getMainImage(36, 36, 'xy')}"></a>
                                    <a href="{$item.product->getUrl()}" class="title">{$item.cartitem.title}</a>
                                    {if $multioffer_titles || $item.cartitem.model}
                                        <div class="multioffersWrap">
                                            {foreach $multioffer_titles as $multioffer}
                                            {$multioffer.value}{if !$multioffer@last}, {/if}
                                            {/foreach}
                                            {if !$multioffer_titles}
                                                {$item.cartitem.model}
                                            {/if}
                                        </div>
                                    {/if}
                                {else}
                                    <span class="image"><img src="{$item.product->getMainImage(36, 36, 'xy')}"></span>
                                    <span class="title">{$item.cartitem.title}</span>
                                {/if}
                            </li>
                        {/foreach}
                    </ul>
                    <a class="collapse rs-parent-switcher">{t}показать кратко{/t}</a>
                </div>            
                {/if}
            {/hook}
        </td>
        <td class="price">
            {$order_data.total_cost}
        </td>
        <td class="status">
            <span class="statusItem" style="background: {$order->getStatus()->bgcolor}">{$order->getStatus()->title}</span>
        </td>
        <td class="actions">
            {hook name="shop-myorders:actions" title="{t}Мои заказы:действия над одним заказом{/t}"}
                {if $order->getPayment()->hasDocs()}
                    {$type_object=$order->getPayment()->getTypeObject()}
                    {foreach $type_object->getDocsName() as $key => $doc}
                    <a href="{$type_object->getDocUrl($key)}" target="_blank">{$doc.title}</a><br>
                    {/foreach}
                {/if}
                {if $order->canOnlinePay()}
                    <a href="{$order->getOnlinePayUrl()}">{t}оплатить{/t}</a><br>
                {/if}
            {/hook}
            <a href="{$router->getUrl('shop-front-myorderview', ["order_id" => $order.order_num])}" class="more">{t}подробнее{/t}</a>
        </td>
    </tr>
    {/foreach}
</table>
{else}
<div class="noData">
    {t}Еще не оформлено ни одного заказа{/t}
</div>
{/if}
<br>
{include file="%THEME%/paginator.tpl"}