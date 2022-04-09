{$catalog_config=ConfigLoader::byModule('catalog')}
{$products = $cart->getProductItems()}
{$cartdata = $cart->getCartData()}
<div class="order-items">
    {hook name="shop-block-checkout:products" title="{t}Подтверждение заказа:товары{/t}"}
    <div role="button" class="order-items__title collapsed" id="orderItemsTitle" data-bs-toggle="collapse" data-bs-target="#orderItemsList">{t num=count($products)}Товаров в заказе: %num{/t}</div>

    <div class="collapse" id="orderItemsList" data-bs-target="orderItemsTitle">
        <div class="pt-4">
            <ul class="order-items__list">
                {foreach $products as $n=>$item}
                    {$barcode = $item.product->getBarCode($item.cartitem.offer)}
                    {$offer_title = $item.product->getOfferTitle($item.cartitem.offer)}
                    {$multioffer_titles = $item.cartitem->getMultiOfferTitles()}
                    <li>
                        <a href="{$item.product->getUrl()}">
                            <div class="mb-2">
                                {$item.product.title}
                                <div class="mt-2">
                                    {if $barcode != ''}
                                        <div class="d-flex align-items-center fs-6">
                                            <div class="text-gray">{t}Артикул{/t}:</div>
                                            <div class="ms-1">
                                                <span>{$barcode}</span>
                                            </div>
                                        </div>
                                    {/if}
                                    {if $multioffer_titles || ($offer_title && $item.product->isOffersUse())}
                                        <div>
                                            {foreach $multioffer_titles as $multioffer}
                                                <div class="d-flex align-items-center fs-6">
                                                    <div class="text-gray">{$multioffer.title}:</div>
                                                    <div class="ms-1">
                                                        <span>{$multioffer.value}</span>
                                                    </div>
                                                </div>
                                            {/foreach}
                                            {if !$multioffer_titles}
                                                <div class="d-flex align-items-center fs-6">
                                                    <div class="text-gray">{$item.product.offer_caption|default:"{t}Комплектация{/t}"}:</div>
                                                    <div class="ms-1">
                                                        <span>{$offer_title}</span>
                                                    </div>
                                                </div>
                                            {/if}
                                        </div>
                                    {/if}
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-gray">{$item.cartitem.amount}
                                    {if $catalog_config.use_offer_unit}
                                        {if isset($item.product.offers.items[$item.cartitem.offer])}
                                            {$item.product.offers.items[$item.cartitem.offer]->getUnit()->stitle}
                                        {else}
                                            {$item.product->getUnit()->stitle}
                                        {/if}
                                    {else}
                                        {$item.product->getUnit()->stitle}
                                    {/if}</div>
                                <div class="fw-bold ms-3 text-body">
                                    {if $cartdata.items[$n].discount_unformated > 0}
                                        <span class="old-price">{$cartdata.items[$n].base_cost}</span>
                                    {/if}
                                    <span>{$cartdata.items[$n].cost}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
    {/hook}
</div>