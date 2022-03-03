{* Просмотр одной новости *}

<div class="sec-content_wrapper">
    <div class="sec-content_text" {$article->getDebugAttributes()}>
        <p class="date">{$article.dateof|dateformat:"@date @time"}</p>

        {if $article.image}
            <div class="col-sm-6 sec-content_head_title">
                <h1>{$article.title}</h1>
            </div>
            <div class="col-sm-6 sec-content_head_image">
                    <img class="mainImage" src="{$article.__image->getUrl(750, 300, 'xy')}" alt="{$article.title}"/>
            </div>
        {else}
            <h1>{$article.title}</h1>
        {/if}

        <article>
            {$article.content}
        </article>
    </div>
</div>

{moduleinsert name="\Photo\Controller\Block\PhotoList" type="article" route_id_param="article_id"}