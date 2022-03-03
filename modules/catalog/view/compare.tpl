{$app->setBodyClass('comparePageBody')}
{addjs file="{$mod_js}jquery.compareshow.js" basepath="root"}
{addjs file="jquery.changeoffer.js"}
{addjs file="{$mod_js}jquery.favorite.js" basepath="root"}
{$shop_config=ConfigLoader::byModule('shop')}
{$check_quantity=$shop_config->check_quantity}

{if count($comp_data.items)}
<div class="centerWrap">
    <div class="center" id="compareShow" data-compare-url='{ "remove":"{$router->getUrl('catalog-front-compare', ["Act" => "remove"])}" }' data-favorite-url="{$router->getUrl('catalog-front-favorite')}">
        <ul class="compareHead">
            <li class="logoZone">
                <div class="back">       
                {if $CONFIG.logo}
                    <img src="{$CONFIG.__logo->getUrl(206, 46)}" alt=""/>
                {/if}                
                </div>
                <a class="print">{t}Распечатать{/t}</a>
            </li>
            {foreach from=$comp_data.items item=product}
            <li class="item productItem{if !$product->isAvailable()} notAvaliable{/if}" data-id="{$product.id}">
                <div class="topline">
                    <a class="remove" title="{t}исключить из сравнения{/t}"></a>
                </div>
                {assign var=imglist value=$product->getImages(false)}
                
                <div class="image{if count($imglist)>1} activelist{/if}">
                    {foreach from=$imglist item=image name=photos}
                    <img src="{$image->getUrl(201,183)}" {if !$smarty.foreach.photos.first}class="hidden"{/if} alt="{$image.title}"/>
                    {/foreach}
                    {if !count($imglist)}
                    <img src="{$product->getImageStub()->getUrl(201,183)}" alt=""/>
                    {/if}
                </div>
                <div class="title">{$product.title}</div>
                {if $product.multioffers.use}
                    <div class="pname">{$product.offer_caption|default:t('Комплектация')}</div>
                    {* Многомерные комплектации *}
                    <div class="multiOffers">
                        {foreach $product.multioffers.levels as $level}
                            {if !empty($level.values)}
                                <div class="title">{if $level.title}{$level.title}{else}{$level.prop_title}{/if}</div>
                                <select name="multioffers[{$level.prop_id}]" data-prop-title="{if $level.title}{$level.title}{else}{$level.prop_title}{/if}">
                                    {foreach $level.values as $value}
                                        <option value="{$value.val_str}">{$value.val_str}</option>   
                                    {/foreach}
                                </select>
                            {/if}
                        {/foreach}
                    </div>
                    {if $product->isOffersUse()}
                        {foreach from=$product.offers.items key=key item=offer name=offers}
                            <input value="{$key}" type="hidden" name="hidden_offers" class="hidden_offers" {if $smarty.foreach.offers.first}checked{/if} id="offer_{$key}" data-info='{$offer->getPropertiesJson()}' {if $check_quantity}data-num="{$product->getNum($key)}"{/if} data-change-cost='{ ".offerBarcode": "{$offer.barcode|default:$product.barcode}", ".myCost": "{$product->getCost(null, $key)}", ".lastPrice": "{$product->getOldCost($key)}"}'/>
                        {/foreach}                        
                        <input type="hidden" name="offer" value="0"/>                                            
                    {/if}                    
                {elseif $product->isOffersUse()}
                    {* Простые комплектации *}
                    <div class="offer">
                        <div class="pname">{$product.offer_caption|default:t('Комплектация')}</div>
                        <select name="offer">
                            {foreach from=$product.offers.items key=key item=offer name=offers}
                                <option value="{$key}" data-change-cost='{ ".myCost": "{$product->getCost(null, $key)}", ".lastPrice": "{$product->getOldCost($key)}"}'>{$offer.title}</option>
                            {/foreach}
                        </select>
                    </div>
                {/if}
                <div class="cost">
                    {$last_price = $product->getOldCost()}
                    {if $last_price>0}<div class="lastPrice">{$last_price}</div>{/if}
                    <span>{$product->getCost()} {$product->getCurrency()}</span>
                </div>

                {if $shop_config}
                <div class="basketwrap">
                    <a class="toBasket addToCart noShowCart" data-href="{$router->getUrl('shop-front-cartpage', ["add" => $product.id])}">{t}в корзину{/t}</a>
                    <span class="unobtainable hidden">{t}Нет в наличии{/t}</span>
                </div>
                {/if}
                {if $THEME_SETTINGS.enable_favorite}
                <br class="clearBoth"><a class="favorite inline{if $product->inFavorite()} inFavorite{/if}" data-favorite-url="{$router->getUrl('catalog-front-favorite')}">
                    <span class="">{t}В избранное{/t}</span>
                    <span class="already">{t}В избранном{/t}</span>
                </a>                
                {/if}
            </li>
            {/foreach}
        </ul>

        <div class="compareBody">
            {foreach from=$comp_data.values key=group_id item=values}
            <h3><span>{$comp_data.groups[$group_id].title|default:t('Общие')}</span></h3>

            <ul class="table">
                {foreach from=$values key=prop_id item=product_values}    
                    {if !$comp_data.props[$prop_id].hidden}
                    <li>
                        <div class="key">{$comp_data.props[$prop_id].title}{if $comp_data.props[$prop_id].unit}, {$comp_data.props[$prop_id].unit}{/if}</div>
                        {foreach from=$product_values key=product_id item=prop}
                        <div class="value" data-id="{$product_id}">
                            <span class="product">{$comp_data.items[$product_id].title}</span>
                            <span class="viewValue">{if $prop}{$prop->textView()}{else}-{/if}</span>
                        </div>
                        {/foreach}
                    </li>
                    {/if}
                {/foreach}            
            </ul>
            {/foreach}
        </div>
    </div>
</div>
{else}
<div class="noCompare">
    {t}Добавьте товары для сравнения{/t}
</div>
{/if}