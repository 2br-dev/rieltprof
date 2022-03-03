{* Корзина во всплывающем блоке *}

<div id="rs-cart-items">

    <div class="t-close rs-close-dlg"><i class="pe-2x pe-7s-close-circle"></i></div>
    <div class="t-drop-basket_wrap">

        <span class="t-drop-basket__title">{t}Корзина{/t}</span>
        {if $cart_data.items_count}
            <div class="t-drop-basket__info">
                <span class="t-drop-basket__info_goods">{t n=$cart_data.items_count}%n [plural:%n:товар|товара|товаров]{/t}</span>
                <a class="t-drop-basket__info_total rs-clear-cart" href="{$router->getUrl('shop-front-cartpage', ["Act" => "cleanCart", "floatCart" => $floatCart])}">{t}Очистить корзину{/t}</a>
            </div>
        {/if}
        {if $cart_data.items}
            <form method="POST" action="{$router->getUrl('shop-front-cartpage', ["Act" => "update", "floatCart" => $floatCart])}" id="rs-cart-form">
                {hook name="shop-cartpage-float:products" title="{t}Корзина:товары{/t}" product_items=$product_items}
                    <ul class="t-drop-basket__list rs-save-scroll">
                    {foreach $cart_data.items as $index => $item}

                        {$product=$product_items[$index].product}
                        {$cartitem=$product_items[$index].cartitem}
                        {if !empty($cartitem.multioffers)}
                            {$multioffers=unserialize($cartitem.multioffers)}
                        {/if}

                        <li data-id="{$index}" data-product-id="{$cartitem.entity_id}">
                            <div class="t-drop-basket__list_item">
                                <a class="t-drop-basket__list_item-img" href="{$product->getUrl()}">
                                    <img src="{$product->getOfferMainImage($cartitem.offer, 128, 78, 'axy')}" alt="{$product.title}">
                                </a>

                                <div class="t-drop-basket__list_item-description">
                                    <small><a href="{$product->getMainDir()->getUrl()}">{$product->getMainDir()->name}</a></small>
                                    <a href="{$product->getUrl()}">{$cartitem.title}</a>

                                    {* Параметры товара. Не даем выбирать, просто отображаем текстом *}
                                    {if $product->isMultiOffersUse()}

                                        <div class="multiOffers">
                                            {foreach $product.multioffers.levels as $level}
                                                {if !empty($level.values)}
                                                    <input type="hidden" name="products[{$index}][multioffers][{$level.prop_id}]" data-prop-title="{if $level.title}{$level.title}{else}{$level.prop_title}{/if}" value="{$multioffers[$level.prop_id].value}">
                                                    <span class="multiofferTitle">{if $level.title}{$level.title}{else}{$level.prop_title}{/if}</span>:
                                                    <span>{$multioffers[$level.prop_id].value}</span><br>
                                                {/if}
                                            {/foreach}

                                            {if $product->isOffersUse()}
                                                <input type="hidden" name="products[{$index}][offer]" value="{$cartitem.offer}"/>
                                            {/if}
                                        </div>

                                    {elseif $product->isOffersUse()}
                                        <input type="hidden" name="products[{$index}][offer]" value="{$cartitem.offer}">
                                        <div class="offers">
                                            {$product->getOfferTitle($cartitem.offer)}
                                        </div>
                                    {/if}
                                </div>

                                <div class="t-drop-basket__list_item-price">
                                    {moduleinsert name="\Shop\Controller\Block\ProductAmountInCart" cart_item=$cartitem style_class="floatCart"}

                                    <span class="price"> {$item.cost}</span>

                                    <div class="discount">
                                        {if $item.discount>0}
                                            {t}скидка{/t} {$item.discount}
                                        {/if}
                                    </div>
                                </div>

                                <div class="t-drop-basket__list_item-del">
                                    {if !$cartitem->getForbidRemove()}
                                        <a class="rs-remove" href="{$router->getUrl('shop-front-cartpage', ["Act" => "removeItem", "id" => $index, "floatCart" => $floatCart])}"><i class="pe-2x pe-7s-close-circle"></i></a>
                                    {/if}
                                </div>
                            </div>
                        </li>

                        {* Покажем выбранные сопутствующие товары *}
                        {$concomitant=$product->getConcomitant()}
                        {foreach $item.sub_products as $id => $sub_product_data}
                            {$sub_product=$concomitant[$id]}
                            {if $sub_product_data.checked}
                                <li>
                                    <div class="t-drop-basket__list_item">
                                        <a class="t-drop-basket__list_item-img" href="{$sub_product->getUrl()}">

                                                <input class="fieldConcomitant"
                                                       type="hidden"
                                                       name="products[{$index}][concomitant][]"
                                                       value="{$sub_product->id}">
                                            <img src="{$sub_product->getMainImage(128,78, 'axy')}" alt="{$sub_product->title}">
                                        </a>

                                        <div class="t-drop-basket__list_item-description">
                                            <small><a href="{$sub_product->getMainDir()->getUrl()}">{$sub_product->getMainDir()->name}</a></small>
                                            <a href="{$sub_product->getUrl()}">{$sub_product.title}</a>
                                        </div>

                                        <div class="t-drop-basket__list_item-price">
                                            {if $shop_config.allow_concomitant_count_edit}
                                                <div class="quantity-float rs-amount{if $item.amount_error} amount-error{/if}">
                                                    {$min = $sub_product->getAmountStep()}
                                                    {$step = $sub_product->getAmountStep()}
                                                    {$amount = $sub_product_data.amount}
                                                    <input type="hidden" name="{$name}" value="{$amount}" class="rs-field-amount rs-concomitant" min="{$min}" step="{$step}" data-id="{$sub_product->id}" {if $shop_config.allow_buy_all_stock_ignoring_amount_step}data-break-point="{$sub_product->getNum()}"{/if}>
                                                    <a class="quantity-button quantity-down rs-dec" data-amount-step="{$sub_product->getAmountStep()}">-</a>
                                                    <div class="quantity-num-wrapper">
                                                        <span class="rs-num{if $sub_product_data.amount_error} has-error{/if}" {if $sub_product_data.amount_error}title="{$sub_product_data.amount_error}"{/if}>{$sub_product_data.amount}</span>
                                                        <span class="unit">{$sub_product->getUnit()->stitle|default:"шт."}</span>
                                                    </div>
                                                    <a class="quantity-button quantity-up rs-inc" data-amount-step="{$sub_product->getAmountStep()}">+</a>
                                                </div>
                                            {else}
                                                <div class="amount" title="Количество">{$sub_product_data.amount} {$sub_product->getUnit()->stitle|default:"шт."}</div>
                                            {/if}

                                            <span> {$sub_product_data.cost}</span>

                                            <div class="discount">
                                                {if $sub_product_data.discount>0}
                                                    {t}скидка{/t} {$sub_product_data.discount}
                                                {/if}
                                            </div>
                                        </div>

                                        <div class="t-drop-basket__list_item-del"></div>
                                    </div>
                                </li>
                            {/if}
                        {/foreach}
                    {/foreach}

                    {foreach $cart->getCouponItems() as $id => $item}
                        <li data-id="{$index}" data-product-id="{$cartitem.entity_id}" class="rs-cart-item rs-couponLine">
                            <div class="t-drop-basket__list_item-wide-title">{t code=$item.coupon.code}Купон на скидку %code{/t}</div>
                            <div class="t-drop-basket__list_item-del">
                                <a class="rs-remove" href="{$router->getUrl('shop-front-cartpage', ["Act" => "removeItem", "id" => $id, "floatCart" => $floatCart])}"><i class="pe-2x pe-7s-close-circle"></i></a>
                            </div>
                        </li>
                    {/foreach}

                </ul>
                {/hook}

                {hook name="shop-cartpage-float:summary" title="{t}Корзина:итог{/t}"}
                    <div class="t-drop-basket__total">
                        <span class="t-drop-basket__total_sum">{t alias="сумма" total=$cart_data.total}Сумма: <b>%total</b>{/t}</span>
                        <a href="{$router->getUrl('shop-front-cartpage')}" class="t-drop-basket__total_link link link-more">{t}Перейти в корзину{/t}</a>
                    </div>
                {/hook}
            </form>
        {else}
            <div class="empty-cart">
                {t}Нет товаров в корзине{/t}
            </div>
        {/if}

    </div>
</div>