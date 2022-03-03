<h4>{t}КАТАЛОГ ТОВАРОВ{/t}</h4>
<ul>
    {foreach from=$dirlist item=item}
    <li {if in_array($item.fields.id, $pathids)}class="act"{/if} {$item.fields->getDebugAttributes()}><a href="{$item.fields->getUrl()}">{$item.fields.name}</a>
    {/foreach}
</ul>