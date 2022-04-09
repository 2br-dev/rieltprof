{if $brands}
    <div class="brandList">
        {foreach from=$brands item=brand}
            <div class="oneBrand" {$brand->getDebugAttributes()}>
                <a href="{$brand->getUrl()}" alt="{$brand.title}"><img src="{$brand->getMainImage(195, 100, 'axy')}" alt="{$brand.title}"/><br><span>{$brand.title}</span></a>
            </div>
        {/foreach}
    </div>
{else}
    <p class="empty">{t}Нет ни одного бренда{/t}</p>
{/if}