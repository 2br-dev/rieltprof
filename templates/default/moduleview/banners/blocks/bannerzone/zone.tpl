{if $zone}
{if $param.rotate}
    {$onebanner=$zone->getOneBanner()}
    {if $onebanner['id']}
        {$banners = [0 => $onebanner]}
    {/if}
{else}
    {$banners = $zone->getBanners()}
{/if}
{if $banners}
<div class="sideBanners">
    {foreach from=$banners item=banner}
    <div class="banner" {$banner->getDebugAttributes()}>
        <a {if $banner.link}href="{$banner.link}"{/if} {if $banner.targetblank}target="_blank"{/if}><img src="{$banner->getBannerUrl($zone.width, $zone.height)}" alt="{$banner.title}"></a>
    </div>
    {/foreach}
</div>
{/if}
{/if}