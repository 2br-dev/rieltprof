{extends file="%THEME%/wrapper.tpl"}
{block name="content"}
    <div class="filters-block desktop{if $query == ""} active{/if}" id="filters">
        <a href="" class="close-filters-trigger"></a>
        <a href="" class="">Применить</a>
        {moduleinsert name="\Catalog\Controller\Block\SideFilters"}
    </div>

{*    <div class="content">*}
        {$app->blocks->getMainContent()}
{*    </div>*}
{/block}
