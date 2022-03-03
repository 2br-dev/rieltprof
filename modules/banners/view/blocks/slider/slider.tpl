{nocache}
{addjs file="{$mod_js}jquery.photoslider.js" basepath="root"}
{/nocache}
{if $zone}
{assign var=banners value=$zone->getBanners()}
<div class="bannerSlider">
    <ul class="banners">
        {foreach from=$banners item=banner}
        <li class="item {if $banner@first} act{/if}" {$banner->getDebugAttributes()}>
            <a {if $banner.link}href="{$banner.link}"{/if} {if $banner.targetblank}target="_blank"{/if}><img src="{$banner->getBannerUrl($zone.width, $zone.height, 'axy')}" alt="{$banner.title}"></a>
        </li>
        {/foreach}
    </ul>
    <div class="pages">
    {foreach from=$banners item=banner}
        <a href="#" class="{if $banner@first}act{/if}"></a>
    {/foreach}
    </div>
</div>
{/if}