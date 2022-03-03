{* Шаблон блока корзины, который отображается на всех страницах *}

<div class="gridblock{if $cart_info.items_count} active{/if}" id="rs-cart">
    <div class="cart-wrapper rs-cart-line">
        <div class="cart-block">
            <div class="cart-block-wrapper">
                <div class="t-drop-basket rs-popup-cart"></div>

                <div class="icon-cart {if $router->getCurrentRoute()->getId() != 'shop-front-cartpage'}rs-show-cart{/if}" data-href="{$router->getUrl('shop-front-cartpage')}">
                    <i class="i-svg i-svg-cart"></i>
                    <i class="counter rs-cart-items-count">{$cart_info.items_count}</i>
                </div>
            </div>
        </div>
    </div>
</div>