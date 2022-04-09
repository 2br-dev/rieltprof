{* Корзина во всплывающем блоке *}
{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Товары в корзине{/t}{/block}
{block "class"}modal-lg{/block}
{block "attributes"}id="rs-cart-page"{/block}
{block "body"}
    {if $cart_data.items}
        <form method="POST" action="{$router->getUrl('shop-front-cartpage', ["Act" => "update", "floatCart" => $floatCart])}" id="rs-cart-form">
            {hook name="shop-cartpage-float:products" title="{t}Корзина:товары{/t}" product_items=$product_items}
                {foreach $cart_data.items as $index => $item}
                    {$product = $product_items[$index].product}
                    {$cartitem = $product_items[$index].cartitem}
                    {if !empty($cartitem.multioffers)}
                        {$multioffers = unserialize($cartitem.multioffers)}
                    {/if}

                    <div class="modal-cart-item rs-cart-item {if $item.amount_error} cart-item_error{/if}" data-id="{$cartitem.entity_id}" data-uniq="{$index}">
                        <div class="row g-3 g-lg-4">
                            <div class="col-lg d-flex overflow-hidden">
                                <a href="{$product->getUrl()}" class="modal-cart-item__img">
                                    <img src="{$product->getOfferMainImage($cartitem.offer, 64, 64, 'xy')}"
                                         srcset="{$product->getOfferMainImage($cartitem.offer, 128, 128, 'xy')} 2x" alt="{$product.title}" loading="lazy">
                                </a>
                                <div class="d-flex flex-column">
                                    <a class="modal-cart-item__title" href="{$product->getUrl()}">{$cartitem.title}</a>
                                    {if $product->isMultiOffersUse()}
                                        <div class="mt-2">
                                            {foreach $product.multioffers.levels as $level}
                                                {if !empty($level.values)}
                                                    <div class="d-flex align-items-center fs-6">
                                                        <div class="text-gray">{$level.title|default:$level.prop_title}:</div>
                                                        <div class="ms-1">
                                                            <span>{$multioffers[$level.prop_id].value}</span>
                                                        </div>
                                                    </div>
                                                {/if}
                                            {/foreach}
                                        </div>
                                    {elseif $product->isOffersUse()}
                                        {$offers = $product->getOffers()}
                                        <div class="d-flex align-items-center mt-2 fs-6">
                                            <div class="text-gray">{$product.offer_caption|default:"{t}Комплектация{/t}"}</div>
                                            <div class="ms-1">
                                                <span>{$offers[$cartitem.offer].title}</span>
                                            </div>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="row h-100 g-2">
                                    <div class="col-lg overflow-hidden text-end">
                                        <span class="modal-cart-item__price">{$item.cost}</span>
                                        {if $item.discount_unformated > 0}
                                            <div class="modal-cart-item__old-price">{$item.base_cost}</div>
                                        {/if}
                                    </div>
                                    <div class="col-lg-12 col d-flex justify-content-lg-end mt-auto align-items-center">
                                        {if !$cartitem->getForbidRemove()}
                                            <a class="modal-cart-item__delete order-lg-last rs-remove" href="{$router->getUrl('shop-front-cartpage', ["Act" => "removeItem", "id" => $index, "floatCart" => $floatCart])}">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14.5 9C14.2238 9 14 9.21419 14 9.47847V18.5215C14 18.7856 14.2238 19 14.5 19C14.7762 19 15 18.7856 15 18.5215V9.47847C15 9.21419 14.7762 9 14.5 9Z"/>
                                                    <path d="M8.5 9C8.22383 9 8 9.21419 8 9.47847V18.5215C8 18.7856 8.22383 19 8.5 19C8.77617 19 9 18.7856 9 18.5215V9.47847C9 9.21419 8.77617 9 8.5 9Z"/>
                                                    <path d="M4.39222 7.95419V19.4942C4.39222 20.1762 4.65398 20.8168 5.11123 21.2764C5.56639 21.7373 6.19981 21.9989 6.86271 22H16.1373C16.8004 21.9989 17.4338 21.7373 17.8888 21.2764C18.346 20.8168 18.6078 20.1762 18.6078 19.4942V7.95419C19.5167 7.72366 20.1057 6.8846 19.9841 5.99339C19.8624 5.10236 19.0679 4.43583 18.1274 4.43565H15.6176V3.85017C15.6205 3.35782 15.4167 2.88505 15.052 2.53724C14.6872 2.18961 14.1916 1.99604 13.6764 2.00006H9.32363C8.80835 1.99604 8.3128 2.18961 7.94803 2.53724C7.58326 2.88505 7.37952 3.35782 7.38239 3.85017V4.43565H4.87265C3.93209 4.43583 3.13764 5.10236 3.01586 5.99339C2.89427 6.8846 3.48326 7.72366 4.39222 7.95419ZM16.1373 21.0632H6.86271C6.0246 21.0632 5.37261 20.3753 5.37261 19.4942V7.99536H17.6274V19.4942C17.6274 20.3753 16.9754 21.0632 16.1373 21.0632ZM8.36277 3.85017C8.35952 3.60628 8.45986 3.37154 8.641 3.19938C8.82195 3.02721 9.06819 2.93262 9.32363 2.93683H13.6764C13.9318 2.93262 14.1781 3.02721 14.359 3.19938C14.5401 3.37136 14.6405 3.60628 14.6372 3.85017V4.43565H8.36277V3.85017ZM4.87265 5.37242H18.1274C18.6147 5.37242 19.0097 5.74987 19.0097 6.21551C19.0097 6.68114 18.6147 7.05859 18.1274 7.05859H4.87265C4.38533 7.05859 3.99031 6.68114 3.99031 6.21551C3.99031 5.74987 4.38533 5.37242 4.87265 5.37242Z"/>
                                                    <path d="M11.5 9C11.2238 9 11 9.21419 11 9.47847V18.5215C11 18.7856 11.2238 19 11.5 19C11.7762 19 12 18.7856 12 18.5215V9.47847C12 9.21419 11.7762 9 11.5 9Z"/>
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
                                    <div class="col-auto order-lg-first">
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

                    {$concomitant=$product->getConcomitant()}
                    {foreach $item.sub_products as $id => $sub_product_data}
                        {$sub_product=$concomitant[$id]}
                        {if $sub_product_data.checked}

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
                                                <span class="modal-cart-item__price">{$sub_product_data.cost}</span>
                                                <div class="modal-cart-item__discount">
                                                    {if $sub_product_data.discount_unformated > 0}
                                                        {t}скидка{/t} {$sub_product_data.discount}
                                                    {/if}
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col d-flex justify-content-lg-end mt-auto">

                                                <div class="form-check form-switch">
                                                    <input class="form-check-input rs-concomitant-checkbox" type="checkbox" id="concomitantCartItem{$product.id}" name="products[{$index}][concomitant][]"
                                                           value="{$sub_product.id}" {if $sub_product_data.checked}checked{/if}>

                                                    <label class="form-check-label" for="concomitantCartItem{$product.id}"></label>
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

            {hook name="shop-cartpage-float:summary" title="{t}Корзина:итог{/t}"}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="text-gray me-3">{t}Итого:{/t}</div>
                    <div class="fs-2 fw-bold text-nowrap">{$cart_data.total}</div>
                </div>
                <div class="row g-4 justify-content-end align-items-center row-cols-1 row-cols-lg-auto">
                    <div>
                        <a href="{$router->getUrl('shop-front-cartpage')}" class="btn btn-primary w-100">{t}Перейти в корзину{/t}</a>
                    </div>
                    <div class="text-center order-lg-first">
                        <a href="{$router->getUrl('shop-front-cartpage')}" data-bs-dismiss="modal" aria-label="Close">{t}Вернуться к покупкам{/t}</a>
                    </div>
                </div>
            {/hook}
        </form>
    {else}
        <div class="text-center">
            <div class="mb-4">
                <img class="empty-page-img" src="{$THEME_IMG}/decorative/cart.svg" alt="{t}Корзина пуста{/t}">
            </div>
            <h3>{t}Корзина пуста{/t}</h3>
        </div>
    {/if}
{/block}