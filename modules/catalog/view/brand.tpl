{addjs file="jquery.changeoffer.js"}
{addjs file="jcarousel/jquery.jcarousel.min.js"}
{addjs file="list_product.js"}
{assign var=shop_config value=ConfigLoader::byModule('shop')}
{assign var=check_quantity value=$shop_config->check_quantity}

<div class="brandPage">
    <h1 class="fn">{$brand.title}</h1>
    <article class="description">
       {if $brand.image} 
         <img src="{$brand->__image->getUrl(250,250,'xy')}" class="mainImage" alt="{$brand.title}"/> 
       {/if}
       {$brand.description} 
    </article>
    {if !empty($dirs)}
    
        {if count($dirs) < 6}
        {elseif count($dirs) < 15}
           {$widthClass="col2"}
        {else}
            {$widthClass="col3"}
        {/if}
    
        <div class="brandDirs">
            <h2><span>{t}Категории товаров{/t} {$brand.title}</span></h2>
            <ul class="cats {$widthClass}">
             {foreach $dirs as $dir}
                <li>
                    <a href="{$router->getUrl('catalog-front-listproducts',['category'=>$dir._alias,'bfilter'=> ["brand" => [$brand.id]]])}">{$dir.name}</a> <sup>({$dir.brands_cnt})</sup>
                </li>
             {/foreach}
            </ul>
        </div>
    {/if}
    
    {if !empty($products)}
       <div class="brand_products">
          <h2><span>{t}Актуальные товары{/t} {$brand.title}</span></h2>
          <div class="productWrap">
              <ul class="productList">  
                  {foreach $products as $product}
                        {assign var=imagelist value=$product->getImages(false)}                
                        <li {$product->getDebugAttributes()} data-id="{$product.id}" class="{if count($imagelist)>1}photoView{/if}{if $product->isOffersUse() || $product->isMultiOffersUse()} showOfferSelect{/if}">
                            <div class="hoverBlock">
                                <div class="galleryWrap{if count($imagelist)>4} scrollable{/if}">
                                    <a class="control up"></a>
                                    <div class="gallery">
                                        <ul class="list">
                                            {foreach from=$imagelist key=n item=image name="allphotos"}
                                            <li data-change-preview="{$image->getUrl(141,185,'xy')}" {if $smarty.foreach.allphotos.first}class="act"{/if}><a href="{$product->getUrl()}#photo-{$n}" class="imgWrap"><img src="{$image->getUrl(64, 64, 'xy')}"></a></li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                    <a class="control down"></a>
                                </div>
                            </div>
                            <div class="dataBlock">
                                <a href="{$product->getUrl()}" class="pic">
                                <span class="labels">
                                    {foreach from=$product->getMySpecDir() item=spec}
                                    {if $spec.image && $spec.is_label}
                                        <img src="{$spec->__image->getUrl(62,62, 'xy')}">
                                    {/if}
                                    {/foreach}
                                </span>
                                {$main_image=$product->getMainImage()}
                                <img src="{$main_image->getUrl(141, 185, 'xy')}" class="middlePreview" alt="{$main_image.title|default:"{$product.title}"}"/></a>
                                <div class="info extra">
                                    <a href="{$product->getUrl()}" class="titleGroup">
                                        <h3>{$product.title}</h3>
                                    </a>
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
                                
                                {hook name="catalog-list_products:blockview-buttons" title="{t}Просмотр категории продукции:кнопки, блочный вид{/t}"}
                                    {if $shop_config || $THEME_SETTINGS.enable_compare || $THEME_SETTINGS.enable_favorite}
                                        <div class="actionBar">
                                            {if $shop_config}
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
          </div>
       </div>
    {/if}
</div>