{assign var=shop_config value=ConfigLoader::byModule('shop')}
{assign var=check_quantity value=$shop_config.check_quantity}
{assign var=catalog_config value=ConfigLoader::byModule('catalog')} 
{* Получаем склады *}
<h2 class="dialogTitle" data-dialog-options='{ "width": "734" }'>{t}Выбор комплектации{/t}</h2>

<div class="multiComplectations">
    <section class="productPreview{if !$product->isAvailable()} notAvaliable{/if}{if $product->canBeReserved()} canBeReserved{/if}{if $product.reservation == 'forced'} forcedReserve{/if}" data-id="{$product.id}">
        <h1 class="fn">{$product.title}</h1>
        
        <div class="leftColumn">
            <div class="image">
                {$main_image=$product->getMainImage()}
                <img src="{$main_image->getUrl(310,310,'xy')}" class="photo" alt="{$main_image.title|default:"{$product.title}"}"/>
            </div>
            <br class="clearboth">
            {if $product.barcode}
                <p class="barcode"><span class="cap">{t}Артикул{/t}:</span> <span class="offerBarcode">{$product.barcode}</span></p>
            {/if}
            {if $product.short_description}
                <p class="descr">{$product.short_description|nl2br}</p>
            {/if}
            <div class="fcost">
                {assign var=last_price value=$product->getOldCost()}
                {if $last_price>0}<div class="lastPrice">{$last_price}</div>{/if}
                <span class="myCost price">{$product->getCost()}</span> {$product->getCurrency()}
            </div>
        </div>
        
        {* Подгружаем остатки по складам*}
        {$product->fillOffersStockStars()}
        {* Подгружаем остатки по складам*}
        <div class="inform">
            
            {include "%catalog%/product_offers.tpl"}
            
            {* Блок с сопутствующими товарами *}
            {moduleinsert name="\Shop\Controller\Block\Concomitant"}
            
            {* Вывод наличия на складах *}
            {assign var=stick_info value=$product->getWarehouseStickInfo()}
            {if !empty($stick_info.warehouses)}
                <div class="warehouseDiv">
                    <div class="title">{t}Наличие{/t}:</div>
                    {foreach $stick_info.warehouses as $warehouse}
                        <div class="warehouseRow" data-warehouse-id="{$warehouse.id}">
                            <div class="stickWrap">
                                {$first_offer = $product->getMainOffer()}
                                {$sticks = $first_offer.sticks[$warehouse.id]}
                                {foreach $stick_info.stick_ranges as $stick_range}
                                    <span class="stick {if $sticks >= $stick_range}filled{/if}"></span>
                                {/foreach}
                            </div>
                            <a class="title" href="{$warehouse->getUrl()}"><span>{$warehouse.title}</span></a>
                        </div>
                    {/foreach}
                </div>
            {/if}

            <div class="floatWrap basketLine">
                    <a data-href="{$router->getUrl('shop-front-cartpage', ["add" => $product.id])}" class="toBasket addToCart noShowCart">{t}в корзину{/t}</a>
                    <a data-href="{$router->getUrl('shop-front-reservation', ["product_id" => $product.id])}" class="inDialog reserve hidden">{t}заказать{/t}</a>
                    <span class="unobtainable hidden">{t}Нет в наличии{/t}</span>
            </div>
        </div>
        <br class="clearboth">
        
    </section>
</div>

{literal}
    <script type="text/javascript">
        $(function() {
            $('[name="offer"]').changeOffer();
        });
        $('.multiComplectations .addToCart').on('click',function(){
            $.colorbox.close();
        });
    </script>
{/literal}