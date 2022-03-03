{* Шаблон страницы оформления заказа. Используется, если в административной панели опция "Тип оформления заказа" установлена в
 * значение "Оформление на одной странице", "Оформление в корзине". *}
{addcss file="%shop%/order/checkout.css"}
{$config = ConfigLoader::byModule('shop')}

{if $cart->getProductItems()}
    <div class="{if $config->getCheckoutType() == 'cart_checkout'}cartCheckout{else}onePageCheckout{/if}">
        {if $config->getCheckoutType() == 'cart_checkout'}
            {moduleinsert name='Shop\Controller\Block\CartFull'}
        {/if}
        {moduleinsert name='Shop\Controller\Block\Checkout'}
    </div>
{else}
    <div class="empty-list">
        <div><img src="{$mod_img}/icons/cart-empty.svg"></div>
        <h1>Корзина пуста</h1>
        <p>В вашей корзине еще нет товаров. Добавьте понравившиеся товары из каталога, они будут отображаться здесь</p>
        <p><a class="link link-more" href="{$router->getUrl('main.index')}">Вернуться на главную</a></p>
    </div>
{/if}