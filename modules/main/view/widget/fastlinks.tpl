{addcss file="%main%/fastlinks.css"}
<div class="m-l-20 m-r-20">
    {if $links}
        <ul class="fastlinks">
            {foreach $links as $item}
                <li>
                    <i class="icon zmdi {$item.icon}" style="background-color:{$item.bgcolor}"></i>
                    <a href="{$item.link}" {if $item.target == 'blank'}target="_blank"{/if}>{$item.title}</a>
                </li>
            {/foreach}
        </ul>
    {else}
        <div class="empty-widget">
            {t}Не добавлено ни одной ссылки{/t}
        </div>
    {/if}
</div>