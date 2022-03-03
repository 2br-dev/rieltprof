{* Диалоговое окно "выбор комплектации". Отображается, если добавить товар с комплектациями в корзину *}

{$shop_config=ConfigLoader::byModule('shop')}
{$check_quantity=$shop_config.check_quantity}
{$catalog_config=ConfigLoader::byModule('catalog')}
{$product->fillOffersStockStars()} {* Загружаем сведения по остаткам на складах *}

<div class="t-page-complect rs-multi-complectations">
    <div class="t-complect_wrapper{if !$product->isAvailable()} rs-not-avaliable{/if}
                {if $product->canBeReserved()} rs-can-be-reserved{/if}
                {if $product.reservation == 'forced'} rs-forced-reserve{/if}" data-id="{$product.id}">

        <h1 class="h1">{t}Выбор комплектации{/t}</h1>

        <div class="t-complect_product-wrapper">
            <div class="t-complect_product">
                <div class="t-complect_img">
                    {$main_image=$product->getMainImage()}
                    <a href="{$product->getUrl()}" class="rs-image"><img src="{$main_image->getUrl(310, 310, 'axy')}" alt="{$main_image.title|default:"{$product.title}"}"></a>
                </div>
                <div class="t-complect_text">
                    <div class="t-complect_category"><a href="{$product->getMainDir()->getUrl()}"><small>{$product->getMainDir()->name}</small></a></div>
                    <div class="t-complect_title"><a href="{$product->getUrl()}"><span>{$product.title}</span></a></div>
                    {if $product.short_description}
                        <p class="t-complect_info">{$product.short_description|nl2br}</p>
                    {/if}
                    <div class="t-complect_price">
                        {$new_cost=$product->getCost()}
                        {if $old_cost = $product->getOldCost() && $old_cost != $new_cost}
                            <span class="t-complect_price_old">
                                <span class="value rs-price-old">{$old_cost}</span>
                                <span class="currency">{$product->getCurrency()}</span>
                            </span>
                        {/if}
                        <span class="t-complect_price_new">
                            <span class="value rs-price-new myCost">{$new_cost}</span>
                            <span class="currency">{$product->getCurrency()}</span>

                            {* Если включена опция единицы измерения в комплектациях *}
                            {if $catalog_config.use_offer_unit && $product->isOffersUse()}
                                <span class="rs-unit-block">/ <span class="rs-unit">{$product->getMainOffer()->getUnit()->stitle}</span></span>
                            {/if}
                        </span>
                    </div>
                </div>
            </div>

            <div class="t-complect_list">

                <ul class="t-complect_characteristics">
                    {if $product.barcode}
                        <li>{t}Артикул{/t}: <span class="page-product_barcode rs-product-barcode offerBarcode">{$product.barcode}</span></li>
                    {/if}
                    {if $product.brand_id}
                        <li>{t}Бренд{/t}: <a href="{$product->getBrand()->getUrl()}">{$product->getBrand()->title}</a></li>
                    {/if}
                    {if !$product->shouldReserve()}
                        <li><a href="{$product->getUrl()}" class="rs-stock-count-text-container"></a></li>
                    {/if}
                </ul>

                {include "%catalog%/product_offers.tpl"}

                {if $shop_config}
                    <a data-url="{$router->getUrl('shop-front-reservation', ["product_id" => $product.id])}" class="link link-one-click rs-reserve rs-in-dialog">{t}Заказать{/t}</a>
                    <span class="rs-unobtainable">{t}Нет в наличии{/t}</span>
                    <a data-url="{$router->getUrl('shop-front-cartpage', ["add" => $product.id])}" class="link link-more rs-to-cart rs-no-show-cart" data-add-text="Добавлено">{t}В корзину{/t}</a>
                {/if}

                {if !$shop_config || (!$product->shouldReserve() && (!$check_quantity || $product.num>0))}
                    {if $catalog_config.buyinoneclick }
                        <a data-url="{$router->getUrl('catalog-front-oneclick',["product_id"=>$product.id])}" title="Купить в 1 клик" class="link link-one-click rs-buy-one-click rs-in-dialog">{t}Купить в 1 клик{/t}</a>
                    {/if}
                {/if}
            </div>

        </div>
    </div>
</div>