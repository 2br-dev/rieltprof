{* Блок, отображает последние новости *}

{if $category && $news}
    <section class="sec sec-news">
        <div class="title anti-container">
            <div class="container-fluid">
                <a class="title-text" href="{$router->getUrl('article-front-previewlist', [category => $category->getUrlId()])}">{$category.title}</a>
            </div>
        </div>

        {$has_big = $news.0.image}
        <div class="clearfix row-news">
                {if $has_big}
                    <div class="col-xs-12 col-md-6">
                        {$item = $news.0}
                        <div class="news news-block" {$item->getDebugAttributes()}>
                            <div class="news-image">
                                <a href="{$item->getUrl()}"><img src="{$item.__image->getUrl(750, 300)}" alt="{$item.title}"></a>
                            </div>
                            <div class="news-text">
                                <div class="news-block_publisher"><small>{$item.dateof|dateformat:"%d %v %Y, %H:%M"}</small></div>
                                <div class="news-block_title"><a href="{$item->getUrl()}"><span>{$item.title}</span></a></div>
                                <div class="news-block_description">
                                    {$item->getPreview()}
                                </div>
                                <div class="news-block_link"><a href="{$item->getUrl()}" class="link link-more">{t}Подробнее{/t}</a></div>
                            </div>
                        </div>
                    </div>
                {/if}

                {if $has_big}
                    {* Если первая новость с фотографией, то отображаем крупно её и еще 3 мелкие новости *}
                    {$chunked_news = array_chunk(array_slice($news, 1, 3), 3)}
                {else}
                    {* Если первая новость без фото, то отображаем 6 мелких новостей *}
                    {$chunked_news = array_chunk(array_slice($news, 0, 6), 3)}
                {/if}

                {foreach $chunked_news as $n => $chunk}
                    <div class="col-xs-12 col-md-6">
                        {foreach $chunk as $item}
                            <div class="news news-list" {$item->getDebugAttributes()}>
                                <div class="news-text">
                                    <div class="news-block_publisher"><small>{$item.dateof|dateformat:"%d %v %Y, %H:%M"}</small></div>
                                    <div class="news-block_title"><a href="{$item->getUrl()}"><span>{$item.title}</span></a></div>
                                    <div class="news-block_description">
                                        {$item->getPreview()}
                                    </div>
                                    <div class="news-block_link"><a href="{$item->getUrl()}" class="link link-more">{t}Подробнее{/t}</a></div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                {/foreach}
        </div>
    </section>

{else}
    <div class="col-padding">
        {include file="%THEME%/block_stub.tpl"  class="blockLastNews" do=[
            [
                'title' => t("Добавьте категорию с новостями"),
                'href' => {adminUrl do=false mod_controller="article-ctrl"}
            ],
            [
                'title' => t("Настройте блок"),
                'href' => {$this_controller->getSettingUrl()},
                'class' => 'crud-add'
            ]
        ]}
    </div>
{/if}