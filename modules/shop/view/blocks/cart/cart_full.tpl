{* Корзина, совмещенная с оформлением заказа *}
{addjs file="%shop%/rscomponent/cart.js"}

{$shop_config = ConfigLoader::byModule('shop')}
{$users_config = ConfigLoader::byModule('users')}
{$cart_checkout = $shop_config->getCheckoutType() == 'cart_checkout'}
{$product_items = $cart->getProductItems()}

<div class="mb-6 mb-lg-0" id="rs-cart-page">
    {if !$is_auth}
        <div class="checkout-auth mb-5">
            {t url=$users_config->getAuthorizationUrl()}Если Вы регистрировались ранее, пожалуйста, <a href="%url" class="rs-in-dialog">авторизуйтесь</a>.{/t}
        </div>
    {/if}
    <form method="POST" action="{$router->getUrl('shop-block-cartfull', ["action" => "update", '_block_id' => $_block_id])}" id="rs-cart-form">
        {hook name="shop-block-cartfull:block" title="{t}Корзина заказа полная:блок{/t}"}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="m-0">{t}Корзина{/t}:</h2>
            <div class="ms-3">
                <a class="cart-checkout-clear rs-clean" href="{$router->getUrl('shop-block-cartfull', ["action" => "cleanCart", '_block_id' => $_block_id])}">{t}Очистить{/t}</a>
            </div>
        </div>
        <div class="last-child-margin-remove">
            {hook name="shop-block-cartfull:products" title="{t}Корзина заказа полная:товары{/t}"}
            {foreach $cart_data.items as $index => $item}
                {$product = $product_items[$index].product}
                {$cartitem = $product_items[$index].cartitem}
                {if !empty($cartitem.multioffers)}
                    {$multioffers = unserialize($cartitem.multioffers)}
                {/if}
                <div class="cart-checkout-item rs-cart-item {if $item.amount_error} cart-checkout-item_error{/if}" data-id="{$cartitem.entity_id}" data-uniq="{$index}">
                    <div class="d-flex">
                        <a href="{$product->getUrl()}" class="cart-checkout-item__img">
                            <noscript class="loading-lazy">
                                <img src="{$product->getOfferMainImage($cartitem.offer, 64, 64, 'xy')}"
                                     srcset="{$product->getOfferMainImage($cartitem.offer, 128, 128, 'xy')} 2x" alt="{$product.title}" loading="lazy">
                            </noscript>
                        </a>
                        <div class="col">
                            <div class="cart-checkout-item__info">
                                <div class="d-flex justify-content-between">
                                    <a class="cart-checkout-item__title" href="{$product->getUrl()}">{$cartitem.title}</a>
                                    {if !$cartitem->getForbidRemove()}
                                        <a class="cart-checkout-item__del rs-remove" href="{$router->getUrl('shop-block-cartfull', ["action" => "removeItem", 'id' => $index, '_block_id' => $_block_id])}">
                                            <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.0218 2.98208C13.2161 3.17825 13.2146 3.49483 13.0185 3.68918L8.6666 8.00064L13.0185 12.3121C13.2147 12.5065 13.2161 12.823 13.0218 13.0192C12.8274 13.2154 12.5109 13.2169 12.3147 13.0225L7.95617 8.70447L3.68515 12.9358C3.48898 13.1302 3.1724 13.1287 2.97805 12.9325C2.7837 12.7364 2.78518 12.4198 2.98135 12.2254L7.24574 8.00064L2.98137 3.77587C2.7852 3.58152 2.78372 3.26494 2.97807 3.06877C3.17242 2.8726 3.489 2.87112 3.68517 3.06547L7.95617 7.29681L12.3147 2.97879C12.5108 2.78444 12.8274 2.78591 13.0218 2.98208Z" />
                                            </svg>
                                        </a>
                                    {/if}
                                </div>
                                <div class="mt-2">
                                    <div class="cart-equipments">
                                        {if $product->isMultiOffersUse()}
                                            {foreach $product.multioffers.levels as $level}
                                                {if !empty($level.values)}
                                                    <div class="catalog-select catalog-select_cart">
                                                        <div class="catalog-select__label">{if $level.title}{$level.title}{else}{$level.prop_title}{/if}</div>
                                                        <div class="catalog-select__options">
                                                            <select class="select rs-multioffer" name="products[{$index}][multioffers][{$level.prop_id}]" data-prop-title="{if $level.title}{$level.title}{else}{$level.prop_title}{/if}">
                                                                {foreach $level.values as $value}
                                                                    <option {if $multioffers[$level.prop_id].value == $value.val_str}selected="selected"{/if} value="{$value.val_str}">{$value.val_str}</option>
                                                                {/foreach}
                                                            </select>
                                                            <div class="catalog-select__value"></div>
                                                        </div>
                                                    </div>
                                                {/if}
                                            {/foreach}

                                            {if $product->isOffersUse()}
                                                {$offers = $product->getOffers()}
                                                {foreach $offers as $key => $offer}
                                                    <input id="offer_{$key}" type="hidden" class="rs-hidden-multioffer" value="{$key}" data-info='{$offer->getPropertiesJson()}' data-num="{$offer.num}"/>
                                                {/foreach}
                                                <input type="hidden" name="products[{$index}][offer]" value="{if isset($offers[$cartitem.offer])}{$cartitem.offer}{else}0{/if}" class="rs-offer"/>
                                            {/if}
                                        {elseif $product->isOffersUse()}
                                            <div class="catalog-select catalog-select_cart">
                                                <div class="catalog-select__label">{$product.offer_caption|default:"{t}Комплектация{/t}"}:</div>
                                                <div class="catalog-select__options">
                                                    <select class="select rs-offer" name="products[{$index}][offer]">
                                                        {foreach $product->getOffers() as $key => $offer}
                                                            <option value="{$key}" {if $cartitem.offer==$key}selected{/if}>{$offer.title}</option>
                                                        {/foreach}
                                                    </select>
                                                    <div class="catalog-select__value"></div>
                                                </div>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            <div class="cart-checkout-item__bar">
                                <div>
                                    <span class="fw-bold">{$item.cost}</span>
                                    {if $item.discount_unformated > 0}
                                        <span class="old-price">{$item.base_cost}</span>
                                    {/if}
                                </div>
                                <div>
                                    {$min = $product->getAmountStep($cartitem.offer)}
                                    {$step = $product->getAmountStep($cartitem.offer)}
                                    {$amount = $cartitem.amount}
                                    <div class="cart-amount rs-number-input">
                                        <button type="button" class="rs-number-down">
                                            <svg width="12" height="12" viewBox="0 0 12 12"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.4752 6.46875H1.47516V4.96875H10.4752V6.46875Z"/>
                                            </svg>
                                        </button>
                                        <div class="cart-amount__input">
                                            <input type="number" value="{$amount}" min="{$min}" step="{$step}" name="products[{$index}][amount]"
                                                   {if $shop_config.allow_buy_all_stock_ignoring_amount_step}data-break-point="{$product->getNum()}"{/if}
                                                    {if $cartitem->getForbidChangeAmount()}disabled{/if} class="rs-amount">
                                            <span>
                                                                        {if $catalog_config.use_offer_unit}
                                                                            {$product.offers.items[$cartitem.offer]->getUnit()->stitle}
                                                                        {else}
                                                                            {$product->getUnit()->stitle}
                                                                        {/if}
                                                                    </span>
                                        </div>
                                        <button type="button" class="rs-number-up">
                                            <svg width="12" height="12" viewBox="0 0 12 12"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.7326 6.94364H6.87549V10.8008H5.58978V6.94364H1.73264V5.65792H5.58978V1.80078H6.87549V5.65792H10.7326V6.94364Z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {if $item.amount_error}
                        <div class="fs-6 danger-link mt-3">{$item.amount_error}</div>
                    {/if}
                </div>

                {$concomitant = $product->getConcomitant()}
                {foreach $item.sub_products as $id => $sub_product_data}
                    {$sub_product=$concomitant[$id]}
                    {if $sub_product_data.checked || $THEME_SETTINGS.show_unselected_concomitant_in_cart}

                        <div class="cart-checkout-item rs-cart-item {if $sub_product_data.amount_error} cart-checkout-item_error{/if}">
                            <div class="d-flex">
                                <a href="{$sub_product->getUrl()}" class="cart-checkout-item__img">
                                    <noscript class="loading-lazy">
                                        <img src="{$sub_product->getOfferMainImage($cartitem.offer, 64, 64, 'xy')}"
                                             srcset="{$sub_product->getOfferMainImage($cartitem.offer, 128, 128, 'xy')} 2x" alt="{$sub_product.title}" loading="lazy">
                                    </noscript>
                                </a>
                                <div class="col">
                                    <div class="cart-checkout-item__info">
                                        <div class="d-flex justify-content-between">
                                            <a class="cart-checkout-item__title" href="{$sub_product->getUrl()}">{$sub_product.title}</a>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input rs-concomitant-checkbox" type="checkbox" id="concomitantCartItem{$product.id}-{$id}" name="products[{$index}][concomitant][]"
                                                       value="{$sub_product.id}" {if $sub_product_data.checked}checked{/if}>

                                                <label class="form-check-label" for="concomitantCartItem{$product.id}-{$id}"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cart-checkout-item__bar">
                                        <div>
                                            <span class="fw-bold">{$sub_product_data.cost}</span>
                                            {if $sub_product_data.discount_unformated > 0}
                                                <span class="old-price">{$sub_product_data.base_cost}</span>
                                            {/if}
                                        </div>

                                        <div>
                                            {$name = "products[{$index}][concomitant_amount][{$sub_product->id}]"}
                                            {$min = $sub_product->getAmountStep()}
                                            {$step = $sub_product->getAmountStep()}
                                            {$amount = $sub_product_data.amount}

                                            <div class="cart-amount rs-number-input">
                                                <button type="button" class="rs-number-down">
                                                    <svg width="12" height="12" viewBox="0 0 12 12"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.4752 6.46875H1.47516V4.96875H10.4752V6.46875Z"/>
                                                    </svg>
                                                </button>
                                                <div class="cart-amount__input">
                                                    <input type="number" value="{$amount}" min="{$min}" step="{$step}" name="{$name}" data-id="{$sub_product->id}"
                                                           {if $shop_config.allow_buy_all_stock_ignoring_amount_step}data-break-point="{$sub_product->getNum()}"{/if}
                                                            {if !$shop_config.allow_concomitant_count_edit}disabled{/if} class="rs-amount">
                                                    <span>
                                                        {$sub_product->getUnit()->stitle|default:"{t}шт.{/t}"}
                                                    </span>
                                                </div>
                                                <button type="button" class="rs-number-up">
                                                    <svg width="12" height="12" viewBox="0 0 12 12"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.7326 6.94364H6.87549V10.8008H5.58978V6.94364H1.73264V5.65792H5.58978V1.80078H6.87549V5.65792H10.7326V6.94364Z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {if $sub_product_data.amount_error}
                                <div class="fs-6 danger-link mt-3">{$sub_product_data.amount_error}</div>
                            {/if}
                        </div>
                    {/if}
                {/foreach}
            {/foreach}
            {/hook}
        </div>
        {if $THEME_SETTINGS.enable_discount_coupons}
            <div class="checkout-block mt-lg-5 mt-4">
                {hook name="shop-block-cartfull:coupon" title="{t}Корзина заказа полная:ввод купона{/t}"}
                {$coupons = $cart->getCouponItems()}
                <div class="checkout-block__title mb-4">{t}Есть промокод?{/t}</div>
                <label for="coupon_input" class="form-label">{t}Введите промокод{/t}</label>
                <div class="row g-3">
                    <div class="promocode-form col-12 col-sm">
                        <input id="coupon_input" type="text" class="form-control" value="{$coupon_code}" name="coupon">
                        <button class="btn btn-primary rs-apply-coupon" data-href="{$router->getUrl('shop-block-cartfull', ["action" => "update", '_block_id' => $_block_id])}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M20.0029 6.39165L10.9059 18L4.00293 11.4783L5.31688 9.85694L10.693 14.9362L18.4797 5L20.0029 6.39165Z" fill="white"/>
                            </svg>
                        </button>
                    </div>
                    {foreach $coupons as $id => $item}
                        <div class="d-flex justify-content-end align-items-center text-end col-sm-auto col-lg-12 col-xl-auto">
                            <div>
                                <div class="fw-bold">{t number=$item.coupon.code}Купон на скидку %number{/t}</div>
                            </div>
                            <a class="ms-2 rs-remove" href="{$router->getUrl('shop-block-cartfull', ["action" => "removeItem", 'id' => $id, '_block_id' => $_block_id])}">
                                <svg width="16" height="16" fill="#FF2F2F" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.0218 2.98208C13.2161 3.17825 13.2146 3.49483 13.0185 3.68918L8.6666 8.00064L13.0185 12.3121C13.2147 12.5065 13.2161 12.823 13.0218 13.0192C12.8274 13.2154 12.5109 13.2169 12.3147 13.0225L7.95617 8.70447L3.68515 12.9358C3.48898 13.1302 3.1724 13.1287 2.97805 12.9325C2.7837 12.7364 2.78518 12.4198 2.98135 12.2254L7.24574 8.00064L2.98137 3.77587C2.7852 3.58152 2.78372 3.26494 2.97807 3.06877C3.17242 2.8726 3.489 2.87112 3.68517 3.06547L7.95617 7.29681L12.3147 2.97879C12.5108 2.78444 12.8274 2.78591 13.0218 2.98208Z" />
                                </svg>
                            </a>
                        </div>
                    {/foreach}
                </div>
                {/hook}
            </div>
        {/if}
        <div class="border-top pt-4 mt-4">
            {if !empty($cart_data.errors)}
                <div class="text-danger mb-4 fs-5">
                    {foreach $cart_data.errors as $error}
                        {$error}<br>
                    {/foreach}
                </div>
            {/if}
        </div>
        <div class="mt-3 d-none d-lg-block">
            <a class="return-link rs-go-back">
                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M14.7803 5.72846C15.0732 6.03307 15.0732 6.52693 14.7803 6.83154L9.81066 12L14.7803 17.1685C15.0732 17.4731 15.0732 17.9669 14.7803 18.2715C14.4874 18.5762 14.0126 18.5762 13.7197 18.2715L8.21967 12.5515C7.92678 12.2469 7.92678 11.7531 8.21967 11.4485L13.7197 5.72846C14.0126 5.42385 14.4874 5.42385 14.7803 5.72846Z"/>
                </svg>
                <span class="ms-2">{t}Назад к покупкам{/t}</span>
            </a>
        </div>
        {/hook}
    </form>
</div>