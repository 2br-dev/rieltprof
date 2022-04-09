{if $category && $news}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="h1 m-0">{$category.title|default:"{t}Новости{/t}"}</div>
        <div class="d-none d-sm-block ms-3">
            <a href="{$router->getUrl('article-front-previewlist', [category => $category->getUrlId()])}" class="btn btn-primary">{t}Все новости{/t}</a>
        </div>
    </div>
    <div class="row row-cols-lg-4 row-cols-sm-2 g-md-6 g-3">
        {foreach $news as $item}
            <div {$item->getDebugAttributes()}>
                <a class="news-card" href="{$item->getUrl()}">
                    <div class="news-card__img">
                        <canvas width="356" height="200"></canvas>
                        {if $item.image}
                            <img src="{$item.__image->getUrl(356, 200)}" alt="">
                        {else}
                            <img src="{$THEME_IMG}/decorative/news-empty.svg" alt="">
                        {/if}
                    </div>
                    <div class="news-card__body">
                        <div class="news-card__date">{$item.dateof|dateformat:"%d %v %Y, %H:%M"}</div>
                        <div class="news-card__title">{$item.title}</div>
                    </div>
                </a>
            </div>
        {/foreach}
    </div>
    <div class="mt-4 d-sm-none">
        <a href="{$router->getUrl('article-front-previewlist', [category => $category->getUrlId()])}" class="btn btn-primary col-12">{t}Все новости{/t}</a>
    </div>
{else}
    <div class="h1 m-0 mb-4">{t}Новости{/t}</div>

    {capture assign = "skeleton_html"}
        <div class="row row-cols-lg-4 row-cols-sm-2 g-lg-4 g-3">
            <div>
                <img class="w-100" width="359" height="315" src="{$THEME_IMG}/skeleton/skeleton-news.svg" alt="">
            </div>
            <div>
                <img class="w-100" width="359" height="315" src="{$THEME_IMG}/skeleton/skeleton-news.svg" alt="">
            </div>
            <div class="d-lg-block d-none">
                <img class="w-100" width="359" height="315" src="{$THEME_IMG}/skeleton/skeleton-news.svg" alt="">
            </div>
            <div class="d-lg-block d-none">
                <img class="w-100" width="359" height="315" src="{$THEME_IMG}/skeleton/skeleton-news.svg" alt="">
            </div>
        </div>
    {/capture}

    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
    name = "{t}Новости{/t}"
    skeleton = $skeleton_html
    do = [
        [
            'title' => "{t}Добавить категорию с новостями{/t}",
            'href' => "{adminUrl do=false mod_controller="article-ctrl"}"
        ],
        [
            'title' => "{t}Настроить блок{/t}",
            'href' => {$this_controller->getSettingUrl()},
            'class' => 'crud-add'
        ]
    ]}
{/if}