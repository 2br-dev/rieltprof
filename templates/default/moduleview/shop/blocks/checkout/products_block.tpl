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
                                {if $cartdata.items[$n].discount_unformated > 0}
                                    {t}скидка{/t} {$cartdata.items[$n].discount}
                                {/if}
                            </div>
                        </div>

                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>