<div class="global-wrapper">
{*    <div class="categories-sidebar collapsed">*}
{*        {include file="%catalog%/sidebar-catalog.tpl"}*}
{*    </div>*}
    {block name="content"}
        {moduleinsert name="\Main\Controller\Block\BreadCrumbs"}
        <div class="title">
            <span>Избранное</span>
        </div>
        {$app->blocks->getMainContent()}
    {/block}
    {include file='%rieltprof%/statusbar.tpl'}
</div>
