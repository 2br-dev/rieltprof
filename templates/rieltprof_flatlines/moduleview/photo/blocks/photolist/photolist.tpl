{if !empty($photos)}
<section class="sec sec-gallery">
    <h2 class="h2">{t}Фото{/t}</h2>
    <ul class="gallery">
        {foreach $photos as $photo}
        <li><a href="{$photo->getUrl(800, 600)}" title="{$photo.title}" class="photo-item" rel="lightbox"><img src="{$photo->getUrl(283, 283, 'cxy')}"></a></li>
        {/foreach}
    </ul>
</section>
{/if}