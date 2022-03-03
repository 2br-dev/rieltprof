{* Список товаров в блочном виде *}
{$imagelist = $product->getImages(false)}
<div {$product->getDebugAttributes()} data-id="{$product.id}"
                                      class="rs-product-item card card-product {if count($imagelist)>1}rs-photo-view{/if}
                                                               {if $product->isOffersUse() || $product->isMultiOffersUse()} rs-show-offer-select{/if}">
    <div class="card-product_ticket">
        {foreach $product->getMySpecDir() as $spec}
            {if $spec.is_label}
                <span class="ticket-new">{$spec.name}</span>
            {/if}
        {/foreach}

        {if $THEME_SETTINGS.enable_compare}
            <a class="ticket-compare rs-compare{if $product->inCompareList()} rs-in-compare{/if}" data-title="{t}сравнить{/t}" data-already-title="{t}В сравнении{/t}"></a>
        {/if}

        {if $THEME_SETTINGS.enable_favorite}
            <a class="ticket-favorite rs-favorite {if $product->inFavorite()}rs-in-favorite{/if}" data-title="{t}В избранное{/t}" data-already-title="{t}В избранном{/t}"></a>
        {/if}
    </div>
    <div class="card-image">
        <a href="{$product->getUrl()}"><img src="{$product->getMainImage()->getUrl(325, 224)}" alt="{$product.title}"></a>
    </div>
    <div class="card-text">
        <div class="card-product_category-name"><a href="{$product->getMainDir()->getUrl()}"><small>{$product->getMainDir()->name}</small></a></div>
        <div class="card-product_info">
            <div class="card-product_title">
                {hook name="catalog-list_products:blockview-title" title="{t}Просмотр категории продукции:название товара, блочный вид{/t}"}
                <a href="{$product->getUrl()}"><span>{$product.title}</span></a>
                {/hook}
            </div>
            {if $THEME_SETTINGS.enable_comments}
                <div class="card-product_rating">
                    <div class="pull-left">
                        <span title="{t rating=$product->getRatingBall()}Средняя оценка: %rating{/t}" class="rating">
                        <span style="width:{$product->getRatingPercent()}%" class="value"></span></span>
                    </div>
                    <div class="pull-right">
                        <div class="comments">
                            <a href="{$product->getUrl()}#comments">
                                <div class="icon-commenting">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="612px" height="612px" viewbox="0 0 612 612" style="enable-background:new 0 0 612 612;"
                                         xml:space="preserve"><g><g><g><path d="M401.625,325.125h-191.25c-10.557,0-19.125,8.568-19.125,19.125s8.568,19.125,19.125,19.125h191.25c10.557,0,19.125-8.568,19.125-19.125S412.182,325.125,401.625,325.125z M439.875,210.375h-267.75c-10.557,0-19.125,8.568-19.125,19.125s8.568,19.125,19.125,19.125h267.75c10.557,0,19.125-8.568,19.125-19.125S450.432,210.375,439.875,210.375z M306,0C137.012,0,0,119.875,0,267.75c0,84.514,44.848,159.751,114.75,208.826V612l134.047-81.339c18.552,3.061,37.638,4.839,57.203,4.839c169.008,0,306-119.875,306-267.75C612,119.875,475.008,0,306,0zM306,497.25c-22.338,0-43.911-2.601-64.643-7.019l-90.041,54.123l1.205-88.701C83.5,414.133,38.25,345.513,38.25,267.75c0-126.741,119.875-229.5,267.75-229.5c147.875,0,267.75,102.759,267.75,229.5S453.875,497.25,306,497.25z"></path></g></g></g></svg></div>
                                <span>{$product->getCommentsNum()}</span>
                            </a>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
        <div class="card-product_price">
            {$cur_cost = $product->getCost()}
            {$old_cost = $product->getOldCost()}
            {if $old_cost && $old_cost != $cur_cost}
                <span class="card-price_old">{$old_cost} {$product->getCurrency()}</span>
            {/if}
            <span class="card-price">{$cur_cost} {$product->getCurrency()}</span>

            <div class="pull-right">
                {hook name="catalog-list_products:blockview-buttons" product=$product title="{t}Просмотр категории продукции:кнопки, блочный вид{/t}"}
                    {if $shop_config  && !$product.disallow_manually_add_to_cart}
                        {if $product->shouldReserve()}
                            {if $product->isOffersUse() || $product->isMultiOffersUse()}
                                <a data-url="{$router->getUrl('shop-front-multioffers', ["product_id" => $product.id])}" class="link link-more pull-right rs-in-dialog">{t}Заказать{/t}</a>
                            {else}
                                <a data-url="{$router->getUrl('shop-front-reservation', ["product_id" => $product.id])}" class="link link-more pull-right rs-in-dialog">{t}Заказать{/t}</a>
                            {/if}
                        {else}
                            {if $check_quantity && $product->getNum()<1}
                                <span class="unobt pull-right" title="{t}Нет в наличии{/t}">{t}Нет в наличии{/t}</span>
                            {else}
                                {if $product->isOffersUse() || $product->isMultiOffersUse()}
                                    <a data-url="{$router->getUrl('shop-front-multioffers', ["product_id" => $product.id])}" class="link link-more pull-right rs-in-dialog">{t}В корзину{/t}</a>
                                {else}
                                    <a data-url="{$router->getUrl('shop-front-cartpage', ["add" => $product.id])}" class="link link-more pull-right rs-to-cart rs-no-show-cart" data-add-text="{t}Добавлено{/t}">{t}В корзину{/t}</a>
                                {/if}
                            {/if}
                        {/if}
                    {/if}
                {/hook}
            </div>
        </div>
    </div>
</div>