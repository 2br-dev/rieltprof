{if $THEME_SETTINGS.enable_favorite}
    {addjs file="{$mod_js}jquery.favorite.js" basepath="root"}
    <a id="favoriteBlock" class="favoriteLink{if $countFavorite} active{/if}" data-href="{$router->getUrl('catalog-front-favorite')}" data-favorite-url="{$router->getUrl('catalog-front-favorite')}">
        <span class="title">{t}Избранное{/t}</span>
        <span class="countFavorite">{$countFavorite}</span>
    </a>
{/if}