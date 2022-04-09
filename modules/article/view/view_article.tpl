{* Просмотр одной новости *}
<h1>{$article.title}</h1>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center">
        <img src="{$THEME_IMG}/icons/time.svg" alt="">
        <span class="ms-2 text-gray fs-5">{$article.dateof|dateformat:"@date @time"}</span>
    </div>
    <div class="d-flex align-items-center">
        <div class="d-none d-sm-block me-2">{t}Поделиться{/t}:</div>
        <script src="https://yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
        <script src="https://yastatic.net/share2/share.js"></script>
        <div class="ya-share2" data-services="collections,vkontakte,facebook,odnoklassniki,moimir"></div>
    </div>
</div>

{if $article.image}
    <div class="mb-md-5 mb-4 text-center">
        <img src="{$article.__image->getUrl(992, 559, 'xy')}" alt="{$article.title}" loading="lazy">
    </div>
{/if}
<article {$article->getDebugAttributes()}>
    {$article.content}
</article>

{moduleinsert name="\Photo\Controller\Block\PhotoList" type="article" route_id_param="article_id"}

{moduleinsert name="\Article\Controller\Block\ArticleProducts" article_id=$article.id}

<div class="mt-md-6 mt-5 d-grid d-sm-block">
    <a href="{$article->getCategory()->getUrl()}" class="btn btn-outline-primary col col-sm-auto">{t}Вернуться к списку{/t}</a>
</div>
