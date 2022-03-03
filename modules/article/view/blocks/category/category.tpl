{* Список категорий из 2-х уровней*}
{if $dirlist}
    <ul class="category">
        {foreach from=$dirlist item=dir}
            <li {if in_array($dir.fields.id, $pathids)}class="act"{/if} {$dir.fields->getDebugAttributes()}><a href="{$dir.fields->getUrl()}">{$dir.fields.title}</a>
            </li>
        {/foreach}
    </ul>
{else}
    {include file="theme:default/block_stub.tpl"  class="blockCategory" do=[
        [
            'title' => t("Добавьте категории"),
            'href' => {adminUrl do=false mod_controller="article-ctrl"}
        ]
    ]}
{/if}