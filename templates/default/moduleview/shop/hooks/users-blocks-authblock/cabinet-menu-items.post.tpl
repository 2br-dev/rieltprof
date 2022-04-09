{$shop_config = ConfigLoader::byModule('shop')}
{$route_id = $router->getCurrentRoute()->getId()}

{if $shop_config.recurring_show_methods_menu}
    <li class="item">
        <a {if $route_id=='shop-front-mysavedpaymentmethods'}class="active"{/if} href="{$router->getUrl('shop-front-mysavedpaymentmethods')}">
            {t}Мои карты{/t}
        </a>
    </li>
{/if}