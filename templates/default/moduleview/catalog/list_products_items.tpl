{if $view_as == 'blocks'}
<ul class="productList">
    {foreach $list as $product}
        {$imagelist=$product->getImages(false)}
        <li {$product->getDebugAttributes()} data-id="{$product.id}" class="{if count($imagelist)>1}photoView{/if}{if $product->isOffersUse() || $product->isMultiOffersUse()} showOfferSelect{/if}">
            <div class="hoverBlock">
                <div class="galleryWrap{if count($imagelist)>4} scrollable{/if}">
                    <a class="control up"></a>
                    <div class="gallery">
                        <ul class="list">
                            {foreach $imagelist as $n => $image}
                            <li data-change-preview="{$image->getUrl(141,185,'xy')}" {if $image@first}class="act"{/if}><a href="{$product->getUrl()}" class="imgWrap"><img src="{$image->getUrl(64, 64, 'xy')}" alt="{$image.title|default:"{$product.title} t('фото') {$n}"}"/></a></li>
                            {/foreach}
                        </ul>
                    </div>
                    <a class="control down"></a>
                </div>
            </div>
            <div class="dataBlock">
                <a href="{$product->getUrl()}" class="pic">
                <span class="labels">
                    {foreach $product->getMySpecDir() as $spec}
                    {if $spec.image && $spec.is_label}
                        <img src="{$spec->__image->getUrl(62,62, 'xy')}" alt=""/>
                    {/if}
                    {/foreach}
                </span>
                {$main_image=$product->getMainImage()}
                <img src="{$main_image->getUrl(141, 185, 'xy')}" class="middlePreview" alt="{$main_image.title|default:"{$product.title}"}"/></a>
                <div class="info extra">
                    {hook name="catalog-list_products:blockview-title" product=$product title="{t}Просмотр категории продукции:название товара, блочный вид{/t}"}
                        <a href="{$product->getUrl()}" class="titleGroup">
                            <h3>{$product.title}</h3>
                        </a>
                    {/hook}
                    <div class="group">
                        <div class="scost">
                            {$last_price = $product->getOldCost()}
                            {if $last_price>0}<div class="lastPrice">{$last_price}</div>{/if}
                            <span>{$product->getCost()} {$product->getCurrency()}</span>
                        </div>
                        <div class="ratingBlock">
                            <span class="rating" title="{t}рейтинг:{/t} {$product->getRatingBall()}"><span class="value" style="width:{$product->getRatingPercent()}%"></span></span>
                            <br><span class="comments">{t}оценок{/t} {$product->getCommentsNum()}</span>
                        </div>
                    </div>
                </div>
                
                {hook name="catalog-list_products:blockview-buttons" product=$product title="{t}Просмотр категории продукции:кнопки, блочный вид{/t}"}
                    {if $shop_config || $THEME_SETTINGS.enable_compare || $THEME_SETTINGS.enable_favorite}
                        <div class="actionBar">
                            {if $shop_config && !$product.disallow_manually_add_to_cart}
                                {if $product->shouldReserve()}
                                    {if $product->isOffersUse() || $product->isMultiOffersUse()}
                                        <a data-href="{$router->getUrl('shop-front-multioffers', ["product_id" => $product.id])}" class="cartButton inDialog reserve" title="{t}Заказать{/t}">&nbsp;</a>
                                    {else}
                                        <a data-href="{$router->getUrl('shop-front-reservation', ["product_id" => $product.id])}" class="cartButton inDialog reserve" title="{t}Заказать{/t}">&nbsp;</a>
                                    {/if}
                                {else}
                                    {if $check_quantity && $product->getNum()<1}
                                        <span class="cartButton unobt" title="{t}Нет в наличии{/t}">&nbsp;</span>
                                    {else}
                                        {if $product->isOffersUse() || $product->isMultiOffersUse()}
                                            <span data-href="{$router->getUrl('shop-front-multioffers', ["product_id" => $product.id])}" class="cartButton showComplekt inDialog" title="{t}В корзину{/t}">&nbsp;</span>
                                        {else}
                                            <a data-href="{$router->getUrl('shop-front-cartpage', ["add" => $product.id])}" class="cartButton addToCart noShowCart" title="{t}В корзину{/t}">&nbsp;</a>
                                        {/if}
                                    {/if}
                                {/if}
                            {/if}
                            
                            {if $THEME_SETTINGS.enable_compare}
                                <a class="compare{if $product->inCompareList()} inCompare{/if}" data-title="{t}сравнить{/t}" data-already-title="{t}В сравнении{/t}"></a>
                            {/if}
                            
                            {if $THEME_SETTINGS.enable_favorite}
                                <a class="favorite listStyle{if $product->inFavorite()} inFavorite{/if}" data-favorite-url="{$router->getUrl('catalog-front-favorite')}" data-title="{t}В избранное{/t}" data-already-title="{t}В избранном{/t}"></a>
                            {/if}
                        </div>
                    {/if}
                {/hook}
            </div>
        </li>
    {/foreach}
</ul>
{else}
<ul class="productListTable">
    {foreach $list as $product}
        <li {$product->getDebugAttributes()} data-id="{$product.id}">
            {$main_image=$product->getMainImage()}
            <a href="{$product->getUrl()}" class="pic"><img src="{$main_image->getUrl(74, 66, 'xy')}" alt="{$main_image.title|default:"{$product.title}"}"/></a>
            <div class="info extra">
                
                {hook name="catalog-list_products:tableview-title" product=$product title="{t}Просмотр категории продукции:название товара, табличный вид{/t}"}
                <a href="{$product->getUrl()}" class="titleGroup">
                    <h3>{$product.title}</h3>
                    <p class="descr">{$product.short_description}</p>
                </a>
                {/hook}
                
                <div class="scost">
                    <span>{$product->getCost()} {$product->getCurrency()}</span>
                </div>

                {hook name="catalog-list_products:tableview-buttons" product=$product title="{t}Просмотр категории продукции:кнопки, табличный вид{/t}"}
                    {if $shop_config && !$product.disallow_manually_add_to_cart}
                        {if $product->shouldReserve()}
                            {if $product->isOffersUse() || $product->isMultiOffersUse()}
                                <a data-href="{$router->getUrl('shop-front-multioffers', ["product_id" => $product.id])}" class="cartButton inDialog reserve" title="{t}Заказать{/t}">&nbsp;</a>
                            {else}
                                <a data-href="{$router->getUrl('shop-front-reservation', ["product_id" => $product.id])}" class="cartButton inDialog reserve" title="{t}Заказать{/t}">&nbsp;</a>
                            {/if}
                        {else}
                            {if $check_quantity && $product->getNum()<1}
                                <span class="cartButton unobt" title="{t}Нет в наличии{/t}">&nbsp;</span>
                            {else}
                                {if $product->isOffersUse() || $product->isMultiOffersUse()}
                                    <span data-href="{$router->getUrl('shop-front-multioffers', ["product_id" => $product.id])}" class="cartButton showComplekt inDialog noShowCart" title="{t}В корзину{/t}">&nbsp;</span>
                                {else}
                                    <a data-href="{$router->getUrl('shop-front-cartpage', ["add" => $product.id])}" class="cartButton addToCart noShowCart" title="{t}В корзину{/t}">&nbsp;</a>
                                {/if}
                            {/if}
                        {/if}
                    {/if}
                    
                    {if $THEME_SETTINGS.enable_compare}
                        <a class="compare{if $product->inCompareList()} inCompare{/if}"><span>{t}сравнить{/t}</span></a>
                    {/if}
                    
                    {if $THEME_SETTINGS.enable_favorite}
                    <a class="favorite inline{if $product->inFavorite()} inFavorite{/if}" data-favorite-url="{$router->getUrl('catalog-front-favorite')}">
                        <span class="">{t}в избранное{/t}</span>
                        <span class="already">{t}в избранном{/t}</span>
                    </a>
                    {/if}
                {/hook}
            </div>
        </li>
    {/foreach}
</ul>
{/if}