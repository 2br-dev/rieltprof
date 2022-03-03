{* Блок полноценной корзины
@param Cart $cart - объект корзины
@param array $cart_info - информация о составе корзины
@param Currency $currency - текущая валюта
*}

{$shop_config = ConfigLoader::byModule('shop')}
{$users_config = ConfigLoader::byModule('users')}
{$is_cart_checkout = $shop_config->getCheckoutType() == 'cart_checkout'}
{$cart_data = $cart->getCartData()}
{$product_items = $cart->getProductItems()}

<div class="page-basket" id="rs-cart-items">
    <div class="page-basket_wrapper">
        {if !empty($cart_data.items)}
            <form method="POST" action="{$router->getUrl('shop-block-cartfull', ["action" => "update", '_block_id' => $_block_id])}" id="rs-cart-form">

                {if !$is_auth}
                    <div class="cart-authorization-info">
                        {t url=$users_config->getAuthorizationUrl()}Если Вы регистрировались ранее, пожалуйста, <a href="%url" class="inDialog">авторизуйтесь</a>.{/t}
                    </div>
                {/if}

                {hook name="shop-cartpage:products" title="{t}Корзина:товары{/t}" product_items=$product_items}
                    <div class=" catalog-list">
                        {foreach $cart_data.items as $index => $item}
                            {$product = $product_items[$index].product}
                            {$cartitem = $product_items[$index].cartitem}
                            {if !empty($cartitem.multioffers)}
                                {$multioffers=unserialize($cartitem.multioffers)}
                            {/if}

                            <div class="rs-cartitem" data-id="{$index}" data-product-id="{$cartitem.entity_id}">
                                <div class="card card-product">
                                    <div class="card-image">
                                        <a href="{$product->getUrl()}"><img src="{$product->getOfferMainImage($cartitem.offer, 476, 280)}" alt="{$product.title}"></a>
                                    </div>
                                    <div class="card-text">
                                        <div class="card-product_category-name">
                                            <a href="{$product->getMainDir()->getUrl()}"><small>{$product->getMainDir()->name}</small></a>
                                        </div>
                                        <div class="card-product_title">
                                            <a href="{$product->getUrl()}"><span>{$cartitem.title}</span></a>

                                            <div class="card-product_quantity">
                                                {$offer_barcode=$product->getBarcode($cartitem.offer)}
                                                {if $offer_barcode}
                                                    <p class="barcode">{t}Артикул{/t}: {$product->getBarcode($cartitem.offer)}</p>
                                                {elseif $product.barcode}
                                                    <p class="barcode">{t}Артикул{/t}: {$product.barcode}</p>
                                                {/if}
                                            </div>


                                            {if $product->isMultiOffersUse()}
                                                <div class="card-product_multi-offers multioffer">

                                                    {foreach $product.multioffers.levels as $level}
                                                        {if !empty($level.values)}
                                                            <div class="multioffer_title">{if $level.title}{$level.title}{else}{$level.prop_title}{/if}</div>
                                                            <select class="select" name="products[{$index}][multioffers][{$level.prop_id}]" data-prop-title="{if $level.title}{$level.title}{else}{$level.prop_title}{/if}">
                                                                {foreach $level.values as $value}
                                                                    <option {if $multioffers[$level.prop_id].value == $value.val_str}selected="selected"{/if} value="{$value.val_str}">{$value.val_str}</option>
                                                                {/foreach}
                                                            </select>
                                                        {/if}
                                                    {/foreach}

                                                    {if $product->isOffersUse()}
                                                        {foreach $product->getOffers() as $key => $offer}
                                                            <input id="offer_{$key}" type="hidden" name="hidden_offers" class="hidden_offers" value="{$key}" data-info='{$offer->getPropertiesJson()}' data-num="{$offer.num}"/>
                                                            {if $cartitem.offer==$key}
                                                                <input type="hidden" name="products[{$index}][offer]" value="{$key}"/>
                                                            {/if}
                                                        {/foreach}
                                                    {/if}

                                                </div>
                                            {elseif $product->isOffersUse()}

                                                <div class="card-product_offers">
                                                    <select name="products[{$index}][offer]" class="select rs-offer">
                                                        {foreach $product->getOffers() as $key => $offer}
                                                            <option value="{$key}" {if $cartitem.offer==$key}selected{/if}>{$offer.title}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>

                                            {/if}

                                        </div>
                                        <div class="card-product_quantity">
                                            {moduleinsert name="\Shop\Controller\Block\ProductAmountInCart" cart_item=$cartitem}
                                            <div class="error">{$item.amount_error}</div>
                                        </div>
                                    </div>
                                    <div class="card-price">
                                        <span class="card-price_present">{$item.cost}</span>
                                        <div class="card-price_discount">
                                            {if $item.discount>0}
                                                {t discount=$item.discount}скидка %discount{/t}
                                            {/if}
                                        </div>

                                        {if !$cartitem->getForbidRemove()}
                                            <div><a href="{$router->getUrl('shop-block-cartfull', ["action" => "removeItem", "id" => $index, '_block_id' => $_block_id])}" class="link link-del rs-remove"><i class="pe-2x pe-7s-close"></i> {t}Удалить{/t}</a></div>
                                        {/if}
                                    </div>
                                </div>

                                {* Сопутствующие товары *}

                                {$concomitant=$product->getConcomitant()}
                                {foreach $item.sub_products as $id => $sub_product_data}
                                    {$sub_product=$concomitant[$id]}
                                    <div class="card card-concomitant">
                                        <div class="card-image">
                                            <label>
                                                <input class="rs-field-concomitant" type="checkbox" name="products[{$index}][concomitant][]" value="{$sub_product->id}"{if $sub_product_data.checked}checked="checked"{/if}>
                                                {$sub_product->title}
                                            </label>
                                        </div>
                                        <div class="card-text">

                                            <div class="card-product_quantity">
                                                {if $shop_config.allow_concomitant_count_edit}
                                                    <div class="quantity rs-amount">
                                                        {$name = "products[{$index}][concomitant_amount][{$sub_product->id}]"}
                                                        {$min = $sub_product->getAmountStep()}
                                                        {$step = $sub_product->getAmountStep()}
                                                        {$amount = $sub_product_data.amount}
                                                        <input type="number" name="{$name}" value="{$amount}" class="rs-field-amount rs-concomitant" min="{$min}" step="{$step}" data-id="{$sub_product->id}" {if $shop_config.allow_buy_all_stock_ignoring_amount_step}data-break-point="{$sub_product->getNum()}"{/if}>
                                                        <div class="quantity-nav">
                                                            <div class="quantity-unit">
                                                                {if $catalog_config.use_offer_unit}
                                                                    {$product_offers = $product->getOffers()}
                                                                    {if isset($product_offers[$cartitem.offer])}
                                                                        {$product_offers[$cartitem.offer]->getUnit()->stitle}
                                                                    {else}
                                                                        {$product->getUnit()->stitle}
                                                                    {/if}
                                                                {else}
                                                                    {$product->getUnit()->stitle}
                                                                {/if}
                                                            </div>
                                                            <div class="quantity-button quantity-up rs-inc" data-amount-step="{$sub_product->getAmountStep()}">+</div>
                                                            <div class="quantity-button quantity-down rs-dec" data-amount-step="{$sub_product->getAmountStep()}">-</div>
                                                        </div>
                                                    </div>
                                                {else}
                                                    <div class="amount" title="{t}Количество{/t}">{$sub_product_data.amount} {$sub_product->getUnit()->stitle|default:"шт."}</div>
                                                {/if}
                                                <div class="error">{$sub_product_data.amount_error}</div>
                                            </div>

                                        </div>
                                        <div class="card-price">
                                            <span class="card-price_present">{$sub_product_data.cost}</span>
                                            <div class="card-price_discount">
                                                {if $sub_product_data.discount>0}
                                                    {t discount=$sub_product_data.discount}скидка %discount{/t}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        {/foreach}
                    </div>
                {/hook}

                <div class="t-actions checkout_block">
                    <h3 class="h3">{t}Акции{/t}:</h3>
                    {$coupons=$cart->getCouponItems()}

                    {if !$coupons}
                        <p>{t}Напишите свой промо-код{/t}:</p>
                        <div>
                            <input type="text" name="coupon" value="{$coupon_code}" placeholder="{t}Напишите свой промо-код{/t}">
                            <button type="button" data-href="{$router->getUrl('shop-block-cartfull', ["action" => "update", '_block_id' => $_block_id])}" class="link link-more rs-apply-coupon">{t}Применить купон{/t}</button>
                        </div>
                    {else}
                        {foreach $coupons as $id => $item}
                            <div class="t-actions-line rs-coupon" data-id="{$id}">
                                <span class="t-actions-title">{t code=$item.coupon.code}Применен купон <strong>%code</strong>{/t}</span>
                                <a class="t-actions-remove rs-remove" title="{t}Удалить скидочный купон из корзины{/t}" href="{$router->getUrl('shop-block-cartfull', ["action" => "removeItem", 'id' => $id, '_block_id' => $_block_id])}">
                                    <i class="pe-2x pe-7s-close"></i>
                                </a>
                            </div>
                        {/foreach}
                    {/if}
                </div>

                {if !empty($cart_data.errors)}
                    <div class="t-order-errors">
                        {foreach $cart_data.errors as $error}
                            {$error}<br>
                        {/foreach}
                    </div>
                {/if}
            </form>
        {else}
            <div class="empty-list">
                {t}В корзине нет товаров{/t}
            </div>
        {/if}
    </div>
</div>