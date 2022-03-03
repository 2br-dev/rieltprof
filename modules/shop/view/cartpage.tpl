{$shop_config = ConfigLoader::byModule('shop')}
{$catalog_config = ConfigLoader::byModule('catalog')}
{$product_items = $cart->getProductItems()}
<div class="cart" id="cartItems">
    <div class="top">
        <div class="cartIcon">{t}Корзина{/t}</div>
        {if !empty($cart_data.items)}
        <a class="clearCart" href="{$router->getUrl('shop-front-cartpage', ["Act" => "cleanCart"])}"><span>{t}очистить корзину{/t}</span></a>
        {/if}
    </div>
    <div class="padd">
        {if !empty($cart_data.items)}
        <div class="head">
            <div class="price">{t}Цена{/t}</div>
            <div class="amount">{t}Количество{/t}</div>
        </div>
        <form method="POST" action="{$router->getUrl('shop-front-cartpage', ["Act" => "update"])}" id="cartForm">
            <input type="submit" class="hidden">
            {hook name="shop-cartpage:products" title="{t}Корзина:товары{/t}" product_items=$product_items}
                <div class="viewport">
                    <table class="cartProducts">
                        {foreach $cart_data.items as $index => $item}
                            {$product = $product_items[$index].product}
                            {$cartitem = $product_items[$index].cartitem}
                            {if !empty($cartitem.multioffers)}
                               {assign var=multioffers value=unserialize($cartitem.multioffers)} 
                            {/if}
                            <tr data-id="{$index}" data-product-id="{$cartitem.entity_id}" class="cartitem{if $smarty.foreach.items.first} first{/if}">
                                <td class="colPreview">
                                    <a class="preview" href="{$product->getUrl()}"><img src="{$product->getOfferMainImage($cartitem.offer, 64, 64)}" alt="{$product.title}"/></a>
                                </td>
                                <td class="colTitle">
                                    <a class="title" href="{$product->getUrl()}">{$cartitem.title}</a><br>
                                    {if $product->isMultiOffersUse()}
                                        <div class="multiOffers">
                                            {foreach $product.multioffers.levels as $level}
                                                {if !empty($level.values)}
                                                    <div class="title">{if $level.title}{$level.title}{else}{$level.prop_title}{/if}</div>
                                                    <select name="products[{$index}][multioffers][{$level.prop_id}]" data-prop-title="{if $level.title}{$level.title}{else}{$level.prop_title}{/if}">
                                                        {foreach $level.values as $value}
                                                            <option {if $multioffers[$level.prop_id].value == $value.val_str}selected="selected"{/if} value="{$value.val_str}">{$value.val_str}</option>   
                                                        {/foreach}
                                                    </select>
                                                {/if}
                                            {/foreach}
                                            {if $product->isOffersUse()}
                                                {foreach from=$product.offers.items key=key item=offer name=offers}
                                                    <input id="offer_{$key}" type="hidden" name="hidden_offers" class="hidden_offers" value="{$key}" data-info='{$offer->getPropertiesJson()}' data-num="{$offer.num}"/>
                                                    {if $cartitem.offer==$key}
                                                        <input type="hidden" name="products[{$index}][offer]" value="{$key}"/>
                                                    {/if}
                                                {/foreach}
                                            {/if}
                                        </div>
                                    {elseif $product->isOffersUse()}
                                        <select name="products[{$index}][offer]" class="offer">
                                            {foreach from=$product.offers.items key=key item=offer name=offers}
                                                <option value="{$key}" {if $cartitem.offer==$key}selected{/if}>{$offer.title}</option>
                                            {/foreach}
                                        </select>
                                    {/if}
                                </td>
                                <td class="colAmount">
                                    <div class="amoutPicker">
                                        {if !$cartitem->getForbidChangeAmount()}
                                            <div class="qpicker">
                                                <a class="inc" data-amount-step="{$product->getAmountStep()}"></a>
                                                <a class="dec" data-amount-step="{$product->getAmountStep()}"></a>
                                            </div>
                                        {/if}
                                        <input type="number" min="{$product->getAmountStep()}" step="{$product->getAmountStep()}" class="fieldAmount" value="{$cartitem.amount}" name="products[{$index}][amount]" {if $shop_config.allow_buy_all_stock_ignoring_amount_step}data-break-point="{$product->getNum()}"{/if}>
                                        <span class="unit">
                                            {if $catalog_config.use_offer_unit}
                                                {$product.offers.items[$cartitem.offer]->getUnit()->stitle}
                                            {else}
                                                {$product->getUnit()->stitle}
                                            {/if}
                                        </span>
                                        <div class="error">{$item.amount_error}</div>
                                    </div>
                                </td>
                                <td class="colPrice">
                                    <div class="floatbox">
                                        <span class="priceBlock">
                                            <span class="priceValue">{$item.cost}</span>
                                        </span>
                                    </div>
                                    <div class="discount">
                                        {if $item.discount>0}
                                        {t}скидка{/t} {$item.discount}
                                        {/if}
                                    </div>
                                </td>
                                <td class="colRemove">
                                    {if !$cartitem->getForbidRemove()}
                                        <a title="{t}Удалить товар из корзины{/t}" class="remove" href="{$router->getUrl('shop-front-cartpage', ["Act" => "removeItem", "id" => $index])}"></a>
                                    {/if}
                                </td>
                            </tr>
                            {assign var=concomitant value=$product->getConcomitant()}
                            
                            {foreach from=$item.sub_products key=id item=sub_product_data}
                                {assign var=sub_product value=$concomitant[$id]}
                                <tr>

                                    <td colspan="2" class="colTitle">
                                        <label>
                                            <input 
                                                class="fieldConcomitant" 
                                                type="checkbox" 
                                                name="products[{$index}][concomitant][]" 
                                                value="{$sub_product->id}"
                                                {if $sub_product_data.checked}
                                                    checked="checked"
                                                {/if}
                                                >
                                            {$sub_product->title}
                                        </label>
                                    </td>
                                    <td class="colAmount">
                                        {if $shop_config.allow_concomitant_count_edit}
                                            <div class="amoutPicker">
                                                <div class="qpicker">
                                                    <a class="inc" data-amount-step="{$sub_product->getAmountStep()}"></a>
                                                    <a class="dec" data-amount-step="{$sub_product->getAmountStep()}"></a>
                                                </div>
                                                <input type="number" min="{$sub_product->getAmountStep()}" step="{$sub_product->getAmountStep()}" class="fieldAmount concomitant" data-id="{$sub_product->id}" value="{$sub_product_data.amount}" name="products[{$index}][concomitant_amount][{$sub_product->id}]" {if $shop_config.allow_buy_all_stock_ignoring_amount_step}data-break-point="{$sub_product->getNum()}"{/if}>
                                                <span class="unit">{$product->getUnit()->stitle}</span>
                                            </div>
                                        {else}
                                            {$sub_product_data.amount} {$sub_product->getUnit()->stitle}
                                        {/if}
                                        <div class="error">{$sub_product_data.amount_error}</div>
                                    </td>
                                    <td class="colPrice">
                                        <span class="priceBlock">
                                            <span class="priceValue">{$sub_product_data.cost}</span>
                                        </span>
                                        <div class="discount">
                                            {if $sub_product_data.discount>0}
                                            {t}скидка{/t} {$sub_product_data.discount}
                                            {/if}
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            {/foreach}
                        {/foreach}
                    </table>
                </div>
            {/hook}
            {hook name="shop-cartpage:summary" title="{t}Корзина:итог{/t}"}
                <div class="cartFooter">
                    <div class="linesContainer">
                        {foreach from=$cart->getCouponItems() key=id item=item}
                            <div class="line">
                                <a href="{$router->getUrl('shop-front-cartpage', ["Act" => "removeItem", "id" => $id])}" class="remove" title="{t}удалить скидочный купон{/t}"></a>
                                <div class="text">{t}Купон на скидку{/t} {$item.coupon.code}</div>
                                <div class="digits"></div>
                            </div>
                        {/foreach}
                        {if $cart_data.total_discount>0}
                            <div class="line">
                                <div class="text">{t}Скидка на заказ{/t}</div>
                                <div class="digits">{$cart_data.total_discount}</div>
                            </div>
                        {/if}
                    </div>
                    <div class="discountText">
                        <span class="info">{t}Купон на скидку (если есть){/t}: </span><input type="text" class="couponCode{if $cart->getUserError('coupon')!==false} hasError{/if}" size="12" name="coupon" value="{$coupon_code}">&nbsp;
                        <a class="applyCoupon">{t}применить{/t}</a>
                    </div>
                    <div class="total"><span class="text">{t}Итого{/t}:</span> <span class="total-value">{$cart_data.total}</span></div>
                    <div class="loader"></div>
                </div>
            {/hook}
            {hook name="shop-cartpage:bottom" title="{t}Корзина:подвал{/t}"}
                <div class="bottom">
                    <noscript><input type="submit" class="onemoreEmpty recalc" value="{t}Пересчитать{/t}"></noscript>
                    <a href="{$router->getUrl('shop-front-checkout')}" class="submit{if $cart_data.has_error} disabled{/if}">{t}Оформить заказ{/t}</a>
                    
                    <a href="JavaScript:;" class="continue">{t}Продолжить покупки{/t}</a>
                    
                    {if $THEME_SETTINGS.enable_one_click_cart}
                    <a href="JavaScript:;" class="toggleOneClickCart"><span class="tabletHidden">{t}Заказать по телефону{/t}</span></a>
                    {/if}
                    
                    <div class="error" {if !empty($cart_data.errors)}style="display: block;"{/if}>
                        {foreach from=$cart_data.errors item=error}
                            {$error}<br>
                        {/foreach}
                    </div>
                </div>
            {/hook}
        </form>
        {* Покупка в один клик в корзине *}
        {if $THEME_SETTINGS.enable_one_click_cart}
            {moduleinsert name="\Shop\Controller\Block\OneClickCart"}
        {/if}
        {else}
            <div class="empty">{t}В корзине нет товаров{/t}</div>
        {/if}
    </div>
</div>