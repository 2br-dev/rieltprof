{if $error}
    <div class="empty-widget">
        {$error}
    </div>
{elseif $items}
    <div class="mp-modules__list{if count($items)==1} no-columns{/if}">
        {foreach $items as $item}
            <div class="item">
                <a class="pic" href="{adminUrl do=false mod_controller="marketplace-ctrl"}#{$item.url}"><img src="{$item.image}"></a>
                <p class="description">{$item.description}</p>
            </div>
        {/foreach}
    </div>
{else}
    <div class="empty-widget">
        {t}Нет рекомендуемых виджетов{/t}
    </div>
{/if}