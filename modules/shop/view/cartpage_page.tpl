{* Корзина на отдельной странице *}
<div id="rs-cart-page">
    {if !empty($cart_data.items)}
        <div class="d-flex align-items-center justify-content-between mb-5">
            <h1 class="m-0">{t}Корзина{/t}</h1>
            <a class="fs-5 ms-3 d-lg-none rs-go-back">{t}Вернуться к покупкам{/t}</a>
        </div>
        <div class="row">
            <form class="row" method="POST" action="{$router->getUrl('shop-front-cartpage', ["Act" => "update"])}" id="rs-cart-form">
                <div class="col mb-6 mb-lg-0">
                    <div class="d-none fs-5 mb-4 d-lg-flex justify-content-end">
                        <a class="rs-go-back">{t}Вернуться к покупкам{/t}</a>
                    </div>
                    <div>
                        {hook name="shop-cartpage:products" title="{t}Корзина:товары{/t}" product_items=$product_items}
                        {foreach $cart_data.items as $index => $item}
                            {$product = $product_items[$index].product}
                            {$cartitem = $product_items[$index].cartitem}
                            {if !empty($cartitem.multioffers)}
                                {$multioffers = unserialize($cartitem.multioffers)}
                            {/if}
                            <div class="cart-item rs-cart-item {if $item.amount_error} cart-item_error{/if}" data-id="{$cartitem.entity_id}" data-uniq="{$index}">
                                <div class="row g-3 g-md-4">
                                    <div class="col-md d-flex overflow-hidden">
                                        <a href="{$product->getUrl()}" class="cart-item__img">
                                            <img src="{$product->getOfferMainImage($cartitem.offer, 64, 64, 'xy')}"
                                                 srcset="{$product->getOfferMainImage($cartitem.offer, 128, 128, 'xy')} 2x" alt="{$product.title}" loading="lazy">
                                        </a>
                                        <div class="d-flex flex-column">
                                            <a class="cart-item__title" href="{$product->getUrl()}">{$cartitem.title}</a>
                                            <div class="cart-item__barcode">{t}Артикул:{/t}
                                                {$offer_barcode = $product->getBarcode($cartitem.offer)}
                                                {if $offer_barcode}
                                                    {$product->getBarcode($cartitem.offer)}
                                                {elseif $product.barcode}
                                                    {$product.barcode}
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
                                    </div>
                                    <div class="col-xl-5 col-md-6">
                                        <div class="row h-100">
                                            <div class="col-md overflow-hidden">
                                                <div class="cart-item__price-wrap">
                                                    {if $item.discount_unformated > 0}
                                                    <span class="cart-item__old-price">{$item.base_cost}</span>
                                                    {/if}
                                                    <span class="cart-item__price">{$item.cost}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col d-flex justify-content-md-end mt-auto">
                                                {if !$cartitem->getForbidRemove()}
                                                    <a class="cart-item__delete order-md-last rs-remove" href="{$router->getUrl('shop-front-cartpage', ["Act" => "removeItem", "id" => $index])}">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M14.5 9C14.2238 9 14 9.21419 14 9.47847V18.5215C14 18.7856 14.2238 19 14.5 19C14.7762 19 15 18.7856 15 18.5215V9.47847C15 9.21419 14.7762 9 14.5 9Z" />
                                                            <path d="M8.5 9C8.22383 9 8 9.21419 8 9.47847V18.5215C8 18.7856 8.22383 19 8.5 19C8.77617 19 9 18.7856 9 18.5215V9.47847C9 9.21419 8.77617 9 8.5 9Z" />
                                                            <path d="M4.39222 7.95419V19.4942C4.39222 20.1762 4.65398 20.8168 5.11123 21.2764C5.56639 21.7373 6.19981 21.9989 6.86271 22H16.1373C16.8004 21.9989 17.4338 21.7373 17.8888 21.2764C18.346 20.8168 18.6078 20.1762 18.6078 19.4942V7.95419C19.5167 7.72366 20.1057 6.8846 19.9841 5.99339C19.8624 5.10236 19.0679 4.43583 18.1274 4.43565H15.6176V3.85017C15.6205 3.35782 15.4167 2.88505 15.052 2.53724C14.6872 2.18961 14.1916 1.99604 13.6764 2.00006H9.32363C8.80835 1.99604 8.3128 2.18961 7.94803 2.53724C7.58326 2.88505 7.37952 3.35782 7.38239 3.85017V4.43565H4.87265C3.93209 4.43583 3.13764 5.10236 3.01586 5.99339C2.89427 6.8846 3.48326 7.72366 4.39222 7.95419ZM16.1373 21.0632H6.86271C6.0246 21.0632 5.37261 20.3753 5.37261 19.4942V7.99536H17.6274V19.4942C17.6274 20.3753 16.9754 21.0632 16.1373 21.0632ZM8.36277 3.85017C8.35952 3.60628 8.45986 3.37154 8.641 3.19938C8.82195 3.02721 9.06819 2.93262 9.32363 2.93683H13.6764C13.9318 2.93262 14.1781 3.02721 14.359 3.19938C14.5401 3.37136 14.6405 3.60628 14.6372 3.85017V4.43565H8.36277V3.85017ZM4.87265 5.37242H18.1274C18.6147 5.37242 19.0097 5.74987 19.0097 6.21551C19.0097 6.68114 18.6147 7.05859 18.1274 7.05859H4.87265C4.38533 7.05859 3.99031 6.68114 3.99031 6.21551C3.99031 5.74987 4.38533 5.37242 4.87265 5.37242Z" />
                                                            <path d="M11.5 9C11.2238 9 11 9.21419 11 9.47847V18.5215C11 18.7856 11.2238 19 11.5 19C11.7762 19 12 18.7856 12 18.5215V9.47847C12 9.21419 11.7762 9 11.5 9Z" />
                                                        </svg>
                                                    </a>
                                                {/if}
                                                {if $THEME_SETTINGS.enable_favorite}
                                                <a class="fav rs-favorite {if $product->inFavorite()}rs-in-favorite{/if}" data-title="{t}В избранное{/t}" data-already-title="{t}В избранном{/t}">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M11.2131 5.5617L12 6.5651L12.7869 5.56171C13.5614 4.57411 14.711 4 15.9217 4C18.1262 4 20 5.89454 20 8.32023C20 10.2542 18.8839 12.6799 16.3617 15.5585C14.6574 17.5037 12.8132 19.0666 11.9999 19.7244C11.1866 19.0667 9.34251 17.5037 7.63817 15.5584C5.1161 12.6798 4 10.2542 4 8.32023C4 5.89454 5.87376 4 8.07829 4C9.28909 4 10.4386 4.57407 11.2131 5.5617ZM11.6434 20.7195L11.7113 20.6333L11.6434 20.7195Z" stroke-width="1"/>
                                                    </svg>
                                                </a>
                                                {/if}
                                            </div>
                                            <div class="col-auto order-md-first">
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
                                <div class="fs-6 danger-link mt-3">{$item.amount_error}</div>
                            </div>

                            {$concomitant = $product->getConcomitant()}
                            {foreach $item.sub_products as $id => $sub_product_data}
                                {$sub_product=$concomitant[$id]}
                                {if $sub_product_data.checked || $THEME_SETTINGS.show_unselected_concomitant_in_cart}

                                    <div class="modal-cart-item {if $sub_product_data.amount_error} cart-item_error{/if}">
                                        <div class="row g-3 g-lg-4">
                                            <div class="col-lg d-flex overflow-hidden">
                                                <a href="{$sub_product->getUrl()}" class="modal-cart-item__img">
                                                    <img src="{$sub_product->getOfferMainImage($cartitem.offer, 64, 64, 'xy')}"
                                                         srcset="{$sub_product->getOfferMainImage($cartitem.offer, 128, 128, 'xy')} 2x" alt="{$sub_product.title}" loading="lazy">
                                                </a>
                                                <div class="d-flex flex-column">
                                                    <a class="modal-cart-item__title" href="{$sub_product->getUrl()}">{$sub_product.title}</a>
                                                </div>
                                            </div>
                                            <div class="col-lg-5">
                                                <div class="row h-100 g-2">
                                                    <div class="col-lg overflow-hidden text-end">
                                                        <div class="cart-item__price-wrap">
                                                            {if $sub_product_data.discount_unformated > 0}
                                                                <span class="cart-item__old-price">{$sub_product_data.base_cost}</span>
                                                            {/if}
                                                            <span class="cart-item__price">{$sub_product_data.cost}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col d-flex justify-content-lg-end mt-auto">

                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input rs-concomitant-checkbox" type="checkbox" id="concomitantCartItem{$product.id}-{$id}" name="products[{$index}][concomitant][]"
                                                                   value="{$sub_product.id}" {if $sub_product_data.checked}checked{/if}>

                                                            <label class="form-check-label" for="concomitantCartItem{$product.id}-{$id}"></label>
                                                        </div>

                                                    </div>
                                                    <div class="col-auto order-lg-first">
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
                                        <div class="fs-6 danger-link mt-3">{$sub_product_data.amount_error}</div>
                                    </div>

                                {/if}
                            {/foreach}
                        {/foreach}
                        {/hook}
                    </div>
                    <div class="fs-5 mt-4 d-flex justify-content-lg-end">
                        <a class="danger-link rs-clean" href="{$router->getUrl('shop-front-cartpage', ["Act" => "cleanCart"])}">{t}Очистить корзину{/t}</a>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <div class="cart-aside">
                        <div class="pt-3 d-flex cart-aside__row">
                            <div class="me-3">{t}Итого{/t}</div>
                            <div class="fw-bold">{$cart_data.total}</div>
                        </div>
                        {if $THEME_SETTINGS.enable_discount_coupons}
                            {$coupons = $cart->getCouponItems()}
                            <div class="border-top pt-4 mt-4">
                            {if !$coupons}
                                <label for="coupon_input" class="form-label">{t}Введите промокод{/t}</label>
                                <div class="promocode-form">
                                    <input id="coupon_input" type="text" class="form-control" value="{$coupon_code}" name="coupon">
                                    <button class="btn btn-primary rs-apply-coupon" type="button" data-href="{$router->getUrl('shop-front-cartpage', ["Act" => "applyCoupon"])}">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.0029 6.39165L10.9059 18L4.00293 11.4783L5.31688 9.85694L10.693 14.9362L18.4797 5L20.0029 6.39165Z" fill="white"/>
                                        </svg>
                                    </button>
                                </div>
                            {else}
                                {foreach $coupons as $id => $item}
                                    <label class="form-label">{t}Ваш промокод{/t}</label>
                                    <div class="promocode-form">
                                        <input type="text" class="form-control" value="{$item.coupon.code}" disabled>
                                        <button class="btn btn-primary rs-remove" type="button" href="{$router->getUrl('shop-front-cartpage', ["Act" => "removeItem", "id" => $id])}">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.5328 4.47215C19.8243 4.76641 19.8221 5.24127 19.5278 5.5328L13 12L19.5279 18.4672C19.8221 18.7587 19.8243 19.2336 19.5328 19.5279C19.2413 19.8221 18.7664 19.8243 18.4721 19.5328L11.9344 13.0557L5.52785 19.4028C5.23359 19.6943 4.75873 19.6921 4.4672 19.3978C4.17568 19.1036 4.17789 18.6287 4.47215 18.3372L10.8687 12L4.47218 5.66282C4.17792 5.3713 4.1757 4.89643 4.46723 4.60217C4.75875 4.30792 5.23362 4.3057 5.52788 4.59722L11.9344 10.9442L18.4721 4.4672C18.7664 4.17568 19.2413 4.17789 19.5328 4.47215Z" fill="white"/>
                                            </svg>
                                        </button>
                                    </div>
                                {/foreach}
                            {/if}
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
                            <button type="button" class="rs-go-checkout btn btn-{if $cart_data.has_error}gray{else}primary{/if} col-12" {if $cart_data.has_error} disabled{/if}>{t}Оформить заказ{/t}</button>

                            {if $THEME_SETTINGS.buy_one_click_in_cart}
                                {moduleinsert name="\Shop\Controller\Block\OneClickCart" disabled=$cart_data.has_error}
                            {/if}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    {else}
        <div class="text-center container col-lg-4 col-md-6 col-sm-8">
            <div class="mb-4">
                <img class="empty-page-img" src="{$THEME_IMG}/decorative/cart.svg" alt="">
            </div>
            <h2>{t}Корзина пуста{/t}</h2>
            <p class="mb-lg-6 mb-5">
                {t}В вашей корзине нет товаров.{/t}
                {t}Добавьте понравившиеся товары из каталога, они будут отображаться здесь{/t}
            </p>
            <a class="btn btn-primary rs-go-back">{t}Вернуться к покупкам{/t}</a>
        </div>
    {/if}
</div>