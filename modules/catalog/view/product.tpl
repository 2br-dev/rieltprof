{addjs file="jcarousel/jquery.jcarousel.min.js"}
{addjs file="jquery.changeoffer.js?v=2"}
{addjs file="product.js"}
{$shop_config=ConfigLoader::byModule('shop')}
{$check_quantity=$shop_config.check_quantity}
{$catalog_config=$this_controller->getModuleConfig()} 
{if $product->isVirtualMultiOffersUse()} {* Если используются виртуальные многомерные комплектации *}
    {addjs file="jquery.virtualmultioffers.js"}
{/if}

{$product->fillOffersStockStars()} {* Загружаем сведения по остаткам на складах *}

<section id="updateProduct" itemscope itemtype="http://schema.org/Product" class="product productItem {if !$product->isAvailable()} notAvaliable{/if}{if $product->canBeReserved()} canBeReserved{/if}{if $product.reservation == 'forced'} forcedReserve{/if}" data-id="{$product.id}">
    <h1 itemprop="name" class="fn">{$product.title}</h1>
    <div class="image">
        {hook name="catalog-product:images" title="{t}Карточка товара:изображения{/t}"}
            {include "%catalog%/product_images.tpl"}
        {/hook}
    </div>
    
    <div class="inform">
        {hook name="catalog-product:rating" title="{t}Карточка товара:рейтинг{/t}"}
            <div class="prLine">
                <div class="ratingBlock bg">
                    <span class="rating" title="{t}Средняя оценка:{/t} {$product->getRatingBall()}"><span class="value" style="width:{$product->getRatingPercent()}%"></span></span><br>
                    <span class="commentsCount">{t}оценок{/t} {$product->getCommentsNum()}</span>
                </div>
                <a href="#comments" class="gotoComment">{t}написать отзыв{/t}</a>
                
                <div class="share">
                    <div class="handler"></div>
                    <div class="block">
                        <i class="corner"></i>
                        <p class="text">{t}Поделиться:{/t}</p>
                        <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
                        <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
                        <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,twitter"></div>
                    </div>
                </div>
            </div>
        {/hook}
        
        {hook name="catalog-product:offers" title="{t}Карточка товара:комплектации{/t}"}
            {include "%catalog%/product_offers.tpl"}
        {/hook}
        
        {* Блок с сопутствующими товарами *}
        {if $shop_config}
            {moduleinsert name="\Shop\Controller\Block\Concomitant"}
        {/if}
        
        {hook name="catalog-product:price" title="{t}Карточка товара:цены{/t}"}
            {* Блок с ценой *}
            <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="fcost">
                {assign var=last_price value=$product->getOldCost()}
                {if $last_price>0}<div class="lastPrice">{$last_price}</div>{/if}
                <span itemprop="price" class="myCost price" content="{$product->getCost(null, null, false)}">{$product->getCost()}</span><span class="myCurrency">{$product->getCurrency()}</span>
                <span itemprop="priceCurrency" class="hidden">{$product->getCurrencyCode()}</span>
            </div>
        
            {* Если включена опция единицы измерения в комплектациях *}
            {if $catalog_config.use_offer_unit && $product->isOffersUse()}
                <span class="unitBlock">/ <span class="unit">{$product->getMainOffer()->getUnit()->stitle}</span></span>
            {/if}
        {/hook}
        
        {hook name="catalog-product:action-buttons" title="{t}Карточка товара:кнопки{/t}"}
            <p itemprop="description" class="descr">{$product.short_description|nl2br}</p>
            
            <div class="floatWrap basketLine">
                {if $shop_config && !$product.disallow_manually_add_to_cart}
                    <a href="{$router->getUrl('shop-front-cartpage', ["add" => $product.id])}" class="toBasket addToCart">{t}в корзину{/t}</a>
                    <span class="unobtainable hidden">{t}Нет в наличии{/t}</span>
                    <a data-href="{$router->getUrl('shop-front-reservation', ["product_id" => $product.id])}" class="inDialog reserve hidden">{t}заказать{/t}</a>
                {/if}
                
                {if !$shop_config || (!$product->shouldReserve() && (!$check_quantity || $product->getNum()>0))}
                    {if $catalog_config.buyinoneclick}
                        <a data-href="{$router->getUrl('catalog-front-oneclick', ["product_id"=>$product.id])}" title="{t}Купить в 1 клик{/t}" class="oneclick buyOneClick inDialog"></a>
                    {/if}
                {/if}
            </div>
            {if $THEME_SETTINGS.enable_compare || $THEME_SETTINGS.enable_favorite}
            <div class="subActionBlock">
                {if $THEME_SETTINGS.enable_compare}
                    <a class="compare{if $product->inCompareList()} inCompare{/if}"><span>{t}Сравнить{/t}</span></a>
                {/if}            
                {if $THEME_SETTINGS.enable_favorite}
                    <a class="favorite inline{if $product->inFavorite()} inFavorite{/if}">
                        <span>{t}В избранное{/t}</span>
                        <span class="already">{t}В избранном{/t}</span>
                    </a>
                {/if}
            </div>            
            {/if}
        {/hook}        
        
        {hook name="catalog-product:information" title="{t}Карточка товара:краткая информация{/t}"}
            {if $product.barcode}
            <p class="barcode"><span class="cap">{t}Артикул:{/t}</span> <span class="offerBarcode">{$product.barcode}</span></p>
            {/if}
            
            {if $product.brand_id}
            <p class="brand"><span class="cap">{t}Бренд:{/t}</span> <a class="brandTitle" href="{$product->getBrand()->getUrl()}">{$product->getBrand()->title}</a></p>
            {/if}               
        {/hook}
        
        {if !$product->shouldReserve()}
            {hook name="catalog-product:stock" title="{t}Карточка товара:остатки{/t}"}
                {* Вывод наличия на складах *}
                {assign var=stick_info value=$product->getWarehouseStickInfo()}
                {if !empty($stick_info.warehouses)}
                    <div class="warehouseDiv">
                        <div class="title">{t}Наличие:{/t}</div>
                        {foreach from=$stick_info.warehouses item=warehouse}
                            <div class="warehouseRow" data-warehouse-id="{$warehouse.id}">
                                <div class="stickWrap">
                                {foreach from=$stick_info.stick_ranges item=stick_range}
                                    {$first_offer = $product->getMainOffer()}
                                    {$sticks=$first_offer.sticks[$warehouse.id]}
                                    <span class="stick {if $sticks>=$stick_range}filled{/if}"></span>
                                {/foreach}
                                </div>
                                <a class="title" href="{$warehouse->getUrl()}"><span>{$warehouse.title}</span></a>
                            </div>
                        {/foreach}
                    </div>
                {/if}
            {/hook}
        {/if}
    </div>            
    <br class="clearboth">

    <div class="properties">
        {hook name="catalog-product:properties" title="{t}Карточка товара:характеристики{/t}"}
            {foreach from=$product.offers.items key=key item=offer name=offers}
            {if $offer.propsdata_arr}
            <div class="offerProperty{if $key>0} hidden{/if}" data-offer="{$key}">
                <h2><span>{t}Характеристики комплектации{/t}</span></h2>
                <table class="kv">
                    {foreach from=$offer.propsdata_arr key=pkey item=pval}
                            <tr>
                                <td class="key"><span>{$pkey}</span></td>
                                <td class="value">{$pval}</td>
                            </tr>
                    {/foreach}
                </table>
            </div>
            {/if}
            {/foreach}
            
            {foreach $product.properties as $data}
                <div class="propertyGroup">
                    <h2><span>{$data.group.title|default:t("Характеристики")}</span></h2>
                    <table class="kv">
                        {foreach from=$data.properties item=property}
                            {assign var=prop_value value=$property->textView()}
                                <tr>
                                    <td class="key"><span>{$property.title}</span>
                                        {if $property.description}
                                            <a class="popover-button"
                                               data-toggle="popover"
                                               tabindex="0"
                                               data-trigger="manual"
                                               data-content="{$property.description}"> ? </a>
                                        {/if}
                                    </td>
                                    <td class="value">{$prop_value} {$property.unit}</td>
                                </tr>
                        {/foreach}
                    </table>
                </div>
            {/foreach}
        {/hook}
    </div>
    
    {if !empty($product.description)}
        <h2><span>{t}Описание{/t}</span></h2>
        {hook name="catalog-product:description" title="{t}Карточка товара:описание{/t}"}
        <article class="description">
            {$product.description}
        </article>
        {/hook}
    {/if}
    
    {* Вывод публичных файлов *}
    {if $files=$product->getFiles()}
    <div class="files">
        <h2><span>{t}Файлы{/t}</span></h2>
        {hook name="catalog-product:files" title="{t}Карточка товара:файлы{/t}"}
        <ul class="filesList">
            {foreach $files as $file}
            <li>
                <a href="{$file->getUrl()}">{$file.name} ({$file.size|format_filesize})</a>
                {if $file.description}<div class="fileDescription">{$file.description}</div>{/if}
            </li>
            {/foreach}
        </ul>
        {/hook}
    </div>
    {/if}    
    
</section>