{* Блок, отображает общее количество товаров в избранном *}
{if $THEME_SETTINGS.enable_favorite}
    {addjs file="rs.favorite.js"}
    <div class="gridblock rs-favorite-block{if $countFavorite} active{/if}" data-favorite-url="{$router->getUrl('catalog-front-favorite')}">
        <div class="cart-wrapper">
            <div class="cart-block">
                <div class="cart-block-wrapper">

                    <div class="icon-favorite rs-favorite-link" data-href="{$router->getUrl('catalog-front-favorite')}" >
                        <i class="i-svg i-svg-favorite"></i>
                        <i class="counter rs-favorite-items-count">{$countFavorite}</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}