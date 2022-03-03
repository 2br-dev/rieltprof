{* Список новостей *}

<section class="catalog-news">
    <div class="catalog-sort">
        <h2 class="h2">{$dir.title}</h2>
    </div>

    {if $list}

        <div class="row">
            <div class="catalog-table catalog-news">
                {foreach $list as $item}
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3" {$item->getDebugAttributes()}>
                        <div class="news news-block">
                            <div class="news-image">
                                <a href="{$item->getUrl()}">
                                    {if $item.image}
                                        <img src="{$item.__image->getUrl(750, 300, 'xy')}" alt="{$item.title}"></a>
                                    {else}
                                        <img src="{$THEME_IMG}/icons/news.svg" alt="" width="128px">
                                    {/if}
                                </a>
                            </div>
                            <div class="news-text">
                                <div class="news-block_publisher"><small>{$item.dateof|date_format:"d.m.Y H:i"}</small></div>
                                <div class="news-block_title"><a href="{$item->getUrl()}"><span>{$item.title}</span></a></div>
                                <div class="news-block_description">
                                    {$item->getPreview()}
                                </div>
                                <div class="news-block_link"><a href="{$item->getUrl()}" class="link link-more">{t}Подробнее{/t}</a></div>
                            </div>
                        </div>
                    </div>
                {/foreach}

            </div>
        </div>
        {include file="%THEME%/paginator.tpl"}

    {else}

        <p class="empty-list">{t}Не найдено ни одной статьи{/t}</p>

    {/if}
</section>