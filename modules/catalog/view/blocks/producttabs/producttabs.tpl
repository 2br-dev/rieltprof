{nocache}
{addjs file="jquery.changeoffer.js"}
{addjs file="jcarousel/jquery.jcarousel.min.js"}
{addjs file="list_product.js"}
{/nocache}
{assign var=shop_config value=ConfigLoader::byModule('shop')}
{assign var=check_quantity value=$shop_config->check_quantity}
{$list = $this_controller->api->addProductsMultiOffersInfo($list)}
{if $dirs}
    {$shop_config=ConfigLoader::byModule('shop')}
    <div class="topProducts">
        <div class="tabs mt40 tabProducts">
            <ul class="tabList">
                {foreach $dirs as $dir}
                    <li ><a {if $dir@first}class="act"{/if} data-tab=".frame{$dir@iteration}">{$dir.name}</a></li>
                {/foreach}
            </ul>
            <div class="botLine"></div>
            {foreach $dirs as $dir}
                <div class="productWrap tabFrame {if $dir@first}act{/if} frame{$dir@iteration}">
                    <ul class="productList">
                        {foreach $products_by_dirs[$dir.id] as $product}
                        {assign var=imagelist value=$product->getImages(false)}   
                        <li {$product->getDebugAttributes()} data-id="{$product.id}" class="{if count($imagelist)>1}photoView{/if}{if $product->isOffersUse() || $product->isMultiOffersUse()} showOfferSelect{/if}">
                            <div class="hoverBlock">
                                <div class="galleryWrap{if count($imagelist)>4} scrollable{/if}">
                                    <a class="control up"></a>
                                    <div class="gallery">
                                        <ul class="list">
                                            {foreach from=$imagelist key=n item=image name="allphotos"}
                                            <li data-change-preview="{$image->getUrl(141,185,'xy')}" {if $smarty.foreach.allphotos.first}class="act"{/if}><a href="{$product->getUrl()}#photo-{$n}" class="imgWrap"><img src="{$image->getUrl(64, 64, 'xy')}" alt="{$image.title} {t}фото{/t}-{$n}"/></a></li>
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
                                                    {if $check_quantity && $product.num<1}
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
                    <a class="onemore" href="{$dir->getUrl()}">{t}Посмотреть все товары{/t}</a>
                </div>
                
            {/foreach}
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.tabProducts').activeTabs();    
        });
    </script>
{else}
    {include file="%THEME%/block_stub.tpl"  class="blockProductTabs" do=[
        [
            'title' => t("Добавьте категории с товарами"),
            'href' => {adminUrl do=false mod_controller="catalog-ctrl"}
        ],
        [
            'title' => t("Настройте блок"),
            'href' => {$this_controller->getSettingUrl()},
            'class' => 'crud-add'
        ]
    ]}
{/if}