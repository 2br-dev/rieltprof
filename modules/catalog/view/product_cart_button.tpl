{* Шаблон реализует логику отображения кнопки в корзину для одного товара *}
{if $shop_config}
    {if !$product.disallow_manually_add_to_cart}
        {$has_any_offers = ($offers_data.offers && count($offers_data.offers) > 1) || ($offers_data.levels && !$offers_data.virtual)}
        {if $has_any_offers || !$THEME_SETTINGS.button_as_amount}
            {* Обычная кнопка, если есть комплектации или выключена опция "Заменять кнопку на количество" *}
            <div class="item-product-cart-action" {if $THEME_SETTINGS.show_offers_in_list}data-sol{/if}>
                <button type="button" class="btn btn-primary primary-svg w-100 rs-buy rs-to-cart" data-add-text="{t}Добавлено{/t}"
                        data-href="{$router->getUrl('shop-front-cartpage', ["add" => $product.id])}"
                        {if !$disable_multioffer_dialog && $has_any_offers}data-select-multioffer-href="{$router->getUrl('shop-front-multioffers', ["product_id" => $product.id])}"{/if}>
                        <img src="{$THEME_IMG}/icons/to-cart-white.svg" alt="">
                    <span class="ms-2">{t}В корзину{/t}</span>
                </button>

                <a data-href="{$router->getUrl('shop-front-reservation', ["product_id" => $product.id])}"
                   {if !$disable_multioffer_dialog && $has_any_offers}data-select-multioffer-href="{$router->getUrl('shop-front-multioffers', ["product_id" => $product.id])}"{/if}
                   class="w-100 btn btn-outline-primary outline-primary-svg rs-reserve">
                    {include "%THEME%/helper/svg/reserve.tpl"}
                    <span class="ms-2">{t}Заказать{/t}</span>
                </a>
                <div class="item-card__not-available rs-unobtainable">{t}Нет в наличии{/t}</div>
                <div class="item-card__not-available rs-bad-offer-error"></div>
            </div>
        {else}
            {* Кнопка с количеством. Может испольоваться только, если нет комплектаций *}
            {if $product->shouldReserve()}
                <div class="item-product-cart-action">
                    <a data-href="{$router->getUrl('shop-front-reservation', ["product_id" => $product.id])}"
                       class="w-100 btn btn-outline-primary outline-primary-svg rs-reserve">
                        {include "%THEME%/helper/svg/reserve.tpl"}
                        <span class="ms-2">{t}Заказать{/t}</span>
                    </a>
                </div>
            {else}
                {if $shop_config->check_quantity && $product->getNum() <= 0}
                    <div class="item-product-cart-action">
                        <div class="item-card__not-available rs-unobtainable">{t}Нет в наличии{/t}</div>
                    </div>
                {else}
                    <div class="item-product-cart-action rs-sa {if $product->inCart()}item-product-cart-action_amount{/if}"
                         data-amount-params='{$product->getAmountParamsJson()}'
                         data-url="{$router->getUrl('shop-front-cartpage', ['Act' => 'changeAmount'])}">
                        <div class="item-product-cart-action__to-cart">
                            <button type="button" class="btn btn-primary primary-svg w-100 rs-to-cart rs-no-modal-cart"
                                    data-href="{$router->getUrl('shop-front-cartpage', ["add" => $product.id])}">
                                <img src="{$THEME_IMG}/icons/to-cart-white.svg" alt="">
                                <span class="ms-2">{t}В корзину{/t}</span>
                            </button>
                        </div>
                        <div class="item-product-cart-action__amount">
                            <div class="item-product-amount">
                                <button class="item-product-amount__prev rs-sa-dec" type="button">
                                    {include "%THEME%/helper/svg/minus.tpl"}
                                </button>
                                <div class="item-product-amount__input">
                                    <input type="number" value="{$product->getAmountInCart()}" class="rs-sa-input">
                                    <span class="fs-6 ms-1">{$product->getUnit()->stitle}</span>
                                </div>
                                <button class="item-product-amount__next rs-sa-inc" type="button">
                                    {include "%THEME%/helper/svg/plus.tpl"}
                                </button>
                            </div>
                        </div>
                    </div>
                {/if}
            {/if}
        {/if}
    {/if}
{elseif $catalog_config.buyinoneclick}
    <div class="item-product-cart-action">
        <a data-href="{$router->getUrl('catalog-front-oneclick',["product_id"=>$product.id])}"
           class="btn btn-primary primary-svg w-100 rs-buy-one-click rs-in-dialog">
            {include "%THEME%/helper/svg/hand.tpl"}
            <span class="ms-2">{t}Купить{/t}</span>
        </a>
    </div>
{/if}