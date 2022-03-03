{$catalog_config=ConfigLoader::byModule('catalog')}
<div class="sidebar t-order-sidebar">

    <div class="t-order-products">
        <h3 class="h3">{t}Сведения о заказе{/t}</h3>
        <div class="clearfix product-side-list">
            {$products=$cart->getProductItems()}
            {$cartdata=$cart->getCartData()}

            {foreach $products as $n=>$item}
                {$barcode=$item.product->getBarCode($item.cartitem.offer)}
                {$offer_title=$item.product->getOfferTitle($item.cartitem.offer)}
                {$multioffer_titles=$item.cartitem->getMultiOfferTitles()}

                <div class="t-order-structure_item">
                    <a href="{$item.product->getUrl()}" class="t-order-structure_img">
                        <img src="{$item.product->getMainImage()->getUrl(65, 65, 'axy')}" alt="{$item.product.title}">
                    </a>
                    <div class="t-order-structure_text">
                        <a href="{$item.product->getUrl()}">{$item.product.title}</a>

                        <div class="code-line">
                            {if $barcode != ''}{t}Артикул{/t}:<span class="value">{$barcode}</span><br>{/if}
                            {if $multioffer_titles || ($offer_title && $item.product->isOffersUse())}
                                <div class="multioffers-wrap">
                                    {foreach $multioffer_titles as $multioffer}
                                        <p class="value">{$multioffer.title} - <strong>{$multioffer.value}</strong></p>
                                    {/foreach}
                                    {if !$multioffer_titles}
                                        <p class="value"><strong>{$offer_title}</strong></p>
                                    {/if}
                                </div>
                            {/if}
                        </div>

                        <small>
                            {$item.cartitem.amount}
                            {if $catalog_config.use_offer_unit}
                                {if isset($item.product.offers.items[$item.cartitem.offer])}
                                    {$item.product.offers.items[$item.cartitem.offer]->getUnit()->stitle}
                                {else}
                                    {$item.product->getUnit()->stitle}
                                {/if}
                            {else}
                                {$item.product->getUnit()->stitle}
                            {/if}
                            {if !empty($cartdata.items[$n].amount_error)}<div class="error">{$cartdata.items[$n].amount_error}</div>{/if}
                        </small>

                        <div class="price-block">
                            <div class="price">{$cartdata.items[$n].cost}</div>
                            <div class="discount">
                                {if $cartdata.items[$n].discount>0}
                                    {t}скидка{/t} {$cartdata.items[$n].discount}
                                {/if}
                            </div>
                        </div>

                    </div>
                </div>
            {/foreach}
        </div>
    </div>

    {*<div class="t-payment-and-delivery">
        <h3 class="h3">{t}Оплата и доставка{/t}</h3>
        <table class="table-keyvalue">
            {foreach $cart->getCouponItems() as $id=>$item}
                <tr>
                    <td class="key">{t}Купон на скидку{/t} {$item.coupon.code}</td>
                    <td class="value text-nowrap text-right"></td>
                </tr>
            {/foreach}
            {if $cartdata.total_discount>0}
                <tr>
                    <td class="key">{t}Скидка на заказ{/t}</td>
                    <td class="value text-nowrap text-right">{$cartdata.total_discount}</td>
                </tr>
            {/if}
            {foreach $cartdata.taxes as $tax}
                <tr {if !$tax.tax.included}class="bold"{/if}>
                    <td class="key">{$tax.tax->getTitle()}</td>
                    <td class="value text-nowrap text-right">{$tax.cost}</td>
                </tr>
            {/foreach}
            {if $order.delivery}
                <tr class="bold">
                    <td class="key">{t}Доставка{/t}: {$delivery.title}</td>
                    <td class="value text-nowrap text-right">{$cartdata.delivery.cost}</td>
                </tr>
            {/if}
            {if $cartdata.payment_commission}
                <tr class="bold">
                    <td class="key">{if $cartdata.payment_commission.cost>0}{t}Комиссия{/t}{else}{t}Скидка{/t}{/if} {t}при оплате через{/t} "{$order->getPayment()->title}": </td>
                    <td class="value text-nowrap text-right">{$cartdata.payment_commission.cost}</td>
                </tr>
            {/if}
        </table>
    </div>

    <div class="t-order-total">
        <div class="t-order-total_wrapper">
            <p>{t}Итого{/t}:</p>
            <div class="t-order-total_price">
                <span>{$cartdata.total}</span>
            </div>
        </div>
    </div>*}


</div>