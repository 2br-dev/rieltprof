{if !empty($list)}
    <ul class="articles">
    {foreach from=$list item=item}
        <li {$item->getDebugAttributes()}>
            <span class="date">{$item.dateof|date_format:"d.m.Y H:i"}</span><br>
            <a class="link" href="{$item->getUrl()}">
                <span class="title">{$item.title}</span><br>
                <div class="preview">{if !empty($item.image)}<img src="{$item.__image->getUrl(120,120,'xy')}" alt="{$item.title}" class="image"/>{/if} 
                {$item->getPreview()}</div>
            </a>
        </li>
    {/foreach}
    </ul>

    {include file="%THEME%/paginator.tpl"}
{else}
    <p class="empty">{t}Не найдено ни одной статьи{/t}</p>
{/if} 