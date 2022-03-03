<article>
    {if !empty($article.image)}
    <img class="mainImage" src="{$article.__image->getUrl(700, 304, 'xy')}">
    {/if}
    {$article.content}
</article>