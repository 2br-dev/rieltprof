{* Список новостей *}
<h1>{$dir.title}</h1>
{if $list}
    <div class="mt-5">
        <div class="row row-cols-xl-4 row-cols-md-3 row-cols-sm-2 g-md-6 g-3">
            {foreach $list as $item}
                <div {$item->getDebugAttributes()}>
                    <a class="news-card" href="{$item->getUrl()}">
                        <div class="news-card__img">
                            <canvas width="356" height="200"></canvas>
                            {if $item.image}
                                <img src="{$item.__image->getUrl(356, 200, 'cxy')}"
                                     srcset="{$item.__image->getUrl(712, 400, 'cxy')} 2x"
                                     alt="{$item.title}">
                            {else}
                                <img src="{$THEME_IMG}/decorative/news-empty.svg" alt="">
                            {/if}
                        </div>
                        <div class="news-card__body">
                            <div class="news-card__date">{$item.dateof|date_format:"d.m.Y H:i"}</div>
                            <div class="news-card__title">{$item.title}</div>
                        </div>
                    </a>
                </div>
            {/foreach}
        </div>
    </div>
    {include file="%THEME%/paginator.tpl"}
{else}
    {include file="%THEME%/helper/usertemplate/include/empty_list.tpl" reason="{t}Не найдено ни одной статьи{/t}"}
{/if}