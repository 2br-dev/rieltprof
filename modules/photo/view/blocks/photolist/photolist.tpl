{addjs file="jcarousel/jquery.jcarousel.min.js"}
{if !empty($photos)}
<section class="recommended">
    <h2><span>{t}Фото{/t}</span></h2>
    <div class="previewList">
        <div class="gallery">
            <ul>
                {foreach from=$photos item=photo}
                <li><a href="{$photo->getUrl(800, 600)}" title="{$photo.title}" class="photoitem" rel="photolist"><img src="{$photo->getUrl(64, 64)}"></a></li>
                {/foreach}
            </ul>
        </div>
        <a class="control prev"></a>
        <a class="control next"></a>
    </div>
</section>

<script type="text/javascript">
    $(function() {
       $('.photoitem').colorbox({
           className: 'titleMargin',
           opacity:0.2
       });
    });
</script>
{/if}