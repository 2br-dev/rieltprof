{if $elem.image}
    <span data-preview-url="{$elem.__image->getUrl(200, 200)}" class="cell-image pvl-item-image">
        <img src="{$elem.__image->getUrl(30, 16, 'axy')}"></span>    
{/if}