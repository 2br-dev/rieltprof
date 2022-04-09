<a class="basket showCart" id="cart" href="{$router->getUrl('shop-front-cartpage')}">
    <div class="cart"><span class="lineHolder"></span><span class="title">{t}КОРЗИНА{/t}</span></div>
    <p class="products">{t}товаров{/t}: <span class="value">{$cart_info.items_count}</span></p>
    <p class="cost">{t}сумма{/t}: <span class="value">{$cart_info.total}</span></p>
</a>