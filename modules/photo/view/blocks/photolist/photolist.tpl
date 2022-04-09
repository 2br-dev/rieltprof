{addjs file="libs/fslightbox.min.js"}
{if !empty($photos)}
    <div class="mt-5">
        <h2>{t}Фото{/t}</h2>

        <ul class="gallery">
            {foreach $photos as $photo}
                <li>
                    <a data-fslightbox="gallery" href="{$photo->getUrl(1200, 1200)}" title="{$photo.title}">
                        <img src="{$photo->getUrl(280, 210, 'cxy')}">
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
{/if}