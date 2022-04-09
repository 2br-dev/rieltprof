{if $THEME_SETTINGS.enable_favorite}
    {addjs file="%catalog%/rscomponent/favorite.js"}
    <a class="{$param.custom_class|default:"head-icon-link"}{if $countFavorite} active{/if} rs-favorite-block"
       href="{$router->getUrl('catalog-front-favorite')}" data-favorite-url="{$router->getUrl('catalog-front-favorite')}">
        <span class="position-relative">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.5753 1.5C14.7876 1.5 13.1142 2.34796 12 3.76871C10.8858 2.34791 9.2124 1.5 7.42468 1.5C4.15782 1.5 1.5 4.28439 1.5 7.70694C1.5 10.3869 3.02538 13.4868 6.03369 16.9203C8.34882 19.5627 10.8673 21.6086 11.584 22.1727L11.9999 22.5L12.4157 22.1727C13.1324 21.6086 15.651 19.5628 17.9662 16.9204C20.9746 13.4869 22.5 10.387 22.5 7.70694C22.5 4.28439 19.8422 1.5 16.5753 1.5ZM16.9461 15.9395C15.0419 18.1128 12.9931 19.8697 11.9999 20.6794C11.0066 19.8697 8.958 18.1128 7.05374 15.9394C4.32628 12.8264 2.88462 9.97966 2.88462 7.70694C2.88462 5.08429 4.92129 2.95058 7.42468 2.95058C9.07209 2.95058 10.5932 3.89123 11.3945 5.40549L12 6.54981L12.6055 5.40549C13.4067 3.89128 14.9279 2.95058 16.5753 2.95058C19.0787 2.95058 21.1154 5.08424 21.1154 7.70694C21.1154 9.97975 19.6737 12.8265 16.9461 15.9395Z"/>
            </svg>
            <span class="label-count rs-favorite-items-count">{$countFavorite}</span>
        </span>
        <div {if !$param.custom_class}class="mt-2"{/if}>{t}Избранное{/t}</div>
    </a>
{/if}