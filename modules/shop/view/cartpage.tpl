{* Шаблон корзины *}
{$shop_config=ConfigLoader::byModule('shop')}
{$catalog_config=ConfigLoader::byModule('catalog')}
{$product_items=$cart->getProductItems()}
{$floatCart=$smarty.request.floatCart}
{if $floatCart}

    {* Это корзина во всплывающем окне *}
    {include file="%shop%/cartpage_popup.tpl"}

{else}

    {* Это корзина на отдельной странице *}
    {include file="%shop%/cartpage_page.tpl"}

{/if}