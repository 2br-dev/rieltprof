{foreach from=$toolbar->getItems() key=num item=item}
    <div class="tool-item{if isset($item.popup)} pnode{/if}">
        <a class="tool{if isset($item.class)} {$item.class}{/if}" href="{$item.href|default:"JavaScript:;"}" {if isset($item.onclick)}onclick="{$item.onclick}"{/if} title="{$item.title}" {foreach from=$item.attr key=key item=value}{$key}="{$value}" {/foreach}>
        <img src="{$item.img}" border="0" alt="{$item.title}">&nbsp;{$item.title}</a>
        {if isset($item.popup)}
            <ul class="popup">
                {foreach from=$item.popup item=popup}
                <li><a href="{$popup.href}" onclick="{$popup.onclick}" class="png {$popup.class}">{$popup.title}</a></li>
                {/foreach}
            </ul>
        {/if}
    </div>
{/foreach}