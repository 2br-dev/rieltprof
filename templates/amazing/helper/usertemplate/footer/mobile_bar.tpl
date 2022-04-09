{* Мобильный таб-бар внизу страницы в мобильной версии *}
{if !in_array($router->getCurrentRoute()->getId(), [
    'shop-front-checkout',
    'catalog-front-compare'
])}

{$route_id = $router->getCurrentRoute()->getId()}
<ul class="mobile-bar">
    <li>
        <a class="mobile-bar__link {if $route_id=='main.index'}mobile-bar__link_act{/if}" href="/">
            {if $CONFIG.logo_xs}
                <div class="mobile-bar__icon-wrapper">
                    {if $CONFIG.__logo_xs->getExtension() == 'svg'}
                        {$CONFIG.__logo_xs->getContent()}
                    {else}
                        <img src="{$CONFIG.__logo_xs->getUrl(48, 48, 'axy')}" width="24" height="24">
                    {/if}
                </div>
            {else}
                <svg width="24" height="24" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path class="cls-1" d="M30,15a1,1,0,0,1-.58-.19L16,5.23,2.58,14.81a1,1,0,0,1-1.16-1.62l14-10a1,1,0,0,1,1.16,0l14,10A1,1,0,0,1,30,15Z"/><path class="cls-1" d="M5,9A1,1,0,0,1,4,8V4A1,1,0,0,1,5,3H9A1,1,0,0,1,9,5H6V8A1,1,0,0,1,5,9Z"/><path class="cls-1" d="M25,29H20a1,1,0,0,1-1-1V21H13v7a1,1,0,0,1-1,1H7a3,3,0,0,1-3-3V16a1,1,0,0,1,2,0V26a1,1,0,0,0,1,1h4V20a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1v7h4a1,1,0,0,0,1-1V16a1,1,0,0,1,2,0V26A3,3,0,0,1,25,29Z"/></svg>
            {/if}
            <div>{t}Главная{/t}</div>
        </a>
    </li>
    <li>
        <a class="mobile-bar__link offcanvas-open" role="button" data-id="offcanvas-menu" data-extra-class="offcanvas-multilevel"
           data-load-url="{$router->getUrl('catalog-front-category', ['referer' => $url->selfUri()])}">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M2.05874 11.5829H4.44126C4.74984 11.5829 5 11.387 5 11.1454C5 10.9038 4.74984 10.7079 4.44126 10.7079H2.05874C1.75016 10.7079 1.5 10.9038 1.5 11.1454C1.5 11.387 1.75016 11.5829 2.05874 11.5829Z" />
                <path d="M2.16767 14.2079H6.08233C6.45106 14.2079 6.75 14.012 6.75 13.7704C6.75 13.5288 6.45106 13.3329 6.08233 13.3329H2.16767C1.79894 13.3329 1.5 13.5288 1.5 13.7704C1.5 14.012 1.79894 14.2079 2.16767 14.2079Z" />
                <path d="M2.14936 16.8329H8.72564C9.08426 16.8329 9.375 16.637 9.375 16.3954C9.375 16.1538 9.08426 15.9579 8.72564 15.9579H2.14936C1.79074 15.9579 1.5 16.1538 1.5 16.3954C1.5 16.637 1.79074 16.8329 2.14936 16.8329Z" />
                <path d="M15.7514 18.5829H2.12359C1.7792 18.5829 1.5 18.7788 1.5 19.0204C1.5 19.262 1.7792 19.4579 2.12359 19.4579H15.7514C16.0958 19.4579 16.375 19.262 16.375 19.0204C16.375 18.7788 16.0958 18.5829 15.7514 18.5829Z" />
                <path d="M14.5275 4.5C11.2055 4.5 8.5 7.2055 8.5 10.5275C8.5 13.8494 11.2055 16.5549 14.5275 16.5549C15.9791 16.5549 17.3135 16.0407 18.3554 15.1817H18.3615L21.5028 18.3292C21.7306 18.5569 22.1014 18.5569 22.3292 18.3292C22.5569 18.1014 22.5569 17.7367 22.3292 17.5089L19.1757 14.3615C19.1766 14.3595 19.1747 14.3566 19.1757 14.3555C20.0346 13.3136 20.5549 11.9792 20.5549 10.5275C20.5549 7.20556 17.8494 4.50006 14.5275 4.50006L14.5275 4.5ZM14.5275 5.66661C17.2189 5.66661 19.3883 7.83599 19.3883 10.5275C19.3883 13.2189 17.2189 15.3883 14.5275 15.3883C11.836 15.3883 9.66661 13.2189 9.66661 10.5275C9.66661 7.83599 11.836 5.66661 14.5275 5.66661Z" />
            </svg>
            <div>{t}Каталог{/t}</div>
        </a>
    </li>
    {if ConfigLoader::byModule('shop')}
    <li>
        {moduleinsert name="\Shop\Controller\Block\Cart" indexTemplate="blocks/cart/cart_mobile.tpl"}
    </li>
    {/if}
    {if $THEME_SETTINGS.enable_favorite}
        <li>
            {moduleinsert name="\Catalog\Controller\Block\Favorite" custom_class="mobile-bar__link{if $route_id == 'catalog-front-favorite'} mobile-bar__link_act{/if}"}
        </li>
    {/if}
    {if $THEME_SETTINGS.enable_compare}
    <li>
        {moduleinsert name="\Catalog\Controller\Block\Compare" custom_class="mobile-bar__link"}
    </li>
    {/if}
</ul>
{/if}