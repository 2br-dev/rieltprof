{* Шаблон страницы оформления заказа. Используется, если в административной панели опция "Тип оформления заказа" установлена в
 * значение "Оформление на одной странице", "Оформление в корзине". *}
{$config = ConfigLoader::byModule('shop')}

{if $cart->getProductItems()}
<section class="section {if $config->getCheckoutType() == 'cart_checkout'}cartCheckout{else}onePageCheckout{/if}">
    <div class="container">
        {if $config->getCheckoutType() == 'cart_checkout'}
            <div class="row row-cols-lg-2">
                {moduleinsert name='Shop\Controller\Block\CartFull'}
                {moduleinsert name='Shop\Controller\Block\Checkout'}
            </div>
        {else}
            <div class="row">
                {moduleinsert name='Shop\Controller\Block\Checkout'}
            </div>
        {/if}
    </div>
</section>
{else}
    <section class="section 100vh">
        <div class="container">
            <div class="text-center container col-lg-4 col-md-6 col-sm-8">
                <div class="mb-4">
                    <img class="empty-page-img" src="{$THEME_IMG}/decorative/cart.svg" alt="">
                </div>
                <h2>{t}Корзина пуста{/t}</h2>
                <p class="mb-lg-6 mb-5">
                    {t}В вашей корзине еще нет товаров.{/t}
                    {t}Добавьте понравившиеся товары из каталога, они будут отображаться здесь{/t}
                </p>
                <a href="{$router->getRootUrl()}" class="btn btn-primary">{t}Вернуться на главную{/t}</a>
            </div>
        </div>
    </section>
{/if}