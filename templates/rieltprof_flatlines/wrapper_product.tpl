{block name="content"}
<main>
    <div class="global-wrapper" id="object">
        <div class="categories-sidebar collapsed">
            {include file="%catalog%/sidebar-catalog.tpl"}
        </div>
{*        <div class="content">*}
            {$app->blocks->getMainContent()}
{*        </div>*}
        {include file='%rieltprof%/statusbar.tpl'}
    </div>
    <div
        class="fab-wrapper phone"
        data-user="{$product['owner']}"
        data-url="{$router->getAdminUrl('getOwnerPhone', [], 'rieltprof-tools')}"
        data-product="{$product['id']}"
    >
        <a href="" class="fab-menu-closer"></a>
        <a href="" class="fab-phone-link"></a>
    </div>
</main>
<div class="shadow"></div>
{/block}





{*{extends file="%THEME%/wrapper.tpl"}*}
{*{block name="content"}*}
{*    <div class="content">*}
{*        {$app->blocks->getMainContent()}*}
{*    </div>*}
{*{/block}*}
