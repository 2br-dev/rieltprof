<section class="article">
    <div class="date">{$article.dateof|date_format:"d.m.Y H:i"}</div>
    <h3>{$article.title}</h3>
    <article>
        {if !empty($article.image)}
        <img class="mainImage" src="{$article.__image->getUrl(700, 304, 'xy')}" alt="{$article.title}"/>
        {/if}
        {$article.content}
    </article>
    {moduleinsert name="\Photo\Controller\Block\PhotoList" type="article" route_id_param="article_id"}
</section>