{if $category && $news}
<h2 class="mtop40"><span>{t}новости{/t}</span></h2>
<ul class="newsLine">
    {foreach from=$news item=item}
    <li {$item->getDebugAttributes()}>
        <p class="date">{$item.dateof|dateformat:"%d %v %Y, %H:%M"}</p>
        <a href="{$item->getUrl()}">{$item.title}</a>
    </li>
    {/foreach}
</ul>
<a href="{$router->getUrl('article-front-previewlist', [category => $category->getUrlId()])}" class="onemore">{t}все новости{/t}</a>
{else}
    {include file="theme:default/block_stub.tpl"  class="blockLastNews" do=[
        [
            'title' => t("Добавьте категорию с новостями"),
            'href' => {adminUrl do=false mod_controller="article-ctrl"}
        ],        
        [
            'title' => t("Настройте блок"),
            'href' => {$this_controller->getSettingUrl()},
            'class' => 'crud-add'
        ]        
    ]}
{/if}