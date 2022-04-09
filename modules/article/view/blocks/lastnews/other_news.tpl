<div class="h2">{t}Другие новости{/t}</div>
<ul class="article-other-news">
    {foreach $news as $item}
    <li {$item->getDebugAttributes()}>
        <a href="{$item->getUrl()}">
            <div class="fs-6 text-gray mb-2">{$item.dateof|dateformat:"%d %v %Y, %H:%M"}</div>
            <div>{$item.title}</div>
        </a>
    </li>
    {/foreach}
</ul>